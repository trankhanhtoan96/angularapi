<?php


namespace SpiceCRM\includes\database;

use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/*********************************************************************************
 * Description: This file generates the appropriate manager for the database
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


/**
 * Database driver factory
 * @api
 * Instantiates and configures appropriate DB drivers
 */
class DBManagerFactory
{
    /**
     * hold the instances
     * @var array
     */
    static $instances = [];

    /**
     * name of the instance
     * @var string
     */
    static $instanceName = '';

    /**
     * Returns a reference to the DB object of specific type
     *
     * @param string $type DB type
     * @param array $config DB configuration
     * @return object DBManager instance
     */
    public static function getTypeInstance($type, $config = [])
    {
        if (empty($config['db_manager'])===false) {
            // standard types
            switch ($type) {
                case "mysql":
                case "mysqli":
                    $my_db_manager = '\\SpiceCRM\\includes\\database\\MysqliManager';
                    break;
                case "mssql":
                    $my_db_manager = '\\SpiceCRM\\includes\\database\\SqlsrvManager';
                    break;
                default:
                    $my_db_manager = self::getManagerByType($type, false);
                    if (empty($my_db_manager)) {
                        LoggerManager::getLogger()->fatal("unable to load DB manager for: $type");
                        sugar_die("Cannot load DB manager");
                    }
            }
        } else {
            $my_db_manager = '\\SpiceCRM\\includes\\database\\' . $config['db_manager'];
        }


        if (class_exists($my_db_manager)) {
            return new $my_db_manager();
        } else {
            return null;
        }
    }

    /**
     * Returns a reference to the DB object for instance $instanceName, or the default
     * instance if one is not specified
     *
     * @param string $instanceName optional, name of the instance
     * @return object DBManager instance
     */
    public static function getInstance($instanceName = '')
    {
        self::$instanceName = $instanceName;
        static $count = 0, $old_count = 0;

        //fall back to the default instance name
        if (!isset(self::$instances[self::$instanceName])) {
            $config = SpiceConfig::getInstance()->config['dbconfig'];
            $count++;
            self::$instances[self::$instanceName] = self::getTypeInstance($config['db_type'], $config);
            if (!empty(SpiceConfig::getInstance()->config['dbconfigoption'])) {
                self::$instances[self::$instanceName]->setOptions(SpiceConfig::getInstance()->config['dbconfigoption']);
            }
            self::$instances[self::$instanceName]->connect($config, true);
            self::$instances[self::$instanceName]->count_id = $count;
            self::$instances[self::$instanceName]->references = 0;
            self::$instances[self::$instanceName]->resetQueryCount();
        } else {
            $old_count++;
            self::$instances[self::$instanceName]->references = $old_count;
        }
        return self::$instances[self::$instanceName];
    }

    /**
     * switch an instance to a new db name
     *
     * @param $dbName
     * @param string $instanceName
     */
    public static function switchInstance($dbName, $config, $instanceName = '')
    {
        if (isset(self::$instances[$instanceName])) {
            self::$instances[$instanceName]->disconnect();
            //    $config = SpiceConfig::getInstance()->config['dbconfig'];
            $config['dbconfig']['db_name'] = $dbName;
            self::$instances[$instanceName] = self::getTypeInstance($config['dbconfig']['db_type'], $config['dbconfig']);
            if (!empty($config['dbconfigoption'])) {
                self::$instances[$instanceName]->setOptions($config['dbconfigoption']);
            }
            self::$instances[$instanceName]->connect($config['dbconfig'], true);
            self::$instances[$instanceName]->references = 0;
            self::$instances[$instanceName]->resetQueryCount();

            return self::$instances[$instanceName];
        }
    }

    /**
     * Disconnect all DB connections in the system
     */
    public static function disconnectAll()
    {
        foreach (self::$instances as $instance) {
            $instance->disconnect();
        }
        self::$instances = [];
    }

    // TODO add hasInstance: boolean function

    /**
     * Get DB manager class name by type name
     *
     * For use in install
     * @param string $type
     * @param bool $validate Return only valid drivers or any?
     * @return string
     */
    public static function getManagerByType($type, $validate = true)
    {
        $drivers = self::getDbDrivers($validate);
        if (!empty($drivers[$type])) {
            return get_class($drivers[$type]);
        }
        return false;
    }

    /**
     * Scan directory for valid DB drivers
     * @param string $dir
     * @param array $drivers
     * @param bool $validate Return only valid drivers or all of them?
     */
    protected static function scanDriverDir($dir, &$drivers, $validate = true)
    {
        if (!is_dir($dir)) return;
        $scandir = opendir($dir);
        if ($scandir === false) return;
        while (($name = readdir($scandir)) !== false) {
            if (substr($name, -11) != "Manager.php") continue;
            if ($name == "DBManager.php") continue;
            // require_once("$dir/$name");
            $classname = '\\SpiceCRM\\includes\\database\\' . substr($name, 0, -4);
            if (!class_exists($classname)) continue;
            $driver = new $classname;
            if (!$validate || $driver->valid()) {
                if (empty($drivers[$driver->dbType])) {
                    $drivers[$driver->dbType] = [];
                }
                $drivers[$driver->dbType][] = $driver;
            }
        }

    }

    /**
     * Compares two drivers by priority
     * @param object $a
     * @param object $b
     * @return int
     * @internal
     */
    public static function _compareDrivers($a, $b)
    {
        return $b->priority - $a->priority;
    }

    /**
     * Get list of all available DB drivers
     * @param bool $validate Return only valid drivers or all of them?
     * @return array List of Db drivers, key - variant (mysql, mysqli), value - driver type (mysql, mssql)
     */
    public static function getDbDrivers($validate = true)
    {
        $drivers = [];
        self::scanDriverDir("include/database", $drivers, $validate);
        self::scanDriverDir("custom/include/database", $drivers, $validate);

        $result = [];
        foreach ($drivers as $type => $tdrivers) {
            if (empty($tdrivers)) continue;
            if (count($tdrivers) > 1) {
                usort($tdrivers, [__CLASS__, "_compareDrivers"]);
            }
            $result[$type] = $tdrivers[0];
        }
        return $result;
    }

    /**
     * Check if we have freeTDS driver installed
     * Invoked when connected to mssql. checks if we have freetds version of mssql library.
     * the response is put into a global variable.
     * @return bool
     */
    public static function isFreeTDS()
    {
        static $is_freetds = null;

        if ($is_freetds === null) {
            ob_start();
            phpinfo(INFO_MODULES);
            $info = ob_get_contents();
            ob_end_clean();

            $is_freetds = (strpos($info, 'FreeTDS') !== false);
        }

        return $is_freetds;
    }

}

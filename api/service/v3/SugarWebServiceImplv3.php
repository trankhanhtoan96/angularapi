<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\includes\authentication\AuthenticationController;

if (!defined('sugarEntry')) define('sugarEntry', true);



/**
 * This class is an implemenatation class for all the rest services
 */
require_once('service/core/SugarWebServiceImpl.php');
require_once('SugarWebServiceUtilv3.php');


class SugarWebServiceImplv3 extends SugarWebServiceImpl
{

    public function __construct()
    {
        self::$helperObject = new SugarWebServiceUtilv3();
    }


    /**
     * Log the user into the application
     *
     * @param UserAuth array $user_auth -- Set user_name and password (password needs to be
     *      in the right encoding for the type of authentication the user is setup for.  For Base
     *      sugar validation, password is the plain text password.
     * @param String $application -- The name of the application you are logging in from.  (Currently unused).
     * @param array $name_value_list -- Array of name value pair of extra parameters. As of today only 'language' and 'notifyonsave' is supported
     * @return Array - id - String id is the session_id of the session that was created.
     *                 - module_name - String - module name of user
     *                 - name_value_list - Array - The name value pair of user_id, user_name, user_language, user_currency_id, user_currency_name,
     *                                         - user_default_team_id, user_is_admin, user_default_dateformat, user_default_timeformat
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    public function login($user_auth, $application = '', $name_value_list = [])
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->login');
        global $system_config;
        $error = new SoapError();

        try {
            AuthenticationController::getInstance()->authenticate($user_auth['user_name'], $user_auth['password']);
        } catch (\SpiceCRM\includes\ErrorHandlers\UnauthorizedException $e) {
            $error->set_error($e->getMessage());
            LoggerManager::getLogger()->fatal('Lockout reached for user ' . $user_auth['user_name']);
            LogicHook::getInstance()->call_custom_logic('Users', 'login_failed');
            self::$helperObject->setFaultObject($error);
            return;
        }


        $system_config = BeanFactory::getBean('Administration');
        $system_config->retrieveSettings('system');


        session_start();
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        //$current_user = $user;
        self::$helperObject->login_success($name_value_list);
        $current_user->loadPreferences();
        $_SESSION['is_valid_session'] = true;
        $_SESSION['ip_address'] = query_client_ip();
        $_SESSION['user_id'] = $current_user->id;
        $_SESSION['type'] = 'user';
        $_SESSION['avail_modules'] = self::$helperObject->get_user_module_list($current_user);
        $_SESSION['authenticated_user_id'] = $current_user->id;
        $_SESSION['unique_key'] = SpiceConfig::getInstance()->config['unique_key'];
        $current_user->call_custom_logic('after_login');
        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->login - succesful login');
            $nameValueArray = [];
        global $current_language;
        $nameValueArray['user_id'] = self::$helperObject->get_name_value('user_id', $current_user->id);
        $nameValueArray['user_name'] = self::$helperObject->get_name_value('user_name', $current_user->user_name);
        $nameValueArray['user_language'] = self::$helperObject->get_name_value('user_language', $current_language);
        $cur_id = $current_user->getPreference('currency');
        $nameValueArray['user_currency_id'] = self::$helperObject->get_name_value('user_currency_id', $cur_id);
        $nameValueArray['user_is_admin'] = self::$helperObject->get_name_value('user_is_admin', is_admin($current_user));
        $nameValueArray['user_default_team_id'] = self::$helperObject->get_name_value('user_default_team_id', $current_user->default_team);
        $nameValueArray['user_default_dateformat'] = self::$helperObject->get_name_value('user_default_dateformat', $current_user->getPreference('datef'));
        $nameValueArray['user_default_timeformat'] = self::$helperObject->get_name_value('user_default_timeformat', $current_user->getPreference('timef'));
        $currencyObject = BeanFactory::getBean('Currencies');
        $currencyObject->retrieve($cur_id);
        $nameValueArray['user_currency_name'] = self::$helperObject->get_name_value('user_currency_name', $currencyObject->name);
        $_SESSION['user_language'] = $current_language;
        return ['id' => session_id(), 'module_name' => 'Users', 'name_value_list' => $nameValueArray];
        LogicHook::getInstance()->call_custom_logic('Users', 'login_failed');
        $error->set_error('invalid_login');
        self::$helperObject->setFaultObject($error);
        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->login - failed login');
    }

    /**
     * Retrieve the md5 hash of the vardef entries for a particular module.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param String|array $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
     * @return String The md5 hash of the vardef definition.
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_module_fields_md5($session, $module_name)
    {

        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_module_fields_md5(v3) for module: ' . print_r($module_name, true));

        $results = [];
        if (is_array($module_name)) {
            foreach ($module_name as $module)
                $results[$module] = md5(serialize(self::get_module_fields($session, $module)));
        } else
            $results[$module_name] = md5(serialize(self::get_module_fields($session, $module_name)));

        return $results;
    }

    /**
     * Gets server info. This will return information like version, flavor and gmt_time.
     * @return Array - flavor - String - Retrieve the specific flavor of sugar.
     *                 - version - String - Retrieve the version number of Sugar that the server is running.
     *                 - gmt_time - String - Return the current time on the server in the format 'Y-m-d H:i:s'. This time is in GMT.
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_server_info()
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_server_info');
        global $sugar_version;
        if (empty($sugar_version)) {
            require_once('sugar_version.php');
        }
        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_server_info');

    	return ['flavor' => 'Spice', 'version' => $sugar_version, 'gmt_time' => TimeDate::getInstance()->nowDb()];
    } // fn

    /**
     * Retrieve the layout metadata for a given module given a specific type and view.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param array $module_name (s) -- The name of the module(s) to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
     * @return array $type The type(s) of views requested.  Current supported types are 'default' (for application) and 'wireless'
     * @return array $view The view(s) requested.  Current supported types are edit, detail, list, and subpanel.
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_module_layout($session, $a_module_names, $a_type, $a_view, $md5 = FALSE)
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_module_layout');

        global $beanList, $beanFiles;
        $error = new SoapError();
        $results = [];
        foreach ($a_module_names as $module_name) {
            if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
                LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_module_layout');
                continue;
            }

            $class_name = $beanList[$module_name];
            require_once($beanFiles[$class_name]);
            $seed = new $class_name();

            foreach ($a_view as $view) {
                $aclViewCheck = (strtolower($view) == 'subpanel') ? 'DetailView' : ucfirst(strtolower($view)) . 'View';
                if ($seed->ACLAccess($aclViewCheck, true)) {
                    foreach ($a_type as $type) {
                        $a_vardefs = self::$helperObject->get_module_view_defs($module_name, $type, $view);
                        if ($md5)
                            $results[$module_name][$type][$view] = md5(serialize($a_vardefs));
                        else
                            $results[$module_name][$type][$view] = $a_vardefs;
                    }
                }
            }
        }

        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_module_layout');

        return $results;
    }

    /**
     * Retrieve the md5 hash of a layout metadata for a given module given a specific type and view.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param array $module_name (s) -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
     * @return array $type(s) The type of view requested.  Current supported types are 'default' (for application) and 'wireless'
     * @return array $view(s) The view requested.  Current supported types are edit, detail, and list.
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_module_layout_md5($session, $module_name, $type, $view)
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_module_layout_md5');
        $results = self::get_module_layout($session, $module_name, $type, $view, TRUE);
            return ['md5'=> $results];
        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_module_layout_md5');
    }

    /**
     * Retrieve the list of available modules on the system available to the currently logged in user.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param String $filter --  Valid values are: all     - Return all modules,
     *                                             default - Return all visible modules for the application
     *                                             mobile  - Return all visible modules for the mobile view
     * @return Array    'modules' -- Array - An array of module names
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_available_modules($session, $filter = 'all')
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_available_modules');

        $error = new SoapError();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $error->set_error('invalid_login');
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_available_modules');
            return;
        } // if

    	$modules = [];
        $availModules = array_keys($_SESSION['avail_modules']); //ACL check already performed.
        switch ($filter) {
            case 'default':
                $modules = self::$helperObject->get_visible_modules($availModules);
                break;
            case 'all':
            default:
                $modules = $availModules;
        }

        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_available_modules');
    	return ['modules'=> $modules];
    } // fn

    /**
     * Retrieve a list of recently viewed records by module.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param String $modules -- An array of modules or 'Home' to indicate all.
     * @return Array The recently viewed records
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_last_viewed($session, $module_names)
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_last_viewed');
        $error = new SoapError();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $error->set_error('invalid_login');
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_last_viewed');
            return;
        } // if

        $results = [];
        foreach ($module_names as $module) {
            if (!self::$helperObject->check_modules_access(AuthenticationController::getInstance()->getCurrentUser(), $module, 'read')) {
                LoggerManager::getLogger()->debug("SugarWebServiceImpl->get_last_viewed: NO ACCESS to $module");
                continue;
            }

            if ($module == 'Home') $module = '';
            $tracker = BeanFactory::getBean('Trackers');
            $entryList = $tracker->get_recently_viewed(AuthenticationController::getInstance()->getCurrentUser()->id, $module);
            foreach ($entryList as $entry)
                $results[] = $entry;
        }

        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_last_viewed');
        return $results;
    }

    /**
     * Retrieve a list of upcoming activities including Calls, Meetings,Tasks and Opportunities
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @return Array List of upcoming activities
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_upcoming_activities($session)
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_upcoming_activities');
        $error = new SoapError();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $error->set_error('invalid_login');
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_upcoming_activities');
            return;
        } // if

        $results = self::$helperObject->get_upcoming_activities();

        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_upcoming_activities');

        return $results;
    }

    /**
     * Given a list of modules to search and a search string, return the id, module_name, along with the fields
     * We will support Accounts, Bugs, Cases, Contacts, Leads, Opportunities, Project, Quotes
     *
     * @param string $session - Session ID returned by a previous call to login.
     * @param string $search_string - string to search
     * @param string[] $modules - array of modules to query
     * @param int $offset - a specified offset in the query
     * @param int $max_results - max number of records to return
     * @param string $assigned_user_id - a user id to filter all records by, leave empty to exclude the filter
     * @param string[] $select_fields - An array of fields to return.  If empty the default return fields will be from the active list view defs.
     * @return Array return_search_result    - Array('Accounts' => array(array('name' => 'first_name', 'value' => 'John', 'name' => 'last_name', 'value' => 'Do')))
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function search_by_module($session, $search_string, $modules, $offset, $max_results, $assigned_user_id = '', $select_fields = [])
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->search_by_module');
        global $beanList, $beanFiles;
        global $current_language;

        $error = new SoapError();
    	$output_list = [];
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', '', '', '', $error)) {
            $error->set_error('invalid_login');
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->search_by_module');
            return;
        }
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if ($max_results > 0) {
            SpiceConfig::getInstance()->config['list_max_entries_per_page'] = $max_results;
        }

        require_once('include/utils/UnifiedSearchAdvanced.php');
        require_once 'include/utils.php';
        $usa = new UnifiedSearchAdvanced();
        if (!file_exists($cachedfile = sugar_cached('modules/unified_search_modules.php'))) {
            $usa->buildCache();
        }

        include($cachedfile);
    	$modules_to_search = [];
    	$unified_search_modules['Users'] =   ['fields' => []];

        foreach ($unified_search_modules as $module => $data) {
            if (in_array($module, $modules)) {
                $modules_to_search[$module] = $beanList[$module];
            } // if
        } // foreach

        LoggerManager::getLogger()->info('SugarWebServiceImpl->search_by_module - search string = ' . $search_string);

        if (!empty($search_string) && isset($search_string)) {
            $search_string = trim(DBManagerFactory::getInstance()->quote(securexss(from_html(clean_string($search_string, 'UNIFIED_SEARCH')))));
            foreach ($modules_to_search as $name => $beanName) {
        		$where_clauses_array = [];
    			$unifiedSearchFields = [];
                foreach ($unified_search_modules[$name]['fields'] as $field => $def) {
                    $unifiedSearchFields[$name] [$field] = $def;
                    $unifiedSearchFields[$name] [$field]['value'] = $search_string;
                }

                require_once $beanFiles[$beanName];
                $seed = new $beanName();
                require_once 'include/SearchForm/SearchForm2.php';
                if ($beanName == "User") {
                    if (!self::$helperObject->check_modules_access($current_user, $seed->module_dir, 'read')) {
                        continue;
                    } // if
                    if (!$seed->ACLAccess('ListView')) {
                        continue;
                    } // if
                }

                if ($beanName != "User") {
                    $searchForm = new SearchForm ($seed, $name);

    				$searchForm->setup([$name => []],$unifiedSearchFields , '' , 'saved_views' /* hack to avoid setup doing further unwanted processing */ ) ;
                    $where_clauses = $searchForm->generateSearchWhere();
                    require_once 'include/SearchForm/SearchForm2.php';
                    $searchForm = new SearchForm ($seed, $name);

    				$searchForm->setup([$name => []],$unifiedSearchFields , '' , 'saved_views' /* hack to avoid setup doing further unwanted processing */ ) ;
                    $where_clauses = $searchForm->generateSearchWhere();
                    $emailQuery = false;

                    $where = '';
                    if (count($where_clauses) > 0) {
                        $where = '(' . implode(' ) OR ( ', $where_clauses) . ')';
                    }

                    $mod_strings = return_module_language($current_language, $seed->module_dir);

                    if (count($select_fields) > 0)
                        $filterFields = $select_fields;
                    else {
                        if (file_exists('custom/modules/' . $seed->module_dir . '/metadata/listviewdefs.php'))
                            require_once('custom/modules/' . $seed->module_dir . '/metadata/listviewdefs.php');
                        else
                            require_once('modules/' . $seed->module_dir . '/metadata/listviewdefs.php');

        				$filterFields = [];
                        foreach ($listViewDefs[$seed->module_dir] as $colName => $param) {
                            if (!empty($param['default']) && $param['default'] == true)
                                $filterFields[] = strtolower($colName);
                        }
                        if (!in_array('id', $filterFields))
                            $filterFields[] = 'id';
                    }

                    //Pull in any db fields used for the unified search query so the correct joins will be added
    				$selectOnlyQueryFields = [];
                    foreach ($unifiedSearchFields[$name] as $field => $def) {
                        if (isset($def['db_field']) && !in_array($field, $filterFields)) {
                            $filterFields[] = $field;
                            $selectOnlyQueryFields[] = $field;
                        }
                    }

                    //Add the assigned user filter if applicable
                    if (!empty($assigned_user_id) && isset($seed->field_defs['assigned_user_id'])) {
                        $ownerWhere = $seed->getOwnerWhere($assigned_user_id);
                        $where = "($where) AND $ownerWhere";
                    }

    				$ret_array = $seed->create_new_list_query('', $where, $filterFields, [], 0, '', true, $seed, true);
    		        if(empty($params) or !is_array($params)) $params = [];
                    if (!isset($params['custom_select'])) $params['custom_select'] = '';
                    if (!isset($params['custom_from'])) $params['custom_from'] = '';
                    if (!isset($params['custom_where'])) $params['custom_where'] = '';
                    if (!isset($params['custom_order_by'])) $params['custom_order_by'] = '';
                    $main_query = $ret_array['select'] . $params['custom_select'] . $ret_array['from'] . $params['custom_from'] . $ret_array['where'] . $params['custom_where'] . $ret_array['order_by'] . $params['custom_order_by'];
                } else {
                    if ($beanName == "User") {
    					$filterFields = ['id', 'user_name', 'first_name', 'last_name', 'email_address'];
                        $main_query = "select users.id, ea.email_address, users.user_name, first_name, last_name from users ";
                        $main_query = $main_query . " LEFT JOIN email_addr_bean_rel eabl ON eabl.bean_module = '{$seed->module_dir}'
    LEFT JOIN email_addresses ea ON (ea.id = eabl.email_address_id) ";
                        $main_query = $main_query . "where ((users.first_name like '{$search_string}') or (users.last_name like '{$search_string}') or (users.user_name like '{$search_string}') or (ea.email_address like '{$search_string}')) and users.deleted = 0 and users.is_group = 0 and users.employee_status = 'Active'";
                    } // if
                } // else

                LoggerManager::getLogger()->info('SugarWebServiceImpl->search_by_module - query = ' . $main_query);
                if ($max_results < -1) {
                    $result = $seed->db->query($main_query);
                } else {
                    if ($max_results == -1) {
                        $limit = SpiceConfig::getInstance()->config['list_max_entries_per_page'];
                    } else {
                        $limit = $max_results;
                    }
                    $result = $seed->db->limitQuery($main_query, $offset, $limit + 1);
                }

    			$rowArray = [];
                while ($row = $seed->db->fetchByAssoc($result)) {
    				$nameValueArray = [];
                    foreach ($filterFields as $field) {
                        if (in_array($field, $selectOnlyQueryFields))
                            continue;
    					$nameValue = [];
                        if (isset($row[$field])) {
                            $nameValueArray[$field] = self::$helperObject->get_name_value($field, $row[$field]);
                        } // if
                    } // foreach
                    $rowArray[] = $nameValueArray;
                } // while
    			$output_list[] = ['name' => $name, 'records' => $rowArray];
            } // foreach

            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->search_by_module');
    	return ['entry_list'=>$output_list];
        } // if
    	return ['entry_list'=>$output_list];
    } // fn

    /**
     * Retrieve a collection of beans that are related to the specified bean and optionally return relationship data for those related beans.
     * So in this API you can get contacts info for an account and also return all those contact's email address or an opportunity info also.
     *
     * @param String $session -- Session ID returned by a previous call to login.
     * @param String $module_name -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
     * @param String $module_id -- The ID of the bean in the specified module
     * @param String $link_field_name -- The name of the lnk field to return records from.  This name should be the name the relationship.
     * @param String $related_module_query -- A portion of the where clause of the SQL statement to find the related items.  The SQL query will already be filtered to only include the beans that are related to the specified bean.
     * @param Array $related_fields - Array of related bean fields to be returned.
     * @param Array $related_module_link_name_to_fields_array - For every related bean returrned, specify link fields name to fields info for that bean to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address'))).
     * @param Number $deleted -- false if deleted records should not be include, true if deleted records should be included.
     * @param String $order_by -- field to order the result sets by
     * @return Array 'entry_list' -- Array - The records that were retrieved
     *                 'relationship_list' -- Array - The records link field data. The example is if asked about accounts contacts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )
     * @exception 'SoapFault' -- The SOAP error, if any
     */
    function get_relationships($session, $module_name, $module_id, $link_field_name, $related_module_query, $related_fields, $related_module_link_name_to_fields_array, $deleted, $order_by = '')
    {
        LoggerManager::getLogger()->info('Begin: SugarWebServiceImpl->get_relationships');
        global $beanList, $beanFiles;
        $error = new SoapError();
        if (!self::$helperObject->checkSessionAndModuleAccess($session, 'invalid_session', $module_name, 'read', 'no_access', $error)) {
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_relationships');
            return;
        } // if

        $class_name = $beanList[$module_name];
        require_once($beanFiles[$class_name]);
        $mod = new $class_name();
        $mod->retrieve($module_id);

        if (!self::$helperObject->checkQuery($error, $related_module_query, $order_by)) {
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_relationships');
            return;
        } // if

        if (!self::$helperObject->checkACLAccess($mod, 'DetailView', $error, 'no_access')) {
            LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_relationships');
            return;
        } // if

        $output_list = [];
    	$linkoutput_list = [];

        // get all the related mmodules data.
        $result = self::$helperObject->getRelationshipResults($mod, $link_field_name, $related_fields, $related_module_query, $order_by);
        if (self::$helperObject->isLogLevelDebug()) {
            LoggerManager::getLogger()->debug('SoapHelperWebServices->get_relationships - return data for getRelationshipResults is ' . var_export($result, true));
        } // if
        if ($result) {
            $list = $result['rows'];
            $filterFields = $result['fields_set_on_rows'];

            if (sizeof($list) > 0) {
                // get the related module name and instantiate a bean for that.
                $submodulename = $mod->$link_field_name->getRelatedModuleName();
                $submoduleclass = $beanList[$submodulename];
                require_once($beanFiles[$submoduleclass]);

                $submoduletemp = new $submoduleclass();
                foreach ($list as $row) {
                    $submoduleobject = @clone($submoduletemp);
                    // set all the database data to this object
                    foreach ($filterFields as $field) {
                        $submoduleobject->$field = $row[$field];
                    } // foreach
                    if (isset($row['id'])) {
                        $submoduleobject->id = $row['id'];
                    }
                    $output_list[] = self::$helperObject->get_return_value_for_fields($submoduleobject, $submodulename, $filterFields);
                    if (!empty($related_module_link_name_to_fields_array)) {
                        $linkoutput_list[] = self::$helperObject->get_return_value_for_link_fields($submoduleobject, $submodulename, $related_module_link_name_to_fields_array);
                    } // if

                } // foreach
            }

        } // if

        LoggerManager::getLogger()->info('End: SugarWebServiceImpl->get_relationships');
    	return ['entry_list'=>$output_list, 'relationship_list' => $linkoutput_list];

    } // fn
}

SugarWebServiceImplv3::$helperObject = new SugarWebServiceUtilv3();

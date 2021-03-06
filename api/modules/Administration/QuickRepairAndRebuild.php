<?php

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarCache\SugarCache;
use SpiceCRM\includes\SugarObjects\LanguageManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\includes\utils\SpiceUtils;

class RepairAndClear
{
    public $module_list;
    public $show_output;
    protected $actions;
    public $execute;
    protected $module_list_from_cache;

    public function repairAndClearAll($selected_actions, $modules, $autoexecute=false, $show_output=true)
    {
        global $mod_strings;
        $this->module_list= $modules;
        $this->show_output = $show_output;
        $this->actions = $selected_actions;
        $this->actions[] = 'repairDatabase';
        $this->execute=$autoexecute;

        //clear vardefs always..
        $this->clearVardefs();
        //first  clear the language cache.
        $this->clearLanguageCache();
        foreach ($this->actions as $current_action)
        switch($current_action)
        {
            case 'repairDatabase':
                if(in_array($mod_strings['LBL_ALL_MODULES'], $this->module_list))
                    $this->repairDatabase();
                else
                    $this->repairDatabaseSelectModules();
                break;
            case 'rebuildExtensions':
                $this->rebuildExtensions();
                break;
            case 'clearVardefs':
                $this->clearVardefs();
                break;
            case 'rebuildAuditTables':
                $this->rebuildAuditTables();
                break;
            case 'clearSearchCache':
                $this->clearUniSearchCache();
                break;
            case 'clearAll':
                $this->clearVardefs();
                $this->clearLanguageCache();
                $this->clearUniSearchCache();
                $this->rebuildExtensions();
                $this->rebuildAuditTables();
                $this->repairDatabase();
                break;
        }
    }

	/////////////OLD


	public function repairDatabase()
	{
		global $dictionary, $mod_strings;
		if(false == $this->show_output)
			$_REQUEST['repair_silent']='1';
		$_REQUEST['execute']=$this->execute;
        $GLOBALS['reload_vardefs'] = true;
        $hideModuleMenu = true;
		include_once('modules/Administration/repairDatabase.php');
	}

	public function repairDatabaseSelectModules()
	{
		global $mod_strings, $dictionary;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
		set_time_limit(3600);

		$db = DBManagerFactory::getInstance();

		if (is_admin($current_user) || is_admin_for_any_module($current_user))
		{
			$export = false;
    		if($this->show_output) echo getClassicModuleTitle($mod_strings['LBL_REPAIR_DATABASE'], [$mod_strings['LBL_REPAIR_DATABASE']], false);
            if($this->show_output) {
                echo "<h1 id=\"rdloading\">{$mod_strings['LBL_REPAIR_DATABASE_PROCESSING']}</h1>";
                ob_flush();
            }
	    	$sql = '';
			if($this->module_list && !in_array($mod_strings['LBL_ALL_MODULES'],$this->module_list))
			{
				$repair_related_modules = array_keys($dictionary);
				//repair DB
				$dm = inDeveloperMode();
				SpiceConfig::getInstance()->config['developerMode'] = true;
				foreach($this->module_list as $bean_name)
				{
                    if (class_exists(SpiceModules::getInstance()->getModuleName($bean_name))) {

						$GLOBALS['reload_vardefs'] = true;

						$focus = new (SpiceModules::getInstance()->getBeanClassForBeanName($bean_name))();
						#30273
						if ($focus->disable_vardefs == false) {
							include(SpiceUtils::getCustomFileIfExists('modules/' . $focus->module_dir . '/vardefs.php'));

							if ($this->show_output) {
                                print_r("<p>" .$mod_strings['LBL_REPAIR_DB_FOR'].' '. $bean_name . "</p>");
                            }
							$sql .= $db->repairTable($focus, $this->execute);
						}
					}
				}

				SpiceConfig::getInstance()->config['developerMode'] = $dm;

		        if ($this->show_output) echo "<script type=\"text/javascript\">document.getElementById('rdloading').style.display = \"none\";</script>";
	    		if (isset ($sql) && !empty ($sql))
	    		{
					$qry_str = "";
					foreach (explode("\n", $sql) as $line) {
						if (!empty ($line) && substr($line, -2) != "*/") {
							$line .= ";";
						}

						$qry_str .= $line . "\n";
					}
					if ($this->show_output){
						echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_DIFFERENCES']}</h3>";
						echo "<p>{$mod_strings['LBL_REPAIR_DATABASE_TEXT']}</p>";

						echo "<form method=\"post\" action=\"index.php?module=Administration&amp;action=repairDatabase\">";
						echo "<textarea name=\"sql\" rows=\"24\" cols=\"150\" id=\"repairsql\">$qry_str</textarea>";
						echo "<br /><input type=\"submit\" value=\"".$mod_strings['LBL_REPAIR_DATABASE_EXECUTE']."\" name=\"raction\" /> <input type=\"submit\" name=\"raction\" value=\"".$mod_strings['LBL_REPAIR_DATABASE_EXPORT']."\" />";
					}
				}
				else
					if ($this->show_output) echo "<h3>{$mod_strings['LBL_REPAIR_DATABASE_SYNCED']}</h3>";
			}

		}
		else {
			sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
		}
	}

	public function rebuildExtensions()
	{
		global $mod_strings;
		if($this->show_output) echo $mod_strings['LBL_QR_REBUILDEXT'];
		$current_user = AuthenticationController::getInstance()->getCurrentUser();
		require_once('ModuleInstall/ModuleInstaller.php');
		$mi = new ModuleInstaller();
		$mi->rebuild_all(!$this->show_output);

		// Remove the "Rebuild Extensions" red text message on admin logins

        if($this->show_output) echo $mod_strings['LBL_REBUILD_REL_UPD_WARNING'];

// cleanup. Tbale versions no longer exists (since spicecrm 2020.03.001)
//        // clear the database row if it exists (just to be sure)
//        $query = "DELETE FROM versions WHERE name='Rebuild Extensions'";
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->info($query);
//        \SpiceCRM\includes\database\DBManagerFactory::getInstance()->query($query);
//
//        // insert a new database row to show the rebuild extensions is done
//        $id = create_guid();
//        $gmdate = gmdate('Y-m-d H:i:s');
//        $date_entered = db_convert("'$gmdate'", 'datetime');
//        $query = 'INSERT INTO versions (id, deleted, date_entered, date_modified, modified_user_id, created_by, name, file_version, db_version) '
//            . "VALUES ('$id', '0', $date_entered, $date_entered, '1', '1', 'Rebuild Extensions', '4.0.0', '4.0.0')";
//        \SpiceCRM\includes\Logger\LoggerManager::getLogger()->info($query);
//        \SpiceCRM\includes\database\DBManagerFactory::getInstance()->query($query);

        // unset the session variable so it is not picked up in DisplayWarnings.php
        if(isset($_SESSION['rebuild_extensions'])) {
            unset($_SESSION['rebuild_extensions']);
        }
	}

	//Cache Clear Methods

    /**
     * @deprecated
     */
	public function clearSmarty()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARSMARTY']}</h3>";
		$this->_clearCache(sugar_cached('smarty/templates_c'), '.tpl.php');
	}

    /**
     * @deprecated
     * module Versions no longer exists as for spicecrm 2020.03.001
     */
//	public function clearXMLfiles()
//	{
//		global $mod_strings;
//		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_XMLFILES']}</h3>";
//		$this->_clearCache(sugar_cached("xml"), '.xml');
//
//		include('modules/Versions/ExpectedVersions.php');
//
//        global $expect_versions;
//
//        if (isset($expect_versions['Chart Data Cache'])) {
//            $version = new Version();
//            $version->retrieve_by_string_fields(array('name'=>'Chart Data Cache'));
//
//            $version->name = $expect_versions['Chart Data Cache']['name'];
//            $version->file_version = $expect_versions['Chart Data Cache']['file_version'];
//            $version->db_version = $expect_versions['Chart Data Cache']['db_version'];
//            $version->save();
//        }
//	}
    /**
     * @deprecated
     */
	public function clearDashlets()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARDASHLET']}</h3>";
		$this->_clearCache(sugar_cached('dashlets'), '.php');
	}
    /**
     * @deprecated
     */
    public function clearThemeCache()
    {
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARTHEMECACHE']}</h3>";
		SugarThemeRegistry::clearAllCaches();
	}

    /**
     * @deprecated
     */
	public function clearTpls()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARTEMPLATE']}</h3>";
		if(!in_array( translate('LBL_ALL_MODULES'),$this->module_list) && !empty($this->module_list))
		{
			foreach($this->module_list as $module_name_singular )
				$this->_clearCache(sugar_cached('modules/').$this->_getModuleNamePlural($module_name_singular), '.tpl');
		}
		else
			$this->_clearCache(sugar_cached('modules/'), '.tpl');
	}
	public function clearVardefs()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARVADEFS']}</h3>";
		if(!empty($this->module_list) && is_array($this->module_list) && !in_array( translate('LBL_ALL_MODULES'),$this->module_list))
		{
			foreach($this->module_list as $module_name_singular )
				$this->_clearCache(sugar_cached('modules/').$this->_getModuleNamePlural($module_name_singular), 'vardefs.php');
		}
		else
			$this->_clearCache(sugar_cached('modules/'), 'vardefs.php');
	}

    /**
     * @deprecated
     */
	public function clearJsFiles()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARJS']}</h3>";

		if(!in_array( translate('LBL_ALL_MODULES'),$this->module_list) && !empty($this->module_list))
		{
			foreach($this->module_list as $module_name_singular )
				$this->_clearCache(sugar_cached('modules/').$this->_getModuleNamePlural($module_name_singular), '.js');
		}

		else
			$this->_clearCache(sugar_cached('modules/'), '.js');

	}

    /**
     * @deprecated
     */
	public function clearJsLangFiles()
	{
		global $mod_strings;
		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARJSLANG']}</h3>";
		if(!in_array(translate('LBL_ALL_MODULES'),$this->module_list ) && !empty($this->module_list))
		{
			foreach($this->module_list as $module_name_singular )
				$this->_clearCache(sugar_cached('jsLanguage/').$this->_getModuleNamePlural($module_name_singular), '.js');
		}
		else
			$this->_clearCache(sugar_cached('jsLanguage'), '.js');
	}
	/**
	 * Remove the language cache files from cache/modules/<module>/language
	 */
	public function clearLanguageCache()
	{
		global $mod_strings;

		if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARLANG']}</h3>";
		//clear cache using the list $module_list_from_cache
		if ( !empty($this->module_list) && is_array($this->module_list) ) {
            if( in_array(translate('LBL_ALL_MODULES'), $this->module_list))
            {
                LanguageManager::clearLanguageCache();
            }
            else { //use the modules selected thrut the select list.
                foreach($this->module_list as $module_name)
                    LanguageManager::clearLanguageCache($module_name);
            }
        }
        // Clear app* cache values too
        if(!empty(SpiceConfig::getInstance()->config['languages'])) {
            $languages = SpiceConfig::getInstance()->config['languages'];
        } else {
            $languages = [$GLOBALS['current_language'] => $GLOBALS['current_language']];
        }
        foreach(array_keys($languages) as $language) {
        	SugarCache::sugar_cache_clear('app_strings.'.$language);
        	SugarCache::sugar_cache_clear('app_list_strings.'.$language);
        }

	}

	/**
	 * Remove the cached unified_search_modules.php file
	 */
    public function clearUniSearchCache() {
        global $mod_strings;
        if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEARSEARCH']}</h3>";
        $search_dir=sugar_cached('');
        $src_file = $search_dir . 'modules/unified_search_modules.php';
        if(file_exists($src_file)) {
            unlink( "$src_file" );
        }
    }
    /**
     * @deprecated
     */
    public function clearExternalAPICache()
	{
        global $mod_strings;
        if($this->show_output) echo "<h3>{$mod_strings['LBL_QR_CLEAR_EXT_API']}</h3>";
        require_once('include/externalAPI/ExternalAPIFactory.php');
        ExternalAPIFactory::clearCache();
    }

	//////////////////////////////////////////////////////////////
	/////REPAIR AUDIT TABLES
	public function rebuildAuditTables() {
		global $mod_strings;
		if ($this->show_output) {
            echo "<h3> {$mod_strings['LBL_QR_REBUILDAUDIT']}</h3>";
        }

		if (!in_array(SpiceUtils::translate('LBL_ALL_MODULES'), $this->module_list) && !empty($this->module_list)) {
			foreach ($this->module_list as $bean_name) {
			    $beanClass = SpiceModules::getInstance()->getBeanClassForBeanName($bean_name);

			    if (class_exists($beanClass)) {
			        $this->_rebuildAuditTablesHelper(new $beanClass());
                }
			}
		} elseif (in_array(SpiceUtils::translate('LBL_ALL_MODULES'), $this->module_list)) {
		    foreach (SpiceModules::getInstance()->getBeanClasses() as $beanClass) {
		        if (class_exists($beanClass)) {
		            $this->_rebuildAuditTablesHelper(new $beanClass());
                }
            }
		}

		if ($this->show_output) {
            echo $mod_strings['LBL_DONE'];
        }
	}

	private function _rebuildAuditTablesHelper($focus)
	{
		global $mod_strings;

		// skip if not a SugarBean object
		if ( !($focus instanceOf SugarBean) )
		    return;

		if ($focus->is_AuditEnabled()) {
			if (!$focus->db->tableExists($focus->get_audit_table_name())) {
				if($this->show_output) echo $mod_strings['LBL_QR_CREATING_TABLE']." ".$focus->get_audit_table_name().' '.$mod_strings['LBL_FOR'].' '. $focus->object_name.'.<br/>';
				$focus->create_audit_table();
			} else {
				if($this->show_output){
					$echo=str_replace('%1$',$focus->object_name,$mod_strings['LBL_REBUILD_AUDIT_SKIP']);
					echo $echo;
				}
			}
		}else
			if($this->show_output) echo $focus->object_name.$mod_strings['LBL_QR_NOT_AUDIT_ENABLED'];
	}

	///////////////////////////////////////////////////////////////
	////END REPAIR AUDIT TABLES


	///////////////////////////////////////////////////////////////
	//// Recursively unlink all files of the given $extension in the given $thedir.
	//
	private function _clearCache($thedir, $extension)
	{
        if ($current = @opendir($thedir)) {
            while (false !== ($children = readdir($current))) {
                if ($children != "." && $children != "..") {
                    if (is_dir($thedir . "/" . $children)) {
                        $this->_clearCache($thedir . "/" . $children, $extension);
                    }
                    elseif (is_file($thedir . "/" . $children) && (substr_count($children, $extension))) {
                        unlink($thedir . "/" . $children);
                    }
                }
            }
        }
	}
	/////////////////////////////////////////////////////////////
	////////
	private function _getModuleNamePlural($module_name_singular)
	{
		$globalBeanList = SpiceModules::getInstance()->getBeanList();
		while ($curr_module = current($globalBeanList))
		{
			if ($curr_module == $module_name_singular)
				return key($globalBeanList); //name of the module, plural.
			next($globalBeanList);
		}
	}

}

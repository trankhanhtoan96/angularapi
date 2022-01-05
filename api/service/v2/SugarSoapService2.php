<?php

use SpiceCRM\includes\Logger\LoggerManager;

if(!defined('sugarEntry'))define('sugarEntry', true);



/**
 * This is a service class for version 2
 */
require_once('service/core/NusoapSoap.php');
class SugarSoapService2 extends NusoapSoap{
		
	/**
	 * This method registers all the functions which you want to be available for SOAP.
	 *
	 * @param array $excludeFunctions - All the functions you don't want to register
	 */
	public function register($excludeFunctions = []){
		LoggerManager::getLogger()->info('Begin: SugarSoapService2->register');
		$this->excludeFunctions = $excludeFunctions;
		$registryObject = new $this->registryClass($this);
		$registryObject->register();
		$this->excludeFunctions = [];
		LoggerManager::getLogger()->info('End: SugarSoapService2->register');
	} // fn
			
} // clazz
?>

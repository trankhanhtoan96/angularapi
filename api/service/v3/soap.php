<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);



/**
 * This is a soap entry point for soap version 3
 */
chdir('../..');
require_once('SugarWebServiceImplv3.php');
$webservice_class = 'SugarSoapService2';
$webservice_path = 'service/v2/SugarSoapService2.php';
$registry_class = 'registry_v3';
$registry_path = 'service/v3/registry.php';
$webservice_impl_class = 'SugarWebServiceImplv3';
$location = '/service/v3/soap.php';
require_once('service/core/webservice.php');

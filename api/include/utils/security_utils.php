<?php



/**
 * func: query_module_access
 * param: $moduleName
 *
 * returns 1 if user has access to a module, else returns 0
 *
 */

use SpiceCRM\modules\Roles\Role;

use SpiceCRM\includes\SugarObjects\SpiceConfig;

$modules_exempt_from_availability_check['Activities']='Activities';
$modules_exempt_from_availability_check['History']='History';
$modules_exempt_from_availability_check['Calls']='Calls';
$modules_exempt_from_availability_check['Meetings']='Meetings';
$modules_exempt_from_availability_check['Tasks']='Tasks';
//$modules_exempt_from_availability_check['Notes']='Notes';

$modules_exempt_from_availability_check['CampaignLog']='CampaignLog';
$modules_exempt_from_availability_check['CampaignTrackers']='CampaignTrackers';
$modules_exempt_from_availability_check['Prospects']='Prospects';
$modules_exempt_from_availability_check['ProspectLists']='ProspectLists';
// CR1000465 cleanup Email
// $modules_exempt_from_availability_check['EmailMarketing']='EmailMarketing';
// $modules_exempt_from_availability_check['EmailMan']='EmailMan';
$modules_exempt_from_availability_check['Users']='Users';
$modules_exempt_from_availability_check['SchedulerJobTasks']='SchedulerJobTasks';
$modules_exempt_from_availability_check['DocumentRevisions']='DocumentRevisions';

/**
 * @deprecated moved to SecurityUtils
 * @param $user
 * @return mixed
 */
function query_module_access_list(&$user)
{
	return [];
}

/**
 * @deprecated moved to SecurityUtils
 * @param $user_id
 * @return mixed
 */
function query_user_has_roles($user_id)
{


	$role = new Role();

	return $role->check_user_role_count($user_id);
}

/**
 * @deprecated moved to SecurityUtils
 * @param $user_id
 * @return array
 */
function get_user_allowed_modules($user_id)
{


	$role = new Role();

	$allowed = $role->query_user_allowed_modules($user_id);
	return $allowed;
}

/**
 * @deprecated moved to SecurityUtils
 * @param $user_id
 * @param $allowed
 * @return array
 */
function get_user_disallowed_modules($user_id, &$allowed)
{


	$role = new Role();
	$disallowed = $role->query_user_disallowed_modules($user_id, $allowed);
	return $disallowed;
}

/**
 * @deprecated moved to SecurityUtils
 * grabs client ip address and returns its value
 * @return false|mixed
 */
function query_client_ip()
{
	global $_SERVER;
	$clientIP = false;
	if(!empty(SpiceConfig::getInstance()->config['ip_variable']) && !empty($_SERVER[SpiceConfig::getInstance()->config['ip_variable']])){
		$clientIP = $_SERVER[SpiceConfig::getInstance()->config['ip_variable']];
	}else if(isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$clientIP = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
	{
		// check for internal ips by looking at the first octet
		foreach($matches[0] AS $ip)
		{
			if(!preg_match("#^(10|172\.16|192\.168)\.#", $ip))
			{
				$clientIP = $ip;
				break;
			}
		}

	}
	elseif(isset($_SERVER['HTTP_FROM']))
	{
		$clientIP = $_SERVER['HTTP_FROM'];
	}
	else
	{
		$clientIP = $_SERVER['REMOTE_ADDR'];
	}
	return $clientIP;
}

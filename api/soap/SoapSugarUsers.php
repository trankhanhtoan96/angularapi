<?php


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\Relationships\Relationship;

require_once('soap/SoapHelperFunctions.php');
require_once('soap/SoapTypes.php');

/*************************************************************************************
 *
 * THIS IS FOR SUGARCRM USERS
 *************************************************************************************/

$server->register(
    'is_user_admin',
    ['session' => 'xsd:string'],
    ['return' => 'xsd:int'],
    $NAMESPACE);

/**
 * Return if the user is an admin or not
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return int 1 or 0 depending on if the user is an admin
 */
function is_user_admin($session)
{
    if (validate_authenticated($session)) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        return is_admin($current_user);

    } else {
        return 0;
    }
}


$server->register(
    'login',
    ['user_auth' => 'tns:user_auth', 'application_name' => 'xsd:string'],
    ['return' => 'tns:set_entry_result'],
    $NAMESPACE);

/**
 * Log the user into the application
 *
 * @param UserAuth array $user_auth -- Set user_name and password (password needs to be
 *      in the right encoding for the type of authentication the user is setup for.  For Base
 *      sugar validation, password is the MD5 sum of the plain text password.
 * @param String $application -- The name of the application you are logging in from.  (Currently unused).
 * @return Array(session_id, error) -- session_id is the id of the session that was
 *      created.  Error is set if there was any error during creation.
 */
function login($user_auth, $application)
{
    global $system_config;
    $GLOBALS['isSOAP'] = true;
    $error = new SoapError();
    try {
        AuthenticationController::getInstance()->authenticate($user_auth['user_name'], $user_auth['password']);
    } catch (\SpiceCRM\includes\ErrorHandlers\UnauthorizedException $e) {
        $error->set_error($e->getMessage());
        LoggerManager::getLogger()->fatal('Lockout reached for user ' . $user_auth['user_name']);
        LogicHook::getInstance()->call_custom_logic('Users', 'login_failed');
        return array('id' => -1, 'error' => $error);
    }

    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    //$current_user = $user;
    login_success();
    $current_user->loadPreferences();
    $_SESSION['is_valid_session'] = true;
    $_SESSION['ip_address'] = \SpiceCRM\includes\utils\SpiceUtils::getClientIP();
    $_SESSION['user_id'] = $current_user->id;
    $_SESSION['type'] = 'user';
    $_SESSION['avail_modules'] = get_user_module_list($current_user);
    $_SESSION['authenticated_user_id'] = $current_user->id;
    $_SESSION['unique_key'] = SpiceConfig::getInstance()->config['unique_key'];

    $current_user->call_custom_logic('after_login');
    return ['id' => AuthenticationController::getInstance()->token, 'error' => $error];
}


//checks if the soap server and client are running on the same machine
$server->register(
    'is_loopback',
    [],
    ['return' => 'xsd:int'],
    $NAMESPACE);

/**
 * Check to see if the soap server and client are on the same machine.
 * We don't allow a server to sync to itself.
 *
 * @return true -- if the SOAP server and client are on the same machine
 * @return false -- if the SOAP server and client are not on the same machine.
 */
function is_loopback()
{
    if (query_client_ip() == $_SERVER['SERVER_ADDR'])
        return 1;
    return 0;
}

/**
 * Validate the provided session information is correct and current.  Load the session.
 *
 * @param String $session_id -- The session ID that was returned by a call to login.
 * @return true -- If the session is valid and loaded.
 * @return false -- if the session is not valid.
 */
function validate_authenticated($session_id)
{
    if (!empty($session_id)) {
        session_id($session_id);
        session_start();

        if (!empty($_SESSION['is_valid_session']) && is_valid_ip_address('ip_address') && $_SESSION['type'] == 'user') {
            $authController = AuthenticationController::getInstance();
            if ($authController->setCurrentUser(BeanFactory::getBean('Users'))) {
                $authController->getCurrentUser()->retrieve($_SESSION['user_id']);
                login_success();
                return true;
            }
        }

        session_destroy();
        return false;
    }

    LoggerManager::getLogger()->fatal('SECURITY: The session ID is invalid');
    LogicHook::getInstance()->call_custom_logic('Users', 'login_failed');
    return false;
}

/**
 * Use the same logic as in SugarAuthenticate to validate the ip address
 *
 * @param string $session_var
 * @return bool - true if the ip address is valid, false otherwise.
 */
function is_valid_ip_address($session_var)
{

    // grab client ip address
    $clientIP = query_client_ip();
    $classCheck = 0;
    // check to see if config entry is present, if not, verify client ip
    if (!isset (SpiceConfig::getInstance()->config['verify_client_ip']) || SpiceConfig::getInstance()->config['verify_client_ip'] == true) {
        // check to see if we've got a current ip address in $_SESSION
        // and check to see if the session has been hijacked by a foreign ip
        if (isset ($_SESSION[$session_var])) {
            $session_parts = explode(".", $_SESSION[$session_var]);
            $client_parts = explode(".", $clientIP);
            if (count($session_parts) < 4) {
                $classCheck = 0;
            } else {
                // match class C IP addresses
                for ($i = 0; $i < 3; $i++) {
                    if ($session_parts[$i] == $client_parts[$i]) {
                        $classCheck = 1;
                        continue;
                    } else {
                        $classCheck = 0;
                        break;
                    }
                }
            }
            // we have a different IP address
            if ($_SESSION[$session_var] != $clientIP && empty ($classCheck)) {
                LoggerManager::getLogger()->fatal("IP Address mismatch: SESSION IP: {$_SESSION[$session_var]} CLIENT IP: {$clientIP}");
                return false;
            }
        } else {
            return false;
        }
    }
    return true;
}

$server->register(
    'seamless_login',
    ['session' => 'xsd:string'],
    ['return' => 'xsd:int'],
    $NAMESPACE);

/**
 * Perform a seamless login.  This is used internally during the sync process.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return true -- if the session was authenticated
 * @return false -- if the session could not be authenticated
 */
function seamless_login($session)
{
    if (!validate_authenticated($session)) {
        return 0;
    }

    return 1;
}

$server->register(
    'get_entry_list',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'query' => 'xsd:string', 'order_by' => 'xsd:string', 'offset' => 'xsd:int', 'select_fields' => 'tns:select_fields', 'max_results' => 'xsd:int', 'deleted' => 'xsd:int'],
    ['return' => 'tns:get_entry_list_result'],
    $NAMESPACE);

/**
 * Retrieve a list of beans.  This is the primary method for getting list of SugarBeans from Sugar using the SOAP API.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $query -- SQL where clause without the word 'where'
 * @param String $order_by -- SQL order by clause without the phrase 'order by'
 * @param String $offset -- The record offset to start from.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @param String $max_results -- The maximum number of records to return.  The default is the sugar configuration value for 'list_max_entries_per_page'
 * @param Number $deleted -- false if deleted records should not be include, true if deleted records should be included.
 * @return Array 'result_count' -- The number of records returned
 *               'next_offset' -- The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
 *               'field_list' -- The vardef information on the selected fields.
 *                      Array -- 'field'=>  'name' -- the name of the field
 *                                          'type' -- the data type of the field
 *                                          'label' -- the translation key for the label of the field
 *                                          'required' -- Is the field required?
 *                                          'options' -- Possible values for a drop down field
 *               'entry_list' -- The records that were retrieved
 *               'error' -- The SOAP error, if any
 */
function get_entry_list($session, $module_name, $query, $order_by, $offset, $select_fields, $max_results, $deleted)
{
    global $beanList, $beanFiles;
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $using_cp = false;
    if ($module_name == 'CampaignProspects') {
        $module_name = 'Prospects';
        $using_cp = true;
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'read')) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    // If the maximum number of entries per page was specified, override the configuration value.
    if ($max_results > 0) {

        SpiceConfig::getInstance()->config['list_max_entries_per_page'] = $max_results;
    }


    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $seed = new $class_name();
    if (!($seed->ACLAccess('Export') && $seed->ACLAccess('list'))) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    require_once 'include/SugarSQLValidate.php';
    $valid = new SugarSQLValidate();
    if (!$valid->validateQueryClauses($query, $order_by)) {
        LoggerManager::getLogger()->error("Bad query: $query $order_by");
        $error->set_error('no_access');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }
    if ($query == '') {
        $where = '';
    }
    if ($offset == '' || $offset == -1) {
        $offset = 0;
    }
    if ($using_cp) {
        $response = $seed->retrieveTargetList($query, $select_fields, $offset, -1, -1, $deleted);
    } else {
        $response = $seed->get_list($order_by, $query, $offset, -1, -1, $deleted, true);
    }
    $list = $response['list'];


    $output_list = [];

    $isEmailModule = false;
    if ($module_name == 'Emails') {
        $isEmailModule = true;
    }
    // retrieve the vardef information on the bean's fields.
    $field_list = [];

    //require_once 'modules/Currencies/Currency.php';

    $userCurrencyId = $current_user->getPreference('currency');
    $userCurrency = new Currency;
    $userCurrency->retrieve($userCurrencyId);

    foreach ($list as $value) {
        if (isset($value->emailAddress)) {
            $value->emailAddress->handleLegacyRetrieve($value);
        }
        if ($isEmailModule) {
            $value->retrieveEmailText();
        }
        $value->fill_in_additional_detail_fields();

        // bug 55129 - populate currency from user settings
        if (property_exists($value, 'currency_id') && $userCurrency->deleted != 1) {
            // walk through all currency-related fields
            foreach ($value->field_defs as $temp_field) {
                if (isset($temp_field['type']) && 'relate' == $temp_field['type']
                    && isset($temp_field['module']) && 'Currencies' == $temp_field['module']
                    && isset($temp_field['id_name']) && 'currency_id' == $temp_field['id_name']) {
                    // populate related properties manually
                    $temp_property = $temp_field['name'];
                    $currency_property = $temp_field['rname'];
                    $value->$temp_property = $userCurrency->$currency_property;
                } else if ($value->currency_id != $userCurrency->id
                    && isset($temp_field['type'])
                    && 'currency' == $temp_field['type']
                    && substr($temp_field['name'], -9) != '_usdollar') {
                    $temp_property = $temp_field['name'];
                    $value->$temp_property *= $userCurrency->conversion_rate;
                }
            }

            $value->currency_id = $userCurrencyId;
        }
        // end of bug 55129

        $output_list[] = get_return_value($value, $module_name);
        if (empty($field_list)) {
            $field_list = get_field_list($value);
        }
    }

    // Filter the search results to only include the requested fields.
    $output_list = filter_return_list($output_list, $select_fields, $module_name);

    // Filter the list of fields to only include information on the requested fields.
    $field_list = filter_return_list($field_list, $select_fields, $module_name);

    // Calculate the offset for the start of the next page
    $next_offset = $offset + sizeof($output_list);

    return ['result_count' => sizeof($output_list), 'next_offset' => $next_offset, 'field_list' => $field_list, 'entry_list' => $output_list, 'error' => $error->get_soap_array()];
}

$server->register(
    'get_entry',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'id' => 'xsd:string', 'select_fields' => 'tns:select_fields'],
    ['return' => 'tns:get_entry_result'],
    $NAMESPACE);

/**
 * Retrieve a single SugarBean based on ID.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $id -- The SugarBean's ID value.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @return unknown
 */
function get_entry($session, $module_name, $id, $select_fields)
{
    return get_entries($session, $module_name, [$id], $select_fields);
}

$server->register(
    'get_entries',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'ids' => 'tns:select_fields', 'select_fields' => 'tns:select_fields'],
    ['return' => 'tns:get_entry_result'],
    $NAMESPACE);

/**
 * Retrieve a list of SugarBean's based on provided IDs.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $ids -- An array of SugarBean IDs.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @return Array 'field_list' -- Var def information about the returned fields
 *               'entry_list' -- The records that were retrieved
 *               'error' -- The SOAP error, if any
 */
function get_entries($session, $module_name, $ids, $select_fields)
{
    global $beanList, $beanFiles;
    $error = new SoapError();
    $field_list = [];
    $output_list = [];
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['field_list' => $field_list, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $using_cp = false;
    if ($module_name == 'CampaignProspects') {
        $module_name = 'Prospects';
        $using_cp = true;
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['field_list' => $field_list, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'read')) {
        $error->set_error('no_access');
        return ['field_list' => $field_list, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);

    //todo can modify in there to call bean->get_list($order_by, $where, 0, -1, -1, $deleted);
    //that way we do not have to call retrieve for each bean
    //perhaps also add a select_fields to this, so we only get the fields we need
    //and not do a select *
    foreach ($ids as $id) {
        $seed = new $class_name();

        if ($using_cp) {
            $seed = $seed->retrieveTarget($id);
        } else {
            if ($seed->retrieve($id) == null)
                $seed->deleted = 1;
        }

        if ($seed->deleted == 1) {
            $list = [];
            $list[] = ['name' => 'warning', 'value' => 'Access to this object is denied since it has been deleted or does not exist'];
            $list[] = ['name' => 'deleted', 'value' => '1'];
            $output_list[] = ['id' => $id,
                'module_name' => $module_name,
                'name_value_list' => $list,
            ];
            continue;
        }
        if (!$seed->ACLAccess('DetailView')) {
            $error->set_error('no_access');
            return ['field_list' => $field_list, 'entry_list' => [], 'error' => $error->get_soap_array()];
        }
        $output_list[] = get_return_value($seed, $module_name);

        if (empty($field_list)) {
            $field_list = get_field_list($seed);

        }
    }

    $output_list = filter_return_list($output_list, $select_fields, $module_name);
    $field_list = filter_field_list($field_list, $select_fields, $module_name);

    return ['field_list' => $field_list, 'entry_list' => $output_list, 'error' => $error->get_soap_array()];
}

$server->register(
    'set_entry',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'name_value_list' => 'tns:name_value_list'],
    ['return' => 'tns:set_entry_result'],
    $NAMESPACE);

/**
 * Update or create a single SugarBean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_list -- The keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 * @return Array    'id' -- the ID of the bean that was written to (-1 on error)
 *                  'error' -- The SOAP error if any.
 */
function set_entry($session, $module_name, $name_value_list)
{
    global $beanList, $beanFiles;

    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'write')) {
        $error->set_error('no_access');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }

    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $seed = new $class_name();

    foreach ($name_value_list as $value) {
        if ($value['name'] == 'id' && isset($value['value']) && strlen($value['value']) > 0) {
            $result = $seed->retrieve($value['value']);
            //bug: 44680 - check to ensure the user has access before proceeding.
            if (is_null($result)) {
                $error->set_error('no_access');
                return ['id' => -1, 'error' => $error->get_soap_array()];
            } else {
                break;
            }

        }
    }
    foreach ($name_value_list as $value) {
        LoggerManager::getLogger()->debug($value['name'] . " : " . $value['value']);
        //PHP7 COMPAT
        //ORIGINAL: $seed->$value['name'] = $value['value'];
        $value_name = $value['name'];
        $seed->$value_name = $value['value'];
        //END
    }
    if (!$seed->ACLAccess('Save') || ($seed->deleted == 1 && !$seed->ACLAccess('Delete'))) {
        $error->set_error('no_access');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }
    $seed->save();
    if ($seed->deleted == 1) {
        $seed->mark_deleted($seed->id);
    }
    return ['id' => $seed->id, 'error' => $error->get_soap_array()];

}

$server->register(
    'set_entries',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'name_value_lists' => 'tns:name_value_lists'],
    ['return' => 'tns:set_entries_result'],
    $NAMESPACE);

/**
 * Update or create a list of SugarBeans
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_lists -- Array of Bean specific Arrays where the keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 * @return Array    'ids' -- Array of the IDs of the beans that was written to (-1 on error)
 *                  'error' -- The SOAP error if any.
 */
function set_entries($session, $module_name, $name_value_lists)
{
    $error = new SoapError();

    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');

        return [
            'ids' => [],
            'error' => $error->get_soap_array()
        ];
    }

    return handle_set_entries($module_name, $name_value_lists, FALSE);
}

/*
NOTE SPECIFIC CODE
*/
$server->register(
    'set_note_attachment',
    ['session' => 'xsd:string', 'note' => 'tns:note_attachment'],
    ['return' => 'tns:set_entry_result'],
    $NAMESPACE);

/**
 * Add or replace the attachment on a Note.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Binary $note -- The flie contents of the attachment.
 * @return Array 'id' -- The ID of the new note or -1 on error
 *               'error' -- The SOAP error if any.
 */
function set_note_attachment($session, $note)
{

    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }

    require_once('modules/Notes/NoteSoap.php');
    $ns = new NoteSoap();
    return ['id' => $ns->saveFile($note), 'error' => $error->get_soap_array()];

}

$server->register(
    'get_note_attachment',
    ['session' => 'xsd:string', 'id' => 'xsd:string'],
    ['return' => 'tns:return_note_attachment'],
    $NAMESPACE);

/**
 * Retrieve an attachment from a note
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Binary $note -- The flie contents of the attachment.
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $id -- The ID of the appropriate Note.
 * @return Array 'id' -- The ID of the new note or -1 on error
 *               'error' -- The SOAP error if any.
 *
 * @return Array 'note_attachment' -- Array String 'id' -- The ID of the Note containing the attachment
 *                                          String 'filename' -- The file name of the attachment
 *                                          Binary 'file' -- The binary contents of the file.
 *               'error' -- The SOAP error if any.
 */
function get_note_attachment($session, $id)
{
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    $note = new Note();

    $note->retrieve($id);
    if (!$note->ACLAccess('DetailView')) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    require_once('modules/Notes/NoteSoap.php');
    $ns = new NoteSoap();
    if (!isset($note->filename)) {
        $note->filename = '';
    }
    $file = $ns->retrieveFile($id, $note->filename);
    if ($file == -1) {
        $error->set_error('no_file');
        $file = '';
    }

    return ['note_attachment' => ['id' => $id, 'filename' => $note->filename, 'file' => $file], 'error' => $error->get_soap_array()];

}

$server->register(
    'relate_note_to_module',
    ['session' => 'xsd:string', 'note_id' => 'xsd:string', 'module_name' => 'xsd:string', 'module_id' => 'xsd:string'],
    ['return' => 'tns:error_value'],
    $NAMESPACE);

/**
 * Attach a note to another bean.  Once you have created a note to store an
 * attachment, the note needs to be related to the bean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $note_id -- The ID of the note that you want to associate with a bean
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $module_id -- The ID of the bean that you want to associate the note with
 * @return no error for success, error for failure
 */
function relate_note_to_module($session, $note_id, $module_name, $module_id)
{
    global $beanList, $beanFiles;
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return $error->get_soap_array();
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return $error->get_soap_array();
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'read')) {
        $error->set_error('no_access');
        return $error->get_soap_array();
    }
    $class_name = $beanList['Notes'];
    require_once($beanFiles[$class_name]);
    $seed = new $class_name();
    $seed->retrieve($note_id);
    if (!$seed->ACLAccess('ListView')) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    if ($module_name != 'Contacts') {
        $seed->parent_type = $module_name;
        $seed->parent_id = $module_id;

    } else {

        $seed->contact_id = $module_id;

    }

    $seed->save();

    return $error->get_soap_array();

}

$server->register(
    'get_related_notes',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'module_id' => 'xsd:string', 'select_fields' => 'tns:select_fields'],
    ['return' => 'tns:get_entry_result'],
    $NAMESPACE);

/**
 * Retrieve the collection of notes that are related to a bean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $module_id -- The ID of the bean that you want to associate the note with
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @return Array    'result_count' -- The number of records returned (-1 on error)
 *                  'next_offset' -- The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
 *                  'field_list' -- The vardef information on the selected fields.
 *                  'entry_list' -- The records that were retrieved
 *                  'error' -- The SOAP error, if any
 */
function get_related_notes($session, $module_name, $module_id, $select_fields)
{
    global $beanList, $beanFiles;
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'read')) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $seed = new $class_name();
    $seed->retrieve($module_id);
    if (!$seed->ACLAccess('DetailView')) {
        $error->set_error('no_access');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }
    $list = $seed->get_linked_beans('notes', 'Note', [], 0, -1, 0);

    $output_list = [];
    $field_list = [];
    foreach ($list as $value) {
        $output_list[] = get_return_value($value, 'Notes');
        if (empty($field_list)) {
            $field_list = get_field_list($value);
        }
    }
    $output_list = filter_return_list($output_list, $select_fields, $module_name);
    $field_list = filter_field_list($field_list, $select_fields, $module_name);

    return ['result_count' => sizeof($output_list), 'next_offset' => 0, 'field_list' => $field_list, 'entry_list' => $output_list, 'error' => $error->get_soap_array()];
}

$server->register(
    'logout',
    ['session' => 'xsd:string'],
    ['return' => 'tns:error_value'],
    $NAMESPACE);

/**
 * Log out of the session.  This will destroy the session and prevent other's from using it.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return Empty error on success, Error on failure
 */
function logout($session)
{
    $current_user = AuthenticationController::getInstance()->getCurrentUser();

    $error = new SoapError();
    $logicHook = LogicHook::getInstance();
    if (validate_authenticated($session)) {
        $current_user->call_custom_logic('before_logout');
        session_destroy();
        $logicHook->call_custom_logic('Users', 'after_logout');
        return $error->get_soap_array();
    }
    $error->set_error('no_session');
    $logicHook->call_custom_logic('Users', 'after_logout');
    return $error->get_soap_array();
}

$server->register(
    'get_module_fields',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string'],
    ['return' => 'tns:module_fields'],
    $NAMESPACE);

/**
 * Retrieve vardef information on the fields of the specified bean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @return Array    'module_fields' -- The vardef information on the selected fields.
 *                  'error' -- The SOAP error, if any
 */
function get_module_fields($session, $module_name)
{
    global $beanList, $beanFiles;
    $error = new SoapError();
    $module_fields = [];
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_session');
        return ['module_fields' => $module_fields, 'error' => $error->get_soap_array()];
    }
    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['module_fields' => $module_fields, 'error' => $error->get_soap_array()];
    }
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if (!check_modules_access($current_user, $module_name, 'read')) {
        $error->set_error('no_access');
        return ['module_fields' => $module_fields, 'error' => $error->get_soap_array()];
    }
    $class_name = $beanList[$module_name];

    if (empty($beanFiles[$class_name])) {
        $error->set_error('no_file');
        return ['module_fields' => $module_fields, 'error' => $error->get_soap_array()];
    }

    require_once($beanFiles[$class_name]);
    $seed = new $class_name();
    if ($seed->ACLAccess('ListView', true) || $seed->ACLAccess('DetailView', true) || $seed->ACLAccess('EditView', true)) {
        return get_return_module_fields($seed, $module_name, $error);
    } else {
        $error->set_error('no_access');
        return ['module_fields' => $module_fields, 'error' => $error->get_soap_array()];
    }
}

$server->register(
    'get_available_modules',
    ['session' => 'xsd:string'],
    ['return' => 'tns:module_list'],
    $NAMESPACE);

/**
 * Retrieve the list of available modules on the system available to the currently logged in user.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return Array    'modules' -- An array of module names
 *                  'error' -- The SOAP error, if any
 */
function get_available_modules($session)
{
    $error = new SoapError();
    $modules = [];
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_session');
        return ['modules' => $modules, 'error' => $error->get_soap_array()];
    }
    $modules = array_keys($_SESSION['avail_modules']);

    return ['modules' => $modules, 'error' => $error->get_soap_array()];
}


$server->register(
    'update_portal_user',
    ['session' => 'xsd:string', 'portal_name' => 'xsd:string', 'name_value_list' => 'tns:name_value_list'],
    ['return' => 'tns:error_value'],
    $NAMESPACE);

/**
 * Update the properties of a contact that is portal user.  Add the portal user name to the user's properties.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $portal_name -- The portal user_name of the contact
 * @param Array $name_value_list -- collection of 'name'=>'value' pairs for finding the contact
 * @return Empty error on success, Error on failure
 */
function update_portal_user($session, $portal_name, $name_value_list)
{
    global $beanList, $beanFiles;
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_session');
        return $error->get_soap_array();
    }
    $contact = BeanFactory::getBean('Contacts');

    $searchBy = ['deleted' => 0];
    foreach ($name_value_list as $name_value) {
        $searchBy[$name_value['name']] = $name_value['value'];
    }
    if ($contact->retrieve_by_string_fields($searchBy) != null) {
        if (!$contact->duplicates_found) {
            $contact->portal_name = $portal_name;
            $contact->portal_active = 1;
            if ($contact->ACLAccess('Save')) {
                $contact->save();
            } else {
                $error->set_error('no_access');
            }
            return $error->get_soap_array();
        }
        $error->set_error('duplicates');
        return $error->get_soap_array();
    }
    $error->set_error('no_records');
    return $error->get_soap_array();
}

$server->register(
    'get_user_id',
    ['session' => 'xsd:string'],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Return the user_id of the user that is logged into the current session.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return String -- the User ID of the current session
 *                  -1 on error.
 */
function get_user_id($session)
{
    if (validate_authenticated($session)) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        return $current_user->id;

    } else {
        return '-1';
    }
}

$server->register(
    'get_user_team_id',
    ['session' => 'xsd:string'],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Return the ID of the default team for the user that is logged into the current session.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return String -- the Team ID of the current user's default team
 *                  1 for Community Edition
 *                  -1 on error.
 */
function get_user_team_id($session)
{
    if (validate_authenticated($session)) {
        return 1;
    } else {
        return '-1';
    }
}

$server->register(
    'get_user_team_set_id',
    ['session' => 'xsd:string'],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Return the Team Set ID for the user that is logged into the current session.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return String -- the Team Set ID of the current user
 *                  1 for Community Edition
 *                  -1 on error.
 */
function get_user_team_set_id($session)
{
    if (validate_authenticated($session)) {
        return 1;
    } else {
        return '-1';
    }
}

$server->register(
    'get_server_time',
    [],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Return the current time on the server in the format 'Y-m-d H:i:s'.  This time is in the server's default timezone.
 *
 * @return String -- The current date/time 'Y-m-d H:i:s'
 */
function get_server_time()
{
    return date('Y-m-d H:i:s');
}

$server->register(
    'get_gmt_time',
    [],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Return the current time on the server in the format 'Y-m-d H:i:s'.  This time is in GMT.
 *
 * @return String -- The current date/time 'Y-m-d H:i:s'
 */
function get_gmt_time()
{
    return TimeDate::getInstance()->nowDb();
}

$server->register(
    'get_sugar_flavor',
    [],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Retrieve the specific flavor of sugar.
 *
 * @return String   'CE' -- For Community Edition
 *                  'PRO' -- For Professional
 *                  'ENT' -- For Enterprise
 */
function get_sugar_flavor()
{

    return 'Spice';
}


$server->register(
    'get_server_version',
    [],
    ['return' => 'xsd:string'],
    $NAMESPACE);

/**
 * Retrieve the version number of Sugar that the server is running.
 *
 * @return String -- The current sugar version number.
 *                   '1.0' on error.
 */
function get_server_version()
{

    $admin = BeanFactory::getBean('Administration');
    $admin->retrieveSettings('info');
    if (isset($admin->settings['info_sugar_version'])) {
        return $admin->settings['info_sugar_version'];
    } else {
        return '1.0';
    }

}

$server->register(
    'get_relationships',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'module_id' => 'xsd:string', 'related_module' => 'xsd:string', 'related_module_query' => 'xsd:string', 'deleted' => 'xsd:int'],
    ['return' => 'tns:get_relationships_result'],
    $NAMESPACE);

/**
 * Retrieve a collection of beans tha are related to the specified bean.
 * As of 4.5.1c, all combinations of related modules are supported
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $module_id -- The ID of the bean in the specified module
 * @param String $related_module -- The name of the related module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $related_module_query -- A portion of the where clause of the SQL statement to find the related items.  The SQL query will already be filtered to only include the beans that are related to the specified bean.
 * @param Number $deleted -- false if deleted records should not be include, true if deleted records should be included.
 * @return unknown
 */
function get_relationships($session, $module_name, $module_id, $related_module, $related_module_query, $deleted)
{
    $error = new SoapError();
    $ids = [];
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['ids' => $ids, 'error' => $error->get_soap_array()];
    }
    global $beanList, $beanFiles;
    $error = new SoapError();

    if (empty($beanList[$module_name]) || empty($beanList[$related_module])) {
        $error->set_error('no_module');
        return ['ids' => $ids, 'error' => $error->get_soap_array()];
    }
    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $mod = new $class_name();
    $mod->retrieve($module_id);
    if (!$mod->ACLAccess('DetailView')) {
        $error->set_error('no_access');
        return ['ids' => $ids, 'error' => $error->get_soap_array()];
    }

    require_once 'include/SugarSQLValidate.php';
    $valid = new SugarSQLValidate();
    if (!$valid->validateQueryClauses($related_module_query)) {
        LoggerManager::getLogger()->error("Bad query: $related_module_query");
        $error->set_error('no_access');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }

    $id_list = get_linked_records($related_module, $module_name, $module_id);

    if ($id_list === FALSE) {
        $error->set_error('no_relationship_support');
        return ['ids' => $ids, 'error' => $error->get_soap_array()];
    } elseif (count($id_list) == 0) {
        return ['ids' => $ids, 'error' => $error->get_soap_array()];
    }

    $list = [];

    $in = "'" . implode("', '", $id_list) . "'";

    $related_class_name = $beanList[$related_module];
    require_once($beanFiles[$related_class_name]);
    $related_mod = new $related_class_name();

    $sql = "SELECT {$related_mod->table_name}.id FROM {$related_mod->table_name} ";


    if (isset($related_mod->custom_fields)) {
        $customJoin = $related_mod->custom_fields->getJOIN();
        $sql .= $customJoin ? $customJoin['join'] : '';
    }

    $sql .= " WHERE {$related_mod->table_name}.id IN ({$in}) ";

    if (!empty($related_module_query)) {
        $sql .= " AND ( {$related_module_query} )";
    }

    $result = $related_mod->db->query($sql);
    while ($row = $related_mod->db->fetchByAssoc($result)) {
        $list[] = $row['id'];
    }

    $return_list = [];

    foreach ($list as $id) {
        $related_class_name = $beanList[$related_module];
        $related_mod = new $related_class_name();
        $related_mod->retrieve($id);

        $return_list[] = [
            'id' => $id,
            'date_modified' => $related_mod->date_modified,
            'deleted' => $related_mod->deleted
        ];
    }

    return ['ids' => $return_list, 'error' => $error->get_soap_array()];
}


$server->register(
    'set_relationship',
    ['session' => 'xsd:string', 'set_relationship_value' => 'tns:set_relationship_value'],
    ['return' => 'tns:error_value'],
    $NAMESPACE);

/**
 * Set a single relationship between two beans.  The items are related by module name and id.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Array $set_relationship_value --
 *      'module1' -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module1_id' -- The ID of the bean in the specified module
 *      'module2' -- The name of the module that the related record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module2_id' -- The ID of the bean in the specified module
 * @return Empty error on success, Error on failure
 */
function set_relationship($session, $set_relationship_value)
{
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return $error->get_soap_array();
    }
    return handle_set_relationship($set_relationship_value, $session);
}

$server->register(
    'set_relationships',
    ['session' => 'xsd:string', 'set_relationship_list' => 'tns:set_relationship_list'],
    ['return' => 'tns:set_relationship_list_result'],
    $NAMESPACE);

/**
 * Setup several relationships between pairs of beans.  The items are related by module name and id.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param Array $set_relationship_list -- One for each relationship to setup.  Each entry is itself an array.
 *      'module1' -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module1_id' -- The ID of the bean in the specified module
 *      'module2' -- The name of the module that the related record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module2_id' -- The ID of the bean in the specified module
 * @return Empty error on success, Error on failure
 */
function set_relationships($session, $set_relationship_list)
{
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return -1;
    }
    $count = 0;
    $failed = 0;
    foreach ($set_relationship_list as $set_relationship_value) {
        $reter = handle_set_relationship($set_relationship_value, $session);
        if ($reter['number'] == 0) {
            $count++;
        } else {
            $failed++;
        }
    }
    return ['created' => $count, 'failed' => $failed, 'error' => $error];
}


//INTERNAL FUNCTION NOT EXPOSED THROUGH SOAP
/**
 * (Internal) Create a relationship between two beans.
 *
 * @param Array $set_relationship_value --
 *      'module1' -- The name of the module that the primary record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module1_id' -- The ID of the bean in the specified module
 *      'module2' -- The name of the module that the related record is from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 *      'module2_id' -- The ID of the bean in the specified module
 * @return Empty error on success, Error on failure
 */
function handle_set_relationship($set_relationship_value, $session = '')
{
    global $beanList, $beanFiles;
    $error = new SoapError();

    $module1 = $set_relationship_value['module1'];
    $module1_id = $set_relationship_value['module1_id'];
    $module2 = $set_relationship_value['module2'];
    $module2_id = $set_relationship_value['module2_id'];

    if (empty($beanList[$module1]) || empty($beanList[$module2])) {
        $error->set_error('no_module');
        return $error->get_soap_array();
    }
    $class_name = $beanList[$module1];
    require_once($beanFiles[$class_name]);
    $mod = new $class_name();
    $mod->retrieve($module1_id);
    if (!$mod->ACLAccess('DetailView')) {
        $error->set_error('no_access');
        return $error->get_soap_array();
    }
    if ($module1 == "Contacts" && $module2 == "Users") {
        $key = 'contacts_users_id';
    } else {
        $key = array_search(strtolower($module2), $mod->relationship_fields);
        if (!$key) {
            $key = Relationship::retrieve_by_modules($module1, $module2, DBManagerFactory::getInstance());

            // BEGIN SnapLogic fix for bug 32064
            if ($module1 == "Quotes" && $module2 == "ProductBundles") {
                // Alternative solution is perhaps to
                // do whatever Sugar does when the same
                // request is received from the web:
                $pb_cls = $beanList[$module2];
                $pb = new $pb_cls();
                $pb->retrieve($module2_id);

                // Check if this relationship already exists
                $query = "SELECT count(*) AS count FROM product_bundle_quote WHERE quote_id = '{$module1_id}' AND bundle_id = '{$module2_id}' AND deleted = '0'";
                $result = DBManagerFactory::getInstance()->query($query, true, "Error checking for previously existing relationship between quote and product_bundle");
                $row = DBManagerFactory::getInstance()->fetchByAssoc($result);
                if (isset($row['count']) && $row['count'] > 0) {
                    return $error->get_soap_array();
                }

                $query = "SELECT MAX(bundle_index)+1 AS idx FROM product_bundle_quote WHERE quote_id = '{$module1_id}' AND deleted='0'";
                $result = DBManagerFactory::getInstance()->query($query, true, "Error getting bundle_index");
                LoggerManager::getLogger()->debug("*********** Getting max bundle_index");
                LoggerManager::getLogger()->debug($query);
                $row = DBManagerFactory::getInstance()->fetchByAssoc($result);

                $idx = 0;
                if ($row) {
                    $idx = $row['idx'];
                }

                $pb->set_productbundle_quote_relationship($module1_id, $module2_id, $idx);
                $pb->save();
                return $error->get_soap_array();

            } else if ($module1 == "ProductBundles" && $module2 == "Products") {
                // And, well, similar things apply in this case
                $pb_cls = $beanList[$module1];
                $pb = new $pb_cls();
                $pb->retrieve($module1_id);

                // Check if this relationship already exists
                $query = "SELECT count(*) AS count FROM product_bundle_product WHERE bundle_id = '{$module1_id}' AND product_id = '{$module2_id}' AND deleted = '0'";
                $result = DBManagerFactory::getInstance()->query($query, true, "Error checking for previously existing relationship between quote and product_bundle");
                $row = DBManagerFactory::getInstance()->fetchByAssoc($result);
                if (isset($row['count']) && $row['count'] > 0) {
                    return $error->get_soap_array();
                }

                $query = "SELECT MAX(product_index)+1 AS idx FROM product_bundle_product WHERE bundle_id='{$module1_id}'";
                $result = DBManagerFactory::getInstance()->query($query, true, "Error getting bundle_index");
                LoggerManager::getLogger()->debug("*********** Getting max bundle_index");
                LoggerManager::getLogger()->debug($query);
                $row = DBManagerFactory::getInstance()->fetchByAssoc($result);

                $idx = 0;
                if ($row) {
                    $idx = $row['idx'];
                }
                $pb->set_productbundle_product_relationship($module2_id, $idx, $module1_id);
                $pb->save();

                $prod_cls = $beanList[$module2];
                $prod = new $prod_cls();
                $prod->retrieve($module2_id);
                $prod->quote_id = $pb->quote_id;
                $prod->save();
                return $error->get_soap_array();
            }
            // END SnapLogic fix for bug 32064

            if (!empty($key)) {
                $mod->load_relationship($key);
                $mod->$key->add($module2_id);
                return $error->get_soap_array();
            } // if
        }
    }

    if (!$key) {
        $error->set_error('no_module');
        return $error->get_soap_array();
    }

    if (($module1 == 'Meetings' || $module1 == 'Calls') && ($module2 == 'Contacts' || $module2 == 'Users')) {
        $key = strtolower($module2);
        $mod->load_relationship($key);
        $mod->$key->add($module2_id);
    } else if ($module1 == 'Contacts' && ($module2 == 'Notes' || $module2 == 'Calls' || $module2 == 'Meetings' || $module2 == 'Tasks') && !empty($session)) {
        $mod->$key = $module2_id;
        $mod->save_relationship_changes(false);
        if (!empty($mod->account_id)) {
            // when setting a relationship from a Contact to these activities, if the Contacts is related to an Account,
            // we want to associate that Account to the activity as well
            $ret = set_relationship($session, ['module1' => 'Accounts', 'module1_id' => $mod->account_id, 'module2' => $module2, 'module2_id' => $module2_id]);
        }
    } else {
        $mod->$key = $module2_id;
        $mod->save_relationship_changes(false);
    }

    return $error->get_soap_array();
}


$server->register(
    'set_document_revision',
    ['session' => 'xsd:string', 'note' => 'tns:document_revision'],
    ['return' => 'tns:set_entry_result'],
    $NAMESPACE);

/**
 * Enter description here...
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param unknown_type $document_revision
 * @return unknown
 */
function set_document_revision($session, $document_revision)
{

    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }

    require_once('modules/Documents/DocumentSoap.php');
    $dr = new DocumentSoap();
    return ['id' => $dr->saveFile($document_revision), 'error' => $error->get_soap_array()];

}

$server->register(
    'search_by_module',
    ['user_name' => 'xsd:string', 'password' => 'xsd:string', 'search_string' => 'xsd:string', 'modules' => 'tns:select_fields', 'offset' => 'xsd:int', 'max_results' => 'xsd:int'],
    ['return' => 'tns:get_entry_list_result'],
    $NAMESPACE);

/**
 * Given a list of modules to search and a search string, return the id, module_name, along with the fields
 * as specified in the $query_array
 *
 * @param string $user_name - username of the Sugar User
 * @param string $password - password of the Sugar User
 * @param string $search_string - string to search
 * @param string[] $modules - array of modules to query
 * @param int $offset - a specified offset in the query
 * @param int $max_results - max number of records to return
 * @return get_entry_list_result    - id, module_name, and list of fields from each record
 */
function search_by_module($user_name, $password, $search_string, $modules, $offset, $max_results)
{
    global $beanList, $beanFiles;

    $error = new SoapError();
    $hasLoginError = false;

    if (empty($user_name) && !empty($password)) {
        if (!validate_authenticated($password)) {
            $hasLoginError = true;
        }
    } else if (!validate_user($user_name, $password)) {
        $hasLoginError = true;
    }

    //If there is a login error, then return the error here
    if ($hasLoginError) {
        $error->set_error('invalid_login');
        return ['result_count' => -1, 'entry_list' => [], 'error' => $error->get_soap_array()];
    }

    $current_user = AuthenticationController::getInstance()->getCurrentUser();
    if ($max_results > 0) {

        SpiceConfig::getInstance()->config['list_max_entries_per_page'] = $max_results;
    }
    //  MRF - BUG:19552 - added a join for accounts' emails below
    $query_array = ['Accounts' => ['where' => ['Accounts' => [0 => "accounts.name like '{0}%'"], 'EmailAddresses' => [0 => "ea.email_address like '{0}%'"]], 'fields' => "accounts.id, accounts.name"],
        'Bugs' => ['where' => ['Bugs' => [0 => "bugs.name like '{0}%'", 1 => "bugs.bug_number = {0}"]], 'fields' => "bugs.id, bugs.name, bugs.bug_number"],
        'Cases' => ['where' => ['Cases' => [0 => "cases.name like '{0}%'", 1 => "cases.case_number = {0}"]], 'fields' => "cases.id, cases.name, cases.case_number"],
        'Leads' => ['where' => ['Leads' => [0 => "leads.first_name like '{0}%'", 1 => "leads.last_name like '{0}%'"], 'EmailAddresses' => [0 => "ea.email_address like '{0}%'"]], 'fields' => "leads.id, leads.first_name, leads.last_name, leads.status"],
        'Projects' => ['where' => ['Projects' => [0 => "projects.name like '{0}%'"]], 'fields' => "projects.id, projects.name"],
        'Contacts' => ['where' => ['Contacts' => [0 => "contacts.first_name like '{0}%'", 1 => "contacts.last_name like '{0}%'"], 'EmailAddresses' => [0 => "ea.email_address like '{0}%'"]], 'fields' => "contacts.id, contacts.first_name, contacts.last_name"],
        'Opportunities' => ['where' => ['Opportunities' => [0 => "opportunities.name like '{0}%'"]], 'fields' => "opportunities.id, opportunities.name"],
        'Users' => ['where' => ['EmailAddresses' => [0 => "ea.email_address like '{0}%'"]], 'fields' => "users.id, users.user_name, users.first_name, ea.email_address"],
    ];

    $more_query_array = [];
    foreach ($modules as $module) {
        if (!array_key_exists($module, $query_array)) {
            $seed = new $beanList[$module]();
            $table_name = $seed->table_name;
            if (!empty($seed->field_defs['name']['db_concat_fields'])) {
                $namefield = $seed->db->concat($table_name, $seed->field_defs['name']['db_concat_fields']);
            } else {
                $namefield = "$table_name.name";
            }
            $more_query_array[$module] = [
                'where' => [
                    $module => [
                        0 => "$namefield like '%{0}%'",
                    ],
                ],
                'fields' => "$table_name.id, $namefield AS name"
            ];
        }
    }

    if (!empty($more_query_array)) {
        $query_array = array_merge($query_array, $more_query_array);
    }

    if (!empty($search_string) && isset($search_string)) {
        foreach ($modules as $module_name) {
            $class_name = $beanList[$module_name];
            require_once($beanFiles[$class_name]);
            $seed = new $class_name();
            if (empty($beanList[$module_name])) {
                continue;
            }
            if (!check_modules_access($current_user, $module_name, 'read')) {
                continue;
            }
            if (!$seed->ACLAccess('ListView')) {
                continue;
            }

            if (isset($query_array[$module_name])) {
                $query = '';
                $tmpQuery = '';
                //split here to do while loop
                foreach ($query_array[$module_name]['where'] as $key => $value) {
                    foreach ($value as $where_clause) {
                        $addQuery = true;
                        if (!empty($query))
                            $tmpQuery = ' UNION ';
                        $tmpQuery .= "SELECT " . $query_array[$module_name]['fields'] . " FROM $seed->table_name ";
                        // We need to confirm that the user is a member of the team of the item.

                        if (isset($seed->emailAddress) && $key == 'EmailAddresses') {
                            $tmpQuery .= " INNER JOIN email_addr_bean_rel eabl  ON eabl.bean_id = $seed->table_name.id and eabl.deleted=0";
                            $tmpQuery .= " INNER JOIN email_addresses ea ON (ea.id = eabl.email_address_id) ";
                        }
                        $where = "WHERE (";
                        $search_terms = explode(", ", $search_string);
                        $termCount = count($search_terms);
                        $count = 1;
                        if ($key != 'EmailAddresses') {
                            foreach ($search_terms as $term) {
                                if (!strpos($where_clause, 'number')) {
                                    $where .= string_format($where_clause, [DBManagerFactory::getInstance()->quote($term)]);
                                } elseif (is_numeric($term)) {
                                    $where .= string_format($where_clause, [DBManagerFactory::getInstance()->quote($term)]);
                                } else {
                                    $addQuery = false;
                                }
                                if ($count < $termCount) {
                                    $where .= " OR ";
                                }
                                $count++;
                            }
                        } else {
                            $where .= '(';
                            foreach ($search_terms as $term) {
                                $where .= "ea.email_address LIKE '" . DBManagerFactory::getInstance()->quote($term) . "'";
                                if ($count < $termCount) {
                                    $where .= " OR ";
                                }
                                $count++;
                            }
                            $where .= ')';
                        }
                        $tmpQuery .= $where;
                        $tmpQuery .= ") AND $seed->table_name.deleted = 0";
                        if ($addQuery)
                            $query .= $tmpQuery;
                    }
                }
                //grab the items from the db
                $result = $seed->db->query($query, $offset, $max_results);

                while (($row = $seed->db->fetchByAssoc($result)) != null) {
                    $list = [];
                    foreach ($row as $field_key => $field_value) {
                        $list[$field_key] = ['name' => $field_key, 'value' => $field_value];
                    }

                    $output_list[] = ['id' => $row['id'],
                        'module_name' => $module_name,
                        'name_value_list' => $list];
                    if (empty($field_list)) {
                        $field_list = get_field_list($row);
                    }
                }//end while
            }
        }//end foreach
    }

    $next_offset = $offset + sizeof($output_list);

    return ['result_count' => sizeof($output_list), 'next_offset' => $next_offset, 'field_list' => $field_list, 'entry_list' => $output_list, 'error' => $error->get_soap_array()];

}//end function


$server->register(
    'get_mailmerge_document',
    ['session' => 'xsd:string', 'file_name' => 'xsd:string', 'fields' => 'tns:select_fields'],
    ['return' => 'tns:get_sync_result_encoded'],
    $NAMESPACE);

/**
 * Get MailMerge document
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param unknown_type $file_name
 * @param unknown_type $fields
 * @return unknown
 */
function get_mailmerge_document($session, $file_name, $fields)
{
    global $beanList, $beanFiles, $app_list_strings;
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['result' => '', 'error' => $error->get_soap_array()];
    }
    if (!preg_match('/^sugardata[\.\d\s]+\.php$/', $file_name)) {
        $error->set_error('no_records');
        return ['result' => '', 'error' => $error->get_soap_array()];
    }
    $html = '';

    $file_name = sugar_cached('MergedDocuments/') . pathinfo($file_name, PATHINFO_BASENAME);

    $master_fields = [];
    $related_fields = [];

    if (file_exists($file_name)) {
        include($file_name);

        $class1 = $merge_array['master_module'];
        $beanL = $beanList[$class1];
        $bean1 = $beanFiles[$beanL];
        require_once($bean1);
        $seed1 = new $beanL();

        if (!empty($merge_array['related_module'])) {
            $class2 = $merge_array['related_module'];
            $beanR = $beanList[$class2];
            $bean2 = $beanFiles[$beanR];
            require_once($bean2);
            $seed2 = new $beanR();
        }

        //parse fields
        //$token1 = strtolower($class1);
        if ($class1 == 'Prospects') {
            $class1 = 'CampaignProspects';
        }
        foreach ($fields as $field) {
            $pos = strpos(strtolower($field), strtolower($class1));
            $pos2 = strpos(strtolower($field), strtolower($class2));
            if ($pos !== false) {
                $fieldName = str_replace(strtolower($class1) . '_', '', strtolower($field));
                array_push($master_fields, $fieldName);
            } else if ($pos2 !== false) {
                $fieldName = str_replace(strtolower($class2) . '_', '', strtolower($field));
                array_push($related_fields, $fieldName);
            }
        }

        $html = '<html ' . get_language_header() . '><body><table border = 1><tr>';

        foreach ($master_fields as $master_field) {
            $html .= '<td>' . $class1 . '_' . $master_field . '</td>';
        }
        foreach ($related_fields as $related_field) {
            $html .= '<td>' . $class2 . '_' . $related_field . '</td>';
        }
        $html .= '</tr>';

        $ids = $merge_array['ids'];
        $is_prospect_merge = ($seed1->object_name == 'Prospect');
        foreach ($ids as $key => $value) {
            if ($is_prospect_merge) {
                $seed1 = $seed1->retrieveTarget($key);
            } else {
                $seed1->retrieve($key);
            }
            $html .= '<tr>';
            foreach ($master_fields as $master_field) {
                if (isset($seed1->$master_field)) {
                    if ($seed1->field_name_map[$master_field]['type'] == 'enum') {
                        //pull in the translated dom
                        $html .= '<td>' . $app_list_strings[$seed1->field_name_map[$master_field]['options']][$seed1->$master_field] . '</td>';
                    } else {
                        $html .= '<td>' . $seed1->$master_field . '</td>';
                    }
                } else {
                    $html .= '<td></td>';
                }
            }
            if (isset($value) && !empty($value)) {
                $seed2->retrieve($value);
                foreach ($related_fields as $related_field) {
                    if (isset($seed2->$related_field)) {
                        if ($seed2->field_name_map[$related_field]['type'] == 'enum') {
                            //pull in the translated dom
                            $html .= '<td>' . $app_list_strings[$seed2->field_name_map[$related_field]['options']][$seed2->$related_field] . '</td>';
                        } else {
                            $html .= '<td>' . $seed2->$related_field . '</td>';
                        }
                    } else {
                        $html .= '<td></td>';
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= "</table></body></html>";
    }

    $result = base64_encode($html);
    return ['result' => $result, 'error' => $error];
}

$server->register(
    'get_mailmerge_document2',
    ['session' => 'xsd:string', 'file_name' => 'xsd:string', 'fields' => 'tns:select_fields'],
    ['return' => 'tns:get_mailmerge_document_result'],
    $NAMESPACE);

/**
 * Enter description here...
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param unknown_type $file_name
 * @param unknown_type $fields
 * @return unknown
 */
function get_mailmerge_document2($session, $file_name, $fields)
{
    global $beanList, $beanFiles, $app_list_strings, $app_strings;

    $error = new SoapError();
    if (!validate_authenticated($session)) {
        LoggerManager::getLogger()->error('invalid_login');
        $error->set_error('invalid_login');
        return ['result' => '', 'error' => $error->get_soap_array()];
    }
    if (!preg_match('/^sugardata[\.\d\s]+\.php$/', $file_name)) {
        LoggerManager::getLogger()->error($app_strings['ERR_NO_SUCH_FILE'] . " ({$file_name})");
        $error->set_error('no_records');
        return ['result' => '', 'error' => $error->get_soap_array()];
    }
    $html = '';

    $file_name = sugar_cached('MergedDocuments/') . pathinfo($file_name, PATHINFO_BASENAME);

    $master_fields = [];
    $related_fields = [];

    if (file_exists($file_name)) {
        include($file_name);

        $class1 = $merge_array['master_module'];
        $beanL = $beanList[$class1];
        $bean1 = $beanFiles[$beanL];
        require_once($bean1);
        $seed1 = new $beanL();

        if (!empty($merge_array['related_module'])) {
            $class2 = $merge_array['related_module'];
            $beanR = $beanList[$class2];
            $bean2 = $beanFiles[$beanR];
            require_once($bean2);
            $seed2 = new $beanR();
        }

        //parse fields
        //$token1 = strtolower($class1);
        if ($class1 == 'Prospects') {
            $class1 = 'CampaignProspects';
        }
        foreach ($fields as $field) {
            $pos = strpos(strtolower($field), strtolower($class1));
            $pos2 = strpos(strtolower($field), strtolower($class2));
            if ($pos !== false) {
                $fieldName = str_replace(strtolower($class1) . '_', '', strtolower($field));
                array_push($master_fields, $fieldName);
            } else if ($pos2 !== false) {
                $fieldName = str_replace(strtolower($class2) . '_', '', strtolower($field));
                array_push($related_fields, $fieldName);
            }
        }

        $html = '<html ' . get_language_header() . '><body><table border = 1><tr>';

        foreach ($master_fields as $master_field) {
            $html .= '<td>' . $class1 . '_' . $master_field . '</td>';
        }
        foreach ($related_fields as $related_field) {
            $html .= '<td>' . $class2 . '_' . $related_field . '</td>';
        }
        $html .= '</tr>';

        $ids = $merge_array['ids'];
        $resultIds = [];
        $is_prospect_merge = ($seed1->object_name == 'Prospect');
        if ($is_prospect_merge) {
            $pSeed = $seed1;
        }
        foreach ($ids as $key => $value) {

            if ($is_prospect_merge) {
                $seed1 = $pSeed->retrieveTarget($key);
            } else {
                $seed1->retrieve($key);
            }
            $resultIds[] = ['name' => $seed1->module_name, 'value' => $key];
            $html .= '<tr>';
            foreach ($master_fields as $master_field) {
                if (isset($seed1->$master_field)) {
                    if ($seed1->field_name_map[$master_field]['type'] == 'enum') {
                        //pull in the translated dom
                        $html .= '<td>' . $app_list_strings[$seed1->field_name_map[$master_field]['options']][$seed1->$master_field] . '</td>';
                    } else if ($seed1->field_name_map[$master_field]['type'] == 'multienum') {

                        if (isset($app_list_strings[$seed1->field_name_map[$master_field]['options']])) {
                            $items = unencodeMultienum($seed1->$master_field);
                            $output = [];
                            foreach ($items as $item) {
                                if (!empty($app_list_strings[$seed1->field_name_map[$master_field]['options']][$item])) {
                                    array_push($output, $app_list_strings[$seed1->field_name_map[$master_field]['options']][$item]);

                                }

                            } // foreach

                            $encoded_output = encodeMultienumValue($output);
                            $html .= "<td>$encoded_output</td>";

                        }
                    } else if ($seed1->field_name_map[$master_field]['type'] == 'currency') {
                        $amount_field = $seed1->$master_field;
                        $params = ['currency_symbol' => false];
                        $amount_field = currency_format_number($amount_field, $params);
                        $html .= '<td>' . $amount_field . '</td>';
                    } else {
                        $html .= '<td>' . $seed1->$master_field . '</td>';
                    }
                } else {
                    $html .= '<td></td>';
                }
            }
            if (isset($value) && !empty($value)) {
                $resultIds[] = ['name' => $seed2->module_name, 'value' => $value];
                $seed2->retrieve($value);
                foreach ($related_fields as $related_field) {
                    if (isset($seed2->$related_field)) {
                        if ($seed2->field_name_map[$related_field]['type'] == 'enum') {
                            //pull in the translated dom
                            $html .= '<td>' . $app_list_strings[$seed2->field_name_map[$related_field]['options']][$seed2->$related_field] . '</td>';
                        } else if ($seed2->field_name_map[$related_field]['type'] == 'currency') {
                            $amount_field = $seed2->$related_field;
                            $params = ['currency_symbol' => false];
                            $amount_field = currency_format_number($amount_field, $params);
                            $html .= '<td>' . $amount_field . '</td>';
                        } else {
                            $html .= '<td>' . $seed2->$related_field . '</td>';
                        }
                    } else {
                        $html .= '<td></td>';
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= "</table></body></html>";
    }
    $result = base64_encode($html);

    return ['html' => $result, 'name_value_list' => $resultIds, 'error' => $error];
}

$server->register(
    'get_document_revision',
    ['session' => 'xsd:string', 'i' => 'xsd:string'],
    ['return' => 'tns:return_document_revision'],
    $NAMESPACE);

/**
 * This method is used as a result of the .htaccess lock down on the cache directory. It will allow a
 * properly authenticated user to download a document that they have proper rights to download.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $id -- ID of the document revision to obtain
 * @return return_document_revision - this is a complex type as defined in SoapTypes.php
 */
function get_document_revision($session, $id)
{


    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }


    $dr = new DocumentRevision();
    $dr->retrieve($id);
    if (!empty($dr->filename)) {
        $filename = "upload://{$dr->id}";
        $contents = base64_encode(sugar_file_get_contents($filename));
        return ['document_revision' => ['id' => $dr->id, 'document_name' => $dr->document_name, 'revision' => $dr->revision, 'filename' => $dr->filename, 'file' => $contents], 'error' => $error->get_soap_array()];
    } else {
        $error->set_error('no_records');
        return ['id' => -1, 'error' => $error->get_soap_array()];
    }

}

$server->register(
    'set_campaign_merge',
    ['session' => 'xsd:string', 'targets' => 'tns:select_fields', 'campaign_id' => 'xsd:string'],
    ['return' => 'tns:error_value'],
    $NAMESPACE);
/**
 *   Once we have successfuly done a mail merge on a campaign, we need to notify Sugar of the targets
 *   and the campaign_id for tracking purposes
 *
 * @param session        the session id of the authenticated user
 * @param targets        a string array of ids identifying the targets used in the merge
 * @param campaign_id    the campaign_id used for the merge
 *
 * @return error_value
 */
function set_campaign_merge($session, $targets, $campaign_id)
{
    $error = new SoapError();
    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return $error->get_soap_array();
    }
    if (empty($campaign_id) or !is_array($targets) or count($targets) == 0) {
        LoggerManager::getLogger()->debug('set_campaign_merge: Merge action status will not be updated, because, campaign_id is null or no targets were selected.');
    } else {
        require_once('modules/Campaigns/utils.php');
        campaign_log_mail_merge($campaign_id, $targets);
    }

    return $error->get_soap_array();
}

$server->register(
    'get_entries_count',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'query' => 'xsd:string', 'deleted' => 'xsd:int'],
    ['return' => 'tns:get_entries_count_result'],
    $NAMESPACE);

/**
 *   Retrieve number of records in a given module
 *
 * @param session        the session id of the authenticated user
 * @param module_name    module to retrieve number of records from
 * @param query          allows webservice user to provide a WHERE clause
 * @param deleted        specify whether or not to include deleted records
 *
 * @return get_entries_count_result - this is a complex type as defined in SoapTypes.php
 */
function get_entries_count($session, $module_name, $query, $deleted)
{
    global $beanList, $beanFiles;
    $current_user = AuthenticationController::getInstance()->getCurrentUser();

    $error = new SoapError();

    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }

    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }

    if (!check_modules_access($current_user, $module_name, 'list')) {
        $error->set_error('no_access');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }

    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $seed = new $class_name();

    if (!$seed->ACLAccess('ListView')) {
        $error->set_error('no_access');
        return [
            'result_count' => -1,
            'error' => $error->get_soap_array()
        ];
    }

    $sql = 'SELECT COUNT(*) result_count FROM ' . $seed->table_name . ' ';


    // build WHERE clauses, if any
    $where_clauses = [];
    if (!empty($query)) {
        require_once 'include/SugarSQLValidate.php';
        $valid = new SugarSQLValidate();
        if (!$valid->validateQueryClauses($query)) {
            LoggerManager::getLogger()->error("Bad query: $query");
            $error->set_error('no_access');
            return [
                'result_count' => -1,
                'error' => $error->get_soap_array()
            ];
        }
        $where_clauses[] = $query;
    }
    if ($deleted == 0) {
        $where_clauses[] = $seed->table_name . '.deleted = 0';
    }

    // if WHERE clauses exist, add them to query
    if (!empty($where_clauses)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
    }

    $res = DBManagerFactory::getInstance()->query($sql);
    $row = DBManagerFactory::getInstance()->fetchByAssoc($res);

    return [
        'result_count' => $row['result_count'],
        'error' => $error->get_soap_array()
    ];
}

$server->register(
    'set_entries_details',
    ['session' => 'xsd:string', 'module_name' => 'xsd:string', 'name_value_lists' => 'tns:name_value_lists', 'select_fields' => 'tns:select_fields'],
    ['return' => 'tns:set_entries_detail_result'],
    $NAMESPACE);

/**
 * Update or create a list of SugarBeans, returning details about the records created/updated
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_lists -- Array of Bean specific Arrays where the keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @return Array    'name_value_lists' --  Array of Bean specific Arrays where the keys of the array are the SugarBean attributes, the values of the array are the values the attributes should have.
 *                  'error' -- The SOAP error if any.
 */
function set_entries_details($session, $module_name, $name_value_lists, $select_fields)
{
    $error = new SoapError();

    if (!validate_authenticated($session)) {
        $error->set_error('invalid_login');

        return [
            'ids' => [],
            'error' => $error->get_soap_array()
        ];
    }

    return handle_set_entries($module_name, $name_value_lists, $select_fields);
}

// INTERNAL FUNCTION NOT EXPOSED THROUGH API
function handle_set_entries($module_name, $name_value_lists, $select_fields = FALSE)
{
    global $beanList, $beanFiles, $app_list_strings;
    $current_user = AuthenticationController::getInstance()->getCurrentUser();

    $error = new SoapError();
    $ret_values = [];

    if (empty($beanList[$module_name])) {
        $error->set_error('no_module');
        return ['ids' => [], 'error' => $error->get_soap_array()];
    }

    if (!check_modules_access($current_user, $module_name, 'write')) {
        $error->set_error('no_access');
        return ['ids' => -1, 'error' => $error->get_soap_array()];
    }

    $class_name = $beanList[$module_name];
    require_once($beanFiles[$class_name]);
    $ids = [];
    $count = 1;
    $total = sizeof($name_value_lists);

    foreach ($name_value_lists as $name_value_list) {
        $seed = new $class_name();

        $seed->update_vcal = false;

        //See if we can retrieve the seed by a given id value
        foreach ($name_value_list as $value) {
            if ($value['name'] == 'id') {
                $seed->retrieve($value['value']);
                break;
            }
        }


        $dataValues = [];

        foreach ($name_value_list as $value) {
            $val = $value['value'];

            if ($seed->field_name_map[$value['name']]['type'] == 'enum' || $seed->field_name_map[$value['name']]['type'] == 'radioenum') {
                $vardef = $seed->field_name_map[$value['name']];
                if (isset($app_list_strings[$vardef['options']]) && !isset($app_list_strings[$vardef['options']][$val])) {
                    if (in_array($val, $app_list_strings[$vardef['options']])) {
                        $val = array_search($val, $app_list_strings[$vardef['options']]);
                    }
                }

            } else if ($seed->field_name_map[$value['name']]['type'] == 'multienum') {

                $vardef = $seed->field_name_map[$value['name']];

                if (isset($app_list_strings[$vardef['options']]) && !isset($app_list_strings[$vardef['options']][$value])) {
                    $items = explode(",", $val);
                    $parsedItems = [];
                    foreach ($items as $item) {
                        if (in_array($item, $app_list_strings[$vardef['options']])) {
                            $keyVal = array_search($item, $app_list_strings[$vardef['options']]);
                            array_push($parsedItems, $keyVal);
                        }
                    }

                    if (!empty($parsedItems)) {
                        $val = encodeMultienumValue($parsedItems);
                    }
                }
            }

            //Apply the non-empty values now since this will be used for duplicate checks
            //allow string or int of 0 to be updated if set.
            if (!empty($val) || ($val === '0' || $val === 0)) {
                //PHP7 COMPAT
                //ORIGINAL: $seed->$value['name'] = $val;
                $value_name = $value['name'];
                $seed->$value_name = $val;
                //END
            }
            //Store all the values in dataValues Array to apply later
            $dataValues[$value['name']] = $val;
        }

        if ($count == $total) {
            $seed->update_vcal = false;
        }
        $count++;

        //Add the account to a contact
        if ($module_name == 'Contacts') {
            LoggerManager::getLogger()->debug('Creating Contact Account');
            add_create_account($seed);
            $duplicate_id = check_for_duplicate_contacts($seed);
            if ($duplicate_id == null) {
                if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                    //Now apply the values, since this is not a duplicate we can just pass false for the $firstSync argument
                    apply_values($seed, $dataValues, false);
                    $seed->save();
                    if ($seed->deleted == 1) {
                        $seed->mark_deleted($seed->id);
                    }
                    $ids[] = $seed->id;
                }
            } else {
                //since we found a duplicate we should set the sync flag
                if ($seed->ACLAccess('Save')) {
                    //Determine if this is a first time sync.  We find out based on whether or not a contacts_users relationship exists
                    $seed->id = $duplicate_id;
                    $seed->load_relationship("user_sync");
                    $beans = $seed->user_sync->getBeans();
                    $first_sync = empty($beans);

                    //Now apply the values and indicate whether or not this is a first time sync
                    apply_values($seed, $dataValues, $first_sync);
                    $seed->contacts_users_id = $current_user->id;
                    $seed->save();
                    $ids[] = $duplicate_id;//we have a conflict
                }
            }

        } else if ($module_name == 'Meetings' || $module_name == 'Calls') {
            //we are going to check if we have a meeting in the system
            //with the same outlook_id. If we do find one then we will grab that
            //id and save it
            if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                // Check if we're updating an old record, or creating a new
                if (empty($seed->id)) {
                    // If it's a new one, and we have outlook_id set
                    // which means we're syncing from OPI check if it already exists
                    if (!empty($seed->outlook_id)) {
                        LoggerManager::getLogger()->debug(
                            'Looking for ' . $module_name . ' with outlook_id ' . $seed->outlook_id
                        );

                        $fields = [
                            'outlook_id' => $seed->outlook_id
                        ];
                        // Try to fetch a bean with this outlook_id
                        $temp = BeanFactory::getBean($module_name);
                        $temp = $temp->retrieve_by_string_fields($fields);

                        // If we fetched one, just copy the ID to the one we're syncing
                        if (!empty($temp)) {
                            $seed->id = $temp->id;
                        } else {
                            LoggerManager::getLogger()->debug(
                                'Looking for ' . $module_name .
                                ' with name/date_start/duration_hours/duration_minutes ' .
                                $seed->name . '/' . $seed->date_start . '/' .
                                $seed->duration_hours . '/' . $seed->duration_minutes
                            );

                            // If we didn't, try to find the meeting by comparing the passed
                            // Subject, start date and duration
                            $fields = [
                                'name' => $seed->name,
                                'date_start' => $seed->date_start,
                                'duration_hours' => $seed->duration_hours,
                                'duration_minutes' => $seed->duration_minutes
                            ];
                            $temp = BeanFactory::getBean($module_name);
                            $temp = $temp->retrieve_by_string_fields($fields);

                            if (!empty($temp)) {
                                $seed->id = $temp->id;
                            }
                        }
                        LoggerManager::getLogger()->debug(
                            $module_name . ' found: ' . !empty($seed->id)
                        );
                    }
                }
                if (empty($seed->reminder_time)) {
                    $seed->reminder_time = -1;
                }
                if ($seed->reminder_time == -1) {
                    $defaultRemindrTime = $current_user->getPreference('reminder_time');
                    if ($defaultRemindrTime != -1) {
                        $seed->reminder_checked = '1';
                        $seed->reminder_time = $defaultRemindrTime;
                    }
                }
                $seed->save();
                if ($seed->deleted == 1) {
                    $seed->mark_deleted($seed->id);
                }
                $ids[] = $seed->id;
            }//fi
        } else {
            if ($seed->ACLAccess('Save') && ($seed->deleted != 1 || $seed->ACLAccess('Delete'))) {
                $seed->save();
                $ids[] = $seed->id;
            }
        }

        // if somebody is calling set_entries_detail() and wants fields returned...
        if ($select_fields !== FALSE) {
            $ret_values[$count] = [];

            foreach ($select_fields as $select_field) {
                if (isset($seed->$select_field)) {
                    $ret_values[$count][] = get_name_value($select_field, $seed->$select_field);
                }
            }
        }
    }

    // handle returns for set_entries_detail() and set_entries()
    if ($select_fields !== FALSE) {
        return [
            'name_value_lists' => $ret_values,
            'error' => $error->get_soap_array()
        ];
    } else {
        return [
            'ids' => $ids,
            'error' => $error->get_soap_array()
        ];
    }
}


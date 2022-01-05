<?php


/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/

/**
 * Stub class to allow Link class easily use SugarEmailAddress
 */

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;

/**
 * Core email_address table
 */
$dictionary['EmailAddress'] = [
    'table' => 'email_addresses',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'vname' => 'LBL_EMAIL_ADDRESS_ID',
            'required' => true,
        ],
        'email_address' => [
            'name' => 'email_address',
            'type' => 'varchar',
            'vname' => 'LBL_EMAIL_ADDRESS',
            'length' => 100,
            'required' => true,
        ],
        'email_address_caps' => [
            'name' => 'email_address_caps',
            'type' => 'varchar',
            'vname' => 'LBL_EMAIL_ADDRESS_CAPS',
            'length' => 100,
            'required' => true,
            'reportable' => false,
        ],
        'invalid_email' => [
            'name' => 'invalid_email',
            'type' => 'bool',
            'default' => 0,
            'vname' => 'LBL_INVALID_EMAIL',
        ],
        'opt_out' => [
            'name' => 'opt_out',
            'type' => 'bool',
            'default' => 0,
            'vname' => 'LBL_OPT_OUT',
        ],
        'date_created' => [
            'name' => 'date_created',
            'type' => 'datetime',
            'vname' => 'LBL_DATE_CREATE',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
            'vname' => 'LBL_DATE_MODIFIED',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => 0,
            'vname' => 'LBL_DELETED',
        ],
        'primary_address' => [
            'name' => 'primary_address',
            'type' => 'bool',
            'source' => 'non-db',
            'vname' => 'LBL_PRIMARY_ADDRESS'
        ],
        'opt_in_status' => [
            'name' => 'opt_in_status',
            'type' => 'varchar',
            'source' => 'non-db',
            'comment' => 'possible values opted_in, opted_out, pending'
        ],
    ],
    'indices' => [
        [
            'name' => 'edremail_adssespk',
            'type' => 'primary',
            'fields' => ['id']
        ],
        [
            'name' => 'idx_ea_caps_opt_out_invalid',
            'type' => 'index',
            'fields' => ['email_address_caps', 'opt_out', 'invalid_email']
        ],
        [
            'name' => 'idx_ea_opt_out_invalid',
            'type' => 'index',
            'fields' => ['email_address', 'opt_out', 'invalid_email']
        ],
    ],
];

VardefManager::createVardef('EmailAddresses', 'EmailAddress', []);

if(file_exists('custom/metadata/email_addressesMetaData.php')) {
  include('custom/metadata/email_addressesMetaData.php');
} else {
  include('metadata/email_addressesMetaData.php');
}

if(file_exists('custom/metadata/emails_beansMetaData.php')) {
  include('custom/metadata/emails_beansMetaData.php');
} else {
  include('metadata/emails_beansMetaData.php');
}

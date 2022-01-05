<?php


/**
 * Relationship table linking emails with 1 or more SugarBeans
 */
$dictionary['emails_beans'] = ['table' => 'emails_beans',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'varchar',
            'dbType' => 'id',
            'len' => '36'
        ],
        'email_id' => [
            'name' => 'email_id',
            'type' => 'varchar',
            'dbType' => 'id',
            'len' => '36',
            'comment' => 'FK to emails table',
        ],
        'bean_id' => [
            'name' => 'bean_id',
            'dbType' => 'id',
            'type' => 'varchar',
            'len' => '36',
            'comment' => 'FK to various beans\'s tables',
        ],
        'bean_module' => [
            'name' => 'bean_module',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'bean\'s Module',
        ],
        'campaign_data' => ['name' => 'campaign_data',
            'type' => 'text',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime'
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false
        ]
    ],
    'indices' => [
        'emails_beanspk' => [
            'name' => 'emails_beanspk',
            'type' => 'primary',
            'fields' => ['id']
        ],
        'idx_emails_beans_bean_id' => [
            'name' => 'idx_emails_beans_bean_id',
            'type' => 'index',
            'fields' => ['bean_id']
        ],
        'idx_emails_beans_email_bean' => [
            'name' => 'idx_emails_beans_email_bean',
            'type' => 'alternate_key',
            'fields' => ['email_id', 'bean_id', 'deleted']
        ],
    ],
    'relationships' => [
        'emails_accounts_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Accounts',
        ],
// CR1000426 cleanup backend, module Bugs removed
//		'emails_bugs_rel' => array(
//			'lhs_module'		=> 'Emails',
//			'lhs_table'			=> 'emails',
//			'lhs_key'			=> 'id',
//			'rhs_module'		=> 'Bugs',
//			'rhs_table'			=> 'bugs',
//			'rhs_key'			=> 'id',
//			'relationship_type'	=> 'many-to-many',
//			'join_table'		=> 'emails_beans',
//			'join_key_lhs'		=> 'email_id',
//			'join_key_rhs'		=> 'bean_id',
//            'relationship_role_column' => 'bean_module',
//            'relationship_role_column_value' => 'Bugs',
//		),
// CR1000426 cleanup backend, module Cases removed
//		'emails_cases_rel' => array(
//			'lhs_module'		=> 'Emails',
//			'lhs_table'			=> 'emails',
//			'lhs_key'			=> 'id',
//			'rhs_module'		=> 'Cases',
//			'rhs_table'			=> 'cases',
//			'rhs_key'			=> 'id',
//			'relationship_type'	=> 'many-to-many',
//			'join_table'		=> 'emails_beans',
//			'join_key_lhs'		=> 'email_id',
//			'join_key_rhs'		=> 'bean_id',
//            'relationship_role_column' => 'bean_module',
//            'relationship_role_column_value' => 'Cases',
//		),
        'emails_contacts_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Contacts',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
        ],
        'emails_leads_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Leads',
            'rhs_table' => 'leads',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Leads',
        ],
        'emails_opportunities_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Opportunities',
        ],
        'emails_tasks_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Tasks',
        ],
        'emails_users_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Users',
            'rhs_table' => 'users',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Users',
        ],
        'emails_projects_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Projects',
            'rhs_table' => 'projects',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Projects',
        ],
        'emails_projectwbss_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'ProjectWBSs',
            'rhs_table' => 'projectwbss',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'ProjectWBSs',
        ],
        'emails_prospects_rel' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Prospects',
            'rhs_table' => 'prospects',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Prospects',
        ],
        'emails_quotes' => [
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Quotes',
            'rhs_table' => 'quotes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Quotes',
        ],
    ]
];

if (file_exists('modules/ServiceOrders/ServiceOrder.php')) {
    $dictionary['emails_beans']['relationships']['emails_serviceorders_rel'] = [
        'lhs_module' => 'Emails',
        'lhs_table' => 'emails',
        'lhs_key' => 'id',
        'rhs_module' => 'ServiceOrders',
        'rhs_table' => 'serviceorders',
        'rhs_key' => 'id',
        'relationship_type' => 'many-to-many',
        'join_table' => 'emails_beans',
        'join_key_lhs' => 'email_id',
        'join_key_rhs' => 'bean_id',
        'relationship_role_column' => 'bean_module',
        'relationship_role_column_value' => 'ServiceOrders',
    ];
}

if (file_exists('modules/ServiceTickets/ServiceTicket.php')) {
    $dictionary['emails_beans']['relationships']['emails_servicetickets_rel'] = [
        'lhs_module' => 'Emails',
        'lhs_table' => 'emails',
        'lhs_key' => 'id',
        'rhs_module' => 'ServiceTickets',
        'rhs_table' => 'servicetickets',
        'rhs_key' => 'id',
        'relationship_type' => 'many-to-many',
        'join_table' => 'emails_beans',
        'join_key_lhs' => 'email_id',
        'join_key_rhs' => 'bean_id',
        'relationship_role_column' => 'bean_module',
        'relationship_role_column_value' => 'ServiceTickets',
    ];
}

/**
 * Large text field table, shares a 1:1 with the emails table.  Moving all longtext fields to this table allows more
 * effiencient email management and full-text search capabilities.
 */
$dictionary['emails_text'] = [
    'table' => 'emails_text',
    'comment' => 'Large email text fields',
    'fields' => [
        'email_id' => [
            'name' => 'email_id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'dbType' => 'id',
            'len' => 36,
            'required' => true,
            'isnull' => false,
            'reportable' => true,
            'comment' => 'Foreign key to emails table',
        ],
        'from_addr' => [
            'name' => 'from_addr',
            'vname' => 'LBL_FROM',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Email address of person who send the email',
        ],
        'reply_to_addr' => [
            'name' => 'reply_to_addr',
            'vname' => 'LBL_REPLY_TO',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'reply to email address',
        ],
        'to_addrs' => [
            'name' => 'to_addrs',
            'vname' => 'LBL_TO',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive the email',
        ],
        'cc_addrs' => [
            'name' => 'cc_addrs',
            'vname' => 'LBL_CC',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive a carbon copy of the email',
        ],
        'bcc_addrs' => [
            'name' => 'bcc_addrs',
            'vname' => 'LBL_BCC',
            'type' => 'text',
            'comment' => 'Email address(es) of person(s) to receive a blind carbon copy of the email',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_TEXT_BODY',
            'type' => 'longtext',
            'reportable' => false,
            'comment' => 'Email body in plain text',
        ],
        'description_html' => [
            'name' => 'description_html',
            'vname' => 'LBL_HTML_BODY',
            'type' => 'longhtml',
            'reportable' => false,
            'comment' => 'Email body in HTML format',
        ],
        'raw_source' => [
            'name' => 'raw_source',
            'vname' => 'LBL_RAW',
            'type' => 'longtext',
            'reportable' => false,
            'comment' => 'Full raw source of email',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'default' => 0,
        ],
    ],
    'indices' => [
        [
            'name' => 'emails_textpk',
            'type' => 'primary',
            'fields' => ['email_id']
        ],
        [
            'name' => 'emails_textfromaddr',
            'type' => 'index',
            'fields' => ['from_addr']
        ],
    ],
];

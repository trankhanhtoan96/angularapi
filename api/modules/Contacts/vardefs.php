<?php


use SpiceCRM\includes\SugarObjects\VardefManager;
global $dictionary;
$dictionary['Contact'] = [
    'table' => 'contacts',
    'audited' => true,
    'fields' =>
        [
            'email_and_name1' => [
                'name' => 'email_and_name1',
                'rname' => 'email_and_name1',
                'vname' => 'LBL_NAME',
                'type' => 'varchar',
                'source' => 'non-db',
                'len' => '510',
                'importable' => 'false',
            ],
            // changed to enum on contact evel
            'gdpr_marketing_agreement' => [
                'name' => 'gdpr_marketing_agreement',
                'vname' => 'LBL_GDPR_MARKETING_AGREEMENT',
                'type' => 'enum',
                'options' => 'gdpr_marketing_agreement_dom',
                'audited' => true
            ],
            'gdpr_marketing_source' => [
                'name' => 'gdpr_marketing_source',
                'vname' => 'LBL_GDPR_MARKETING_SOURCE',
                'type' => 'varchar',
                'len' => '100',
                'audited' => true
            ],
            'gdpr_data_source' => [
                'name' => 'gdpr_data_source',
                'vname' => 'LBL_GDPR_DATA_SOURCE',
                'type' => 'varchar',
                'len' => '100',
                'audited' => true
            ],
            'lead_source' => [
                'name' => 'lead_source',
                'vname' => 'LBL_LEAD_SOURCE',
                'type' => 'enum',
                'options' => 'lead_source_dom',
                'len' => '255',
                'comment' => 'How did the contact come about',
            ],

            'account_name' => [
                'name' => 'account_name',
                'rname' => 'name',
                'id_name' => 'account_id',
                'vname' => 'LBL_ACCOUNT_NAME',
                'join_name' => 'accounts',
                'type' => 'relate',
                'link' => 'accounts',
                'table' => 'accounts',
                'isnull' => 'true',
                'module' => 'Accounts',
                'dbType' => 'varchar',
                'len' => '255',
                'source' => 'non-db',
                'unified_search' => false,
            ],
            'account_id' => [
                'name' => 'account_id',
                'rname' => 'id',
                'id_name' => 'account_id',
                'vname' => 'LBL_ACCOUNT_ID',
                'type' => 'relate',
                'link' => 'accounts',
                'table' => 'accounts',
                'isnull' => 'true',
                'module' => 'Accounts',
                'dbType' => 'id',
                'reportable' => false,
                'source' => 'non-db',
                'massupdate' => false,
                'duplicate_merge' => 'disabled',
                'hideacl' => true,
            ],
            'opportunity_role_fields' => [
                'name' => 'opportunity_role_fields',
                'rname' => 'id',
                'relationship_fields' => [
                    'id' => 'opportunity_role_id',
                    'contact_role' => 'opportunity_role',
                    'propensity_to_buy' => 'opportunity_propensity_to_buy',
                    'level_of_support' => 'opportunity_level_of_support',
                    'level_of_influence' => 'opportunity_level_of_influence'
                ],
                'vname' => 'LBL_ACCOUNT_NAME',
                'type' => 'relate',
                'link' => 'opportunities',
                'link_type' => 'relationship_info',
                'join_link_name' => 'opportunities_contacts',
                'source' => 'non-db',
                'importable' => 'false',
                'duplicate_merge' => 'disabled',
                'studio' => false,
            ],
            'opportunity_role_id' => [
                'name' => 'opportunity_role_id',
                'type' => 'varchar',
                'source' => 'non-db',
                'vname' => 'LBL_OPPORTUNITY_ROLE_ID',
                'studio' => ['listview' => false],
            ],
            // Opportunities link olus related non db bfelder
            'opportunities' => [
                'name' => 'opportunities',
                'type' => 'link',
                'relationship' => 'opportunities_contacts',
                'source' => 'non-db',
                'module' => 'Opportunities',
                'bean_name' => 'Opportunity',
                'vname' => 'LBL_OPPORTUNITIES',
            ],
            'opportunity_role' => [
                'name' => 'opportunity_role',
                'type' => 'enum',
                'source' => 'non-db',
                'vname' => 'LBL_ROLE',
                'options' => 'opportunity_relationship_type_dom',
            ],
            'opportunity_propensity_to_buy' => [
                'name' => 'opportunity_propensity_to_buy',
                'type' => 'enum',
                'source' => 'non-db',
                'vname' => 'LBL_PROPENSITY_TO_BUY',
                'options' => 'opportunity_relationship_buying_center_dom',
            ],
            'opportunity_level_of_support' => [
                'name' => 'opportunity_level_of_support',
                'type' => 'enum',
                'source' => 'non-db',
                'vname' => 'LBL_LEVEL_OF_SUPPORT',
                'options' => 'opportunity_relationship_buying_center_dom',
            ],
            'opportunity_level_of_influence' => [
                'name' => 'opportunity_level_of_influence',
                'type' => 'enum',
                'source' => 'non-db',
                'vname' => 'LBL_LEVEL_OF_INFLUENCE',
                'options' => 'opportunity_relationship_buying_center_dom',
            ],
            'activity_accept_status' => [
                'name' => 'activity_accept_status',
                'type' => 'enum',
                'source' => 'non-db',
                'vname' => 'LBL_ACTIVITY_ACCEPT_STATUS',
                'options' => 'dom_meeting_accept_status',
                'comment' => 'non db field retrieved from the relationship to the meeting call etc'
            ],
            'activity_status_date_modified' => [
                'name' => 'activity_status_date_modified',
                'type' => 'datetime',
                'source' => 'non-db',
                'vname' => 'LBL_ACTIVITY_STATUS_DATE_MODFIFIED',
                'comment' => 'non db field retrieved from the relationship to the meeting call etc'
            ],
            'activity_required' => [
                'name' => 'activity_required',
                'type' => 'bool',
                'source' => 'non-db',
                'vname' => 'LBL_ACTIVITY_REQUIRED',
                'comment' => 'non db field retrieved from the relationship to the meeting call etc'
            ],
            'reports_to_id' => [
                'name' => 'reports_to_id',
                'vname' => 'LBL_REPORTS_TO_ID',
                'type' => 'id',
                'required' => false,
                'reportable' => false,
                'comment' => 'The contact this contact reports to'
            ],
            'report_to_name' => [
                'name' => 'report_to_name',
                'rname' => 'last_name',
                'id_name' => 'reports_to_id',
                'vname' => 'LBL_REPORTS_TO',
                'type' => 'relate',
                'link' => 'reports_to_link',
                'table' => 'contacts',
                'isnull' => 'true',
                'module' => 'Contacts',
                'dbType' => 'varchar',
                'len' => 'id',
                'reportable' => false,
                'source' => 'non-db',
            ],
            'birthdate' => [
                'name' => 'birthdate',
                'vname' => 'LBL_BIRTHDATE',
                'massupdate' => false,
                'type' => 'date',
                'comment' => 'The birthdate of the contact'
            ],
            'accounts' => [
                'name' => 'accounts',
                'type' => 'link',
                'relationship' => 'accounts_contacts',
                'link_type' => 'one',
                'source' => 'non-db',
                'vname' => 'LBL_ACCOUNT',
                'duplicate_merge' => 'disabled',
                'module' => 'Accounts'
            ],
            'reports_to_link' => [
                'name' => 'reports_to_link',
                'type' => 'link',
                'relationship' => 'contact_direct_reports',
                'link_type' => 'one',
                'side' => 'right',
                'source' => 'non-db',
                'vname' => 'LBL_REPORTS_TO',
            ],
// CR1000426 cleanup backend, module Bugs removed
//            'bugs' => array(
//                'name' => 'bugs',
//                'type' => 'link',
//                'relationship' => 'contacts_bugs',
//                'source' => 'non-db',
//                'vname' => 'LBL_BUGS',
//            ),
            'calls' => [
                'name' => 'calls',
                'type' => 'link',
                'relationship' => 'calls_contacts',
                'source' => 'non-db',
                'module' => 'Calls',
                'vname' => 'LBL_CALLS',
            ],
            'calls_parent' => [
                'name' => 'calls_parent',
                'type' => 'link',
                'relationship' => 'contact_calls_parent',
                'source' => 'non-db',
                'vname' => 'LBL_CALLS',
            ],
            'callattempts_parent' => [
                'name' => 'callattempts_parent',
                'type' => 'link',
                'relationship' => 'contact_callattempts_parent',
                'source' => 'non-db',
                'vname' => 'LBL_CALLATTEMPTS',
            ],
// CR1000426 cleanup backend, module Cases removed
//            'cases' => array(
//                'name' => 'cases',
//                'type' => 'link',
//                'relationship' => 'contacts_cases',
//                'source' => 'non-db',
//                'vname' => 'LBL_CASES',
//            ),
            'direct_reports' => [
                'name' => 'direct_reports',
                'type' => 'link',
                'relationship' => 'contact_direct_reports',
                'source' => 'non-db',
                'vname' => 'LBL_DIRECT_REPORTS',
            ],
            'emails' => [
                'name' => 'emails',
                'type' => 'link',
                'relationship' => 'emails_contacts_rel',
                'source' => 'non-db',
                'vname' => 'LBL_EMAILS',
            ],
            'letters' => [
                'name'         => 'letters',
                'type'         => 'link',
                'relationship' => 'contact_letters',
                'module' => 'Letters',
                'bean_name' => 'Letter',
                'source'       => 'non-db',
                'vname'        => 'LBL_LETTERS',
            ],
            'documents' => [
                'name' => 'documents',
                'type' => 'link',
                'relationship' => 'documents_contacts',
                'source' => 'non-db',
                'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
            ],
            'leads' => [
                'name' => 'leads',
                'type' => 'link',
                'relationship' => 'contact_leads',
                'source' => 'non-db',
                'vname' => 'LBL_LEADS',
                'module' => 'Leads'
            ],
            'meetings' => [
                'name' => 'meetings',
                'type' => 'link',
                'relationship' => 'meetings_contacts',
                'source' => 'non-db',
                'vname' => 'LBL_MEETINGS',
            ],
            'meetings_parent' => [
                'name' => 'meetings_parent',
                'type' => 'link',
                'relationship' => 'contact_meetings_parent',
                'source' => 'non-db',
                'vname' => 'LBL_MEETINGS',
            ],
            'notes' => [
                'name' => 'notes',
                'type' => 'link',
                'relationship' => 'contact_notes',
                'source' => 'non-db',
                'vname' => 'LBL_NOTES',
            ],
            //@deprecated name project. Use projects
//            'project' => array(
//                'name' => 'project',
//                'type' => 'link',
//                'relationship' => 'projects_contacts',
//                'source' => 'non-db',
//                'vname' => 'LBL_PROJECTS_DEPRECATED',
//            ),
            'projects' => [
                'name' => 'projects',
                'type' => 'link',
                'relationship' => 'projects_contacts',
                'source' => 'non-db',
                'vname' => 'LBL_PROJECTS',
            ],
            'tasks' => [
                'name' => 'tasks',
                'type' => 'link',
                'relationship' => 'contact_tasks',
                'source' => 'non-db',
                'vname' => 'LBL_TASKS',
            ],
            'tasks_parent' => [
                'name' => 'tasks_parent',
                'type' => 'link',
                'relationship' => 'contact_tasks_parent',
                'source' => 'non-db',
                'vname' => 'LBL_TASKS',
                'reportable' => false
            ],
            'notes_parent' => [
                'name' => 'notes_parent',
                'type' => 'link',
                'relationship' => 'contact_notes_parent',
                'source' => 'non-db',
                'vname' => 'LBL_TASKS',
                'reportable' => false
            ],
            'user_sync' => [
                'name' => 'user_sync',
                'type' => 'link',
                'relationship' => 'contacts_users',
                'source' => 'non-db',
                'vname' => 'LBL_USER_SYNC',
            ],
            'created_by_link' => [
                'name' => 'created_by_link',
                'type' => 'link',
                'relationship' => 'contacts_created_by',
                'vname' => 'LBL_CREATED_BY_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
            ],
            'modified_user_link' => [
                'name' => 'modified_user_link',
                'type' => 'link',
                'relationship' => 'contacts_modified_user',
                'vname' => 'LBL_MODIFIED_BY_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
            ],
            'assigned_user_link' => [
                'name' => 'assigned_user_link',
                'type' => 'link',
                'relationship' => 'contacts_assigned_user',
                'vname' => 'LBL_ASSIGNED_TO',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
                'rname' => 'user_name',
                'id_name' => 'assigned_user_id',
                'table' => 'users',
                'duplicate_merge' => 'enabled'
            ],
            'campaign_id' => [
                'name' => 'campaign_id',
                'comment' => 'Campaign that generated lead',
                'vname' => 'LBL_CAMPAIGN_ID',
                'rname' => 'id',
                'id_name' => 'campaign_id',
                'type' => 'id',
                'table' => 'campaigns',
                'isnull' => 'true',
                'module' => 'Campaigns',
                'massupdate' => false,
                'duplicate_merge' => 'disabled',
            ],
            'campaign_name' => [
                'name' => 'campaign_name',
                'rname' => 'name',
                'vname' => 'LBL_CAMPAIGN',
                'type' => 'relate',
                'link' => 'campaign_contacts',
                'isnull' => 'true',
                'reportable' => false,
                'source' => 'non-db',
                'table' => 'campaigns',
                'id_name' => 'campaign_id',
                'module' => 'Campaigns',
                'duplicate_merge' => 'disabled',
                'comment' => 'The first campaign name for Contact (Meta-data only)',
            ],
            'campaigns' => [
                'name' => 'campaigns',
                'type' => 'link',
                'relationship' => 'contact_campaign_log',
                'module' => 'CampaignLog',
                'bean_name' => 'CampaignLog',
                'source' => 'non-db',
                'vname' => 'LBL_CAMPAIGNLOG',
            ],
            'campaign_contacts' => [
                'name' => 'campaign_contacts',
                'type' => 'link',
                'vname' => 'LBL_CAMPAIGN_CONTACT',
                'relationship' => 'campaign_contacts',
                'source' => 'non-db',
            ],
            'c_accept_status_fields' => [
                'name' => 'c_accept_status_fields',
                'rname' => 'id',
                'relationship_fields' => ['id' => 'accept_status_id', 'accept_status' => 'accept_status_name'],
                'vname' => 'LBL_LIST_ACCEPT_STATUS',
                'type' => 'relate',
                'link' => 'calls',
                'link_type' => 'relationship_info',
                'source' => 'non-db',
                'importable' => 'false',
                'duplicate_merge' => 'disabled',
                'studio' => false,
            ],
            'm_accept_status_fields' => [
                'name' => 'm_accept_status_fields',
                'rname' => 'id',
                'relationship_fields' => ['id' => 'accept_status_id', 'accept_status' => 'accept_status_name'],
                'vname' => 'LBL_LIST_ACCEPT_STATUS',
                'type' => 'relate',
                'link' => 'meetings',
                'link_type' => 'relationship_info',
                'source' => 'non-db',
                'importable' => 'false',
                'hideacl' => true,
                'duplicate_merge' => 'disabled',
                'studio' => false,
            ],
            'accept_status_id' => [
                'name' => 'accept_status_id',
                'type' => 'varchar',
                'source' => 'non-db',
                'vname' => 'LBL_LIST_ACCEPT_STATUS',
                'studio' => ['listview' => false],
            ],
            'accept_status_name' => [
                'massupdate' => false,
                'name' => 'accept_status_name',
                'type' => 'enum',
                'studio' => 'false',
                'source' => 'non-db',
                'vname' => 'LBL_LIST_ACCEPT_STATUS',
                'options' => 'dom_meeting_accept_status',
                'importable' => 'false',
            ],
            'prospect_lists' => [
                'name' => 'prospect_lists',
                'type' => 'link',
                'relationship' => 'prospect_list_contacts',
                'module' => 'ProspectLists',
                'source' => 'non-db',
                'vname' => 'LBL_PROSPECT_LIST',
                'rel_fields' => [
                    'quantity' => [
                        'map' => 'prospectlists_contacts_quantity'
                    ]
                ]
            ],
            'sync_contact' => [
                'massupdate' => false,
                'name' => 'sync_contact',
                'vname' => 'LBL_SYNC_CONTACT',
                'type' => 'bool',
                'source' => 'non-db',
                'comment' => 'Synch to outlook?  (Meta-Data only)',
                'studio' => 'true',
            ],
            'eventregistrations' => [
                'name' => 'eventregistrations',
                'vname' => 'LBL_EVENTREGISTRATOINS_LINK',
                'type' => 'link',
                'relationship' => 'eventregistration_contact_rel',
                'source' => 'non-db',
            ],
            'ext_id' => [
                'name' => 'ext_id',
                'vname' => 'LBL_EXT_ID',
                'type' => 'varchar',
                'len' => 50
            ],
            'portal_user_id' => [
                'name' => 'portal_user_id',
                'vname' => 'LBL_PORTAL_USER_ID',
                'type' => 'varchar',
                'len' => 36
            ],
            'events_contact_role' => [
                'name' => 'events_contact_role',
                'vname' => 'LBL_ROLE',
                'type' => 'enum',
                'source' => 'non-db',
                'options' => 'events_contact_roles_dom'
            ],
            'events' => [
                'name' => 'events',
                'type' => 'link',
                'relationship' => 'events_contacts',
                'module' => 'Events',
                'bean_name' => 'Event',
                'source' => 'non-db',
                'vname' => 'LBL_EVENT',
                'rel_fields' => [
                    'contact_role' => [
                        'map' => 'events_contact_role'
                    ]
                ]
            ],
            'bonuscards' => [
                'name' => 'bonuscards',
                'type' => 'link',
                'relationship' => 'bonuscards_contacts',
                'module' => 'BonusCards',
                'bean_name' => 'BonusCard',
                'source' => 'non-db',
                'vname' => 'LBL_BONUSCARDS',
            ],
            'prospectlists_contacts_quantity' => [
                'name' => 'prospectlists_contacts_quantity',
                'vname' => 'LBL_QUANTITY',
                'type' => 'varchar',
                'source' => 'non-db'
            ],
            'catalogorders' => [
                'name' => 'catalogorders',
                'type' => 'link',
                'module' => 'CatalogOrders',
                'relationship' => 'contacts_catalogorders',
                'source' => 'non-db'
            ],
            'inquiries' => [
                'name' => 'inquiries',
                'type' => 'link',
                'module' => 'Inquiries',
                'relationship' => 'contact_inquiries',
                'source' => 'non-db'
            ],
            'relationship_type' => [
                'name' => 'relationship_type',
                'type' => 'enum',
                'source' => 'non-db',
                'options' => 'relationship_type_dom',
                'vname' => 'LBL_RELATIONSHIP_TYPE'
            ],
            'environments' => [
                'name' => 'environments',
                'type' => 'link',
                'relationship' => 'contacts_contacts',
                'module' => 'Contacts',
                'bean_name' => 'Contact',
                'source' => 'non-db',
                'vname' => 'LBL_ENVIRONMENT_CONTACTS',
                'duplicate_merge' => 'disabled',
                'link_type' => 'one',
                'rel_fields' =>
                    [
                        'relationship_type' =>
                            [
                                'map' => 'relationship_type'
                            ],
                    ],
            ]
        ],
    'indices' => [
        [
            'name' => 'idx_cont_last_first',
            'type' => 'index',
            'fields' => ['last_name', 'first_name', 'deleted']
        ],
        [
            'name' => 'idx_contacts_del_last',
            'type' => 'index',
            'fields' => ['deleted', 'last_name'],
        ],
        [
            'name' => 'idx_cont_del_reports',
            'type' => 'index',
            'fields' => ['deleted', 'reports_to_id', 'last_name']
        ],
        [
            'name' => 'idx_reports_to_id',
            'type' => 'index',
            'fields' => ['reports_to_id'],
        ],
        [
            'name' => 'idx_del_id_user',
            'type' => 'index',
            'fields' => ['deleted', 'id', 'assigned_user_id'],
        ],
        [
            'name' => 'idx_cont_assigned',
            'type' => 'index',
            'fields' => ['assigned_user_id']
        ]
    ],
    'relationships' => [
        'contact_direct_reports' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'reports_to_id',
            'relationship_type' => 'one-to-many'],
        'contact_leads' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Leads',
            'rhs_table' => 'leads',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'],
        'contact_notes' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'],
        'contact_letters' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Letters',
            'rhs_table' => 'letters',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contact_textmessages' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'TextMessages',
            'rhs_table' => 'textmessages',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many',
        ],
        'contact_tasks' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'],
        'contact_tasks_parent' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contact_notes_parent' => ['lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contacts_assigned_user' => ['lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'],
        'contacts_modified_user' => ['lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'],
        'contacts_created_by' => ['lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'],
        'contact_campaign_log' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'CampaignLog',
            'rhs_table' => 'campaign_log',
            'rhs_key' => 'target_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'target_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contact_calls_parent' => [
            'lhs_module' => 'Contacts', 'lhs_table' => 'contacts', 'lhs_key' => 'id',
            'rhs_module' => 'Calls', 'rhs_table' => 'calls', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contact_meetings_parent' => [
            'lhs_module' => 'Contacts', 'lhs_table' => 'contacts', 'lhs_key' => 'id',
            'rhs_module' => 'Meetings', 'rhs_table' => 'meetings', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
        'contact_callattempts_parent' => [
            'lhs_module' => 'Contacts', 'lhs_table' => 'contacts', 'lhs_key' => 'id',
            'rhs_module' => 'CallAttempts', 'rhs_table' => 'callattempts', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many', 'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Contacts'
        ],
    ],
    'optimistic_locking' => true,
];

// CE version has not all modules...
//set global else error with PHP7.1: Uncaught Error: Cannot use string offset as an array
global $dictionary;
if (file_exists("extensions/modules/SalesDocs")) {
    $dictionary['Contact']['fields']['salesdocsop'] = [
        'name' => 'salesdocsop',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCSOP',
        'relationship' => 'salesdocs_contactsop',
        'module' => 'SalesDocs',
        'source' => 'non-db',
    ];
    $dictionary['Contact']['fields']['salesdocsrp'] = [
        'name' => 'salesdocsrp',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCSRP',
        'relationship' => 'salesdocs_contactsrp',
        'module' => 'SalesDocs',
        'source' => 'non-db',
    ];
    $dictionary['Contact']['fields']['salesdocs'] = [
        'name' => 'salesdocs',
        'type' => 'link',
        'vname' => 'LBL_SALESDOCS',
        'relationship' => 'salesdocs_contacts',
        'module' => 'SalesDocs',
        'source' => 'non-db',
    ];
}
if (file_exists("extensions/modules/ContactsOnlineProfiles")) {
    $dictionary['Contact']['fields']['contactsonlineprofiles'] = [
        'name' => 'contactsonlineprofiles',
        'type' => 'link',
        'vname' => 'LBL_CONTACTSONLINEPROFILES',
        'relationship' => 'contact_contactonlineprofiles',
        'module' => 'ContactsOnlineProfiles',
        'source' => 'non-db',
    ];
}
if (file_exists("extensions/modules/ContactCCDetails")) {
    $dictionary['Contact']['fields']['contactccdetails'] = [
        'name' => 'contactccdetails',
        'vname' => 'LBL_CONTACTCCDETAILS_LINK',
        'type' => 'link',
        'relationship' => 'contacts_contactccdetails',
        'link_type' => 'one',
        'source' => 'non-db',
        'duplicate_merge' => 'disabled',
        'massupdate' => false,
        'default' => true, //UI: load related beans on contact load. module property required!
        'module' => 'ContactCCDetails'
    ];
}
if (file_exists("modules/Addresses")) {
    $dictionary['Contact']['fields']['addresses'] = [
        'name' => 'addresses',
        'type' => 'link',
        'relationship' => 'contact_addresses',
        'source' => 'non-db',
        'vname' => 'LBL_ADDRESSES',
        'module' => 'Addresses',
        'default' => true
    ];
}
if (file_exists("extensions/modules/ServiceOrders")) {
    $dictionary['Contact']['fields']['serviceorders'] = [
        'name' => 'serviceorders',
        'type' => 'link',
        'relationship' => 'serviceorders_contacts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEORDERS',
        'module' => 'ServiceOrders',
        'default' => false
    ];
}
if (file_exists("modules/ServiceTickets")) {
    $dictionary['Contact']['fields']['servicetickets'] = [
        'name' => 'servicetickets',
        'type' => 'link',
        'relationship' => 'servicetickets_contacts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICETICKETS',
        'module' => 'ServiceTickets',
        'default' => false
    ];
}
if (file_exists("extensions/modules/ServiceCalls")) {
    $dictionary['Contact']['fields']['servicecalls'] = [
        'name' => 'servicecalls',
        'type' => 'link',
        'relationship' => 'servicecalls_contacts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICECALLS',
        'module' => 'ServiceCalls',
        'default' => false
    ];
}

if (file_exists("extensions/modules/ServiceFeedbacks")) {
    $dictionary['Contact']['fields']['servicefeedbacks'] = [
        'name' => 'servicefeedbacks',
        'type' => 'link',
        'relationship' => 'servicefeedbacks_contacts',
        'source' => 'non-db',
        'vname' => 'LBL_SERVICEFEEDBACKS',
        'module' => 'ServiceFeedbacks',
        'default' => false
    ];

}
// Not sure we need this at all.... commented for now
//if (file_exists("extensions/modules/ServiceEquipments")) {
//    $dictionary['Contact']['fields']['serviceequipments'] = array(
//        'name' => 'serviceequipments',
//        'type' => 'link',
//        'relationship' => 'serviceequipments_contacts',
//        'source' => 'non-db',
//        'vname' => 'LBL_SERVICEEQUIPMENTS',
//        'module' => 'ServiceEquipments',
//        'default' => false
//    );
//}

if (file_exists('extensions/modules/SalesVouchers')) {
    $dictionary['Contact']['fields']['salesvouchers'] = [
        'name' => 'salesvouchers',
        'type' => 'link',
        'relationship' => 'contacts_salesvouchers',
        'module' => 'SalesVouchers',
        'source' => 'non-db',
        'vname' => 'LBL_SALESVOUCHERS',
    ];
}
if (file_exists('extensions/modules/Potentials')) {
    $dictionary['Contact']['fields']['potentials'] = [
        'name' => 'potentials',
        'type' => 'link',
        'relationship' => 'contacts_potentials',
        'module' => 'Potentials',
        'source' => 'non-db',
        'vname' => 'LBL_POTENTIALS',
    ];
}
VardefManager::createVardef('Contacts', 'Contact', ['default', 'assignable', 'person']);

// CR1000661
global $dictionary;
if(file_exists('extensions/modules/PartnerAgreements')) {
    $dictionary['Contact']['fields']['partneragreements'] = [
        'name' => 'partneragreements',
        'vname' => 'LBL_PARTNERAGREEMENTS',
        'type' => 'link',
        'relationship' => 'partneragreements_contacts',
        'module' => 'PartnerAgreements',
        'bean_name' => 'PartnerAgreement',
        'source' => 'non-db',
    ];
    $dictionary['Contact']['fields']['partneragreement_role'] = [
        'name' => 'partneragreement_role',
        'vname' => 'LBL_ROLE',
        'type' => 'enum',
        'options' => 'partneragreement_contact_role_dom',
        'source' => 'non-db',
        'comment' => 'for relationship field partneragreement_role'
    ];
    $dictionary['Contact']['fields']['propensity_to_partnership'] = [
        'name' => 'propensity_to_partnership',
        'vname' => 'LBL_PROPENSITY_TO_PARTNERSHIP',
        'type' => 'enum',
        'options' => 'partneragreement_propensity_role_dom',
        'source' => 'non-db',
        'comment' => 'for relationship field propensity_to_partnership'
    ];
}

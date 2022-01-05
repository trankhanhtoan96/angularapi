<?php


$vardefs = [
    'fields' => [
        'assigned_user_id' =>
            [
                'name' => 'assigned_user_id',
                'rname' => 'user_name',
                'id_name' => 'assigned_user_id',
                'vname' => 'LBL_ASSIGNED_TO_ID',
                'group' => 'assigned_user_name',
                'type' => 'varchar',
                'dbtype' => 'char',
                'table' => 'users',
                'module' => 'Users',
                'reportable' => true,
                'isnull' => 'false',
                'len' => '36',
                'audited' => true,
                'comment' => 'User ID assigned to record',
                'duplicate_merge' => 'disabled',
                'link' => 'assigned_user_link'
            ],
        'assigned_user_name' =>
            [
                'name' => 'assigned_user_name',
                'link' => 'assigned_user_link',
                'vname' => 'LBL_ASSIGNED_TO',
                'rname' => 'user_name',
                'type' => 'relate',
                'reportable' => false,
                'source' => 'non-db',
                'table' => 'users',
                'id_name' => 'assigned_user_id',
                'module' => 'Users',
                'duplicate_merge' => 'disabled'
            ],
        'assigned_user_link' =>
            [
                'name' => 'assigned_user_link',
                'type' => 'link',
                'relationship' => strtolower($module) . '_assigned_user',
                'vname' => 'LBL_ASSIGNED_TO_USER',
                'link_type' => 'one',
                'module' => 'Users',
                'bean_name' => 'User',
                'source' => 'non-db',
                'rname' => 'user_name',
                'id_name' => 'assigned_user_id',
                'table' => 'users',
                'recover' => false,
                'duplicate_merge' => 'disabled'
            ]
    ],
    'relationships' => [
        strtolower($module) . '_assigned_user' =>
            [
                'lhs_module' => 'Users',
                'lhs_table' => 'users',
                'lhs_key' => 'id',
                'rhs_module' => $module,
                'rhs_table' => strtolower($module),
                'rhs_key' => 'assigned_user_id',
                'relationship_type' => 'one-to-many'
            ]
    ]
];

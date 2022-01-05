<?php


global $dictionary;
$dictionary['ACLAction'] = ['table' => 'acl_actions', 'comment' => 'Determine the allowable actions available to users'
, 'fields' => [
        'id' =>
            [
                'name' => 'id',
                'vname' => 'LBL_ID',
                'required' => true,
                'type' => 'id',
                'reportable' => false,
                'comment' => 'Unique identifier'
            ],
        'date_entered' =>
            [
                'name' => 'date_entered',
                'vname' => 'LBL_DATE_ENTERED',
                'type' => 'datetime',
                'required' => true,
                'comment' => 'Date record created'
            ],
        'date_modified' =>
            [
                'name' => 'date_modified',
                'vname' => 'LBL_DATE_MODIFIED',
                'type' => 'datetime',
                'required' => true,
                'comment' => 'Date record last modified'
            ],
        'modified_user_id' =>
            [
                'name' => 'modified_user_id',
                'rname' => 'user_name',
                'id_name' => 'modified_user_id',
                'vname' => 'LBL_MODIFIED_BY_ID',
                'type' => 'assigned_user_name',
                'table' => 'modified_user_id_users',
                'isnull' => 'false',
                'dbType' => 'id',
                'required' => false,
                'len' => 36,
                'reportable' => true,
                'comment' => 'User who last modified record'
            ],
        'created_by' =>
            [
                'name' => 'created_by',
                'rname' => 'user_name',
                'id_name' => 'created_by',
                'vname' => 'LBL_CREATED',
                'type' => 'assigned_user_name',
                'table' => 'created_by_users',
                'isnull' => 'false',
                'dbType' => 'id',
                'len' => 36,
                'comment' => 'User ID who created record'
            ],
        'name' =>
            [
                'name' => 'name',
                'type' => 'varchar',
                'vname' => 'LBL_NAME',
                'len' => 150,
                'comment' => 'Name of the allowable action (view, list, delete, edit)'
            ],
        'category' =>
            [
                'name' => 'category',
                'vname' => 'LBL_CATEGORY',
                'type' => 'varchar',
                'len' => 100,
                'reportable' => true,
                'comment' => 'Category of the allowable action (usually the name of a module)'
            ],
        'acltype' =>
            [
                'name' => 'acltype',
                'vname' => 'LBL_TYPE',
                'type' => 'varchar',
                'len' => 100,
                'reportable' => true,
                'comment' => "Specifier for Category, usually 'module'"
            ],
        'aclaccess' =>
            [
                'name' => 'aclaccess',
                'vname' => 'LBL_ACCESS',
                'type' => 'int',
                'len' => 3,
                'reportable' => true,
                'comment' => "Number specifying access priority; highest access 'wins'"
            ],
        'deleted' =>
            [
                'name' => 'deleted',
                'vname' => 'LBL_DELETED',
                'type' => 'bool',
                'reportable' => false,
                'comment' => 'Record deletion indicator'
            ],
        'roles' =>
            [
                'name' => 'roles',
                'type' => 'link',
                'relationship' => 'acl_roles_actions',
                'source' => 'non-db',
                'vname' => 'LBL_USERS',
            ],
    ],
    'indices' => [
        ['name' => 'aclactionid', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_aclaction_id_del', 'type' => 'index', 'fields' => ['id', 'deleted']],
        ['name' => 'idx_category_name', 'type' => 'index', 'fields' => ['category', 'name']],
    ]
];

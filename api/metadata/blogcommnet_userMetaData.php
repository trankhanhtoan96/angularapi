<?php
global $dictionary;
$dictionary['blogcomment_user'] = [
    'table' => 'blogcomment_user',
    'fields' => [
        ['name' => 'id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'user_id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'blogcomment_id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'date_modified', 'type' => 'datetime']
        , ['name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => false, 'default' => 0]
    ], 'indices' => [
        ['name' => 'blogcomment_user1', 'type' => 'primary', 'fields' => ['id']]
        , ['name' => 'blogcomment_user2', 'type' => 'alternate_key', 'fields' => ['user_id', 'blogcomment_id']]
        , ['name' => 'blogcomment_user3', 'type' => 'index', 'fields' => ['user_id', 'deleted', 'blogcomment_id']]
    ],
    'relationships' => [
        'blogcomment_user' => [
            'lhs_module' => 'BlogComment', 'lhs_table' => 'blogcomment', 'lhs_key' => 'id',
            'rhs_module' => 'Users', 'rhs_table' => 'users', 'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'blogcomment_user', 'join_key_lhs' => 'blogcomment_id', 'join_key_rhs' => 'user_id'
        ]
    ]
];

<?php
global $dictionary;
$dictionary['blog_user'] = [
    'table' => 'blog_user',
    'fields' => [
        ['name' => 'id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'user_id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'blog_id', 'type' => 'varchar', 'len' => '36']
        , ['name' => 'date_modified', 'type' => 'datetime']
        , ['name' => 'deleted', 'type' => 'bool', 'len' => '1', 'required' => false, 'default' => 0]
    ], 'indices' => [
        ['name' => 'blog_user1', 'type' => 'primary', 'fields' => ['id']]
        , ['name' => 'blog_user2', 'type' => 'alternate_key', 'fields' => ['user_id', 'blog_id']]
        , ['name' => 'blog_user3', 'type' => 'index', 'fields' => ['user_id', 'deleted', 'blog_id']]
    ],
    'relationships' => [
        'blog_user' => [
            'lhs_module' => 'Blog', 'lhs_table' => 'blog', 'lhs_key' => 'id',
            'rhs_module' => 'Users', 'rhs_table' => 'users', 'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'blog_user', 'join_key_lhs' => 'blog_id', 'join_key_rhs' => 'user_id'
        ]
    ]
];

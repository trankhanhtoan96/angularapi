<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['BlogPage'] = [
    'table' => 'blogpage',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => [
        'content' => [
            'name' => 'content',
            'vname' => 'LBL_CONTENT',
            'type' => 'html',
            'dbType' => 'longtext'
        ],
        'image' => [
            'name' => 'image',
            'vname' => 'LBL_IMAGE',
            'dbType' => 'text',
            'type' => 'image'
        ],
        'slug' => [
            'name' => 'slug',
            'vname' => 'LBL_SLUG',
            'type' => 'varchar',
            'len' => 255
        ],
        'seo_title' => [
            'name' => 'seo_title',
            'vname' => 'LBL_SEO_TITLE',
            'type' => 'varchar',
            'len' => 255
        ],
        'seo_keywork' => [
            'name' => 'seo_keywork',
            'vname' => 'LBL_SEO_KEYWORK',
            'type' => 'varchar',
            'len' => 255
        ],
        'seo_description' => [
            'name' => 'seo_description',
            'vname' => 'LBL_SEO_DESCRIPTION',
            'type' => 'text'
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options'=>'blog_status_options'
        ],
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogPage', 'BlogPage', ['default']);

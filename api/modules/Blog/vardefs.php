<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['Blog'] = [
    'table' => 'blog',
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
        'allow_comment' => [
            'name' => 'allow_comment',
            'vname' => 'LBL_ALLOW_COMMENT',
            'type' => 'enum',
            'options'=>'blog_allow_comment_options'
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options'=>'blog_status_options'
        ],
        'category_id' => array(
            'name' => 'category_id',
            'type' => 'id',
            'vname'=>'LBL_CATEGORY'
        ),
        'category_name' => array(
            'id_name' => 'category_id',
            'vname'=>'LBL_CATEGORY',
            'name' => 'category_name',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'BlogCategory',
        ),
        'view_count' => array(
            'name' => 'view_count',
            'vname'=>'LBL_VIEW_COUNT',
            'type' => 'int'
        ),
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('Blog', 'Blog', ['default']);

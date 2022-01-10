<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['BlogComment'] = [
    'table' => 'blogcomment',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => [
        'blog_id' => array(
            'name' => 'blog_id',
            'type' => 'id',
            'vname'=>'LBL_BLOG'
        ),
        'blog_name' => array(
            'id_name' => 'blog_id',
            'vname'=>'LBL_BLOG',
            'name' => 'blog_name',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'Blog',
        ),
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'html',
            'dbType' => 'text'
        ],
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogComment', 'BlogComment', ['default']);

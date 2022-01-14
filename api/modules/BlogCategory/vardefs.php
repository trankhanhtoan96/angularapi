<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['BlogCategory'] = [
    'table' => 'blogcategory',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => [
        'parent_id' => array(
            'name' => 'parent_id',
            'type' => 'id',
            'vname'=>'LBL_PARENT'
        ),
        'parent_name' => array(
            'id_name' => 'parent_id',
            'vname'=>'LBL_PARENT',
            'name' => 'parent_name',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'BlogCategory',
        ),
        'description' => [
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'html',
            'dbType' => 'text'
        ],
        'slug' => [
            'name' => 'slug',
            'vname' => 'LBL_SLUG',
            'type'=>'slug',
            'dbType' => 'varchar',
            'len' => 255
        ],
        'color_style' => [
            'name' => 'color_style',
            'vname' => 'LBL_COLOR_STYLE',
            'type' => 'enum',
            'options' => 'blogcategory_color_style_options'
        ],
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogCategory', 'BlogCategory', ['default']);

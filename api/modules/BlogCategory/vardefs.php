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
            'type' => 'id'
        ),
        'parent_name' => array(
            'id_name' => 'parent_name',
            'vname'=>'LBL_PARENT_NAME',
            'name' => 'referal_student_name',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'BlogCategory',
        ),
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogCategory', 'BlogCategory', ['default']);

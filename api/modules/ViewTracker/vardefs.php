<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['ViewTracker'] = [
    'table' => 'viewtracker',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => [
        'parent_id' => array(
            'name' => 'parent_id',
            'type' => 'id',
            'vname' => 'LBL_PARENT'
        ),
        'parent_name' => array(
            'id_name' => 'parent_id',
            'vname' => 'LBL_PARENT',
            'name' => 'parent_name',
            'type' => 'relate',
            'source' => 'non-db',
            'module' => 'Blog',
        ),
    ],
    'indices' => [
        [
            'name' => 'viewtrackerparentid',
            'type' => 'index',
            'fields' => [
                'parent_id'
            ]
        ],
    ],
    'relationships' => []
];

VardefManager::createVardef('ViewTracker', 'ViewTracker', ['default']);

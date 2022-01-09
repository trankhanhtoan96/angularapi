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
    'fields' => [],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogCategory', 'BlogCategory', ['default']);

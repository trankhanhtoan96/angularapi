<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

global $dictionary;
$dictionary['BlogSubcribeEmail'] = [
    'table' => 'blogsubcribeemail',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'fields' => [
    ],
    'indices' => [],
    'relationships' => []
];

VardefManager::createVardef('BlogSubcribeEmail', 'BlogSubcribeEmail', ['default']);

$dictionary['BlogSubcribeEmail']['fields']['name']['vname']='LBL_BLOG_SUBCRIBE_EMAIL';

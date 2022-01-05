<?php


use SpiceCRM\includes\SugarObjects\VardefManager;
global $dictionary;
$dictionary['DocumentRevision'] = [
    'table' => 'document_revisions',
    'audited' => true,
    'fields' => [
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_name',
            'type' => 'varchar',
            'len' => '100',
            'required' => false,
        ],
        'change_log' => [
            'name' => 'change_log',
            'vname' => 'LBL_LOGGED_CHANGES',
            'type' => 'varchar',
            'len' => '255',
        ],
        'document_id' => [
            'name' => 'document_id',
            'vname' => 'LBL_DOCUMENT',
            'type' => 'varchar',
            'len' => '36',
            'required' => false,
            'reportable' => false,
        ],
        'file_name' => [
            'name' => 'file_name',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'dbType' => 'varchar',
            'required' => true,
            'len' => '255'
        ],
        'file_ext' => [
            'name' => 'file_ext',
            'vname' => 'LBL_FILE_EXTENSION',
            'type' => 'varchar',
            'len' => 100,
        ],
        'file_mime_type' => [
            'name' => 'file_mime_type',
            'vname' => 'LBL_MIME',
            'type' => 'varchar',
            'len' => '100',
        ],
        'file_md5' => [
            'name' => 'file_md5',
            'vname' => 'LBL_FILE_MD5',
            'type' => 'char',
            'len' => '32',
        ],
        'revision' => [
            'name' => 'revision',
            'vname' => 'LBL_REVISION',
            'type' => 'int',
            'len' => 5,
        ],
        'documentrevisionstatus' => [
            'name' => 'documentrevisionstatus',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 1,
            'options' => 'document_revisionstatus_dom',
            'default' => 'c'
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'source' => 'non-db',
            'vname' => 'LBL_REVISIONS',
        ],
        'latest_revision_id' => [
            'name' => 'latest_revision_id',
            'vname' => 'LBL_REVISION',
            'type' => 'varchar',
            'len' => '36',
            'source' => 'non-db',
        ],
        'document_name' => [
            'name' => 'document_name',
            'rname' => 'name',
            'id_name' => 'document_id',
            'vname' => 'LBL_DOCUMENT',
            'join_name' => 'documents',
            'type' => 'relate',
            'link' => 'documents',
            'table' => 'documents',
            'isnull' => 'true',
            'module' => 'Documents',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'massupdate' => false,
        ],
        'documents' => [
            'name' => 'documents',
            'vname' => 'lbl_documents_link',
            'type' => 'link',
            'relationship' => 'document_revisions',
            'link_type' => 'one',
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ],
        'latest_revision' => [
            'name' => 'latest_revision',
            'vname' => 'LBL_CURRENT_DOC_VERSION',
            'type' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ]
    ],
    'relationships' => [
        'revisions_created_by' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'DocumentRevisions',
            'rhs_table' => 'document_revisions',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ]
    ],
    'indices' => [
//        ['name' => 'documentrevisionspk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'documentrevision_mimetype', 'type' => 'index', 'fields' => ['file_mime_type']],
    ]
];

VardefManager::createVardef('DocumentRevisions', 'DocumentRevision', ['default', 'assignable']);

<?php
global $dictionary;
$dictionary['UpgradeHistory'] = [
    'table' => 'upgrade_history', 'comment' => 'Tracks Sugar upgrades made over time; used by Upgrade Wizard and Module Loader',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'filename' => [
            'name' => 'filename',
            'type' => 'varchar',
            'len' => '255',
        ],
        'md5sum' => [
            'name' => 'md5sum',
            'type' => 'varchar',
            'len' => '32',
        ],
        'type' => [
            'name' => 'type',
            'type' => 'varchar',
            'len' => '30',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'varchar',
            'len' => '50',
        ],
        'version' => [
            'name' => 'version',
            'type' => 'varchar',
            'len' => '64',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'len' => '255',
        ],
        'description' => [
            'name' => 'description',
            'type' => 'text',
        ],
        'id_name' => [
            'name' => 'id_name',
            'type' => 'varchar',
            'len' => '255',
        ],
        'manifest' => [
            'name' => 'manifest',
            'type' => 'longtext',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'type' => 'datetime',
            'required' => true,
        ],
        'enabled' => [
            'name' => 'enabled',
            'type' => 'bool',
            'len' => '1',
            'default' => '1',
        ],
    ],

    'indices' => [
        ['name' => 'upgrade_history_pk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'upgrade_history_md5_uk', 'type' => 'unique', 'fields' => ['md5sum']],
    ],
];

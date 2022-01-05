<?php
if (empty(getenv('DB_NAME'))) foreach (file(dirname(__FILE__) . '/../.env') as $line) putenv(rtrim($line, "\n"));
$sugar_config = array(
    'dbconfig' =>
        array(
            'db_host_name' => getenv('DB_HOST'),
            'db_host_instance' => 'SQLEXPRESS',
            'db_port' => getenv('DB_PORT'),
            'db_user_name' => getenv('DB_USER'),
            'db_password' => getenv('DB_PASS'),
            'db_manager' => 'MysqliManager',
            'db_type' => 'mysql',
            'db_name' => getenv('DB_NAME'),
        ),
    'dbconfigoption' =>
        array(
            'persistance' => true,
            'autofree' => false,
            'debug' => 0,
            'ssl' => false,
            'collation' => 'utf8_general_ci',
            'charset' => 'utf8',
        ),
    'fts' =>
        array(
            'protocol' => 'http',
            'server' => 'localhost',
            'port' => '9200',
            'prefix' => 'spicecrm_',
        ),
    'site_url' => getenv('SITE_URL'),
    'developerMode' => true,
    'cache_dir' => 'cache/',
    'log_dir' => '.',
    'log_file' => 'spicecrm.log',
    'session_dir' => '',
    'sugar_version' => '2020.01.00',
    'default_language' => 'en_us',
    'tmp_dir' => 'cache/xml/',
    'media_files_dir' => 'media/',
    'upload_dir' => 'upload/',
    'upload_maxsize' => 30000000,
    'import_max_records_per_file' => 500,
    'unique_key' => getenv('APP_KEY'),
    'verify_client_ip' => false,
    'krest' =>
        array(
            'error_reporting' => 22517,
            'display_errors' => 0,
        ),
    'languages' =>
        array(
            'en_us' => 'English (US)',
        ),
    'logger' =>
        array(
            'level' => 'error',
        ),
    'frontend_url' => 'http://localhost/spicecrm/',
);

<?php
// created: 2021-12-26 19:15:30
$sugar_config = array (
  'dbconfig' => 
  array (
    'db_host_name' => 'localhost',
    'db_host_instance' => 'SQLEXPRESS',
    'db_port' => '',
    'db_user_name' => 'root',
    'db_password' => '123456',
    'db_manager' => 'MysqliManager',
    'db_type' => 'mysql',
    'db_name' => 'api',
  ),
  'dbconfigoption' => 
  array (
    'persistance' => true,
    'autofree' => false,
    'debug' => 0,
    'ssl' => false,
    'collation' => 'utf8_general_ci',
    'charset' => 'utf8',
  ),
  'fts' => 
  array (
    'protocol' => 'http',
    'server' => 'localhost',
    'port' => '9200',
    'prefix' => 'spicecrm_',
  ),
  'site_url' => 'http://localhost/spicecrm/api',
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
  'unique_key' => '467b34fb05d5c534f8482b71363e80ce',
  'verify_client_ip' => false,
  'krest' => 
  array (
    'error_reporting' => 22517,
    'display_errors' => 0,
  ),
  'languages' => 
  array (
    'en_us' => 'English (US)',
  ),
  'logger' => 
  array (
    'level' => 'error',
  ),
  'frontend_url' => 'http://localhost/spicecrm/',
);

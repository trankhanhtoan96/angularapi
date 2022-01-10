<?php


use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceUI\api\controllers\ConfServerController;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * restrict routes to authenticated users and only if the system acts as repository
 * also enable general unautohrized access if system is public repository
 */
#global $sugar_config;
//todo clarify ... do we need this if? should we add a auth middleware?
if((SpiceConfig::getInstance()->config['configrepository']['public'] !== true) || SpiceConfig::getInstance()->config['configrepository']['enabled'] !== true) return;


$routes = [
    [
        'method'      => 'get',
        'route'       => '/config',
        'class'       => ConfServerController::class,
        'function'    => 'getAvailable',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/repositoryitems',
        'class'       => ConfServerController::class,
        'function'    => 'getRepositoryItems',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/repositorymodules',
        'class'       => ConfServerController::class,
        'function'    => 'getRepositoryModules',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/{packages}/{version}',
        'class'       => ConfServerController::class,
        'function'    => 'getConfig',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/language/{language}/{package}/{version}',
        'class'       => ConfServerController::class,
        'function'    => 'getLanguageLabels',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/config/language/{language}',
        'class'       => ConfServerController::class,
        'function'    => 'getLanguageLabels',
        'description' => '',
        'options'     => ['noAuth' => true, 'adminOnly' => false],
    ],
];

/**
 * register the Extension
 */
$RESTManager->registerExtension('configrepository', '1.0', [], $routes);

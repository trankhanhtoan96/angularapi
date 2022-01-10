<?php


use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\api\controllers\UserSignatureController;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('usersignatures', '1.0');

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Users/{id}/signature',
        'class'       => UserSignatureController::class,
        'function'    => 'getUserSignature',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [

        ]
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/signature',
        'class'       => UserSignatureController::class,
        'function'    => 'setUserSignature',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);

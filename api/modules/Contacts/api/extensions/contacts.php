<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Contacts\api\controllers\ContactsController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('exchange', '1.0');

$routes = [
    [
        'method'      => 'put',
        'route'       => '/module/Contacts/{id}/exchangesync',
        'class'       => ContactsController::class,
        'function'    => 'ewsSync',
        'description' => 'send contact to ews',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'id'    => [
                'in'          => 'path',
                'description' => 'Contact id',
                'type'        => ValidationMiddleware::TYPE_STRING,
            ],
        ],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/Contacts/{id}/exchangesync',
        'class'       => ContactsController::class,
        'function'    => 'ewsDelete',
        'description' => 'delete contact from ews',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'id'    => [
                'in'          => 'path',
                'description' => 'Contact id',
                'type'        => ValidationMiddleware::TYPE_STRING,
            ],
        ],
    ],
];

$RESTManager->registerRoutes($routes);

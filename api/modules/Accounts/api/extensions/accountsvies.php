<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Accounts\api\controllers\AccountsVIESController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;
/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('accountvies', '1.0');

/**
 * restrict routes to authenticated users
 */
$routes = [
    [
        'method'      => 'get',
        'route'       => '/common/VIES/{vatid}',
        'oldroute'    => '/module/Accounts/VIES/{vatid}',
        'class'       => AccountsVIESController::class,
        'function'    => 'getVatResponse',
        'description' => 'get the response of European VAT url check',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'vatid'      => [
                'in'                => 'path',
                'description'       => 'the VAT ID',
                'type'              => ValidationMiddleware::TYPE_STRING,
            ],
        ],
    ],
];

$RESTManager->registerRoutes($routes);

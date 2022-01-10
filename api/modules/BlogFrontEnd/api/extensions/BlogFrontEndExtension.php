<?php
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\BlogFrontEnd\api\controllers\BlogFrontEndController;

$routes = [
    [
        'method' => 'get',
        'route' => '/frontend/home',
        'class' => BlogFrontEndController::class,
        'function' => 'home',
        'description' => 'home',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
];
RESTManager::getInstance()->registerExtension('blogfrontend', '1.0', [], $routes);

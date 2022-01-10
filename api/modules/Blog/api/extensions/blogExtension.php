<?php
use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Blog\api\controllers\BlogController;

$routes = [
    [
        'method' => 'get',
        'route' => '/blog/importdata',
        'class' => BlogController::class,
        'function' => 'importData',
        'description' => 'importData',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
];
RESTManager::getInstance()->registerExtension('blog', '1.0', [], $routes);

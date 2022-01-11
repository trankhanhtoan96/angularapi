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
    [
        'method' => 'get',
        'route' => '/frontend/home/loadmore',
        'class' => BlogFrontEndController::class,
        'function' => 'homeLoadMore',
        'description' => 'homeLoadMore',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/category/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getCategory',
        'description' => 'getCategory',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/categorydata/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getCategoryData',
        'description' => 'getCategoryData',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/categoryloadmore/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getCategoryLoadMore',
        'description' => 'getCategoryLoadMore',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/getcategoryslug/{id}',
        'class' => BlogFrontEndController::class,
        'function' => 'getCategorySlug',
        'description' => 'getcategoryslug',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    //blog
    [
        'method' => 'get',
        'route' => '/frontend/blog/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getBlog',
        'description' => 'getBlog',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/blogdata/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getBlogData',
        'description' => 'getBlogData',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
];
RESTManager::getInstance()->registerExtension('blogfrontend', '1.0', [], $routes);

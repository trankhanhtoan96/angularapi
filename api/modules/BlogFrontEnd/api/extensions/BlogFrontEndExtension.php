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
        'route' => '/frontend/getblogslug/{id}',
        'class' => BlogFrontEndController::class,
        'function' => 'getBlogSlug',
        'description' => 'getBlogSlug',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
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
    [
        'method' => 'get',
        'route' => '/frontend/blogcomment/{slug}',
        'class' => BlogFrontEndController::class,
        'function' => 'getBlogComment',
        'description' => 'getBlogComment',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'post',
        'route' => '/frontend/blogcomment/{blog_id}',
        'class' => BlogFrontEndController::class,
        'function' => 'saveBlogComment',
        'description' => 'saveBlogComment',
        'options' => ['noAuth' => false],
        'parameters' => []
    ],
    [
        'method' => 'post',
        'route' => '/frontend/blogcommentreact/{comment_id}',
        'class' => BlogFrontEndController::class,
        'function' => 'reactBlogComment',
        'description' => 'reactBlogComment',
        'options' => ['noAuth' => false],
        'parameters' => []
    ],
    //profile
    [
        'method' => 'get',
        'route' => '/frontend/profile/{id}',
        'class' => BlogFrontEndController::class,
        'function' => 'getProfile',
        'description' => 'getProfile',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/profiledata/{id}',
        'class' => BlogFrontEndController::class,
        'function' => 'getProfileData',
        'description' => 'getProfileData',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
    [
        'method' => 'get',
        'route' => '/frontend/profileloadmore/{id}',
        'class' => BlogFrontEndController::class,
        'function' => 'getProfileLoadMore',
        'description' => 'getProfileLoadMore',
        'options' => ['noAuth' => true],
        'parameters' => []
    ],
];
RESTManager::getInstance()->registerExtension('blogfrontend', '1.0', [], $routes);

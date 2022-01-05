<?php


use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Users\api\controllers\UsersController;
use SpiceCRM\modules\Users\api\controllers\ACLforPortalUserController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}',
        'oldroute'    => '/module/Users/{userId}',
        'class'       => UsersController::class,
        'function'    => 'saveUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [
            'email1' => [
                'in' => 'body',
                'description' => 'Force user to change the password on next login',
                'type' => ValidationMiddleware::TYPE_EMAIL,
                'required' => true,
            ],
            'id' => [
                'in' => 'body',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
            'userId' => [
                'in' => 'query',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
        ]
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/Users/{id}',
        'oldroute'    => '/module/Users/{userId}',
        'class'       => UsersController::class,
        'function'    => 'setUserInactive',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [
            'userId' => [
                'in' => 'query',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
        ]
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Users/{id}/deactivate',
        'oldroute'    => '/module/Users/{id}/deactivate',
        'class'       => UsersController::class,
        'function'    => 'getDeactivateUserStats',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [
            'id' => [
                'in' => 'query',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
        ]
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/deactivate',
        'oldroute'    => '/module/Users/{id}/deactivate',
        'class'       => UsersController::class,
        'function'    => 'deactivateUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [
            'id' => [
                'in' => 'query',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
        ]
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/{id}/activate',
        'oldroute'    => '/module/Users/{id}/activate',
        'class'       => UsersController::class,
        'function'    => 'activateUser',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => [
            'id' => [
                'in' => 'query',
                'description' => '',
                'type' => ValidationMiddleware::TYPE_GUID,
                'required' => true,
            ],
        ]
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Users/ACLforPortalUsers/set',
        'class'       => ACLforPortalUserController::class,
        'function'    => 'setACLprofile',
        'description' => 'Script (to execute by Postman) to set an ACL profile for all portal users. The ACL profile ID has to be set in config.php or config DB table (acl/portal_user_profile).',
        'options'     => ['noAuth' => false, 'adminOnly' => true, 'validate' => true ],
        'parameters' => [
            'doIt' => [
                'in' => 'body',
                'description' => 'Ensures that the script won´t get startet unintended.',
                'type' => ValidationMiddleware::TYPE_BOOL,
                'required' => true
            ],
        ]
    ],
];

/**
 * register the Extension
 */
$RESTManager->registerExtension('users', '1.0', [], $routes);

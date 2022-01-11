<?php


use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Contacts\api\controllers\ContactsPortalController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;

$routes = [
    [
        'method'      => 'get',
        'route'       => '/module/Contacts/{id}/portalAccess',
        'oldroute'    => '/portal/{id}/portalaccess',
        'class'       => ContactsPortalController::class,
        'function'    => 'getPortalUser',
        'description' => 'Gets the data of an Contact that is also Portal User.',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true ],
        'parameters'  => [
            'id' => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the Contact.',
            ]
        ]
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Contacts/{id}/portalAccess',
        'oldroute'    => '/portal/{contactId}/portalaccess/{action:create|update}',
        'class'       => ContactsPortalController::class,
        'function'    => 'createPortalUser',
        'description' => 'handles the creating and updating of contacts as portal users',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true ],
        'parameters'  => [
            'username'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The name of the user to be created.',
                'validationOptions' => [
                    ValidationMiddleware::VOPT_MIN_SIZE => 1,
                    ValidationMiddleware::VOPT_MAX_SIZE => $GLOBALS['dictionary']['User']['fields']['user_name']['len']
                ]
            ],
            'password'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The name of the user to be created.',
            ],
            'aclRole'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'The ACL role of the user to be created.',
            ],
            'aclProfile'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'The ACL profile of the user to be created.',
            ],
            'portalRole'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The portal role of the user to be created.',
            ],
            'setDateTimePrefsWithSystemDefaults' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_BOOL,
                'required'    => false,
                'description' => 'Set the date and time preferences of the user with the system defaults.',
            ],
            'status' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_BOOL,
                'required'    => false,
                'description' => 'The status of the user to be created.',
            ],
            'id' => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the Contact.',
            ]
        ]
    ],
    [
        'method'      => 'put',
        'route'       => '/module/Contacts/{id}/portalAccess',
        'oldroute'    => '/portal/{contactId}/portalaccess/{action:create|update}',
        'class'       => ContactsPortalController::class,
        'function'    => 'updatePortalUser',
        'description' => 'handles the creating and updating of contacts as portal users',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true ],
        'parameters'  => [
            'username'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The name of the user to be created.',
                'validationOptions' => [
                    ValidationMiddleware::VOPT_MIN_SIZE => 1,
                    ValidationMiddleware::VOPT_MAX_SIZE => $GLOBALS['dictionary']['User']['fields']['user_name']['len']
                ]
            ],
            'password'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'The name of the user to be created.',
            ],
            'aclRole'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'The ACL role of the user to be created.',
            ],
            'aclProfile'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'The ACL profile of the user to be created.',
            ],
            'portalRole'        => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The portal role of the user to be created.',
            ],
            'setDateTimePrefsWithSystemDefaults' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_BOOL,
                'required'    => false,
                'description' => 'Set the date and time preferences of the user with the system defaults.',
            ],
            'status' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_BOOL,
                'required'    => false,
                'description' => 'The status of the user to be created.',
            ],
            'id' => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the Contact.',
            ]
        ]
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Contacts/{id}/testUsername',
        'oldroute'    => '/portal/{contactId}/testUsername',
        'class'       => ContactsPortalController::class,
        'function'    => 'checkUsernameExistance',
        'description' => 'Checks if a potential user name already exists (except the concerning user).',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true ],
        'parameters'  => [
            'username'        => [
                'in'          => 'query',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The user name to check for.',
            ],
            'id' => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the Contact.',
            ]
        ]
    ],
];

/**
 * Register the API Extension
 */
RESTManager::getInstance()->registerExtension('portal', '2.0', [], $routes);

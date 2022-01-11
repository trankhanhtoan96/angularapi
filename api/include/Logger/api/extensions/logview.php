<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\Logger\api\controllers\LogViewController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;

$routes = [
    [
        'method'      => 'get',
        'route'       => '/admin/crmlog/entries',
        'oldroute'    => '/crmlog',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogGetEntries',
        'description' => 'Get the entries of the error log.',
        'options'     => ['noAuth' => false, 'adminOnly' => true, 'validate' => false],
        'parameters'  => [
            'level' => [
                'in' => 'query',
                'type'        => ValidationMiddleware::TYPE_ENUM,
                'options'     => ['debug', 'info', 'warn', 'deprecated', 'login', 'error', 'fatal', 'security'],
                'description' => 'Error level of the requested error log entries.',
            ],
            'limit' => [
                'in' => 'query',
                'description' => 'Maximal numbers of requested error log entries.',
                'type' => ValidationMiddleware::TYPE_NUMERIC,
                'default' => 250
            ],
            'processId' => [
                'in' => 'query',
                'description' => 'Process ID of the requested error log entries.',
                'type' => ValidationMiddleware::TYPE_NUMERIC
            ],
            'transactionId' => [
                'in' => 'query',
                'description' => 'Transaction ID of the requested error log entries.',
                'type' => ValidationMiddleware::TYPE_STRING,
            ],
            'userId' => [
                'in' => 'query',
                'description' => 'User ID of the requested error log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'text' => [
                'in' => 'query',
                'description' => 'Filter text for the requested error log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'end' => [
                'in' => 'query',
                'description' => 'End date and hour of the requested error log entries.',
                'type' => ValidationMiddleware::TYPE_STRING,
                'validationOptions' => [
                    ValidationMiddleware::VOPT_REGEX => '/^\d{10}$/',
                ],
            ],
        ]
    ],
    [
        'method'      => 'get',
        'route'       => '/admin/crmlog/entry/{id}',
        'oldroute'    => '/crmlog/entry/{entryId}',
        'class'       => LogViewController::class,
        'function'    => 'CRMLogGetFullEntry',
        'description' => 'Get a specific full (not truncated) entry of the error log.',
        'options'     => ['noAuth' => false, 'adminOnly' => true, 'validate' => true ],
        'parameters'  => [
            'id' => [
                'in' => 'path',
                'description' => 'GUID of the requested error log entry.',
                'type' => ValidationMiddleware::TYPE_STRING,
                'required' => true
            ]
        ],
    ],
    [
        'method'      => 'delete',
        'route'       => '/admin/crmlog',
        'class'       => LogViewController::class,
        'function'    => 'CRMlogTruncate',
        'description' => 'Deletes all the CRM log data.',
        'options'     => ['noAuth' => false, 'adminOnly' => true]
    ],
    [
        'method'      => 'get',
        'route'       => '/admin/apilog',
        'oldroute'    => '/restlog/entries',
        'class'       => LogViewController::class,
        'function'    => 'APIlogGetRecords',
        'description' => 'Get the entries of the REST log.',
        'options'     => ['noAuth' => false, 'adminOnly' => true, 'validate' => true],
        'parameters'  => [
            'limit' => [
                'in' => 'query',
                'description' => 'Maximal numbers of requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_NUMERIC,
                'default' => 5000
            ],
            'session_id' => [
                'in' => 'query',
                'description' => 'Transaction ID of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'user_id' => [
                'in' => 'query',
                'description' => 'User ID of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'method' => [
                'in' => 'query',
                'type'        => ValidationMiddleware::TYPE_ENUM,
                'options'     => ['CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PATCH', 'PUT', 'TRACE', 'SOAP'],
                'description' => 'HTTP method of the requested REST log entries.',
            ],
            'route' => [
                'in' => 'query',
                'description' => 'url or route fragment of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'filter' => [
                'in' => 'query',
                'description' => 'Route arguments fragment of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_STRING
            ],
            'status' => [
                'in' => 'query',
                'description' => 'HTTP status code of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_NUMERIC
            ],
            'direction' => [
                'in' => 'query',
                'description' => 'either inbound or outbound.',
                'type' => ValidationMiddleware::TYPE_ENUM,
                'options' => ['I', 'O']
            ],
            'ip' => [
                'in' => 'query',
                'description' => 'IP address of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_STRING,
                'validationOptions' => [
                    ValidationMiddleware::VOPT_REGEX => '#^\d+\.\d+\.\d+\.\d+$#',
                ]
            ],
            'end' => [
                'in' => 'query',
                'description' => 'End date and hour of the requested REST log entries.',
                'type' => ValidationMiddleware::TYPE_DATETIME
            ]
        ]
    ],
    [
        'method'      => 'delete',
        'route'       => '/admin/apilog',
        'class'       => LogViewController::class,
        'function'    => 'APIlogTruncate',
        'description' => 'truncates the API log',
        'options'     => ['noAuth' => false, 'adminOnly' => true, 'validate' => true]
    ],
    [
        'method'      => 'get',
        'route'       => '/admin/apilog/{id}',
        'oldroute'    => '/restlog/entry/{id}',
        'class'       => LogViewController::class,
        'function'    => 'APIlogGetRecord',
        'description' => 'Get a specific full (not truncated) entry of the REST log.',
        'options'     => [ 'noAuth' => false, 'adminOnly' => true, 'validate' => true ],
        'parameters'  => [
            'id' => [
                'in' => 'path',
                'description' => 'GUID of the requested REST log entry.',
                'type' => ValidationMiddleware::TYPE_STRING,
                'required' => true
            ]
        ]
    ]
];

/**
 * register the Extension
 */
RESTManager::getInstance()->registerExtension('crmlog', '2.0', [], $routes);

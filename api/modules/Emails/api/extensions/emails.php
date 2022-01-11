<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\Emails\Email;
use SpiceCRM\modules\Emails\api\controllers\EmailsController;
use SpiceCRM\includes\Middleware\ValidationMiddleware;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('emails', '1.0', ['check_all_attachments_on_archive' => SpiceConfig::getInstance()->config['groupware']['check_all_attachments_on_archive']]);

$routes = [
    [
        'method'      => 'post',
        'route'       => '/channels/groupware/gsuite/email',
        'oldroute'    => '/module/Emails/groupware/saveGSuiteEmailWithBeans',
        'class'       => EmailsController::class,
        'function'    => 'saveGSuiteEmailWithBeans',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => false], // disabled validation temporary todo #fixroute
        'parameters'  => [
            'email' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_OBJECT,
                'required'    => true,
                'description' => 'Email data',
                'parameters'  => [
                    'message_id' => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email message ID',
                    ],
                    'subject'    => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email subject',
                    ],
                    'body'       => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email body',
                    ],
                    'thread_id'  => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email thread ID',
                    ],
                    'date'       => [
                        'type'        => ValidationMiddleware::TYPE_DATETIME,
                        'required'    => true,
                        'description' => 'Email date sent',
                    ],
                    'from'       => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email from address',
                    ],
                    'to'         => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email to address',
                    ],
                    'replyto'    => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email reply to address',
                    ],
                    'cc'         => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email CC addresses',
                    ],
                    'bcc'        => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email BCC addresses',
                    ],

                ],
            ],
            'beans' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_OBJECT,
                'subtype'     => [
                    'type'       => ValidationMiddleware::TYPE_OBJECT,
                    'parameters' => [
                        'id'     => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'GUID of the bean',
                        ],
                        'module' => [
                            'type'        => ValidationMiddleware::TYPE_MODULE,
                            'required'    => true,
                            'description' => 'Module name of the bean',
                        ],
                    ]
                ],
                'required'    => true,
                'description' => 'An array with bean ID and module data',
            ],
            'bean'  => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_COMPLEX,
                'required'    => false,
                'description' => 'Email bean',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/channels/groupware/outlook/email',
        'oldroute'    => '/module/Emails/groupware/saveOutlookEmailWithBeans',
        'class'       => EmailsController::class,
        'function'    => 'saveOutlookEmailWithBeans',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'email' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_OBJECT,
                'required'    => true,
                'description' => 'Email data',
                'parameters'  => [
                    'message_id' => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email message ID',
                    ],
                    'subject'    => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email subject',
                    ],
                    'body'       => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email body',
                    ],
                    'thread_id'  => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email thread ID',
                    ],
                    'date'       => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email date sent as javascript format like Tue, 11 May 2021 08:49:11 GMT',
                    ],
                    'from'       => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email from address',
                    ],
                    'to'         => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email to address',
                    ],
                    'replyto'    => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email reply to address',
                    ],
                    'cc'         => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email CC addresses',
                    ],
                    'bcc'        => [
                        'type'        => ValidationMiddleware::TYPE_STRING,
                        'required'    => true,
                        'description' => 'Email BCC addresses',
                    ],

                ],
            ],
            'beans' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_ARRAY,
                'subtype'     => [
                    'type'       => ValidationMiddleware::TYPE_OBJECT,
                    'parameters' => [
                        'id'     => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'GUID of the bean',
                        ],
                        'module' => [
                            'type'        => ValidationMiddleware::TYPE_MODULE,
                            'required'    => true,
                            'description' => 'Module name of the bean',
                        ],
                        'selected' => [
                            'type'        => ValidationMiddleware::TYPE_BOOL,
                            'required'    => true,
                            'description' => 'selected bean',
                        ],
                        'summary_text' => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'bean summary text (usually name)',
                        ],
                        'data' => [
                            'type' => ValidationMiddleware::TYPE_COMPLEX,
                            'required'    => true,
                            'description' => 'array with bean data'
                        ]
                    ],
                ],
                'required'    => true,
                'description' => 'An array with bean ID and module data',
            ],
            'bean'  => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_COMPLEX,
                'required'    => false,
                'description' => 'Email bean',
            ],
        ],
    ],
    [
        'method'      => 'get',
        'route'       => '/channels/groupware/email',
        'oldroute'    => '/module/Emails/groupware/getemail',
        'class'       => EmailsController::class,
        'function'    => 'getEmail',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'messageId' => [
                'in'          => 'query',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'Email message ID',
            ],
            'threadId'  => [
                'in'          => 'query',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => false,
                'description' => 'Email thread ID',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/channels/groupware/outlook/attachments',
        'oldroute'    => '/module/Emails/groupware/saveOutlookAttachments',
        'class'       => EmailsController::class,
        'function'    => 'saveOutlookAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'   => [
            'emailId'            => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the email',
            ],
            'outlookAttachments' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_ARRAY,
                'subtype'     => [
                    'type'       => ValidationMiddleware::TYPE_OBJECT,
                    'parameters' => [

                        'name'           => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'File name',
                        ],
                        'contentType'    => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'File MIME type',
                        ],
                        'size'           => [
                            'type'        => ValidationMiddleware::TYPE_NUMERIC,
                            'required'    => true,
                            'description' => 'File size',
                        ],
                        'attachmentType' => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => false,
                            'description' => 'Attachment type. Does not seem to be used anywhere.',
                        ],
                        'isInline'       => [
                            'type'        => ValidationMiddleware::TYPE_BOOL,
                            'required'    => false,
                            'description' => 'Is inline flag. Does not seem to be used anywhere.',
                        ],
                        'selected'       => [
                            'type'        => ValidationMiddleware::TYPE_BOOL,
                            'required'    => false,
                            'description' => 'selected flag. Does not seem to be used anywhere.',
                        ],
                        'id'             => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'External ID of the attachment',
                        ],
                    ]
                ],
                'required'    => true,
                'description' => 'An array with Outlook attachments',
            ],
            'ewsUrl'             => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'EWS URL',
            ],
            'attachmentToken'    => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'Authorization token',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/channels/groupware/gsuite/attachments',
        'oldroute'    => '/module/Emails/groupware/saveGSuiteAttachments',
        'class'       => EmailsController::class,
        'function'    => 'saveGSuiteAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'   => [
            'email_id'            => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'GUID of the email',
            ],
            'attachments' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_ARRAY,
                'subtype'     => [
                    'type'       => ValidationMiddleware::TYPE_OBJECT,
                    'parameters' => [
                        'name'           => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'File name',
                        ],
                        'content'    => [
                            'type'        => ValidationMiddleware::TYPE_BASE64,
                            'required'    => true,
                            'description' => 'file content',
                        ],
                        'contentType'    => [
                            'type'        => ValidationMiddleware::TYPE_STRING,
                            'required'    => true,
                            'description' => 'File MIME type',
                        ],
                        'isInline'       => [
                            'type'        => ValidationMiddleware::TYPE_BOOL,
                            'required'    => false,
                            'description' => 'Is inline flag. Does not seem to be used anywhere.',
                        ],
                        'id'             => [
                            'type'        => ValidationMiddleware::TYPE_NUMERIC,
                            'required'    => true,
                            'description' => 'External ID of the attachment',
                        ],
                    ]
                ],
                'required'    => true,
                'description' => 'An array with GSuite attachments',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/{id}/status',
        'oldroute'    => '/module/Emails/{id}/setstatus/{status}',
        'class'       => EmailsController::class,
        'function'    => 'setStatus',
        'description' => 'Sets the status of the email',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'id'     => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'ID of the email',
            ],
            'status' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_ENUM,
                'options'     => [Email::STATUS_CREATED, Email::STATUS_READ, Email::STATUS_UNREAD],
                'required'    => true,
                'description' => 'Status of the email',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/{id}/openness',
        'oldroute'    => '/module/Emails/{id}/setopenness/{openness}',
        'class'       => EmailsController::class,
        'function'    => 'setOpenness',
        'description' => 'Sets the openness of the email',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'id'       => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'ID of the email',
            ],
            'openness' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_ENUM,
                'options'     => [Email::OPENNESS_OPEN, Email::OPENNESS_USER_CLOSED, Email::OPENNESS_SYSTEM_CLOSED],
                'required'    => true,
                'description' => 'The openness value',
            ],
        ],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/Emails/msg',
        'oldroute'    => '/module/Emails/msg',
        'class'       => EmailsController::class,
        'function'    => 'createEmailFromMSGFile',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'filename'     => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The file name',
            ],
            'filemimetype' => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The mime type of the file',
            ],
            'file'         => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_BASE64,
                'required'    => true,
                'description' => 'The base64 contents of the file',
            ],
            'beanModule'   => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_MODULE,
                'required'    => true,
                'description' => 'The bean module name',
            ],
            'beanId'       => [
                'in'          => 'body',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The bean ID',
            ],
        ],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/Emails/msg/{attachmentId}',
        'oldroute'    => '/module/Emails/msg/{attachmentId}/preview',
        'class'       => EmailsController::class,
        'function'    => 'previewMsgFromAttachment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'validate' => true],
        'parameters'  => [
            'attachmentId' => [
                'in'          => 'path',
                'type'        => ValidationMiddleware::TYPE_STRING,
                'required'    => true,
                'description' => 'The attachment ID',
            ],
        ],
    ],
];

$RESTManager->registerRoutes($routes);

<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\utils\api\controllers\UtilsController;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

$routes = [
    [
        'method'      => 'get',
        'route'       => '/system/pdf/toImage/base64data/{filepath}',
        'class'       => UtilsController::class,
        'function'    => 'RestPDFToBaseImage',
        'description' => 'convert a pdf to a base64 image',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/system/pdf/toImageurl/{filepath}',
        'class'       => UtilsController::class,
        'function'    => 'RestPDFToUrlImage',
        'description' => 'converts a pdf to Url image',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/system/pdf/upload/tmp',
        'class'       => UtilsController::class,
        'function'    => 'PutToTmpPdfPath',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/system/pdf/upload/uploadsDir',
        'class'       => UtilsController::class,
        'function'    => 'PutToUpPath',
        'description' => 'puts the content to an upload path',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

/**
 * register the Extension
 */
$RESTManager->registerExtension('utils', '1.0', [], $routes);

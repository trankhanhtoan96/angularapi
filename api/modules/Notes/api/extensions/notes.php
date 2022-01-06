<?php

use SpiceCRM\includes\RESTManager;
use SpiceCRM\modules\Notes\api\controllers\NotesController;

$RESTManager = RESTManager::getInstance();
$RESTManager->registerExtension('notesv1', '1.0');

$routes = [
    [
        'method' => 'post',
        'route' => '/v1/notes/uploadimage',
        'class' => NotesController::class,
        'function' => 'uploadImage',
        'options' => ['noAuth' => false, 'adminOnly' => false],
        'parameters' => []
    ],

];

$RESTManager->registerRoutes($routes);


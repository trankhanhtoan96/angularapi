<?php

namespace SpiceCRM\modules\Notes\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\utils\SpiceUtils;

class NotesController
{
    public function uploadImage(Request $req, Response $res, array $args): Response
    {
        $id = SpiceUtils::createGuid();
        if (move_uploaded_file($_FILES["file"]["tmp_name"], 'upload/' . $id)) {
            return $res->withJson(['success' => 1, 'path' => SpiceConfig::getInstance()->config['site_url'] . '/upload/' . $id]);
        } else {
            return $res->withJson(['success' => 0]);
        }
    }
}

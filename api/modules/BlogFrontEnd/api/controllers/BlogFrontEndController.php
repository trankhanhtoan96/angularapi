<?php

namespace SpiceCRM\modules\BlogFrontEnd\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\KREST\handlers\ModuleHandler;

class BlogFrontEndController
{
    public function home(Request $req, Response $res, array $args): Response
    {
        $data = [
            'recent' => [],
            'topViews' => []
        ];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);

        $result = $db->query("select id from blog where status='publish' order by date_entered desc limit 15");
        while ($row = $db->fetchByAssoc($result)) {
            $data['recent'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }

        $result = $db->query("select id from blog where status='publish' order by view_count desc limit 10");
        while ($row = $db->fetchByAssoc($result)) {
            $data['topViews'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }

    public function homeLoadMore(Request $req, Response $res, array $args): Response
    {
        $data=[];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);

        $result = $db->query("select id from blog where status='publish' order by date_entered desc limit 20 offset {$_GET['offset']}");
        while ($row = $db->fetchByAssoc($result)) {
            $data[] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }
}
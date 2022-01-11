<?php

namespace SpiceCRM\modules\BlogFrontEnd\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\modules\BlogCategory\BlogCategory;

class BlogFrontEndController
{
    public function getCategorySlug(Request $req, Response $res, array $args): Response
    {
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $bean = new BlogCategory();
        $bean->retrieve($args['id']);
        return $res->withJson(['slug'=>$bean->slug]);
    }
    public function getCategory(Request $req, Response $res, array $args): Response
    {
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $bean = new BlogCategory();
        $bean->retrieve_by_string_fields(['slug' => $args['slug']]);
        return $res->withJson($moduleHandler->mapBeanToArray('BlogCategory', $bean));
    }

    public function getCategoryData(Request $req, Response $res, array $args): Response
    {
        $data = [
            'recent' => [],
            'topViews' => []
        ];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $cid=$db->getOne("select id from blogcategory where slug='{$args['slug']}'");

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by date_entered desc limit 15");
        while ($row = $db->fetchByAssoc($result)) {
            $data['recent'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by view_count desc limit 10");
        while ($row = $db->fetchByAssoc($result)) {
            $data['topViews'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }

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
        $data = [];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);

        $result = $db->query("select id from blog where status='publish' order by date_entered desc limit 20 offset {$_GET['offset']}");
        while ($row = $db->fetchByAssoc($result)) {
            $data[] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }
    public function getCategoryLoadMore(Request $req, Response $res, array $args): Response
    {
        $data = [];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $cid=$db->getOne("select id from blogcategory where slug='{$args['slug']}'");

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by date_entered desc limit 20 offset {$_GET['offset']}");
        while ($row = $db->fetchByAssoc($result)) {
            $data[] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }
}

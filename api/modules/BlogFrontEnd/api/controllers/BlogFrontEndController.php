<?php

namespace SpiceCRM\modules\BlogFrontEnd\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\includes\utils\SpiceUtils;
use SpiceCRM\KREST\handlers\ModuleHandler;
use SpiceCRM\modules\Blog\Blog;
use SpiceCRM\modules\BlogCategory\BlogCategory;
use SpiceCRM\modules\BlogComment\BlogComment;
use SpiceCRM\modules\Users\User;

class BlogFrontEndController
{
    public function getProfile(Request $req, Response $res, array $args): Response
    {
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        return $res->withJson($moduleHandler->mapBeanToArray('Users', BeanFactory::getBean('Users', $args['id'])));
    }

    public function reactBlogComment(Request $req, Response $res, array $args): Response
    {
        global $timedate;
        $body = $req->getParsedBody();
        $db = DBManagerFactory::getInstance();
        $blogComment = new BlogComment();
        $blogComment->retrieve($args['comment_id']);
        $blogComment->reaction = (int)$blogComment->reaction + 1;
        $blogComment->save();

        $db->query("delete from blogcomment_user where blogcomment_id='{$args['comment_id']}' and user_id='{$body['userid']}'");
        $id = SpiceUtils::createGuid();
        $now = $timedate->nowDb();
        $db->query("insert into blogcomment_user(id, user_id, blogcomment_id, date_modified) values('$id','{$body['userid']}','{$args['comment_id']}','$now')");

        return $res->withJson(['success' => 1]);
    }

    public function saveBlogComment(Request $req, Response $res, array $args): Response
    {
        $body = $req->getParsedBody();
        $blogComment = new BlogComment();
        $blogComment->description = $body['content'];
        $blogComment->blog_id = $args['blog_id'];
        $blogComment->save();
        return $res->withJson(['success' => 1]);
    }

    public function getBlogComment(Request $req, Response $res, array $args): Response
    {
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $blog_id = $db->getOne("select id  from blog where blog.slug='{$args['slug']}'");
        $result = $db->query("select id from blogcomment where blog_id='$blog_id' and deleted=0 order by date_entered DESC");
        $comments = [];
        $commentids = [];
        while ($row = $db->fetchByAssoc($result)) {
            $comments[] = $moduleHandler->mapBeanToArray('BlogComment', BeanFactory::getBean('BlogComment', $row['id']));
            $commentids[] = $row['id'];
        }

        $result = $db->query("select blogcomment_id from blogcomment_user where user_id='{$_GET['userid']}' and blogcomment_id in ('" . implode("','", $commentids) . "') and deleted=0");
        $authorReact = [];
        while ($row = $db->fetchByAssoc($result)) {
            $authorReact[] = $row['blogcomment_id'];
        }
        return $res->withJson([
            'comments' => $comments,
            'authorReact' => $authorReact
        ]);
    }

    public function getBlog(Request $req, Response $res, array $args): Response
    {
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $bean = new Blog();
        $bean->retrieve_by_string_fields(['slug' => $args['slug']]);

        $author = new User();
        $author->retrieve($bean->created_by);
        return $res->withJson([
            'bean' => $moduleHandler->mapBeanToArray('Blog', $bean),
            'author' => $moduleHandler->mapBeanToArray('Blog', $author)
        ]);
    }

    public function getBlogData(Request $req, Response $res, array $args): Response
    {
        $data = [
            'recent' => [],
            'topViews' => []
        ];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);
        $cid = $db->getOne("select blogcategory.id  from blog inner join blogcategory on blog.category_id=blogcategory.id where blog.slug='{$args['slug']}'");

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by date_entered desc limit 5");
        while ($row = $db->fetchByAssoc($result)) {
            $data['recent'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by view_count desc limit 10");
        while ($row = $db->fetchByAssoc($result)) {
            $data['topViews'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }

    public function getBlogSlug(Request $req, Response $res, array $args): Response
    {
        $bean = new Blog();
        $bean->retrieve($args['id']);
        return $res->withJson(['slug' => $bean->slug]);
    }

    public function getCategorySlug(Request $req, Response $res, array $args): Response
    {
        $bean = new BlogCategory();
        $bean->retrieve($args['id']);
        return $res->withJson(['slug' => $bean->slug]);
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
        $cid = $db->getOne("select id from blogcategory where slug='{$args['slug']}'");

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

    public function getProfileData(Request $req, Response $res, array $args): Response
    {
        $data = [
            'recent' => [],
            'topViews' => []
        ];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);

        $result = $db->query("select id from blog where status='publish' and created_by='{$args['id']}' order by date_entered desc limit 10");
        while ($row = $db->fetchByAssoc($result)) {
            $data['recent'][] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }

        $result = $db->query("select id from blog where status='publish' and created_by='{$args['id']}' order by view_count desc limit 10");
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
        $cid = $db->getOne("select id from blogcategory where slug='{$args['slug']}'");

        $result = $db->query("select id from blog where status='publish' and category_id='$cid' order by date_entered desc limit 20 offset {$_GET['offset']}");
        while ($row = $db->fetchByAssoc($result)) {
            $data[] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }
    public function getProfileLoadMore(Request $req, Response $res, array $args): Response
    {
        $data = [];
        $db = DBManagerFactory::getInstance();
        $moduleHandler = new ModuleHandler(RESTManager::getInstance()->app);

        $result = $db->query("select id from blog where status='publish' and created_by='{$args['id']}' order by date_entered desc limit 20 offset {$_GET['offset']}");
        while ($row = $db->fetchByAssoc($result)) {
            $data[] = $moduleHandler->mapBeanToArray('Blog', BeanFactory::getBean('Blog', $row['id']));
        }
        return $res->withJson($data);
    }
}

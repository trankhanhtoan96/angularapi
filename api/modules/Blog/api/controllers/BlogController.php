<?php

namespace SpiceCRM\modules\Blog\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\modules\Blog\Blog;

class BlogController
{
    public function importData(Request $req, Response $res, array $args): Response
    {
        $data = json_decode(file_get_contents('modules/Blog/data.json'), true);
        $result = [];
        foreach ($data as $item) {
            $bean = new Blog();
            $bean->name = $item['Title'];
            $bean->date_entered = $item['Date'];
            $bean->description = $item['Excerpt'];
            $bean->tags = $item['Tags'];
            $bean->content = $item['Content'];
            $bean->view_count = $item['post_views_count'];
            $bean->image = explode('|',$item['Image URL'])[0];
            $bean->slug = $item['Slug'];
            $bean->status = $item['Status'];
            $c=explode(',',$item['Categories'])[0];
            $bean->category_id = DBManagerFactory::getInstance()->getOne("select id from blogcategory where name='$c'");
            $bean->save();
            DBManagerFactory::getInstance()->query("update blog set created_by='{$item['Author ID']}' where id='$bean->id'");
            $result[] = $bean->id;
        }
        return $res->withJson($result);
    }
}

<?php

namespace SpiceCRM\modules\Blog;

use SpiceCRM\includes\database\DBManagerFactory;

class BlogHooks
{
    function slugify($text)
    {
        // Strip html tags
        $text = strip_tags($text);
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        setlocale(LC_ALL, 'en_US.utf8');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    public function before_save(&$bean, $event, $arguments)
    {
        if (empty($bean->slug)) {
            $bean->slug = $this->slugify($bean->name) . '-' . time();
        }
        $bean->set_created_by = false;
    }

    public function after_retrieve(&$bean, $event, $arguments)
    {
        if (strtotime($bean->schedule_post) > time()) {
            DBManagerFactory::getInstance()->query("update blog set status='publish' where id='$bean->id'");
            $bean->status = 'publish';
        }
    }
}

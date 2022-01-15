<?php

namespace SpiceCRM\modules\Blog;

class BlogHooks
{
    public function before_save(&$bean, $event, $arguments)
    {
        $bean->slug = strtolower(trim(preg_replace('/[\s-]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $bean->name))))), '-'));
        $bean->slug = $bean->slug . '-' . time();
    }
}

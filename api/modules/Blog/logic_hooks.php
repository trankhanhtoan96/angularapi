<?php
return [
    [
        'event' => 'before_save',
        'hook_index' => 1,
        'hook_class' => '\SpiceCRM\modules\Blog\BlogHooks',
        'hook_method' => 'before_save',
        'hook_active' => 1
    ],
    [
        'event' => 'after_retrieve',
        'hook_index' => 1,
        'hook_class' => '\SpiceCRM\modules\Blog\BlogHooks',
        'hook_method' => 'after_retrieve',
        'hook_active' => 1
    ]
];

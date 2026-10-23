<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_get_tag_archive_settings()
{
    return [

        'featured' => true,

        'toolbar' => false,

        'breadcrumb' => true,

        'sidebar' => true,

        'posts' => 9, 

        'columns' => 3
    ];
}
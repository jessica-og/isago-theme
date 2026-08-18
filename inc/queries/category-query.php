<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Category Query
=========================================================*/

function isago_get_category_posts($category_slug = '', $args = [])
{

    if (!is_array($args)) {

        $args = [

            'posts_per_page' => absint($args)

        ];

    }

    if (!empty($category_slug)) {

        $args['category_name'] = sanitize_title($category_slug);

    }

    return isago_query($args);

}



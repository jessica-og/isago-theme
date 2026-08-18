<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
Posts From Category
|--------------------------------------------------------------------------
*/

function isago_posts_from_category($category)
{

    return new WP_Query([

        'post_type' => 'post',

        'post_status' => 'publish',

        'posts_per_page' => 9,

        'cat' => $category->term_id

    ]);

}
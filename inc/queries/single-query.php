<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Related Posts
=========================================================*/

function isago_get_related_posts($posts = 4)
{

    $category = get_the_category();

    if (empty($category)) {
        return new WP_Query();
    }

    return new WP_Query([

        'post_type' => 'post',

        'posts_per_page' => $posts,

        'post__not_in' => [get_the_ID()],

        'cat' => $category[0]->term_id,

        'ignore_sticky_posts' => true

    ]);

}

/*=========================================================
Watch More Videos
=========================================================*/

function isago_get_single_videos($posts = 5)
{

    return new WP_Query([

        'post_type' => 'isago_video',

        'posts_per_page' => $posts,

        'post_status' => 'publish'

    ]);

}
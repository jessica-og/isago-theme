<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Base Query Engine
=========================================================*/

function isago_query(array $args = [])
{

    $defaults = [

        'post_type'              => 'post',

        'post_status'            => 'publish',

        'ignore_sticky_posts'    => true,

        'orderby'                => 'date',

        'order'                  => 'DESC',

        'posts_per_page'         => get_option('posts_per_page'),

        'paged'                  => max(
            1,
            get_query_var('paged'),
            get_query_var('page')
        ),

        /*
        Homepage queries stay fast.
        Category page can override this.
        */

        'no_found_rows'          => true,

        'update_post_meta_cache' => false,

        'update_post_term_cache' => true

    ];

    return new WP_Query(

        wp_parse_args(

            $args,

            $defaults

        )

    );

}
<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
Hero Queries
=========================================================*/




function isago_get_featured_hero($id)
{

    return isago_query([

        'p' => absint($id),

        'posts_per_page' => 1,

    ]);

}




function isago_get_left_hero($slug)
{

    $slug = sanitize_title($slug);

    if (empty($slug)) {

        return new WP_Query();

    }


    /*
     * Find the category by slug.
     */

    $category = get_category_by_slug($slug);


    /*
     * Category does not exist.
     */

    if (!$category) {

        return new WP_Query();

    }




    if ((int) $category->parent !== 0) {

        return new WP_Query();

    }


   

    return isago_query([

        'tax_query' => [

            [

                'taxonomy'         => 'category',

                'field'            => 'term_id',

                'terms'            => $category->term_id,

                'include_children' => false,

            ]

        ],

        'posts_per_page' => 2,

    ]);

}
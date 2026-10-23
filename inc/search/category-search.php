<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
FIND MATCHING TERMS
=========================================================*/

/*
 * Find terms whose name matches the search keyword.
 *
 * Supported taxonomies:
 *
 * category  
 * post_tag
 * ai_platform
 */

function isago_find_categories($keyword)
{

    $keyword = sanitize_text_field($keyword);

    if (!$keyword) {
        return [];
    }


    $taxonomies = [
        'category',
        'post_tag',
        'ai_platform'
    ];


    $matches = [];


    foreach ($taxonomies as $taxonomy) {

        /*
         * Make sure the taxonomy exists.
         */

        if (!taxonomy_exists($taxonomy)) {
            continue;
        }


        $terms = get_terms(
            [
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'search'     => $keyword
            ]
        );


        if (is_wp_error($terms)) {
            continue;
        }


        foreach ($terms as $term) {

            /*
             * WordPress "search" is not always sufficient
             * for partial matching, so verify the term name.
             */

            if (
                stripos(
                    $term->name,
                    $keyword
                ) === false
            ) {
                continue;
            }


            $matches[] = $term;


            /*
             * Include descendants where supported.
             *
             * This is especially useful for categories.
             */

            $children = get_term_children(
                $term->term_id,
                $taxonomy
            );


            if (
                !is_wp_error($children)
                &&
                !empty($children)
            ) {

                foreach ($children as $child_id) {

                    $child = get_term(
                        $child_id,
                        $taxonomy
                    );


                    if (
                        $child
                        &&
                        !is_wp_error($child)
                    ) {

                        $matches[] = $child;

                    }

                }

            }

        }

    }


    return $matches;

}


<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO SEARCH ENGINE
=========================================================*/

/*
 * Search sources:
 *  
 * 1. Post title
 * 2. Categories
 * 3. Category descendants
 * 4. Post tags
 * 5. AI Platforms
 *
 * Post content is intentionally NOT searched.
 */


/*=========================================================
SEARCH TAXONOMIES
=========================================================*/

function isago_search_taxonomies()
{

    return [

        'category',

        'post_tag',

        'ai_platform',
        'ai_prompt_category',

    ];

}


/*=========================================================
FIND SEARCH TERM IDS
=========================================================*/

function isago_get_search_term_matches(
    $keyword
)
{

    $keyword = sanitize_text_field(
        $keyword
    );


    if (!$keyword) {
        return [];
    }


    $matches = [];


    foreach (
        isago_search_taxonomies()
        as $taxonomy
    ) {


        /*
         * Only search registered taxonomies.
         */

        if (!taxonomy_exists($taxonomy)) {
            continue;
        }


        $terms = get_terms(

            [

                'taxonomy' =>
                    $taxonomy,

                'hide_empty' =>
                    false,

                'search' =>
                    $keyword,

                'number' =>
                    0

            ]

        );


        if (is_wp_error($terms)) {
            continue;
        }


        foreach ($terms as $term) {


            /*
             * Confirm actual partial name match.
             */

            if (
                stripos(
                    $term->name,
                    $keyword
                ) === false
            ) {

                continue;

            }


            /*
             * Add matching term.
             */

            $matches[] = [

                'taxonomy' =>
                    $taxonomy,

                'term_id' =>
                    (int) $term->term_id

            ];


            /*
             * Add all descendants.
             */

            $children =
                get_term_children(

                    $term->term_id,

                    $taxonomy

                );


            if (
                !is_wp_error($children)
                &&
                !empty($children)
            ) {


                foreach (
                    $children
                    as $child_id
                ) {

                    $matches[] = [

                        'taxonomy' =>
                            $taxonomy,

                        'term_id' =>
                            (int) $child_id

                    ];

                }

            }

        }

    }


    return $matches;

}


/*=========================================================
GET POSTS FROM SEARCH TAXONOMY TERMS
=========================================================*/

function isago_get_search_taxonomy_post_ids(
    $keyword
)
{

    $matches =
        isago_get_search_term_matches(
            $keyword
        );


    if (empty($matches)) {
        return [];
    }


    $post_ids = [];


    /*
     * Group terms by taxonomy.
     */

    $taxonomy_terms = [];


    foreach ($matches as $match) {

        $taxonomy =
            $match['taxonomy'];

        $term_id =
            absint(
                $match['term_id']
            );


        if (!$term_id) {
            continue;
        }


        if (
            !isset(
                $taxonomy_terms[$taxonomy]
            )
        ) {

            $taxonomy_terms[$taxonomy] = [];

        }


        $taxonomy_terms[$taxonomy][] =
            $term_id;

    }


    /*
     * Query each taxonomy separately.
     *
     * This avoids mixing term IDs from different
     * taxonomies.
     */

    foreach (
        $taxonomy_terms
        as $taxonomy => $term_ids
    ) {


        $term_ids =
            array_unique(
                array_map(
                    'absint',
                    $term_ids
                )
            );


        if (empty($term_ids)) {
            continue;
        }


        $query = new WP_Query(

            [

                'post_type' => [

                    'post',

                    'isago_video',
                    'ai_prompt',
                   

                ],

                'post_status' =>
                    'publish',

                'fields' =>
                    'ids',

                'posts_per_page' =>
                    -1,

                'no_found_rows' =>
                    true,

                'ignore_sticky_posts' =>
                    true,

                'update_post_meta_cache' =>
                    false,

                'update_post_term_cache' =>
                    false,

                'tax_query' => [

                    [

                        'taxonomy' =>
                            $taxonomy,

                        'field' =>
                            'term_id',

                        'terms' =>
                            $term_ids,

                        'include_children' =>
                            true

                    ]

                ]

            ]

        );


        if (!empty($query->posts)) {

            $post_ids =
                array_merge(
                    $post_ids,
                    $query->posts
                );

        }

    }


    return array_values(

        array_unique(

            array_map(
                'absint',
                $post_ids
            )

        )

    );

}


/*=========================================================
GET POSTS MATCHING TITLE
=========================================================*/

function isago_get_search_title_post_ids(
    $keyword
)
{

    $keyword =
        sanitize_text_field(
            $keyword
        );


    if (!$keyword) {
        return [];
    }


    /*
     * We deliberately search only the title.
     *
     * WordPress' normal "s" search can also search
     * post content, so we do not use it here.
     */

    add_filter(
        'posts_search',
        'isago_search_title_only',
        10,
        2
    );


    $query = new WP_Query(

        [

            'post_type' => [

                'post',

                'isago_video',
                  'ai_prompt',
                   

            ],

            'post_status' =>
                'publish',

            'fields' =>
                'ids',

            'posts_per_page' =>
                -1,

            's' =>
                $keyword,

            'no_found_rows' =>
                true,

            'ignore_sticky_posts' =>
                true,

            'update_post_meta_cache' =>
                false,

            'update_post_term_cache' =>
                false

        ]

    );


    remove_filter(
        'posts_search',
        'isago_search_title_only',
        10
    );


    return !empty($query->posts)
        ? array_map(
            'absint',
            $query->posts
        )
        : [];

}


/*=========================================================
TITLE-ONLY SEARCH FILTER
=========================================================*/

function isago_search_title_only(
    $search,
    $query
)
{

    global $wpdb;


    if (
        !$query->is_search()
        &&
        !$query->get('s')
    ) {

        return $search;

    }


    $keyword =
        $query->get('s');

$query->set('isago_search_keyword', $keyword);
    if (!$keyword) {
        return $search;
    }


    /*
     * Escape the search phrase safely.
     */

    $like =
        '%' .
        $wpdb->esc_like(
            $keyword
        ) .
        '%';


    /*
     * Search only post_title.
     *
     * No post_content.
     */

    return $wpdb->prepare(

        " AND {$wpdb->posts}.post_title LIKE %s ",

        $like

    );

}




/*=========================================================
GET MATCHING AI PLATFORM TERMS
=========================================================*/

function isago_get_search_platform_terms($keyword)
{
    $keyword = sanitize_text_field($keyword);

    if (
        $keyword === ''
        || !taxonomy_exists('ai_platform')
    ) {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => 'ai_platform',
        'hide_empty' => false,
        'search'     => $keyword,
        'number'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return [];
    }

    $matched_terms = [];

    foreach ($terms as $term) {
        if (
            stripos($term->name, $keyword) !== false
        ) {
            $matched_terms[] = $term;
        }
    }

    return $matched_terms;
}



/*=========================================================
MAIN SEARCH QUERY
=========================================================*/

add_action(
    'pre_get_posts',
    function ($query) {


        /*
         * Front-end main search only.
         */

        if (
            is_admin()
            ||
            !$query->is_main_query()
            ||
            !$query->is_search()
        ) {

            return;

        }


        $keyword =
            sanitize_text_field(
                $query->get('s')
            );

$query->set('isago_search_keyword', $keyword);
        if (!$keyword) {
            return;
        }


        /*
         * ----------------------------------------------
         * TITLE MATCHES
         * ----------------------------------------------
         */

        $title_ids =
            isago_get_search_title_post_ids(
                $keyword
            );


        /*
         * ----------------------------------------------
         * TAXONOMY MATCHES
         *
         * category
         * post_tag
         * ai_platform
         * ----------------------------------------------
         */

        $taxonomy_ids =
            isago_get_search_taxonomy_post_ids(
                $keyword
            );


        /*
         * ----------------------------------------------
         * MERGE
         * ----------------------------------------------
         */

        $all_ids =
            array_values(

                array_unique(

                    array_merge(

                        $title_ids,

                        $taxonomy_ids

                    )

                )

            );


        /*
         * No matches.
         */

        if (empty($all_ids)) {

            /*
             * Force an impossible post ID.
             */

            $query->set(
                'post__in',
                [0]
            );

            $query->set(
                's',
                ''
            );

            $query->set(
                'post_type',
                [
                    'post',
                    'isago_video',
                            'ai_prompt',
                                   
                ]
            );

            $query->set(
                'posts_per_page',
                9
            );

            return;

        }


        /*
         * ----------------------------------------------
         * FINAL RESULT SET
         * ----------------------------------------------
         */

        $query->set(
            'post_type',
            [
                'post',
                'isago_video',
                  'ai_prompt',
            ]
        );


        $query->set(
            'post_status',
            'publish'
        );


        /*
         * Use post__in so only our explicitly
         * calculated matches are returned.
         */

        $query->set(
            'post__in',
            $all_ids
        );


        /*
         * Remove WordPress' normal "s" search.
         *
         * Otherwise it could search post content.
         */

        $query->set(
            's',
            ''
        );


        /*
         * Preserve the matching IDs as our
         * deterministic search result set.
         */

        $query->set(
            'orderby',
            'post__in'
        );


        $query->set(
            'posts_per_page',
            9
        );


        /*
         * Make sure sticky posts don't interfere.
         */

        $query->set(
            'ignore_sticky_posts',
            true
        );

    }
);


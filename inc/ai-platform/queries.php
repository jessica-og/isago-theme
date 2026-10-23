<?php

if (!defined('ABSPATH')) {
    exit;
}
//'ai_prompt'

/*=========================================================
AI Tool POSTS
=========================================================*/

function isago_get_ai_platform_posts(
    $platform_id,
    $args = []
) {

    $platform_id =
        absint($platform_id);


    if (!$platform_id) {

        return new WP_Query([
            'post_type' =>
                'post',

            'post__in' =>
                [0]

        ]);

    }


    $defaults = [

        'post_type' => [

            'post',
            'isago_video',
            'ai_prompt'

        ],

        'post_status' =>
            'publish',

        'posts_per_page' =>
            12,

        'paged' =>
            max(
                1,
                get_query_var('paged'),
                get_query_var('page')
            ),

        'tax_query' => [

            [

                'taxonomy' =>
                    'ai_platform',

                'field' =>
                    'term_id',

                'terms' =>
                    $platform_id,

            ]

        ],

        'ignore_sticky_posts' =>
            true,

        'orderby' =>
            'date',

        'order' =>
            'DESC'

    ];


    $args =
        wp_parse_args(
            $args,
            $defaults
        );


    return new WP_Query(
        $args
    );

}


/*=========================================================
AI Tool RELATED POSTS
=========================================================*/

function isago_get_ai_platform_related_posts(
    $platform_ids = [],
    $exclude_id = 0,
    $posts = 5
) {

    $platform_ids =
        array_map(
            'absint',
            (array) $platform_ids
        );


    $platform_ids =
        array_filter(
            $platform_ids
        );


    if (empty($platform_ids)) {

        return new WP_Query([
            'post_type' =>
                'post',

            'post__in' =>
                [0]

        ]);

    }


    return new WP_Query([

        'post_type' => [

            'post',
            'isago_video',
            'ai_prompt'

        ],

        'post_status' =>
            'publish',

        'posts_per_page' =>
            absint($posts),

        'post__not_in' =>
            $exclude_id
                ? [absint($exclude_id)]
                : [],

        'tax_query' => [

            [

                'taxonomy' =>
                    'ai_platform',

                'field' =>
                    'term_id',

                'terms' =>
                    $platform_ids

            ]

        ],

        'ignore_sticky_posts' =>
            true,

        'orderby' =>
            'date',

        'order' =>
            'DESC'

    ]);

}


/*=========================================================
PLATFORM POSTS BY CATEGORY
=========================================================*/

function isago_get_ai_platform_category_posts(
    $platform_id,
    $category_id,
    $posts = 6
) {

    $platform_id =
        absint($platform_id);

    $category_id =
        absint($category_id);


    if (
        !$platform_id
        ||
        !$category_id
    ) {

        return new WP_Query([
            'post_type' =>
                'post',

            'post__in' =>
                [0]

        ]);

    }


    return new WP_Query([

        'post_type' => [

            'post',
            'isago_video',
'ai_prompt'
        ],

        'post_status' =>
            'publish',

        'posts_per_page' =>
            absint($posts),

        'tax_query' => [

            'relation' =>
                'AND',

            [

                'taxonomy' =>
                    'ai_platform',

                'field' =>
                    'term_id',

                'terms' =>
                    $platform_id

            ],

            [

                'taxonomy' =>
                    'category',

                'field' =>
                    'term_id',

                'terms' =>
                    $category_id,

                'include_children' =>
                    true

            ]

        ],

        'ignore_sticky_posts' =>
            true,

        'orderby' =>
            'date',

        'order' =>
            'DESC'

    ]);

}


/*=========================================================
GET CATEGORY BY SLUG
=========================================================*/

function isago_get_category_by_slug(
    $slug
) {

    $term =
        get_term_by(
            'slug',
            sanitize_title($slug),
            'category'
        );


    return (
        $term  
        &&
        !is_wp_error($term)
    )
        ? $term
        : null;

}


/*=========================================================
PLATFORM PROMPTS
=========================================================*/

function isago_get_ai_platform_prompts(
    $platform_id,
    $posts = 6
) {

    $category =
        isago_get_category_by_slug(
            'ai-prompts'
        );


    if (!$category) {

        return new WP_Query([
            'post_type' =>
                'post',

            'post__in' =>
                [0]

        ]);

    }


    return isago_get_ai_platform_category_posts(

        $platform_id,

        $category->term_id,

        $posts

    );

}


/*=========================================================
PLATFORM TOOLS
=========================================================*/

function isago_get_ai_platform_tools(
    $platform_id,  
    $posts = 6  
) {
  
    $category =
        isago_get_category_by_slug(
            'ai-tools'
        );


    if (!$category) {

        return new WP_Query([
            'post_type' =>
                'post',

            'post__in' =>
                [0]

        ]);

    }


    return isago_get_ai_platform_category_posts(

        $platform_id,

        $category->term_id,

        $posts

    );

}





/*=========================================================
AI Tool DIRECTORY
=========================================================*/

/**
 * Get AI Tool directory data.
 *
 * Handles:
 *
 * - Platform type filtering
 * - Platform searching
 * - Pagination
 * - Current page calculation
 * - Total matching platforms
 *
 * This function is used by BOTH:
 *
 * - Normal AI Tools page
 * - AJAX AI Tools directory
 */
function isago_get_ai_platform_directory(
    $selected_category = '',
    $search = '',
    $current_page = 1
) {

    /*=====================================================
    SANITIZE VALUES
    =====================================================*/

    $selected_category =
        sanitize_key(
            $selected_category
        );


    $search =
        sanitize_text_field(
            $search
        );


    $current_page =
        max(
            1,
            absint(
                $current_page
            )
        );


    /*=====================================================
    PLATFORMS PER PAGE
    =====================================================*/

    $platforms_per_page = 12;


    /*=====================================================
    PLATFORM TYPES
    =====================================================*/

    $platform_categories =
        isago_get_ai_platform_categories();


   /*=====================================================
BASE TERM QUERY — LATEST AI TOOLS FIRST
=====================================================*/

$platform_args = [

    'taxonomy' =>
        'ai_platform',

    'hide_empty' =>
        false,

    /*
     * Newer taxonomy term IDs first.
     * Newly created AI Tools normally receive
     * higher IDs than previously created tools.
     */

    'orderby' =>
        'term_id',

    'order' =>
        'DESC'

];

    /*=====================================================
    PLATFORM TYPE FILTER
    =====================================================*/

    if (
        !empty($selected_category)
        &&
        isset(
            $platform_categories[
                $selected_category
            ]
        )
    ) {

        $platform_args['meta_query'] = [

            [

                'key' =>
                    '_isago_ai_platform_category',

                'value' =>
                    $selected_category,

                'compare' =>
                    '='

            ]

        ];

    }


    /*=====================================================
    SEARCH
    =====================================================*/

    if (
        $search !== ''
    ) {

        $platform_args['search'] =
            $search;

    }


    /*=====================================================
    TOTAL MATCHING PLATFORMS
    =====================================================*/

    $count_args =
        $platform_args;


    $count_args['fields'] =
        'count';


    $total_platforms =
        get_terms(
            $count_args
        );


    if (
        is_wp_error(
            $total_platforms
        )
    ) {

        $total_platforms = 0;

    }


    $total_platforms =
        (int) $total_platforms;


    /*=====================================================
    TOTAL PAGES
    =====================================================*/

    $total_pages =
        $total_platforms > 0
            ? (int) ceil(
                $total_platforms
                /
                $platforms_per_page
            )
            : 0;


    /*=====================================================
    NORMALIZE CURRENT PAGE
    =====================================================*/

    if (
        $total_pages > 0
        &&
        $current_page > $total_pages
    ) {

        $current_page =
            $total_pages;

    }


    /*
     * If there are no results,
     * always use page 1.
     */

    if (
        $total_pages === 0
    ) {

        $current_page = 1;

    }


    /*=====================================================
    CALCULATE OFFSET
    =====================================================*/

    $platform_offset =
        (
            $current_page - 1
        )
        *
        $platforms_per_page;


    /*=====================================================
    LIMIT RESULTS
    =====================================================*/

    $platform_args['number'] =
        $platforms_per_page;


    $platform_args['offset'] =
        $platform_offset;


    /*=====================================================
    GET CURRENT PAGE
    =====================================================*/

    $platforms =
        get_terms(
            $platform_args
        );


    if (
        is_wp_error(
            $platforms
        )
    ) {

        $platforms = [];

    }


    /*=====================================================
    PAGINATION OBJECT
    =====================================================*/

    $platform_pagination =
        new stdClass();


    $platform_pagination->max_num_pages =
        $total_pages;


    /*=====================================================
    RETURN DIRECTORY DATA
    =====================================================*/

    return [

        'selected_category' =>
            $selected_category,

        'search' =>
            $search,

        'platform_categories' =>
            $platform_categories,

        'platforms' =>
            $platforms,

        'total_platforms' =>
            $total_platforms,

        'platform_pagination' =>
            $platform_pagination,

        'current_page' =>
            $current_page,

        'platforms_per_page' =>
            $platforms_per_page

    ];

}






/*=========================================================
AI Tool DIRECTORY AJAX
=========================================================*/

add_action(
    'wp_ajax_isago_ai_platforms',
    'isago_ajax_ai_platforms'
);


add_action(
    'wp_ajax_nopriv_isago_ai_platforms',
    'isago_ajax_ai_platforms'
);


/**
 * AJAX handler for the AI Tool directory.
 */
function isago_ajax_ai_platforms()
{

    /*=====================================================
    SECURITY
    =====================================================*/

    if (
        !isset(
            $_POST['nonce']
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'Security verification failed.'
            ],
            403
        );

    }


    $nonce =
        sanitize_text_field(
            wp_unslash(
                $_POST['nonce']
            )
        );


    if (
        !wp_verify_nonce(
            $nonce,
            'isago_ai_platforms'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'Security verification failed.'
            ],
            403
        );

    }


    /*=====================================================
    CATEGORY
    =====================================================*/

    $selected_category =
        isset(
            $_POST['platform_category']
        )
            ? sanitize_key(
                wp_unslash(
                    $_POST['platform_category']
                )
            )
            : '';


    /*=====================================================
    SEARCH
    =====================================================*/

    $search =
        isset(
            $_POST['platform_search']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['platform_search']
                )
            )
            : '';


    /*=====================================================
    CURRENT PAGE
    =====================================================*/

    $current_page =
        isset(
            $_POST['paged']
        )
            ? absint(
                $_POST['paged']
            )
            : 1;


    $current_page =
        max(
            1,
            $current_page
        );


    /*=====================================================
    GET DIRECTORY DATA
    =====================================================*/

    $directory =
        isago_get_ai_platform_directory(
            $selected_category,
            $search,
            $current_page
        );


    /*=====================================================
    TEMPLATE ARGUMENTS
    =====================================================*/

    $template_args = [

        'selected_category' =>
            $directory[
                'selected_category'
            ],

        'search' =>
            $directory[
                'search'
            ],

        'platform_categories' =>
            $directory[
                'platform_categories'
            ],

        'platforms' =>
            $directory[
                'platforms'
            ],

        'total_platforms' =>
            $directory[
                'total_platforms'
            ],

        'platform_pagination' =>
            $directory[
                'platform_pagination'
            ],

        'current_page' =>
            $directory[
                'current_page'
            ],

        'platforms_per_page' =>
            $directory[
                'platforms_per_page'
            ]

    ];


    /*=====================================================
    RENDER THE EXACT SAME RESULTS TEMPLATE
    =====================================================*/

    ob_start();


    get_template_part(
        'template-parts/ai-tools/browse-ai-tools/results',
        null,
        $template_args
    );


    $html =
        ob_get_clean();


    /*=====================================================
    CHECK HTML
    =====================================================*/

    if (
        trim($html) === ''
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'AI Tools results template returned empty HTML.'
            ],
            500
        );

    }


    /*=====================================================
    RETURN AJAX RESPONSE
    =====================================================*/

    wp_send_json_success(
        [

            'html' =>
                $html,

            'page' =>
                (int) $directory[
                    'current_page'
                ],

            'total_pages' =>
                (int) $directory[
                    'platform_pagination'
                ]->max_num_pages,

            'total_platforms' =>
                (int) $directory[
                    'total_platforms'
                ]

        ]
    );

}
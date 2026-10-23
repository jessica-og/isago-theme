<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
LIVE SEARCH AJAX
=========================================================*/

add_action(
    'wp_ajax_isago_live_search',
    'isago_live_search'  
);

add_action(
    'wp_ajax_nopriv_isago_live_search',
    'isago_live_search'
);


/*=========================================================
VIDEO THUMBNAIL
=========================================================*/

function isago_get_video_thumbnail_url(
    $post_id
)
{

    $video_url =
        isago_get_video_url(
            $post_id
        );


    if (!$video_url) {
        return false;
    }


    /*
     * YouTube watch URL
     * YouTube short URL
     */

    if (
        preg_match(
           '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
         
            $video_url,
            $matches
        )
    ) {

        return
            'https://img.youtube.com/vi/' .
            $matches[1] .
            '/hqdefault.jpg';

    }


    return false;

}


/*=========================================================
FIND LIVE SEARCH TERMS
=========================================================*/

function isago_live_search_term_matches(
    $keyword
)
{

$taxonomies = [
    'category',
    'post_tag',
    'ai_platform',
    'ai_prompt_category',
];


    $matches = [];


    foreach (
        $taxonomies
        as $taxonomy
    ) {


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


            if (
                stripos(
                    $term->name,
                    $keyword
                ) === false
            ) {

                continue;

            }


            $matches[] = [

                'taxonomy' =>
                    $taxonomy,

                'term_id' =>
                    (int) $term->term_id

            ];


            /*
             * Include descendants.
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
LIVE SEARCH
=========================================================*/

function isago_live_search()
{

    $keyword =
        sanitize_text_field(

            $_GET['keyword']
            ??
            ''

        );


    if (!$keyword) {

        wp_send_json([]);

    }


    /*
     * ----------------------------------------------
     * STEP 1
     * TITLE MATCHES
     * ----------------------------------------------
     */

    add_filter(
        'posts_search',
        'isago_live_search_title_only',
        10,
        2
    );


    $title_query =
        new WP_Query(

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

                's' =>
                    $keyword,

                'posts_per_page' =>
                    -1,

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
        'isago_live_search_title_only',
        10
    );


    $title_ids =
        !empty($title_query->posts)
            ? array_map(
                'absint',
                $title_query->posts
            )
            : [];


    /*
     * ----------------------------------------------
     * STEP 2
     * TAXONOMY MATCHES
     *
     * category
     * post_tag
     * ai_platform
     * ----------------------------------------------
     */

    $matches =
        isago_live_search_term_matches(
            $keyword
        );


    $tax_post_ids = [];


    /*
     * Group term IDs by taxonomy.
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


        $tax_query =
            new WP_Query(

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


        if (!empty($tax_query->posts)) {

            $tax_post_ids =
                array_merge(

                    $tax_post_ids,

                    $tax_query->posts

                );

        }

    }


    /*
     * ----------------------------------------------
     * STEP 3
     * MERGE
     * ----------------------------------------------
     */

    $all_ids =
        array_values(

            array_unique(

                array_merge(

                    $title_ids,

                    $tax_post_ids

                )

            )

        );


 if (empty($all_ids)) {
    $all_ids = [0];
}


    /*
     * ----------------------------------------------
     * STEP 4
     * FINAL QUERY
     * ----------------------------------------------
     */

    $query =
        new WP_Query(

            [

                'post_type' => [

                    'post',

                    'isago_video',
    'ai_prompt',

                ],

                'post_status' =>
                    'publish',

                'post__in' =>
                    $all_ids,

                'orderby' =>
                    'post__in',

                'posts_per_page' =>
                    6,

                'no_found_rows' =>
                    true,

                'ignore_sticky_posts' =>
                    true

            ]

        );


    $results = [];


    if ($query->have_posts()) {


        while ($query->have_posts()) {

            $query->the_post();


            $post_id =
                get_the_ID();


            /*
             * Featured image.
             */

         
/*
 * Select the correct thumbnail for this content type.
 */

$post_type = get_post_type($post_id);

$image = '';

/*
 * AI Prompt: use the dedicated prompt image field.
 */
if (
    $post_type === 'ai_prompt'
    && function_exists('isago_media_get_field_image_url')
) {
    $image = isago_media_get_field_image_url(
        'post',
        $post_id,
        'ai_prompt_image',
        'thumbnail'
    );
}

/*
 * Regular posts and videos: use Isago's universal
 * thumbnail helper, which is also used by post cards.
 */
if (
    empty($image)
    && function_exists('isago_get_post_thumbnail')
) {
    $thumbnail_html = isago_get_post_thumbnail(
        $post_id,
        'thumbnail',
        [
            'loading' => 'lazy',
            'alt' => get_the_title($post_id),
        ]
    );

    if (!empty($thumbnail_html)) {
        /*
         * Extract the image URL from the helper's HTML.
         */
        if (
            preg_match(
                '/<img[^>]+src=["\']([^"\']+)["\']/i',
                $thumbnail_html,
                $image_matches
            )
        ) {
            $image = html_entity_decode(
                $image_matches[1],
                ENT_QUOTES,
                'UTF-8'
            );
        }
    }
}

/*
 * Video thumbnail fallback.
 */
if (
    empty($image)
    && $post_type === 'isago_video'
    && function_exists('isago_get_video_thumbnail_url')
) {
    $image = isago_get_video_thumbnail_url($post_id);
}

/*
 * Final fallback.
 */
if (empty($image)) {
    $image = get_template_directory_uri()
        . '/assets/images/placeholder.png';
}



            /*
             * Video thumbnail.
             */

            if (!$image) {

                $image =
                    isago_get_video_thumbnail_url(
                        $post_id
                    );

            }


            /*
             * Fallback image.
             */

            if (!$image) {

                $image =
                    get_template_directory_uri() .
                    '/assets/images/placeholder.png';

            }


            $results[] = [
  'type'  => get_post_type($post_id),
                'title' =>
                    get_the_title(),

                'link' =>
                    get_permalink(),

                'image' =>
                    $image

            ];

        }

    }


    wp_reset_postdata();

/*
 * Add matching AI Platform terms as their own suggestions.
 */

if (taxonomy_exists('ai_platform')) {

    $platform_terms = get_terms([
        'taxonomy'   => 'ai_platform',
        'hide_empty' => false,
        'search'     => $keyword,
        'number'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (!is_wp_error($platform_terms) && !empty($platform_terms)) {

        foreach ($platform_terms as $platform) {

            // Match platform names only.
            if (stripos($platform->name, $keyword) === false) {
                continue;
            }

            $platform_link = get_term_link($platform);

            if (is_wp_error($platform_link)) {
                continue;
            }

           
$image_id = absint(
    get_term_meta(
        $platform->term_id,
        '_isago_ai_platform_image',
        true
    )
);

$image_url = '';

if ($image_id) {
    $image_url = isago_get_media_image_url(
        $image_id,
        'medium'
    );
}

$results[] = [
    'type'  => 'ai_platform',
    'title' => $platform->name,
    'link'  => $platform_link,
    'image' => $image_url,
];

        }
    }
}

// Keep the suggestions within the existing six-result limit.
$results = array_slice($results, 0, 6);

    wp_send_json(
        $results
    );

}


/*=========================================================
LIVE SEARCH TITLE ONLY
=========================================================*/

function isago_live_search_title_only(
    $search,
    $query
)
{

    global $wpdb;


    $keyword =
        $query->get('s');


    if (!$keyword) {
        return $search;
    }


    $like =
        '%' .
        $wpdb->esc_like(
            $keyword
        ) .
        '%';


    return $wpdb->prepare(

        " AND {$wpdb->posts}.post_title LIKE %s ",

        $like

    );

}


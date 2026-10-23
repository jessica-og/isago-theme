<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
Tag Pagination
=========================================================*/

function isago_get_tag_paged()
{

    if (!empty($_GET['paged'])) {

        return absint(
            $_GET['paged']  
        );

    }

    return max(

        1,

        get_query_var('paged'),

        get_query_var('page')

    );

}


/*=========================================================
Current Tag ID
=========================================================*/

function isago_get_current_tag_id()
{

    return absint(

        $_POST['tag']

        ??

        get_queried_object_id()

    );

}


/*=========================================================
Tag Featured
=========================================================*/

function isago_get_tag_featured()
{

    $filter = sanitize_key(
        $_GET['filter'] ?? 'latest'
    );

    $sort = sanitize_key(
        $_GET['sort'] ?? 'latest'
    );

    $tag_id = isago_get_current_tag_id();

    if (!$tag_id) {

        return new WP_Query();

    }


   $args = [

    'post_type' => [  

        'post',

        'isago_video',  
       

    ],

    'post_status' => 'publish',

    'posts_per_page' => 1,

    'tax_query' => [

        [

            'taxonomy' => 'post_tag',

            'field' => 'term_id',

            'terms' => $tag_id,
                  'include_children' => true

        ]

    ],

    'ignore_sticky_posts' => true

];


    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    switch ($filter) {

        case 'popular':

            $args['meta_key'] =
                '_isago_post_views';

            $args['orderby'] =
                'meta_value_num';

            $args['order'] =
                'DESC';

            break;


        case 'trending':

            $args['meta_key'] =
                '_isago_post_views_week';

            $args['orderby'] =
                'meta_value_num';

            $args['order'] =
                'DESC';

            break;


        default:

            $args['orderby'] =
                'date';

            $args['order'] =
                'DESC';

            break;

    }


    /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */

    switch ($sort) {

        case 'oldest':

            $args['orderby'] =
                'date';

            $args['order'] =
                'ASC';

            break;


        case 'title':

            $args['orderby'] =
                'title';

            $args['order'] =
                'ASC';

            break;


        case 'title-desc':

            $args['orderby'] =
                'title';

            $args['order'] =
                'DESC';

            break;

    }


    return new WP_Query($args);

}


/*=========================================================
Tag Grid
=========================================================*/

function isago_get_tag_grid()
{

    $settings =
        isago_get_category_archive_settings();


    $filter = sanitize_key(
        $_GET['filter'] ?? 'latest'
    );

    $sort = sanitize_key(
        $_GET['sort'] ?? 'latest'
    );


    $featured_post_id =
        get_query_var(
            'isago_tag_featured_post_id'
        );


    $tag_id =
        isago_get_current_tag_id();


    if (!$tag_id) {

        return new WP_Query();

    }


   $args = [

    'post_type' => [

        'post',

        'isago_video',
          'ai_prompt'

    ],

    'post_status' => 'publish',

    'tax_query' => [

        [

            'taxonomy' => 'post_tag',

            'field' => 'term_id',

            'terms' => $tag_id,
                  'include_children' => true

        ]

    ],

    'posts_per_page' =>
        $settings['posts'],

    'paged' =>
        isago_get_tag_paged(),

    'ignore_sticky_posts' => true,

    'post__not_in' =>
        $featured_post_id
            ? [$featured_post_id]
            : []

];

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    switch ($filter) {

        case 'popular':

            $args['meta_key'] =
                '_isago_post_views';

            $args['orderby'] =
                'meta_value_num';

            $args['order'] =
                'DESC';

            break;


        case 'trending':

            $args['meta_key'] =
                '_isago_post_views_week';

            $args['orderby'] =
                'meta_value_num';

            $args['order'] =
                'DESC';

            break;


        default:

            $args['orderby'] =
                'date';

            $args['order'] =
                'DESC';

            break;

    }


    /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */

    switch ($sort) {

        case 'oldest':

            $args['orderby'] =
                'date';

            $args['order'] =
                'ASC';

            break;


        case 'title':

            $args['orderby'] =
                'title';

            $args['order'] =
                'ASC';

            break;


        case 'title-desc':

            $args['orderby'] =
                'title';

            $args['order'] =
                'DESC';

            break;

    }


    return new WP_Query($args);

}


/*=========================================================
Popular Tag Posts
=========================================================*/

function isago_get_tag_popular($posts = 5)
{

    $tag_id =
        isago_get_current_tag_id();


    if (!$tag_id) {

        return new WP_Query();

    }


   return new WP_Query([

    'post_type' => [

        'post',

        'isago_video',
        'ai_prompt'

    ],

    'post_status' => 'publish',

    'posts_per_page' => absint($posts),

    'tax_query' => [

        [

            'taxonomy' => 'post_tag',

            'field' => 'term_id',

            'terms' => $tag_id,
                  'include_children' => true

        ]

    ],  

    'ignore_sticky_posts' => true,

    'meta_key' => '_isago_post_views',

    'orderby' => 'meta_value_num',

    'order' => 'DESC'

]);

}











/*=========================================================
ISAGO CONTEXTUAL POPULAR TAGS
- Current post tags first
- Related subcategory tags second
- Parent/current category tags third

Taxonomies:
ai_prompt     => ai_prompt_category
post          => category
isago_video   => category
=========================================================*/

function isago_get_contextual_popular_tags($limit = 8)
{
    $limit = max(1, absint($limit));

    if (!is_singular()) {
        return [];
    }

    $post_id = get_queried_object_id();
    $post = get_post($post_id);

    if (!$post) {
        return [];
    }

    /*
     * Use the taxonomy actually registered
     * for the current post type.
     */
    $taxonomy_map = [
        'ai_prompt'   => 'ai_prompt_category',
        'post'        => 'category',
        'isago_video' => 'category',
    ];

    if (!isset($taxonomy_map[$post->post_type])) {
        return [];
    }

    $taxonomy = $taxonomy_map[$post->post_type];

    if (!taxonomy_exists($taxonomy)) {
        return [];
    }

    /*
     * Query only post types that use this taxonomy.
     * Do not query AI Prompt categories against blog posts/videos.
     */
    $related_post_types = ($post->post_type === 'ai_prompt')
        ? ['ai_prompt']
        : ['post', 'isago_video'];

    /*
     * Get the current post's tags.
     */
    $current_tag_ids = wp_get_object_terms(
        $post_id,
        'post_tag',
        ['fields' => 'ids']
    );

    if (is_wp_error($current_tag_ids)) {
        $current_tag_ids = [];
    }

    $current_tag_ids = array_map(
        'intval',
        $current_tag_ids
    );

    $current_tags = [];

    if (!empty($current_tag_ids)) {
        $current_tags = get_terms([
            'taxonomy'   => 'post_tag',
            'include'    => $current_tag_ids,
            'hide_empty' => false,
        ]);

        if (is_wp_error($current_tags)) {
            $current_tags = [];
        }
    }

    /*
     * Get the categories assigned to the current post.
     */
    $assigned_terms = wp_get_object_terms(
        $post_id,
        $taxonomy
    );

    if (is_wp_error($assigned_terms) || empty($assigned_terms)) {
        $assigned_terms = [];
    }

    $assigned_ids = [];
    $parent_ids = [];
    $family_root_ids = [];

    foreach ($assigned_terms as $term) {
        $term_id = (int) $term->term_id;

        $assigned_ids[] = $term_id;

        /*
         * Use the immediate parent as the family root.
         * This includes sibling subcategories when the current
         * post is assigned to a deeper subcategory.
         *
         * For a top-level category, use that category itself.
         */
        $family_root_ids[] = !empty($term->parent)
            ? (int) $term->parent
            : $term_id;

        /*
         * Include all ancestors as parent-category terms.
         */
        $ancestors = get_ancestors(
            $term_id,
            $taxonomy,
            'taxonomy'
        );

        if (!is_wp_error($ancestors)) {
            $parent_ids = array_merge(
                $parent_ids,
                array_map('intval', $ancestors)
            );
        }
    }

    $assigned_ids = array_values(array_unique($assigned_ids));
    $parent_ids = array_values(array_unique($parent_ids));
    $family_root_ids = array_values(array_unique($family_root_ids));

    /*
     * Collect all terms under the relevant family roots.
     */
    $family_ids = $family_root_ids;

    foreach ($family_root_ids as $root_id) {
        $descendants = get_term_children(
            $root_id,
            $taxonomy
        );

        if (!is_wp_error($descendants)) {
            $family_ids = array_merge(
                $family_ids,
                array_map('intval', $descendants)
            );
        }
    }

    $family_ids = array_values(array_unique($family_ids));

    /*
     * Subcategory terms are family terms excluding the
     * family roots and their ancestors.
     *
     * This captures sibling subcategories and deeper levels.
     */
    $subcategory_ids = array_values(
        array_diff(
            $family_ids,
            $family_root_ids,
            $parent_ids
        )
    );

    /*
     * Helper: retrieve published posts assigned directly
     * to the specified terms.
     */
    $get_posts_for_terms = function ($term_ids) use (
        $taxonomy,
        $related_post_types
    ) {
        if (empty($term_ids)) {
            return [];
        }

        $query = new WP_Query([
            'post_type'              => $related_post_types,
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'tax_query'              => [
                [
                    'taxonomy'         => $taxonomy,
                    'field'            => 'term_id',
                    'terms'            => $term_ids,
                    'include_children' => false,
                ],
            ],
        ]);

        return $query->posts;
    };

    /*
     * Helper: collect tags attached to a set of posts.
     */
    $get_tags_for_posts = function ($post_ids) {
        if (empty($post_ids)) {
            return [];
        }

        $tags = get_terms([
            'taxonomy'   => 'post_tag',
            'hide_empty' => true,
            'object_ids' => $post_ids,
        ]);

        return (
            is_wp_error($tags) || empty($tags)
        ) ? [] : $tags;
    };

    /*
     * Group 1: tags used by posts in subcategories.
     * These tags do not need to be assigned to the current post.
     */
    $subcategory_posts = $get_posts_for_terms(
        $subcategory_ids
    );

    $subcategory_tags = $get_tags_for_posts(
        $subcategory_posts
    );

    /*
     * Group 2: tags used by posts in the parent/current
     * category family.
     */
    $related_posts = $get_posts_for_terms(
        $family_ids
    );

    $related_tags = $get_tags_for_posts(
        $related_posts
    );

    /*
     * Sort related groups by usage count.
     */
    $sort_by_popularity = function (&$tags) {
        usort($tags, function ($a, $b) {
            $comparison = (int) $b->count <=> (int) $a->count;

            return $comparison !== 0
                ? $comparison
                : strcasecmp($a->name, $b->name);
        });
    };

    $sort_by_popularity($subcategory_tags);
    $sort_by_popularity($related_tags);

    /*
     * Combine in the required order, removing duplicates.
     */
    $result = [];
    $seen = [];

    foreach ([
        $current_tags,
        $subcategory_tags,
        $related_tags,
    ] as $group) {
        foreach ($group as $tag) {
            $tag_id = (int) $tag->term_id;

            if (isset($seen[$tag_id])) {
                continue;
            }

            $seen[$tag_id] = true;
            $result[] = $tag;

            if (count($result) >= $limit) {
                return $result;
            }
        }
    }

    return $result;
}


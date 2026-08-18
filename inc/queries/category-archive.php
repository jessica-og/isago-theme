<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Current Page
=========================================================*/

function isago_get_category_paged()
{

    if (!empty($_GET['paged'])) {

        return absint($_GET['paged']);

    }

    return max(

        1,

        get_query_var('paged'),

        get_query_var('page')

    );

}

/*=========================================================
Featured Post
=========================================================*/

function isago_get_category_featured()
{

    $filter = sanitize_key(
        $_GET['filter'] ?? 'latest'
    );

    $sort = sanitize_key(
        $_GET['sort'] ?? 'latest'
    );

 $settings = isago_get_category_archive_settings();

$args = [

    'post_type' => 'post',

    'cat' => absint(

        $_POST['category']
        ??
        get_queried_object_id()

    ),

    /*
     * Always show ONE featured article.
     */

    'posts_per_page' => 1

];

    switch ($filter) {

        case 'popular':

            $args['orderby'] = 'comment_count';

            break;

        case 'trending':

            $args['orderby'] = 'modified';

            break;

        default:

            $args['orderby'] = 'date';

    }

    if ($sort === 'oldest') {

        $args['order'] = 'ASC';

    }

    if ($sort === 'title') {

        $args['orderby'] = 'title';

        $args['order'] = 'ASC';

    }

    if ($sort === 'title-desc') {

        $args['orderby'] = 'title';

        $args['order'] = 'DESC';

    }

    return new WP_Query($args);

}

/*=========================================================
Category Grid
=========================================================*/


function isago_get_category_grid()
{
$settings = isago_get_category_archive_settings();

    $filter = sanitize_key(
        $_GET['filter'] ?? 'latest'
    );

    $sort = sanitize_key(
        $_GET['sort'] ?? 'latest'
    );
$featured_post_id = get_query_var(
    'isago_category_featured_post_id'
);
 $args = [

    'post_type' => 'post',

    'cat' => absint(

        $_POST['category']

        ??

        get_queried_object_id()

    ),

    'posts_per_page' => $settings['posts'],

    'paged' => isago_get_category_paged(),

    'ignore_sticky_posts' => false,

    'post__not_in' => $featured_post_id
        ? [$featured_post_id]
        : []

];
    /*
    --------------------------
    FILTER
    --------------------------
    */

    switch ($filter) {

        case 'popular':

       $args['meta_key'] = '_isago_post_views';

$args['orderby'] = 'meta_value_num';

            $args['order'] = 'DESC';

            break;

        case 'trending':

            $args['orderby'] = 'modified';
            //$args['orderby'] = 'comment_count';

            $args['order'] = 'DESC';

            break;

        default:

            $args['orderby'] = 'date';

            $args['order'] = 'DESC';

    }

    /*
    --------------------------
    SORT
    --------------------------
    */

    switch ($sort) {

        case 'oldest':

            $args['orderby'] = 'date';

            $args['order'] = 'ASC';

            break;

        case 'title':

            $args['orderby'] = 'title';

            $args['order'] = 'ASC';

            break;

        case 'title-desc':

            $args['orderby'] = 'title';

            $args['order'] = 'DESC';

            break;

    }

    return new WP_Query($args);

}


/*=========================================================
Popular Posts
=========================================================*/

function isago_get_category_popular($posts = 5)
{
    return new WP_Query([

        'post_type' => 'post',

        'cat' => absint(

    $_POST['category']

    ??

    get_queried_object_id()

),

        'posts_per_page' => $posts,

        'ignore_sticky_posts' => true,

        'orderby' => 'comment_count'

    ]);
}
   
/*=========================================================
Child Categories
=========================================================*/

function isago_get_category_children()
{
    return get_categories([

        'parent' => get_queried_object_id(),

        'hide_empty' => true,

        'orderby' => 'name',

        'order' => 'ASC'

    ]);
}
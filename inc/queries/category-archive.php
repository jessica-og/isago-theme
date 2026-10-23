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
Recursive Category Article Count
=========================================================*/

function isago_get_category_total_articles($category_id = null)
{
    $category_id = absint(
        $category_id ?: get_queried_object_id()
    );

    if (!$category_id) {
        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Count Posts In This Category + ALL Descendants
    |--------------------------------------------------------------------------
    |
    | WordPress automatically includes child categories when
    | include_children is true.
    |
    | This includes:
    |
    | Parent
    | ├── Child
    | │   ├── Grandchild
    | │   └── Grandchild
    | └── Child
    |
    */

    $query = new WP_Query([

        'post_type' => [
            'post',
            'isago_video'
        ],

        'post_status' => 'publish',

        'posts_per_page' => 1,

        'fields' => 'ids',

        'tax_query' => [

            [
                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => [$category_id],

                'include_children' => true
            ]

        ],

        /*
        |--------------------------------------------------------------------------
        | We need found_posts for the total.
        |--------------------------------------------------------------------------
        */

        'no_found_rows' => false,

        /*
        |--------------------------------------------------------------------------
        | Performance
        |--------------------------------------------------------------------------
        */

        'ignore_sticky_posts' => true,

        'update_post_meta_cache' => false,

        'update_post_term_cache' => false

    ]);


    return (int) $query->found_posts;
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

$category_id = absint(

    $_POST['category']

    ??

    get_queried_object_id()

);

$args = [

    'post_type' => [

        'post',

        'isago_video'

    ],



'tax_query' => [

    [

        'taxonomy' => 'category',

        'field' => 'term_id',

        'terms' => $category_id,
          'include_children' => true

    ]

],

    /*
     * Always show ONE featured article.
     */

    'posts_per_page' => 1

];

switch ($filter) {

  case 'popular':

    $args['meta_key'] = '_isago_post_views';

    $args['orderby'] = 'meta_value_num';

    $args['order'] = 'DESC';

    break;

case 'trending':

    $args['meta_key'] = '_isago_post_views_week';

    $args['orderby'] = 'meta_value_num';

    $args['order'] = 'DESC';

    break;

    default:

        $args['orderby'] = 'date';

        $args['order'] = 'DESC';

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


$category_id = absint(

    $_POST['category']

    ??

    get_queried_object_id()

);

$args = [

    'post_type' => [

        'post',

        'isago_video'

    ],


'tax_query' => [

    [

        'taxonomy' => 'category',

        'field' => 'term_id',

        'terms' => $category_id,
          'include_children' => true

    ]

],
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

    $args['meta_key'] = '_isago_post_views_week';

    $args['orderby'] = 'meta_value_num';

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
    $category_id = absint(

        $_POST['category']

        ??

        get_queried_object_id()

    );

    $args = [

        'post_type' => [

            'post',

            'isago_video'

        ],

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $category_id,
                  'include_children' => true

            ]

        ],

        'posts_per_page' => absint($posts),

        'ignore_sticky_posts' => true,
        
'meta_key' => '_isago_post_views',

'orderby' => 'meta_value_num',

'order' => 'DESC'

    ];

    return new WP_Query($args);
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
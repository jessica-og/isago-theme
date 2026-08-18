<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_get_tag_grid()
{
    $tag = get_queried_object();

    $filter = sanitize_key(
        $_GET['filter'] ?? 'latest'
    );

    $sort = sanitize_key(
        $_GET['sort'] ?? 'latest'
    );

  $featured_post_id = get_query_var(
    'isago_featured_post_id'
);

$args = [

    'post_type' => 'post',

    'post_status' => 'publish',

    'posts_per_page' => 9,

    'paged' => max(
        1,
        get_query_var('paged'),
        $_GET['paged'] ?? 1
    ),

    'tag_id' => $tag->term_id,

    'post__not_in' => $featured_post_id
        ? [$featured_post_id]
        : []
];

    switch ($sort) {

        case 'oldest':

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

        default:

            $args['orderby'] = 'date';

            $args['order'] = 'DESC';
    }

    switch ($filter) {

        case 'popular':

            $args['orderby'] = 'comment_count';

            break;

        case 'trending':

            $args['orderby'] = 'modified';

            break;
    }

    return new WP_Query($args);
}
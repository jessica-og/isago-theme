<?php

if (!defined('ABSPATH')) {
    exit;
}

  

add_action('wp_ajax_isago_live_search', 'isago_live_search');
add_action('wp_ajax_nopriv_isago_live_search', 'isago_live_search');


function isago_get_video_thumbnail_url($post_id)
{
    $video_url = isago_get_video_url($post_id);

    if (!$video_url) return false;

    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $matches)) {
        return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
    }

    return false;
}

function isago_live_search()
{
    $keyword = sanitize_text_field($_GET['keyword'] ?? '');

    if (!$keyword) {
        wp_send_json([]);
    }

    /*
    -------------------------------------
    STEP 1: FIND MATCHING TERMS
    -------------------------------------
    */

  $term_ids = [];

foreach (['category', 'post_tag'] as $taxonomy) {

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'search'     => $keyword,
    ]);

    if (is_wp_error($terms)) continue;

    foreach ($terms as $term) {

        if (stripos($term->name, $keyword) !== false) {

            $term_ids[] = $term->term_id;

            $children = get_term_children($term->term_id, $taxonomy);

            if (!is_wp_error($children)) {
                $term_ids = array_merge($term_ids, $children);
            }
        }
    }
}

$term_ids = array_unique($term_ids);

    /*
    -------------------------------------
    STEP 2: GET POSTS FROM TERMS
    -------------------------------------
    */

    $tax_post_ids = [];

    if (!empty($term_ids)) {

        $tax_query = new WP_Query([
    'post_type' => ['post', 'isago_video'],
            'fields'    => 'ids',
            'posts_per_page' => -1,
            'tax_query' => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                ],
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                ],
            ],
        ]);

        $tax_post_ids = $tax_query->posts;
        
    }

    /*
    -------------------------------------
    STEP 3: GET NORMAL SEARCH POSTS
    -------------------------------------
    */

    $search_query = new WP_Query([
      'post_type' => ['post', 'isago_video'],
        'fields'    => 'ids',
        's'         => $keyword,
        'posts_per_page' => -1,
    ]);

    $search_ids = $search_query->posts;

    /*
    -------------------------------------
    STEP 4: MERGE ( THIS IS THE FIX)
    -------------------------------------
    */

    $all_ids = array_unique(array_merge($search_ids, $tax_post_ids));

    if (empty($all_ids)) {
        wp_send_json([]);
    }

    /*
    -------------------------------------
    STEP 5: FINAL QUERY
    -------------------------------------
    */

    $query = new WP_Query([
       'post_type' => ['post', 'isago_video'],
        'post__in'  => $all_ids,
        'orderby'   => 'post__in',
        'posts_per_page' => 6,
    ]);

    $results = [];
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        
        $image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');

        if (!$image) {
            $image = isago_get_video_thumbnail_url(get_the_ID());
        }


        if (!$image) {
            $image = get_template_directory_uri() . '/assets/images/video.png';
        }

        $results[] = [
            'title' => get_the_title(),
            'link'  => get_permalink(),
            'image' => $image,
        ];
    }
}

    wp_reset_postdata();

    wp_send_json($results);
}
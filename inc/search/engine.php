<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('pre_get_posts', function ($query) {

    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $keyword = $query->get('s');
    if (!$keyword) return;

    $term_ids = [];

    foreach (['category', 'post_tag'] as $taxonomy) {

      $terms = get_terms([
    'taxonomy'   => ['category', 'post_tag'],
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

    if (!empty($term_ids)) {

        $tax_query = new WP_Query([
            'post_type' => 'post',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'tax_query' => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $term_ids,
                ],
                [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $term_ids,
                ],
            ],
        ]);

        $tax_post_ids = $tax_query->posts;

        if (!empty($tax_post_ids)) {

            add_filter('posts_where', function ($where) use ($tax_post_ids, $query) {

                global $wpdb;

               
                if (!$query->is_main_query()) return $where;

                $ids = implode(',', array_map('intval', $tax_post_ids));

                return $where . " OR {$wpdb->posts}.ID IN ($ids)";
            });
        }
    }

    $query->set('posts_per_page', 9);
});




<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Pagination
=========================================================*/

function isago_pagination($query = null)
{
    if (!$query) {
        global $wp_query;
        $query = $wp_query;  
    }

    if ($query->max_num_pages <= 1) {
        return;
    }

 
    $current = max(
        1,
        get_query_var('paged'),
        get_query_var('page')
    );

    echo '<nav class="isago-pagination">';

 echo paginate_links([
    'base' => esc_url_raw(add_query_arg('paged', '%#%')),
    'format' => '',
    'current' => $current,
    'total' => $query->max_num_pages,

    'mid_size' => 1,
    'end_size' => 1,

    'prev_text' => '&laquo;',
    'next_text' => 'Next',

    'type' => 'list',

    'add_args' => array_filter([
        's' => get_search_query(), 
        'filter' => $_GET['filter'] ?? null,
        'sort' => $_GET['sort'] ?? null,
    ]),
]);

    echo '</nav>';
}
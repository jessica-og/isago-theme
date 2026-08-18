<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'wp_ajax_isago_category_archive',
    'isago_category_archive_ajax'
);

add_action(
    'wp_ajax_nopriv_isago_category_archive',
    'isago_category_archive_ajax'
);

function isago_category_archive_ajax()
{

    check_ajax_referer(
        'isago-category',
        'nonce'
    );

    $_GET['filter'] = sanitize_key(
        $_POST['filter'] ?? 'latest'
    );

    $_GET['sort'] = sanitize_key(
        $_POST['sort'] ?? 'latest'
    );

    $_GET['paged'] = absint(
        $_POST['page'] ?? 1
    );
set_query_var('paged', $_GET['paged']);
  
$_POST['category'] = absint(  

    $_POST['category']

    ??

    get_queried_object_id()

);
    ob_start();
get_template_part(
    'template-parts/category/category-toolbar'
);

get_template_part(
    'template-parts/category/category-featured'
);

$query = isago_get_category_grid();

set_query_var('category_query', $query);
get_template_part(
    'template-parts/category/category-grid'
);

set_query_var('isago_pagination_query', $query);
get_template_part(
    'template-parts/category/category-pagination'
);
    wp_send_json_success([

        'html' => ob_get_clean()

    ]);

}
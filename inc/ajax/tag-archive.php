<?php

if (!defined('ABSPATH')) {
    exit;
}


add_action(
    'wp_ajax_isago_tag_archive',
    'isago_tag_archive_ajax'
);

add_action(
    'wp_ajax_nopriv_isago_tag_archive',
    'isago_tag_archive_ajax'
);


function isago_tag_archive_ajax()
{

    check_ajax_referer(
        'isago-tag',
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


    set_query_var(
        'paged',
        $_GET['paged']
    );


    $_POST['tag'] = absint(
        $_POST['tag']
        ??
        get_queried_object_id()
    );


    ob_start();


    get_template_part(
        'template-parts/tag/tag-toolbar'
    );


    get_template_part(
        'template-parts/tag/tag-featured'
    );


    $query =
        isago_get_tag_grid();


    set_query_var(
        'tag_query',
        $query
    );


    get_template_part(
        'template-parts/tag/tag-grid'
    );


    set_query_var(
        'isago_pagination_query',
        $query
    );


    get_template_part(
        'template-parts/tag/tag-pagination'
    );


    wp_send_json_success([

        'html' =>
            ob_get_clean()

    ]);

}
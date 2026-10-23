<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ADMIN ASSETS
=========================================================*/

add_action(
    'admin_enqueue_scripts',
    'isago_admin_assets'
);


function isago_admin_assets($hook)
{

    $screen =
        get_current_screen();


    if (!$screen) {

        return;

    }


    /*-----------------------------------------------------
    ISAGO ADMIN PAGE
    -----------------------------------------------------*/

    $is_isago_page = (

        strpos(
            $hook,
            'isago'
        ) !== false

    );


    /*-----------------------------------------------------
    POST EDITOR
    -----------------------------------------------------*/

    $supported_post_types = [

        'post',

        'isago_video',

        'ai_prompt'

    ];


    $is_post_editor = (

        in_array(
            $hook,
            [
                'post.php',
                'post-new.php'
            ],
            true
        )

        &&

        in_array(
            $screen->post_type,
            $supported_post_types,
            true
        )

    );


    /*-----------------------------------------------------
    AI PLATFORM TAXONOMY SCREEN
    -----------------------------------------------------*/

  $is_ai_platform_screen = (

    in_array(
        $screen->base,
        [
            'edit-tags',
            'term'
        ],
        true
    )

    &&

    $screen->taxonomy === 'ai_platform'

);


    /*-----------------------------------------------------
    STOP ON OTHER ADMIN SCREENS
    -----------------------------------------------------*/

    if (
        !$is_isago_page
        &&
        !$is_post_editor
        &&
        !$is_ai_platform_screen
    ) {

        return;

    }


  


    /*-----------------------------------------------------
    ADMIN CSS
    -----------------------------------------------------*/

    wp_enqueue_style(
        'isago-admin',
        ISAGO_URI . '/assets/css/admin.css',
        [],
        isago_asset_version(
            '/assets/css/admin.css'
        )
    );


    /*-----------------------------------------------------
    MAIN ADMIN JAVASCRIPT
    -----------------------------------------------------*/

wp_enqueue_script(
    'isago-admin',
    ISAGO_URI . '/assets/js/admin/admin.js',
    [
        'jquery',
        'media-editor',
        'media-views'
    ],
    isago_asset_version(
        '/assets/js/admin/admin.js'
    ),
    true
);


wp_enqueue_script(
    'isago-imagekit',
    ISAGO_URI . '/assets/js/admin/imagekit.js',
    ['jquery'],
    isago_asset_version(
        '/assets/js/admin/imagekit.js'
    ),
    true
);




wp_localize_script(
    'isago-imagekit',
    'isagoImageKit',
    [
        'ajaxUrl' =>
            admin_url('admin-ajax.php'),

        'nonce' =>
            wp_create_nonce(
                'isago_imagekit_upload'
            ),
    ]
);


    /*-----------------------------------------------------
    HOMEPAGE BUILDER
    -----------------------------------------------------*/

    if ($is_isago_page) {

        wp_enqueue_script(
            'isago-home-builder',
            ISAGO_URI . '/assets/js/admin/home-builder.js',
            [],
            isago_asset_version(
                '/assets/js/admin/home-builder.js'
            ),
            true
        );

    }


    /*-----------------------------------------------------
    TAXONOMY HERO IMAGE
    -----------------------------------------------------*/

    if (
        $hook ===
        'isago_page_isago-taxonomy-hero-images'
    ) {

        wp_enqueue_script(
            'isago-taxonomy-image',
            ISAGO_URI . '/assets/js/admin/taxonomy-image.js',
            [
                'jquery',
                'media-editor',
                'media-views'
            ],
            isago_asset_version(
                '/assets/js/admin/taxonomy-image.js'
            ),
            true
        );

    }


  
/*-----------------------------------------------------
BROKEN SOCIAL VIDEOS
-----------------------------------------------------*/

$is_broken_social_videos_page = false;


/*
 * WordPress can use different screen/hook identifiers
 * depending on how the parent admin menu was registered.
 */
if (
    isset($screen->id)
    &&
    (
        $screen->id ===
            'isago-home_page_isago-broken-social-videos'

        ||

        $screen->id ===
            'isago_page_isago-broken-social-videos'
    )
) {

    $is_broken_social_videos_page = true;

}


if (
    strpos(
        (string) $hook,
        'isago-broken-social-videos'
    ) !== false
) {

    $is_broken_social_videos_page = true;

}


if ($is_broken_social_videos_page) {

    wp_enqueue_script(
        'isago-broken-social-videos',
        ISAGO_URI .
            '/assets/js/admin/broken-social-videos.js',
        [],
        isago_asset_version(
            '/assets/js/admin/broken-social-videos.js'
        ),
        true
    );


    wp_localize_script(
        'isago-broken-social-videos',
        'isagoBrokenSocialVideos',
        [

            'ajaxUrl' =>
                admin_url(
                    'admin-ajax.php'
                ),

            'nonce' =>
                wp_create_nonce(
                    'isago_broken_social_videos'
                ),

        ]
    );

}

}


/*=========================================================
REMOVE NATIVE WORDPRESS CATEGORY META BOX
=========================================================*/

add_action(
    'admin_menu',
    'isago_remove_native_category_metabox',
    999
);


function isago_remove_native_category_metabox()
{

    remove_meta_box(
        'categorydiv',
        'post',
        'side'
    );


    remove_meta_box(
        'categorydiv',
        'isago_video',
        'side'
    );

}


/*=========================================================
HIDE NATIVE CATEGORY UI
=========================================================*/

add_action(
    'admin_head-post.php',
    'isago_hide_native_category_ui'
);

add_action(
    'admin_head-post-new.php',
    'isago_hide_native_category_ui'
);


function isago_hide_native_category_ui()
{

    $screen =
        get_current_screen();


    if (!$screen) {

        return;

    }


    if (
        !in_array(
            $screen->post_type,
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;

    }

    ?>

    <style id="isago-hide-native-category-ui">

        #categorydiv,
        #categorydiv.postbox,
        .categorydiv {

            display: none !important;

        }


        .editor-post-taxonomies__hierarchical-terms-list,
        .editor-post-taxonomies__hierarchical-terms-choice,
        .editor-post-taxonomies__flat-terms-list {

            display: none !important;

        }

    </style>

    <?php

}


/*=========================================================
KEEP CATEGORY TAXONOMY REGISTERED
=========================================================*/

add_filter(
    'register_taxonomy_args',
    'isago_modify_category_editor_args',
    10,
    2
);


function isago_modify_category_editor_args(
    $args,
    $taxonomy
) {

    if (
        $taxonomy !== 'category'
    ) {

        return $args;

    }


    return $args;

}






/*=========================================================
ISAGO ADMIN TAG SORTING
=========================================================*/

add_action('admin_footer-edit-tags.php', 'isago_tag_sorting_ui');

function isago_tag_sorting_ui()
{
    $screen = get_current_screen();

    if (
        !$screen
        || $screen->taxonomy !== 'post_tag'
        || $screen->base !== 'edit-tags'
    ) {
        return;
    }

    $selected = isset($_GET['isago_tag_sort'])
        ? sanitize_key(wp_unslash($_GET['isago_tag_sort']))
        : 'newest';

    $options = [
        'newest'     => 'Newest first',
        'oldest'     => 'Oldest first',
        'name_asc'   => 'Name: A–Z',
        'name_desc'  => 'Name: Z–A',
        'most_used'  => 'Most used',
        'least_used' => 'Least used',
    ];
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector('.wp-list-table');

        if (!table) return;

        const toolbar = document.createElement('form');
        toolbar.method = 'get';
        toolbar.style.cssText =
            'display:flex;align-items:center;gap:8px;margin:12px 0;';

        const taxonomy = document.createElement('input');
        taxonomy.type = 'hidden';
        taxonomy.name = 'taxonomy';
        taxonomy.value = 'post_tag';
        toolbar.appendChild(taxonomy);

        const label = document.createElement('label');
        label.htmlFor = 'isago-tag-sort';
        label.textContent = 'Sort tags:';
        toolbar.appendChild(label);

        const select = document.createElement('select');
        select.id = 'isago-tag-sort';
        select.name = 'isago_tag_sort';

        const options = <?php echo wp_json_encode($options); ?>;
        const selected = <?php echo wp_json_encode($selected); ?>;

        Object.entries(options).forEach(function ([value, text]) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = text;
            option.selected = value === selected;
            select.appendChild(option);
        });

        toolbar.appendChild(select);

        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'button';
        button.textContent = 'Sort';
        toolbar.appendChild(button);

        table.parentNode.insertBefore(toolbar, table);
    });
    </script>
    <?php
}

add_action('pre_get_terms', 'isago_sort_admin_tag_query');

function isago_sort_admin_tag_query($query)
{
    if (
        !is_admin()
        || !($query instanceof WP_Term_Query)
    ) {
        return;
    }

    $screen = get_current_screen();

    if (
        !$screen
        || $screen->base !== 'edit-tags'
        || $screen->taxonomy !== 'post_tag'
    ) {
        return;
    }

    $taxonomies = (array) ($query->query_vars['taxonomy'] ?? []);

    if (!in_array('post_tag', $taxonomies, true)) {
        return;
    }

    $sort = isset($_GET['isago_tag_sort'])
        ? sanitize_key(wp_unslash($_GET['isago_tag_sort']))
        : 'newest';

    $orders = [
        'newest'     => ['term_id', 'DESC'],
        'oldest'     => ['term_id', 'ASC'],
        'name_asc'   => ['name', 'ASC'],
        'name_desc'  => ['name', 'DESC'],
        'most_used'  => ['count', 'DESC'],
        'least_used' => ['count', 'ASC'],
    ];

    $order = $orders[$sort] ?? $orders['newest'];

    $query->query_vars['orderby'] = $order[0];
    $query->query_vars['order'] = $order[1];
}








/*=========================================================
ADMIN PAGES
=========================================================*/

require_once __DIR__
    . '/pages/homepage-builder.php';

require_once __DIR__
    . '/pages/category-archive.php';

require_once __DIR__
    . '/fields/taxonomy-image.php';

require_once __DIR__
    . '/pages/theme-settings.php';

require_once __DIR__
    . '/pages/prompt-performance.php';

require_once __DIR__
    . '/pages/advertisement-manager.php';

require_once __DIR__
    . '/pages/social-settings.php';

require_once __DIR__
    . '/pages/performance-settings.php';

require_once __DIR__
    . '/pages/import-export.php';

require_once __DIR__
    . '/fields/helpers.php';

require_once __DIR__
    . '/helpers/posts.php';

require_once __DIR__
    . '/helpers/rich-text-editor.php';

require_once __DIR__
    . '/pages/admin-contact-page.php';

require_once __DIR__
    . '/pages/newsletter-subscribers.php';


    require_once __DIR__
    . '/pages/broken-social-videos.php';

    
<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ISAGO ADMIN LOADER
=========================================================*/


/*
|--------------------------------------------------------------------------
| Admin Assets
|--------------------------------------------------------------------------
*/

add_action(
    'admin_enqueue_scripts',
    'isago_admin_assets'
);


function isago_admin_assets($hook)
{

    /*
    |--------------------------------------------------------------------------
    | Only load on Isago admin pages
    |--------------------------------------------------------------------------
    */

    if (strpos($hook, 'isago') === false) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | WordPress Media Library
    |--------------------------------------------------------------------------
    */

    wp_enqueue_media();


    /*
    |--------------------------------------------------------------------------
    | Admin CSS
    |--------------------------------------------------------------------------
    */

    wp_enqueue_style(

        'isago-admin',

        ISAGO_URI . '/assets/css/admin.css',

        [],

        isago_asset_version(
            '/assets/css/admin.css'
        )

    );

/*
|--------------------------------------------------------------------------
| Isago Admin Accordion
|--------------------------------------------------------------------------
*/

wp_enqueue_script(
    'isago-home-builder',
    ISAGO_URI . '/assets/js/admin/home-builder.js',
    [],
    isago_asset_version(
        '/assets/js/admin/home-builder.js'
    ),
    true
);


    /*
    |--------------------------------------------------------------------------
    | Main Admin JavaScript
    |--------------------------------------------------------------------------
    */

    wp_enqueue_script(

        'isago-admin',

        ISAGO_URI . '/assets/js/admin/admin.js',

        ['jquery'],

        isago_asset_version(
            '/assets/js/admin/admin.js'
        ),

        true

    );




}


/*=========================================================
ADMIN PAGES
=========================================================*/

require_once __DIR__ . '/pages/homepage-builder.php';

require_once __DIR__ . '/pages/category-archive.php';

require_once __DIR__ . '/pages/theme-settings.php';

require_once __DIR__ . '/pages/advertisement-manager.php';

require_once __DIR__ . '/pages/seo-settings.php';

require_once __DIR__ . '/pages/social-settings.php';

require_once __DIR__ . '/pages/performance-settings.php';

require_once __DIR__ . '/pages/import-export.php';

require_once __DIR__ . '/fields/helpers.php';

require_once __DIR__ . '/helpers/posts.php';

require_once __DIR__ . '/pages/admin-contact-page.php';
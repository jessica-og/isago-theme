<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Asset Version Helper
=========================================================*/

function isago_asset_version($file)
{
    $path = ISAGO_DIR . $file;

    if (file_exists($path)) {
        return filemtime($path);
    }

    return ISAGO_VERSION;
}

/*=========================================================
Frontend Assets
=========================================================*/

add_action('wp_enqueue_scripts', 'isago_enqueue_assets');

function isago_enqueue_assets()
{

    /*
    -------------------------------------------------------
    Theme Settings
    -------------------------------------------------------
    */

    $performance = function_exists('isago_get_performance_settings')
        ? isago_get_performance_settings()
        : [];

    /*
    -------------------------------------------------------
    Main Theme Style
    -------------------------------------------------------
    */

    wp_enqueue_style(
        'isago-style',
        get_stylesheet_uri(),
        [],
        isago_asset_version('/style.css')
    );

    /*
    -------------------------------------------------------
    Font Awesome
    -------------------------------------------------------
    */


if (empty($performance) || !empty($performance['fontawesome'])) {

    wp_enqueue_style(
        'fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        [],
        '6.7.2'
    );
}

/*
-------------------------------------------------------
Google Fonts
-------------------------------------------------------
*/

$theme = isago_get_theme_settings();

$font = $theme['font_family'] ?? 'Inter';

$fonts = [

    'Inter' =>
        'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap',

    'Nunito' =>
        'https://fonts.googleapis.com/css2?family=Nunito:wght@200..1000&display=swap',

    'Poppins' =>
        'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap',

    'Roboto' =>
        'https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap',

    'Open Sans' =>
        'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap',

    'Montserrat' =>
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap',

    'Manrope' =>
        'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap',

    'Outfit' =>
        'https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap',

    'Lora' =>
        'https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap',

    'Playfair Display' =>
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap',

    'Merriweather' =>
        'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap',

    'Cormorant Garamond' =>
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',

    'Libre Baskerville' =>
        'https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&display=swap',

    'Source Serif 4' =>
        'https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@200..900&display=swap',

    'Crimson Text' =>
        'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&display=swap',

    'DM Serif Display' =>
        'https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap',
];

if (isset($fonts[$font])) {

    wp_enqueue_style(
        'isago-font',
        $fonts[$font],
        [],
        null
    );
}

    /*
    -------------------------------------------------------
    Theme Styles
    -------------------------------------------------------
    */

    $styles = [

        'base',
        'header',
        'responsive-post',
        'header-search',
        'search',
        'navigation',
        'mobile-menu',
        'hero',
        'layout',
        'sections',
        'page-layout',
        'sidebar',
        'category',
        'post',
          'about',
          'contact',
          'privacy-policy',
        'footer'
       

    ];

    $dependency = ['isago-style'];

    foreach ($styles as $style) {

        wp_enqueue_style(

            'isago-' . $style,

            ISAGO_URI . "/assets/css/{$style}.css",

            $dependency,

            isago_asset_version("/assets/css/{$style}.css")

        );

        $dependency = ['isago-' . $style];

    }

    /*
    -------------------------------------------------------
    Theme Scripts
    -------------------------------------------------------
    */

    wp_enqueue_script(

        'isago-header',

        ISAGO_URI . '/assets/js/header.js',

        [],

        isago_asset_version('/assets/js/header.js'),

        true

    );
    



    wp_enqueue_script(

        'isago-navigation',

        ISAGO_URI . '/assets/js/navigation.js',

        ['isago-header'],

        isago_asset_version('/assets/js/navigation.js'),

        true

    );

wp_enqueue_script(
    'isago-mobile-menu',
    ISAGO_URI . '/assets/js/mobile-menu.js',
    [],
    ISAGO_VERSION,
    true
);

    wp_enqueue_script(

        'isago-theme',

        ISAGO_URI . '/assets/js/theme.js',

        [],

        isago_asset_version('/assets/js/theme.js'),

        true

    );

    wp_enqueue_script(

        'isago-video',

        ISAGO_URI . '/assets/js/video-lightbox.js',

        [],

        isago_asset_version('/assets/js/video-lightbox.js'),

        true

    );

    wp_enqueue_script(
  
    'isago-category',

    ISAGO_URI . '/assets/js/category.js',

    [],

    isago_asset_version('/assets/js/category.js'),

    true

);

wp_enqueue_script(
    'isago-search',
    ISAGO_URI . '/assets/js/search.js',
    [],
    time(),
    true
);

wp_localize_script(
    'isago-search', 
    'isago_search',
    [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]
);


wp_enqueue_script(
'isago-contact',
ISAGO_URI . '/assets/js/contact.js',
[],
null,
true
); 


wp_enqueue_script(
'isago-faq',
ISAGO_URI . '/assets/js/faq.js',
[],
null,
true
); 
    /*
    -------------------------------------------------------
    Localized Data
    -------------------------------------------------------
    */
wp_localize_script(

    'isago-video',

    'isago',

    [

        'ajax' => admin_url('admin-ajax.php'),

        'theme' => get_template_directory_uri()

    ]

);
 wp_localize_script(

    'isago-category',

    'isagoCategory',

    [

        'ajax'  => admin_url('admin-ajax.php'),

        'nonce' => wp_create_nonce('isago-category')

    ]

);

}
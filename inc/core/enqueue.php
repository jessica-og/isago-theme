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
    Theme Styles
    -------------------------------------------------------
    */

    $styles = [
    
        'base',
        'account',
        'ai-prompt',
        'saved-prompts',
        'advertisement',
        'header',
        'responsive-post',
        'header-search',
        'search',
        '404',
        'navigation',
        'mobile-menu',
        'hero',
        'layout',
        'video-section',
        'home',
        'sidebar',
        'ai-tool-categories',
        'orbit',
        'category',
        'post',
        'ai-prompt-single',
        'ai-platform',
        'anew',
          'about',
          'contact',
          'privacy-policy',
        'footer',
         'cookie',

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





 /*=========================================================
GLOBAL SCRIPTS
=========================================================*/

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

        'isago-orbit',

        ISAGO_URI . '/assets/js/orbit.js',

        [],

        isago_asset_version('/assets/js/orbit.js'),

        true

    );

wp_enqueue_script(

    'isago-video-preview',

    ISAGO_URI . '/assets/js/video-preview.js',

    [],

    isago_asset_version(
        '/assets/js/video-preview.js'
    ),

    true

);


wp_enqueue_script(

    'isago-cookie-consent',

    ISAGO_URI . '/assets/js/cookie-consent.js',

    [],

    isago_asset_version(
        '/assets/js/cookie-consent.js'
    ),

    true

);

wp_localize_script(

    'isago-cookie-consent',

    'isagoCookie',

    [

        'days' => isago_get_cookie_settings()['cookie_days'],

    ]

);





/*=========================================================
AI PROMPT 
=========================================================*/
if (is_singular('ai_prompt')) {
    wp_enqueue_script(
        'isago-ai-prompt-single',
        ISAGO_URI . '/assets/js/ai-prompt-single.js',
        array(),
        ISAGO_VERSION,
        true
    );
}

    wp_enqueue_script(
    'isago-post',

    ISAGO_URI . '/assets/js/copy-link.js',

    ['jquery'],

    isago_asset_version('/assets/js/copy-link.js'),

    true

);



/*=========================================================
AI PROMPTS DIRECTORY
=========================================================*/

if (is_page('ai-prompts-library')) {

    wp_enqueue_script(
        'isago-ai-prompts-directory',
        ISAGO_URI . '/assets/js/ai-prompts-directory.js',
        [],
        isago_asset_version(
            '/assets/js/ai-prompts-directory.js'
        ),
        true
    );

    wp_add_inline_script(
        'isago-ai-prompts-directory',
        'window.IsagoAIPrompts = ' .
        wp_json_encode(
            [

                'ajaxUrl' =>
                    admin_url(
                        'admin-ajax.php'
                    ),

                'nonce' =>
                    wp_create_nonce(
                        'isago_ai_prompts'
                    )

            ]
        ) .
        ';',
        'before'
    );

}




/*=========================================================
AI PROMPT CATEGORY TABS — INDEPENDENT AJAX
=========================================================*/

add_action(
    'wp_enqueue_scripts',
    function () {

        $script_path = get_template_directory()
            . '/assets/js/ai-prompt-category-tabs.js';

        if (!file_exists($script_path)) {
            error_log(
                'Isago AI Prompt Tabs: JavaScript file not found: '
                . $script_path
            );
            return;
        }

        wp_enqueue_script(
            'isago-ai-prompt-category-tabs',
            get_template_directory_uri()
                . '/assets/js/ai-prompt-category-tabs.js',
            array(),
            (string) filemtime($script_path),
            true
        );

    },
    99
);







if (
    is_category()
    ||
    is_tax()
) {

    wp_enqueue_script(
        'isago-category',
        ISAGO_URI . '/assets/js/category.js',
        [],
        isago_asset_version(
            '/assets/js/category.js'
        ),
        true
    );


    wp_localize_script(
        'isago-category',
        'isagoCategory',
        [
            'ajax' =>
                admin_url(
                    'admin-ajax.php'
                ),

            'nonce' =>
                wp_create_nonce(
                    'isago-category'
                )
        ]
    );

}







wp_enqueue_script(
    'video-ai-prompt',
    ISAGO_URI . '/assets/js/video-ai-prompt.js',
    [],
    isago_asset_version('/assets/js/video-ai-prompt.js'),
    true
);



wp_enqueue_script(
    'isago-search',
    ISAGO_URI . '/assets/js/search.js',
    [],
    isago_asset_version('/assets/js/search.js'),
    true
);
wp_localize_script(
    'isago-search', 
    'isago_search',
    [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]
);





/*=========================================================
ISAGO DIRECTORY ENGINE
=========================================================*/

wp_enqueue_script(
    'isago-directory',
    ISAGO_URI . '/assets/js/isago-directory.js',
    [],
    isago_asset_version(
        '/assets/js/isago-directory.js'
    ),
    true
);


/*=========================================================
AI PLATFORMS DIRECTORY
=========================================================*/

if (
    is_page('ai-tools')
    ||
    is_tax('ai_platform')
) {

    wp_enqueue_script(
        'isago-ai-platforms-directory',
        ISAGO_URI . '/assets/js/ai-platforms-directory.js',
        ['isago-directory'],
        isago_asset_version(
            '/assets/js/ai-platforms-directory.js'
        ),
        true
    );


    wp_add_inline_script(
        'isago-ai-platforms-directory',
        'window.IsagoAIPlatforms = ' .
        wp_json_encode(
            [

                'ajaxUrl' =>
                    admin_url(
                        'admin-ajax.php'
                    ),

                'nonce' =>
                    wp_create_nonce(
                        'isago_ai_platforms'
                    )

            ]
        ) .
        ';',
        'before'
    );

}





if (is_page('contact')) {
wp_enqueue_script(
'isago-contact',
ISAGO_URI . '/assets/js/contact.js',
[],
null,
true
); 
}
if (is_page('contact')) {
wp_enqueue_script(
'isago-faq',
ISAGO_URI . '/assets/js/faq.js',
[],
null,
true
); 
}



/*=========================================================
AI PROMPT PAYMENT
=========================================================*/

if (
    is_singular('ai_prompt')
) {

    wp_enqueue_script(
        'isago-payment',
        ISAGO_URI . '/assets/js/payment.js',
        [],
        isago_asset_version(
            '/assets/js/payment.js'
        ),
        true
    );


    wp_localize_script(
        'isago-payment',
        'isagoPayment',
        [
            'ajaxUrl' =>
                admin_url(
                    'admin-ajax.php'
                ),

            'nonce' =>
                wp_create_nonce(
                    'isago_prompt_checkout'
                ),

            'isLoggedIn' =>
                is_user_logged_in(),

            'loginUrl' =>
                wp_login_url(
                    get_permalink()
                ),
        ]
    );

}


/*=========================================================
SAVED PROMPTS
=========================================================*/

if (
    is_user_logged_in()
) {

    wp_enqueue_script(
        'isago-saved-prompts',
        ISAGO_URI . '/assets/js/saved-prompts.js',
        [],
        isago_asset_version(
            '/assets/js/saved-prompts.js'
        ),
        true
    );


    wp_localize_script(
        'isago-saved-prompts',
        'isagoSavedPrompts',
        [
            'ajaxUrl' =>
                admin_url(
                    'admin-ajax.php'
                ),

            'nonce' =>
                wp_create_nonce(
                    'isago_saved_prompts'
                ),
        ]
    );

}




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
//  wp_localize_script(

//     'isago-category',

//     'isagoCategory',

//     [

//         'ajax'  => admin_url('admin-ajax.php'),

//         'nonce' => wp_create_nonce('isago-category')

//     ]

// );






}



<?php

if (!defined('ABSPATH')) {
    exit;
}

/*--------------------------------------------------------------
Export Helper
--------------------------------------------------------------*/

function isago_export_settings()
{

    return [

        'homepage' => get_option(
            'isago_home_settings',
            []
        ),

        'theme' => get_option(
            'isago_theme_settings',
            []
        ),

        'ads' => get_option(
            'isago_ads_settings',
            []
        ),

        'seo' => get_option(
            'isago_seo_settings',
            []
        ),

        'performance' => get_option(
            'isago_performance_settings',
            []
        )

    ];

}


/*--------------------------------------------------------------
Download Export
--------------------------------------------------------------*/

add_action(
    'admin_init',
    'isago_download_export'
);

function isago_download_export()
{

    if (
        empty($_POST['isago_export'])
    ) {
        return;
    }

    check_admin_referer(
        'isago_export'
    );

    $settings = isago_export_settings();

    nocache_headers();

    header(
        'Content-Type: application/json'
    );

    header(
        'Content-Disposition: attachment; filename=isago-settings.json'
    );

    echo wp_json_encode(
        $settings,
        JSON_PRETTY_PRINT
    );

    exit;

}

/*--------------------------------------------------------------
Import Settings
--------------------------------------------------------------*/

add_action(
    'admin_init',
    'isago_import_settings'
);

function isago_import_settings()
{

    if (
        empty($_FILES['settings'])
    ) {
        return;
    }

    check_admin_referer(
        'isago_import'
    );

    $json = file_get_contents(
        $_FILES['settings']['tmp_name']
    );

    $settings = json_decode(
        $json,
        true
    );

    if (!is_array($settings)) {
        return;
    }

    foreach ($settings as $key => $value) {

        update_option(
            "isago_{$key}_settings",
            $value
        );

    }

}


/*--------------------------------------------------------------
Reset Settings
--------------------------------------------------------------*/

add_action(
    'admin_init',
    'isago_reset_settings'
);

function isago_reset_settings()
{

    if (
        empty($_POST['reset_target'])
    ) {
        return;
    }

    check_admin_referer(
        'isago_reset'
    );

    switch ($_POST['reset_target']) {

        case 'homepage':

            delete_option(
                'isago_home_settings'
            );

        break;

        case 'theme':

            delete_option(
                'isago_theme_settings'
            );

        break;

        case 'ads':

            delete_option(
                'isago_ads_settings'
            );

        break;

        case 'seo':

            delete_option(
                'isago_seo_settings'
            );

        break;

        case 'performance':

            delete_option(
                'isago_performance_settings'
            );

        break;

        case 'all':

            delete_option(
                'isago_home_settings'
            );

            delete_option(
                'isago_theme_settings'
            );

            delete_option(
                'isago_ads_settings'
            );

            delete_option(
                'isago_seo_settings'
            );

            delete_option(
                'isago_performance_settings'
            );

        break;

    }

}


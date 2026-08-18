<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_get_hero_settings()
{

$options = isago_get_option(

'isago_home_settings'
);

return [

'featured'=>

$options['hero_featured']  

??0,

'left'=>

$options['hero_left_category']

??'',



];

}



/**
 * ----------------------------------------------------
 * Homepage Sections
 * ----------------------------------------------------
 */

function isago_get_home_sections()
{
    $options = isago_get_option('isago_home_settings');

    $sections = [];

    for ($i = 1; $i <= 8; $i++) {

        if (empty($options["section{$i}_category"])) {
            continue;
        }

        $sections[] = [

            'title' => sanitize_text_field(
                $options["section{$i}_title"] ?? ''
            ),

            'slug' => sanitize_title(
                $options["section{$i}_category"]
            ),

            'posts' => absint(
                $options["section{$i}_posts"] ?? 4
            ),

            // Layout is the ONLY field using underscores
            'layout' => sanitize_key(
                $options["section{$i}_layout"] ?? 'grid'
            ),

            'show_ad' => !empty(
                $options["section{$i}_ad"]
            )

        ];
    }

    return $sections;
}

/**
 * ----------------------------------------------------
 * Sidebar Settings
 * ----------------------------------------------------
 */

function isago_get_sidebar_settings()
{

    $options = isago_get_option(
        'isago_home_settings'
    );

    return [

          /*
        |--------------------------------------------------------------------------
        | Latest Posts Widget
        |--------------------------------------------------------------------------
        */

        'latest_posts' => absint(
            $options['sidebar_latest_posts'] ?? 5
        ),

        'show_thumb' => !empty($options['sidebar_latest_thumb']) ? 1 : 0,

        'show_date' => !empty($options['sidebar_latest_date']) ? 1 : 0,

        /*
        |--------------------------------------------------------------------------
        | Featured Story Widget
        |--------------------------------------------------------------------------
        */

        'featured_post' => absint(
            $options['sidebar_featured_post'] ?? 0
        ),

        /*
        |--------------------------------------------------------------------------
        | Editorial Stories Widget
        |--------------------------------------------------------------------------
        */

        'editorial_category' => isset($options['sidebar_editorial_category'])
            ? sanitize_title($options['sidebar_editorial_category'])
            : '',

        'editorial_posts' => absint(
            $options['sidebar_editorial_posts'] ?? 2
        ),

        /*
        |--------------------------------------------------------------------------
        | Video Widget
        |--------------------------------------------------------------------------
        */

        // 'video_category' => isset($options['sidebar_video_category'])
        //     ? sanitize_title($options['sidebar_video_category'])
        //     : '',

        'video_posts' => absint(
            $options['sidebar_video_posts'] ?? 3
        ),

        /*
|--------------------------------------------------------------------------
| Newsletter
|--------------------------------------------------------------------------
*/

'newsletter_enable' => !empty(
    $options['newsletter_enable']
) ? 1 : 0,

'newsletter_title' => sanitize_text_field(
    $options['newsletter_title']
    ?? 'Stay informed, daily.'
),

'newsletter_text' => sanitize_textarea_field(
    $options['newsletter_text']
    ?? 'Get the latest AI stories delivered to your inbox.'
),

'newsletter_button' => sanitize_text_field(
    $options['newsletter_button']
    ?? 'Subscribe'
),

'popular_tags_count' => absint(
    $options['popular_tags_count']
    ?? 12
),

/*
|--------------------------------------------------------------------------
| Advertisement
|--------------------------------------------------------------------------
*/

'sidebar_ad_enable' => !empty(
    $options['sidebar_ad_enable']
) ? 1 : 0,

'sidebar_ad_image' => esc_url_raw(
    $options['sidebar_ad_image']
    ?? ''
),

'sidebar_ad_link' => esc_url_raw(
    $options['sidebar_ad_link']
    ?? '#'
),
    ];

}




/**
 * ----------------------------------------------------
 * Homepage Video Section
 * ----------------------------------------------------
 */

function isago_get_home_video_settings()
{

    $options = isago_get_option(
        'isago_home_settings'
    );

    return [

        'title' => sanitize_text_field(
            $options['homepage_video_title']
            ?? 'Videos'
        ),

        'category' => sanitize_title(
            $options['homepage_video_category']
            ?? ''
        ),

        'posts' => absint(
            $options['homepage_video_posts']
            ?? 3
        )

    ];

}
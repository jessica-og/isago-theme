<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Global Theme Settings
|--------------------------------------------------------------------------
*/

function isago_get_theme_settings()
{

    $options = isago_get_option(
        'isago_theme_settings'
    );

    return [

        /*
        |--------------------------------------------------------------------------
        | Branding
        |--------------------------------------------------------------------------
        */

        'logo_width' => absint(
            $options['logo_width'] ?? 180
        ),

        'site_accent' => sanitize_hex_color(
            $options['site_accent'] ?? ''
        ) ?: '#00d4f5',

        'site_accent_alt' => sanitize_hex_color(
            $options['site_accent_alt'] ?? ''
        ) ?: '#36fefb',

'font_family' => sanitize_text_field(
    $options['font_family'] ?? 'Inter'
),





        
        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        'sticky_header' => !empty(
            $options['sticky_header']
        ),

        'dark_mode' => !empty(
            $options['dark_mode']
        ),

        'header_search' => !empty(
            $options['header_search']
        ),

        'header_dark_toggle' => !empty(
            $options['header_dark_toggle']
        ),

        'header_subscribe' => !empty(
            $options['header_subscribe']
        ),








        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        'container_width' => absint(
            $options['container_width'] ?? 1320
        ),











        /*
        |--------------------------------------------------------------------------
        | Footer
        |--------------------------------------------------------------------------
        */

        'footer_newsletter' => !empty(
            $options['footer_newsletter']
        ),

        'copyright' => sanitize_text_field(
            $options['copyright']
            ?? '© Isago. All rights reserved.'
        ),

'footer_description' => sanitize_textarea_field(

$options['footer_description']

?? ''

),

'footer_newsletter_title' => sanitize_text_field(

$options['footer_newsletter_title']

?? 'Newsletter'

),

'footer_newsletter_text' => sanitize_textarea_field(

$options['footer_newsletter_text']

?? ''

),

'footer_newsletter_button' => sanitize_text_field(

$options['footer_newsletter_button']

?? 'Subscribe'

),






/*
|--------------------------------------------------------------------------
| About Page
|--------------------------------------------------------------------------
*/

'about_title' => sanitize_text_field(
    $options['about_title'] ?? 'About Isago'
),

'about_desc' => sanitize_textarea_field(
    $options['about_desc'] ?? ''
),

'about_cta_text' => sanitize_text_field(
    $options['about_cta_text'] ?? 'Explore Now'
),

'about_cta_link' => esc_url_raw(
    $options['about_cta_link'] ?? '#'
),


'about_story_title' => sanitize_text_field($options['about_story_title'] ?? ''),
'about_story_text'  => sanitize_textarea_field($options['about_story_text'] ?? ''),
'about_story_image' => esc_url_raw($options['about_story_image'] ?? ''),

'about_hero_image' => esc_url_raw($options['about_hero_image'] ?? ''),

'about_sidebar_title' => sanitize_text_field($options['about_sidebar_title'] ?? ''),
'about_sidebar_desc'  => sanitize_textarea_field($options['about_sidebar_desc'] ?? ''),

'about_founded' => sanitize_text_field($options['about_founded'] ?? ''),
'about_hq'      => sanitize_text_field($options['about_hq'] ?? ''),
'about_focus'   => sanitize_text_field($options['about_focus'] ?? ''),




/*
|--------------------------------------------------------------------------
| Contact Page
|--------------------------------------------------------------------------
*/
'contact_email' => sanitize_email($options['contact_email'] ?? ''),
'contact_phone' => sanitize_text_field($options['contact_phone'] ?? ''),
'contact_address' => sanitize_text_field($options['contact_address'] ?? ''),


    ];
}
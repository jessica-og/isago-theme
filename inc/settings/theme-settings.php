<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Global Theme Settings
|--------------------------------------------------------------------------

| Centralized accessor for Isago theme settings.
|
| IMPORTANT:
| - Media Library images are stored as attachment IDs.
| - URLs are stored as URLs.
| - Each value is sanitized according to the type of data it contains.
|
| This function does NOT output anything.
| It only returns normalized theme settings for use throughout the theme.
|
*/

function isago_get_theme_settings()
{

    $options = isago_get_option(
        'isago_theme_settings'
    );

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    */

    $logo_width = absint(
        $options['logo_width'] ?? 180
    );

    /*
    |--------------------------------------------------------------------------
    | Author Image
    |--------------------------------------------------------------------------
    |
    | Author image is stored as a Media Library attachment ID.
    |
    | DO NOT use esc_url_raw() here.
    |
    | The frontend can convert this ID into an image URL with:
    |
    | wp_get_attachment_image_url(
    |     $settings['author_image'],
    |     'medium'
    | );
    |
    */

    $author_image = absint(
        $options['author_image'] ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | About Images
    |--------------------------------------------------------------------------
    |
    | These fields are currently stored as URLs by the existing settings
    | system, so they remain URL values here.
    |
    */

$about_story_image = absint(
    $options['about_story_image'] ?? 0
);

$about_hero_image = absint(
    $options['about_hero_image'] ?? 0
);

$about_hero_image_mobile = absint(
    $options['about_hero_image_mobile'] ?? 0
);

    /*
    |--------------------------------------------------------------------------
    | Contact Image
    |--------------------------------------------------------------------------
    */

 $contact_hero_image = absint(
    $options['contact_hero_image'] ?? 0
);



/*---------------------------------------------------------
CURRENCY
---------------------------------------------------------*/

$currency = strtoupper(
    sanitize_text_field(
        $options['currency'] ?? 'USD'
    )
);

/*
 * USD is the current Isago commerce currency.
 *
 * Keep the validation centralized so frontend,
 * orders, checkout, and payment processing all
 * use the same currency.
 */
if ($currency !== 'USD') {

    $currency = 'USD';

}


    /*
    |--------------------------------------------------------------------------
    | Return Normalized Settings
    |--------------------------------------------------------------------------
    */

    return [

        /*
        |----------------------------------------------------------------------
        | Branding
        |----------------------------------------------------------------------
        */

        'logo_width' => $logo_width,

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
        |----------------------------------------------------------------------
        | Author
        |----------------------------------------------------------------------
        */

   'author_name' => sanitize_text_field(
    $options['author_name'] ?? 'Isago'
),

'author_bio' => sanitize_textarea_field(
    $options['author_bio']
    ?? 'Isago publishes practical insights, guides, prompts, and resources for creators exploring AI.'
),

/*
| Author image is a central Isago media ID.
|
| During migration, an old WordPress attachment ID may
| still exist here.
*/
'author_image' => $author_image,


        /*
        |----------------------------------------------------------------------
        | Header
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | Layout
        |----------------------------------------------------------------------
        */

        'container_width' => absint(
            $options['container_width'] ?? 1320
        ),


        /*
        |----------------------------------------------------------------------
        | Footer
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | About Page
        |----------------------------------------------------------------------
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

        'about_story_title' => sanitize_text_field(
            $options['about_story_title'] ?? ''
        ),

        'about_story_text' => sanitize_textarea_field(
            $options['about_story_text'] ?? ''
        ),

        'about_story_image' => $about_story_image,

        'about_hero_image' => $about_hero_image,

        'about_hero_image_mobile' => $about_hero_image_mobile,

        'about_sidebar_title' => sanitize_text_field(
            $options['about_sidebar_title'] ?? ''
        ),

        'about_sidebar_desc' => sanitize_textarea_field(
            $options['about_sidebar_desc'] ?? ''
        ),

        'about_founded' => sanitize_text_field(
            $options['about_founded'] ?? ''
        ),

        'about_hq' => sanitize_text_field(
            $options['about_hq'] ?? ''
        ),

        'about_focus' => sanitize_text_field(
            $options['about_focus'] ?? ''
        ),


        /*
        |----------------------------------------------------------------------
        | Contact Page
        |----------------------------------------------------------------------
        */

        'contact_hero_image' => $contact_hero_image,

        'contact_email' => sanitize_email(
            $options['contact_email'] ?? ''
        ),

        'contact_phone' => sanitize_text_field(
            $options['contact_phone'] ?? ''
        ),

        'contact_address' => sanitize_text_field(
            $options['contact_address'] ?? ''
        ),

    ];
}






/*=========================================================
SYNC THEME SETTINGS MEDIA REFERENCES
=========================================================*/

add_action(
    'updated_option',
    'isago_sync_theme_settings_media_references',
    10,
    3
);

function isago_sync_theme_settings_media_references(
    $option,
    $old_value,
    $new_value
) {

    if (
        $option !== 'isago_theme_settings'
    ) {
        return;
    }


    if (
        !is_array($new_value)
    ) {
        return;
    }


    /*
     * --------------------------------------------------------
     * AUTHOR IMAGE
     * --------------------------------------------------------
     */

    isago_sync_theme_setting_media_field(
        $new_value['author_image'] ?? 0,
        'author_image'
    );


    /*
     * --------------------------------------------------------
     * CONTACT HERO IMAGE
     * --------------------------------------------------------
     */

    isago_sync_theme_setting_media_field(
        $new_value['contact_hero_image'] ?? 0,
        'contact_hero_image'
    );


/*
 * --------------------------------------------------------
 * ABOUT STORY IMAGE
 * --------------------------------------------------------
 */

isago_sync_theme_setting_media_field(
    $new_value['about_story_image'] ?? 0,
    'about_story_image'
);


/*
 * --------------------------------------------------------
 * ABOUT DESKTOP HERO IMAGE
 * --------------------------------------------------------
 */

isago_sync_theme_setting_media_field(
    $new_value['about_hero_image'] ?? 0,
    'about_hero_image'
);


/*
 * --------------------------------------------------------
 * ABOUT MOBILE HERO IMAGE
 * --------------------------------------------------------
 */

isago_sync_theme_setting_media_field(
    $new_value['about_hero_image_mobile'] ?? 0,
    'about_hero_image_mobile'
);

}









/*=========================================================
SYNC FIRST-TIME THEME SETTINGS OPTION
=========================================================*/

add_action(
    'added_option',
    'isago_sync_theme_settings_media_references_added',
    10,
    2
);

function isago_sync_theme_settings_media_references_added(
    $option,
    $value
) {

    if (
        $option !== 'isago_theme_settings'
    ) {
        return;
    }


    if (
        !is_array($value)
    ) {
        return;
    }


    /*
     * Author image.
     */
    isago_sync_theme_setting_media_field(
        $value['author_image'] ?? 0,
        'author_image'
    );


    /*
     * Contact hero image.
     */
    isago_sync_theme_setting_media_field(
        $value['contact_hero_image'] ?? 0,
        'contact_hero_image'
    );


    /*
 * About story image.
 */
isago_sync_theme_setting_media_field(
    $value['about_story_image'] ?? 0,
    'about_story_image'
);


/*
 * About desktop hero image.
 */
isago_sync_theme_setting_media_field(
    $value['about_hero_image'] ?? 0,
    'about_hero_image'
);


/*
 * About mobile hero image.
 */
isago_sync_theme_setting_media_field(
    $value['about_hero_image_mobile'] ?? 0,
    'about_hero_image_mobile'
);
}


/*=========================================================
SYNC ONE THEME SETTING MEDIA FIELD
=========================================================*/

function isago_sync_theme_setting_media_field(
    $media_id,
    $field_key
) {

    $media_id =
        absint(
            $media_id
        );


    $field_key =
        sanitize_key(
            $field_key
        );


    /*
     * --------------------------------------------------------
     * No image
     * --------------------------------------------------------
     */

    if (!$media_id) {

        $result =
            isago_media_set_field(
                'theme_settings',
                1,
                $field_key,
                0
            );


        if (
            is_wp_error($result)
        ) {

            error_log(
                'Isago Theme Media: Could not clear '
                . $field_key
                . '. '
                . $result->get_error_message()
            );

        }

        return;

    }


    /*
     * --------------------------------------------------------
     * Make sure this is an Isago media record.
     * --------------------------------------------------------
     *
     * Legacy WordPress attachment IDs are left alone.
     */

    $media =
        isago_media_get(
            $media_id
        );


    if (!$media) {

        return;

    }


    /*
     * --------------------------------------------------------
     * Create/update central reference.
     * --------------------------------------------------------
     */

    $result =
        isago_media_set_field(
            'theme_settings',
            1,
            $field_key,
            $media_id
        );


    if (
        is_wp_error($result)
    ) {

        error_log(
            'Isago Theme Media: Could not sync '
            . $field_key
            . '. '
            . $result->get_error_message()
        );

    }

}







/*=========================================================
GET AUTHOR IMAGE URL
=========================================================*/

function isago_get_author_image_url(
    $size = 'medium'
) {

    $settings =
        isago_get_theme_settings();


    $media_id =
        absint(
            $settings['author_image'] ?? 0
        );


    if (!$media_id) {
        return '';
    }


    /*
     * New central Isago media.
     */
    $media =
        isago_media_get(
            $media_id
        );


    if ($media) {

        return
            isago_media_get_field_image_url(
                'theme_settings',
                1,
                'author_image',
                $size
            );

    }


    /*
     * Legacy WordPress attachment.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }


    return '';
}





/*=========================================================
GET CONTACT HERO IMAGE URL
=========================================================*/

function isago_get_contact_hero_image_url(
    $size = 'full'
) {

    $settings =
        isago_get_theme_settings();


    $media_id =
        absint(
            $settings['contact_hero_image'] ?? 0
        );


    if (!$media_id) {
        return '';
    }


    /*
     * New central Isago media.
     */
    $media =
        isago_media_get(
            $media_id
        );


    if ($media) {

        return
            isago_media_get_field_image_url(
                'theme_settings',
                1,
                'contact_hero_image',
                $size
            );

    }


    /*
     * Legacy WordPress attachment.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }


    return '';
}



/*=========================================================
GET ABOUT STORY IMAGE URL
=========================================================*/

function isago_get_about_story_image_url(
    $size = 'full'
) {

    $settings =
        isago_get_theme_settings();


    $media_id =
        absint(
            $settings['about_story_image'] ?? 0
        );


    if (!$media_id) {
        return '';
    }


    /*
     * New central Isago/ImageKit media.
     */
    $media =
        isago_media_get(
            $media_id
        );


    if ($media) {

        return
            isago_media_get_field_image_url(
                'theme_settings',
                1,
                'about_story_image',
                $size
            );

    }


    /*
     * Legacy WordPress attachment fallback.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }


    return '';
}


/*=========================================================
GET ABOUT DESKTOP HERO IMAGE URL
=========================================================*/

function isago_get_about_hero_image_url(
    $size = 'full'
) {

    $settings =
        isago_get_theme_settings();


    $media_id =
        absint(
            $settings['about_hero_image'] ?? 0
        );


    if (!$media_id) {
        return '';
    }


    /*
     * New central Isago/ImageKit media.
     */
    $media =
        isago_media_get(
            $media_id
        );


    if ($media) {

        return
            isago_media_get_field_image_url(
                'theme_settings',
                1,
                'about_hero_image',
                $size
            );

    }


    /*
     * Legacy WordPress attachment fallback.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }


    return '';
}


/*=========================================================
GET ABOUT MOBILE HERO IMAGE URL
=========================================================*/

function isago_get_about_hero_image_mobile_url(
    $size = 'full'
) {

    $settings =
        isago_get_theme_settings();


    $media_id =
        absint(
            $settings['about_hero_image_mobile'] ?? 0
        );


    if (!$media_id) {
        return '';
    }


    /*
     * New central Isago/ImageKit media.
     */
    $media =
        isago_media_get(
            $media_id
        );


    if ($media) {

        return
            isago_media_get_field_image_url(
                'theme_settings',
                1,
                'about_hero_image_mobile',
                $size
            );

    }


    /*
     * Legacy WordPress attachment fallback.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }


    return '';
}








<?php

if (!defined('ABSPATH')) {
    exit;
}


/*--------------------------------------------------------------
Advertisement Manager
--------------------------------------------------------------*/
  
add_action(
    'admin_menu',
    'isago_ads_menu'
);

add_action(
    'admin_init',
    'isago_register_ads_settings'
);


function isago_ads_menu()
{
    add_submenu_page(
        'isago-home',
        'Advertisement Manager',
        'Advertisement Manager',
        'manage_options',
        'isago-ads',
        'isago_ads_page'
    );
}


function isago_register_ads_settings()
{
    register_setting(
        'isago_ads_options',
        'isago_ads_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'isago_sanitize_ads_settings',
            'default'           => [],
        ]
    );
}
function isago_sanitize_ads_settings($input)
{
    if (!is_array($input)) {
        return [];
    }

    $slots = [
        'header',
        'category',
        'post',
        'tag',
        'search',
        'homepage1',
        'homepage2',
        'homepage3',
        'homepage4',
        'homepage5',
        'homepage6',
        'homepage7',
        'homepage8',
        'sidebar',
        'footer',
    ];

    $output = [];

    foreach ($slots as $slot) {

        $data = $input[$slot] ?? [];

        if (!is_array($data)) {
            $data = [];
        }

    $image_id =
    absint(
        $data['image'] ?? 0
    );

        /*
        |--------------------------------------------------------------------------
        | Sanitize Settings
        |--------------------------------------------------------------------------
        */

        $output[$slot] = [

            'enable' => !empty(
                $data['enable']
            ) ? 1 : 0,

            'label' => !empty(
                $data['label']
            ) ? 1 : 0,

            'image' => $image_id,

            'url' => esc_url_raw(
                $data['url'] ?? ''
            ),

            'alt' => sanitize_text_field(
                $data['alt'] ?? ''
            ),

            'blank' => !empty(
                $data['blank']
            ) ? 1 : 0,

            /*
            |--------------------------------------------------------------------------
            | AdSense
            |--------------------------------------------------------------------------
            |
            | Do not use sanitize_textarea_field() here.
            | AdSense requires HTML/script markup.
            |
            | This setting is protected by the WordPress Settings API
            | and the page itself requires manage_options.
            |
            */

            'adsense' => is_string(
                $data['adsense'] ?? ''
            )
                ? trim($data['adsense'])
                : '',
        ];
    }

    return $output;
}



/*=========================================================
SYNC ADVERTISEMENT MEDIA REFERENCES
=========================================================*/

add_action(
    'updated_option',
    'isago_sync_advertisement_media_references',
    10,
    3
);

function isago_sync_advertisement_media_references(
    $option,
    $old_value,
    $new_value
) {

    if (
        $option !== 'isago_ads_settings'
    ) {
        return;
    }

    if (
        !is_array($new_value)
    ) {
        return;
    }

    $slots = [

        'header',
        'category',
        'post',
        'tag',
        'search',

        'homepage1',
        'homepage2',
        'homepage3',
        'homepage4',
        'homepage5',
        'homepage6',
        'homepage7',
        'homepage8',

        'sidebar',
        'footer',

    ];


    foreach ($slots as $slot) {

        /*
         * -----------------------------------------------------
         * Get the permanent numeric object ID for this slot.
         * -----------------------------------------------------
         */

        $object_id =
            isago_get_advertisement_media_object_id(
                $slot
            );


        if (!$object_id) {
            continue;
        }


        /*
         * -----------------------------------------------------
         * Get the media ID saved in the advertisement settings.
         * -----------------------------------------------------
         */

        $media_id =
            absint(
                $new_value[$slot]['image'] ?? 0
            );


        /*
         * -----------------------------------------------------
         * No image.
         *
         * Clear the media reference for this slot.
         * The media system will automatically clean up
         * an unused ImageKit file.
         * -----------------------------------------------------
         */

        if (!$media_id) {

            $result =
                isago_media_set_field(
                    'advertisement',
                    $object_id,
                    'image',
                    0
                );


            if (
                is_wp_error($result)
            ) {

                error_log(
                    'Isago Advertisement Media: Could not clear '
                    . $slot
                    . '. '
                    . $result->get_error_message()
                );

            }

            continue;
        }


        /*
         * -----------------------------------------------------
         * Check whether this is a central Isago media record.
         * -----------------------------------------------------
         */

        $media =
            isago_media_get(
                $media_id
            );


        /*
         * -----------------------------------------------------
         * Legacy WordPress attachment.
         *
         * New ImageKit media is handled by the central media
         * system. Legacy attachment IDs remain supported by
         * the frontend, but should not be registered as
         * central ImageKit media.
         * -----------------------------------------------------
         */

        if (!$media) {

            /*
             * Make sure an old ImageKit reference for this
             * advertisement slot is removed if the slot has
             * been changed back to a legacy attachment.
             */

            $result =
                isago_media_set_field(
                    'advertisement',
                    $object_id,
                    'image',
                    0
                );


            if (
                is_wp_error($result)
            ) {

                error_log(
                    'Isago Advertisement Media: Could not clear legacy '
                    . $slot
                    . ' reference. '
                    . $result->get_error_message()
                );

            }

            continue;
        }


        /*
         * -----------------------------------------------------
         * Connect this central media record to this specific
         * advertisement slot.
         *
         * Example:
         *
         * Header:
         * advertisement / 1 / image
         *
         * Homepage 1:
         * advertisement / 6 / image
         * -----------------------------------------------------
         */

        $result =
            isago_media_set_field(
                'advertisement',
                $object_id,
                'image',
                $media_id
            );


        if (
            is_wp_error($result)
        ) {

            error_log(
                'Isago Advertisement Media: Could not sync '
                . $slot
                . '. '
                . $result->get_error_message()
            );

        }

    }
}



/*=========================================================
SYNC ADVERTISEMENT MEDIA WHEN OPTION IS FIRST CREATED
=========================================================*/

add_action(
    'added_option',
    'isago_sync_advertisement_media_references_added',
    10,
    2
);

function isago_sync_advertisement_media_references_added(
    $option,
    $value
) {

    if (
        $option !== 'isago_ads_settings'
    ) {
        return;
    }

    if (
        !is_array($value)
    ) {
        return;
    }


    $slots = [

        'header',
        'category',
        'post',
        'tag',
        'search',

        'homepage1',
        'homepage2',
        'homepage3',
        'homepage4',
        'homepage5',
        'homepage6',
        'homepage7',
        'homepage8',

        'sidebar',
        'footer',

    ];


    foreach ($slots as $slot) {

        /*
         * -----------------------------------------------------
         * Get the permanent numeric object ID.
         * -----------------------------------------------------
         */

        $object_id =
            isago_get_advertisement_media_object_id(
                $slot
            );


        if (!$object_id) {
            continue;
        }


        /*
         * -----------------------------------------------------
         * Get the media ID.
         * -----------------------------------------------------
         */

        $media_id =
            absint(
                $value[$slot]['image'] ?? 0
            );


        if (!$media_id) {
            continue;
        }


        /*
         * -----------------------------------------------------
         * Make sure this is an Isago media record.
         * -----------------------------------------------------
         */

        $media =
            isago_media_get(
                $media_id
            );


        if (!$media) {
            continue;
        }


        /*
         * -----------------------------------------------------
         * Connect the media to this advertisement slot.
         * -----------------------------------------------------
         */

        $result =
            isago_media_set_field(
                'advertisement',
                $object_id,
                'image',
                $media_id
            );


        if (
            is_wp_error($result)
        ) {

            error_log(
                'Isago Advertisement Media: Could not create '
                . $slot
                . ' reference. '
                . $result->get_error_message()
            );

        }

    }
}



function isago_ads_page()
{

    $options = isago_get_option(
        'isago_ads_settings'
    );


     $slots = [

    'header'   => 'Header Banner',

    'category' => 'Category Banner',

     'post' => 'Post Banner',

    'tag'      => 'Tag Banner',

     'search'      => 'Search Banner',

    'homepage1' => 'Homepage Banner 1',
    'homepage2' => 'Homepage Banner 2',
    'homepage3' => 'Homepage Banner 3',
    'homepage4' => 'Homepage Banner 4',
    'homepage5' => 'Homepage Banner 5',
    'homepage6' => 'Homepage Banner 6',
    'homepage7' => 'Homepage Banner 7',
    'homepage8' => 'Homepage Banner 8',

    'sidebar' => 'Sidebar Banner',

    'footer' => 'Footer Banner'
    ];

?>

<div class="wrap isago-admin isago-ads-manager">

    <h1>Advertisement Manager</h1>

    <form
        method="post"
        action="options.php"
        class="isago-ads-form"
    >

        <?php settings_fields(
            'isago_ads_options'
        ); ?>


        <div class="isago-builder-sections">

            <?php foreach ($slots as $key => $label) : ?>

                <div class="isago-builder-card">

                    <!-- =================================================
                    ACCORDION HEADER
                    ================================================= -->

        <button
    type="button"
    class="isago-builder-card-header"
    aria-expanded="false"
>

    <span class="isago-builder-card-title">

        <span
            class="dashicons dashicons-megaphone isago-builder-card-symbol"
            aria-hidden="true"
        ></span>

        <span>
            <?php echo esc_html($label); ?>
        </span>

    </span>

    <span
        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
        aria-hidden="true"
    ></span>

</button>


                    <!-- =================================================
                    ACCORDION CONTENT
                    ================================================= -->

                    <div class="isago-builder-card-content">

                        <table class="form-table">

                            <tbody>


                                <!-- Enable -->

                                <tr>

                                    <th>
                                        Enable
                                    </th>

                                    <td>

                                        <label>

                                            <input
                                                type="checkbox"
                                                name="isago_ads_settings[<?php echo esc_attr($key); ?>][enable]"
                                                value="1"
                                                <?php checked(
                                                    $options[$key]['enable'] ?? 0,
                                                    1
                                                ); ?>
                                            >

                                            Enable Banner

                                        </label>

                                    </td>

                                </tr>




<tr>

    <th>
        Advertisement Label
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_ads_settings[<?php echo esc_attr($key); ?>][label]"
                value="1"
                <?php checked(
                    $options[$key]['label'] ?? 0,
                    1
                ); ?>
            >

            Show "Advertisement" label

        </label>

    </td>

</tr>





<!-- =================================================
Banner Image
================================================= -->

<tr>

    <th>
        Banner Image
    </th>

    <td>

        <?php

        /*
         * --------------------------------------------------------
         * ADVERTISEMENT MEDIA ID
         * --------------------------------------------------------
         */

        $ad_image_id =
            absint(
                $options[$key]['image'] ?? 0
            );
$ad_object_id =
    isago_get_advertisement_media_object_id(
        $key
    );

        /*
         * --------------------------------------------------------
         * IMAGE URL
         * --------------------------------------------------------
         */

        $ad_image_url = '';


        if ($ad_image_id) {

            /*
             * New central Isago media.
             */
            $media =
                isago_media_get(
                    $ad_image_id
                );


            if ($media) {

                /*
                 * The actual media reference is:
                 *
                 * advertisement / slot ID / image
                 */
                $ad_image_url =
    isago_media_get_field_image_url(
        'advertisement',
        $ad_object_id,
        'image',
        'medium'
    );

            }


            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$ad_image_url
                &&
                wp_attachment_is_image(
                    $ad_image_id
                )
            ) {

                $ad_image_url =
                    wp_get_attachment_image_url(
                        $ad_image_id,
                        'medium'
                    );

            }

        }


        /*
         * --------------------------------------------------------
         * IMAGEKIT ACCOUNT
         * --------------------------------------------------------
         */

        $ad_account_id =
            isago_get_imagekit_media_account_id(
                $ad_image_id
            );

        ?>


        <!--=================================================
        CENTRAL MEDIA ID
        =================================================-->

        <input
            type="hidden"
            id="isago-ad-image-<?php echo esc_attr($key); ?>"
            class="isago-imagekit-media-id"
            name="isago_ads_settings[<?php echo esc_attr($key); ?>][image]"
            value="<?php echo esc_attr(
                $ad_image_id
            ); ?>"
        >


        <!--=================================================
        IMAGEKIT ACCOUNT
        =================================================-->

        <?php

        isago_render_imagekit_account_selector(
            [
                'id' =>
                    'isago-ad-image-account-' .
                    $key,

                'selected' =>
                    $ad_account_id,

                'label' =>
                    'ImageKit Account',

                'class' =>
                    'isago-imagekit-account-selector',

                'style' =>
                    'width:100%; max-width:320px; margin-bottom:10px;',
            ]
        );

        ?>


        <!--=================================================
        PREVIEW
        =================================================-->

        <div
            id="isago-ad-image-preview-<?php echo esc_attr($key); ?>"
            class="isago-media-preview"
            style="
                <?php
                echo $ad_image_url
                    ? ''
                    : 'display:none;';
                ?>
                margin-bottom:10px;width:200px;
            "
        >

            <?php if ($ad_image_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $ad_image_url
                    ); ?>"
                    alt="<?php echo esc_attr($label); ?>"
                    style="
                       
                        width:200px;
                        height:auto;
                        display:block;
                    "
                >

            <?php endif; ?>

        </div>


        <!--=================================================
        UPLOAD
        =================================================-->

        <button
            type="button"
            class="button button-primary isago-imagekit-upload-button"
            data-target="#isago-ad-image-<?php echo esc_attr($key); ?>"
            data-preview="#isago-ad-image-preview-<?php echo esc_attr($key); ?>"
            data-type="image"
            data-account-selector="#isago-ad-image-account-<?php echo esc_attr($key); ?>"
            data-object-type="advertisement"
            data-object-id="<?php echo esc_attr($ad_object_id); ?>"
            data-field-key="image"
        >

            <?php

            echo $ad_image_id
                ? 'Change Banner'
                : 'Upload Banner';

            ?>

        </button>


        <!--=================================================
        REMOVE
        =================================================-->

        <button
            type="button"
            class="button isago-imagekit-remove-button"
            data-target="#isago-ad-image-<?php echo esc_attr($key); ?>"
            data-preview="#isago-ad-image-preview-<?php echo esc_attr($key); ?>"
            data-type="image"
            data-object-type="advertisement"
           data-object-id="<?php echo esc_attr($ad_object_id); ?>"
            data-field-key="image"
            <?php if (!$ad_image_id) : ?>
                disabled
            <?php endif; ?>
        >

            Remove Banner

        </button>

    </td>

</tr>





                                <!-- Destination URL -->

                                <tr>

                                    <th>
                                        Destination URL
                                    </th>

                                    <td>

                                        <input
                                            class="regular-text"
                                            type="url"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][url]"
                                            value="<?php echo esc_attr(
                                                $options[$key]['url'] ?? ''
                                            ); ?>"
                                        >

                                    </td>

                                </tr>


                                <!-- Alt Text -->

                                <tr>

                                    <th>
                                        Alt Text
                                    </th>

                                    <td>

                                        <input
                                            class="regular-text"
                                            type="text"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][alt]"
                                            value="<?php echo esc_attr(
                                                $options[$key]['alt'] ?? ''
                                            ); ?>"
                                        >

                                    </td>

                                </tr>


                                <!-- Open New Tab -->

                                <tr>

                                    <th>
                                        Open in New Tab
                                    </th>

                                    <td>

                                        <label>

                                            <input
                                                type="checkbox"
                                                name="isago_ads_settings[<?php echo esc_attr($key); ?>][blank]"
                                                value="1"
                                                <?php checked(
                                                    $options[$key]['blank'] ?? 0,
                                                    1
                                                ); ?>
                                            >

                                            Open in new tab

                                        </label>

                                    </td>

                                </tr>


                                <!-- AdSense -->

                                <tr>

                                    <th>
                                        Google AdSense Code
                                    </th>

                                    <td>

                                        <textarea
                                            rows="6"
                                            cols="60"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][adsense]"
                                        ><?php

                                            echo esc_textarea(
                                                $options[$key]['adsense'] ?? ''
                                            );

                                        ?></textarea>

                                    </td>

                                </tr>


                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div class="isago-builder-submit">

            <?php submit_button(
                'Save Advertisement Settings'
            ); ?>

        </div>

    </form>

</div>

<?php

}
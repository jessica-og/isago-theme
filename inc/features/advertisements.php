<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ADVERTISEMENT SLOT OBJECT IDS
=========================================================*/

/**
 * Return the permanent numeric media object ID
 * for an advertisement slot.
 *
 * These IDs must NEVER be changed or reordered.
 *
 * @param string $slot
 * @return int
 */
function isago_get_advertisement_media_object_id($slot)
{
    $slot_ids = [

        'header'    => 1,
        'category'  => 2,
        'post'      => 3,
        'tag'       => 4,
        'search'    => 5,

        'homepage1' => 6,
        'homepage2' => 7,
        'homepage3' => 8,
        'homepage4' => 9,
        'homepage5' => 10,
        'homepage6' => 11,
        'homepage7' => 12,
        'homepage8' => 13,

        'sidebar'   => 14,
        'footer'    => 15,

    ];

    return absint(
        $slot_ids[$slot] ?? 0
    );
}


/*=========================================================
GET ADVERTISEMENT IMAGE URL
=========================================================*/

/**
 * Resolve an advertisement image.
 *
 * New advertisements use the central Isago media system.
 * Legacy WordPress attachment IDs remain supported.
 *
 * @param mixed  $image Image/media ID.
 * @param string $slot  Advertisement slot.
 *
 * @return string
 */

function isago_get_ad_image_url(
    $image,
    $slot = ''
) {

    $image_id =
        absint(
            $image
        );


    if (!$image_id) {
        return '';
    }


    /*
     * --------------------------------------------------------
     * New central Isago media
     * --------------------------------------------------------
     */

    if (
        $slot !== ''
        &&
        isago_media_get(
            $image_id
        )
    ) {

      $object_id =
    isago_get_advertisement_media_object_id(
        $slot
    );

if (!$object_id) {
    return '';
}

return
    isago_media_get_field_image_url(
        'advertisement',
        $object_id,
        'image',
        'full'
    );

    }


    /*
     * --------------------------------------------------------
     * Legacy WordPress attachment
     * --------------------------------------------------------
     */

    if (
        wp_attachment_is_image(
            $image_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $image_id,
                'full'
            );

    }


    /*
     * --------------------------------------------------------
     * Legacy external URL
     * --------------------------------------------------------
     */

    if (
        is_string($image)
        &&
        filter_var(
            $image,
            FILTER_VALIDATE_URL
        )
    ) {

        return esc_url(
            $image
        );

    }


    return '';
}


/**
 * Get advertisement settings for a specific slot.
 *
 * @param string $slot Advertisement slot.
 * @return array
 */
function isago_get_ad($slot)
{

    /*
    ---------------------------------------------------------
    Get Advertisement Manager settings
    ---------------------------------------------------------
    */

    $options =
        isago_get_option(
            'isago_ads_settings'
        );


    /*
    ---------------------------------------------------------
    Invalid / Missing Slot
    ---------------------------------------------------------
    */

    if (
        !is_array($options) ||
        empty($options[$slot]) ||
        !is_array($options[$slot])
    ) {

        return [

            'enable'  => 0,

            'label'   => 0,

            'image'   => '',

            'url'     => '',

            'alt'     => 'Advertisement',

            'blank'   => 0,

            'adsense' => '',

        ];

    }


    /*
    ---------------------------------------------------------
    Raw Image Value
    ---------------------------------------------------------
    
    Advertisement Manager stores the attachment ID here.
    ---------------------------------------------------------
    */

    $image_value =
        $options[$slot]['image']
        ?? '';


    /*
    ---------------------------------------------------------
    Resolve Image
    ---------------------------------------------------------
    */

   $image =
    isago_get_ad_image_url(
        $image_value,
        $slot
    );


    /*
    ---------------------------------------------------------
    Return Normalized Advertisement Data
    ---------------------------------------------------------
    */

    return [

        'enable' =>
            !empty(
                $options[$slot]['enable']
            )
                ? 1
                : 0,


        'label' =>
            !empty(
                $options[$slot]['label']
            )
                ? 1
                : 0,


        'image' =>
            $image,


        'url' =>
            !empty(
                $options[$slot]['url']
            )
                ? esc_url(
                    $options[$slot]['url']
                )
                : '',


        'alt' =>
            !empty(
                $options[$slot]['alt']
            )
                ? sanitize_text_field(
                    $options[$slot]['alt']
                )
                : 'Advertisement',


        'blank' =>
            !empty(
                $options[$slot]['blank']
            )
                ? 1
                : 0,


        'adsense' =>
            !empty(
                $options[$slot]['adsense']
            )
                ? $options[$slot]['adsense']
                : '',

    ];

}


/**
 * Check whether an advertisement slot is enabled.
 *
 * @param string $slot Advertisement slot.
 * @return bool
 */
function isago_ad_enabled($slot)
{

    $ad =
        isago_get_ad(
            $slot
        );


    return !empty(
        $ad['enable']
    );

}


/*=========================================================
ADVERTISEMENT RENDERER
=========================================================*/


/**
 * Render an advertisement.
 *
 * @param string      $slot          Advertisement slot.
 * @param string      $wrapper_class Wrapper CSS class.
 * @param string      $image_class   Image CSS class.
 * @param bool|null   $show_label    Override label setting.
 * @return string
 */
function isago_render_ad(
    $slot,
    $wrapper_class = '',
    $image_class = '',
    $show_label = null
) {

    /*
    ---------------------------------------------------------
    Get Advertisement
    ---------------------------------------------------------
    */

    $ad =
        isago_get_ad(
            $slot
        );


    /*
    ---------------------------------------------------------
    Advertisement Disabled
    ---------------------------------------------------------
    */

    if (
        empty(
            $ad['enable']
        )
    ) {

        return '';

    }


    /*
    ---------------------------------------------------------
    No Advertisement Content
    ---------------------------------------------------------
    */

    if (
        empty(
            $ad['adsense']
        ) &&
        empty(
            $ad['image']
        )
    ) {

        return '';

    }


    /*
    ---------------------------------------------------------
    Label Setting
    ---------------------------------------------------------
    */

    if (
        $show_label === null
    ) {

        $show_label =
            !empty(
                $ad['label']
            );

    }


    /*
    ---------------------------------------------------------
    Start Output Buffer
    ---------------------------------------------------------
    */

    ob_start();

    ?>


    <?php if ($show_label) : ?>

        <div class="ad-label">

            Advertisement

        </div>

    <?php endif; ?>


    <div
        class="<?php echo esc_attr($wrapper_class); ?>"
    >

        <?php

        /*
        -----------------------------------------------------
        Google AdSense
        -----------------------------------------------------
        */

        if (
            !empty(
                $ad['adsense']
            )
        ) :

            /*
             * AdSense code is intentionally output directly.
             *
             * Administrators with manage_options access
             * control this field through the Advertisement
             * Manager.
             */
            ?>

            <?php
            echo $ad['adsense'];
            ?>


        <?php

        /*
        -----------------------------------------------------
        Image Advertisement
        -----------------------------------------------------
        */

        elseif (
            !empty(
                $ad['image']
            )
        ) :

        ?>

            <?php if (!empty($ad['url'])) : ?>

                <a
                    class="ad-link"
                    href="<?php echo esc_url($ad['url']); ?>"
                    <?php
                    if (!empty($ad['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                >

            <?php endif; ?>


            <img
                class="<?php echo esc_attr($image_class); ?>"
                src="<?php echo esc_url($ad['image']); ?>"
                alt="<?php echo esc_attr($ad['alt']); ?>"
                loading="lazy"
                decoding="async"
            >


            <?php if (!empty($ad['url'])) : ?>

                </a>

            <?php endif; ?>


        <?php endif; ?>

    </div>


    <?php

    /*
    ---------------------------------------------------------
    Return Generated HTML
    ---------------------------------------------------------
    */

    return ob_get_clean();

}
<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ISAGO ADVERTISEMENT HELPERS
=========================================================*/

/**
 * Get advertisement settings for a specific slot.
 *
 * @param string $slot Advertisement slot.
 * @return array
 */
function isago_get_ad($slot)
{
    $options = isago_get_option('isago_ads_settings');

    if (
        !is_array($options) ||
        empty($options[$slot]) ||
        !is_array($options[$slot])
    ) {
        return [
            'enable'   => 0,
            'image'    => ISAGO_URI . '/assets/images/placeholder.png',
            'url'      => '',
            'alt'      => 'Advertisement',
            'blank'    => 0,
            'adsense'  => '',
        ];
    }

    return [
        'enable'  => !empty($options[$slot]['enable']) ? 1 : 0,

        'image'   => !empty($options[$slot]['image'])
            ? esc_url($options[$slot]['image'])
            : ISAGO_URI . '/assets/images/placeholder.png',

        'url'     => !empty($options[$slot]['url'])
            ? esc_url($options[$slot]['url'])
            : '',

        'alt'     => !empty($options[$slot]['alt'])
            ? sanitize_text_field($options[$slot]['alt'])
            : 'Advertisement',

        'blank'   => !empty($options[$slot]['blank']) ? 1 : 0,

        'adsense' => !empty($options[$slot]['adsense'])
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
    $ad = isago_get_ad($slot);

    return !empty($ad['enable']);
}
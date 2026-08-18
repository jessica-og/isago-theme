<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_option(
    'isago_theme_settings'
);

?>

<tr>

    <th colspan="2">

        <h2>
            Contact Page
        </h2>

    </th>

</tr>


<!-- =====================================================
Hero Image
===================================================== -->

<tr>

    <th>
        Hero Image
    </th>

    <td>

        <input
            type="text"
            class="isago-image-url regular-text"
            name="isago_theme_settings[contact_hero_image]"
            value="<?php echo esc_attr(
                $settings['contact_hero_image'] ?? ''
            ); ?>"
        >

        <button
            type="button"
            class="button isago-upload-button"
        >

            Upload Image

        </button>

        <br>
        <br>

        <img
            class="isago-preview"
            src="<?php echo esc_url(
                $settings['contact_hero_image'] ?? ''
            ); ?>"
            style="
                max-width:200px;
                display:<?php
                    echo !empty(
                        $settings['contact_hero_image']
                    )
                        ? 'block'
                        : 'none';
                ?>;
                border-radius:6px;
            "
        >

    </td>

</tr>


<!-- =====================================================
Email
===================================================== -->

<tr>

    <th>
        Email
    </th>

    <td>

        <input
            type="text"
            class="regular-text"
            name="isago_theme_settings[contact_email]"
            value="<?php echo esc_attr(
                $settings['contact_email'] ?? ''
            ); ?>"
        >

    </td>

</tr>


<!-- =====================================================
Phone
===================================================== -->

<tr>

    <th>
        Phone
    </th>

    <td>

        <input
            type="text"
            class="regular-text"
            name="isago_theme_settings[contact_phone]"
            value="<?php echo esc_attr(
                $settings['contact_phone'] ?? ''
            ); ?>"
        >

    </td>

</tr>


<!-- =====================================================
Address
===================================================== -->

<tr>

    <th>
        Address
    </th>

    <td>

        <input
            type="text"
            class="regular-text"
            name="isago_theme_settings[contact_address]"
            value="<?php echo esc_attr(
                $settings['contact_address'] ?? ''
            ); ?>"
        >

    </td>

</tr>
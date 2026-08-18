<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<tr>

    <th colspan="2">

        <h2>
            Branding
        </h2>

    </th>

</tr>


<tr>

    <th>
        Site Logo Width
    </th>

    <td>

        <input
            type="number"
            min="80"
            max="500"
            name="isago_theme_settings[logo_width]"
            value="<?php echo esc_attr(
                $options['logo_width'] ?? 180
            ); ?>"
        >

        px

    </td>

</tr>


<tr>

    <th>
        Primary Color
    </th>

    <td>

        <input
            class="isago-color-picker"
            type="text"
            name="isago_theme_settings[site_accent]"
            value="<?php echo esc_attr(
                $options['site_accent'] ?? '#00d4f5'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Secondary Color
    </th>

    <td>

        <input
            class="isago-color-picker"
            type="text"
            name="isago_theme_settings[site_accent_alt]"
            value="<?php echo esc_attr(
                $options['site_accent_alt'] ?? '#36fefb'
            ); ?>"
        >

    </td>

</tr>
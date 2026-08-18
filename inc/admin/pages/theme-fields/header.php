<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<tr>

    <th>
        Sticky Header
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[sticky_header]"
                value="1"
                <?php checked(
                    $options['sticky_header'] ?? 1,
                    1
                ); ?>
            >

            Enable Sticky Header

        </label>

    </td>

</tr>


<tr>

    <th>
        Enable Dark Mode
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[dark_mode]"
                value="1"
                <?php checked(
                    $options['dark_mode'] ?? 1,
                    1
                ); ?>
            >

            Enable Dark Theme

        </label>

    </td>

</tr>


<tr>

    <th>
        Show Search Button
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[header_search]"
                value="1"
                <?php checked(
                    $options['header_search'] ?? 1,
                    1
                ); ?>
            >

            Show Search

        </label>

    </td>

</tr>


<tr>

    <th>
        Show Dark Toggle
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[header_dark_toggle]"
                value="1"
                <?php checked(
                    $options['header_dark_toggle'] ?? 1,
                    1
                ); ?>
            >

            Show Toggle Button

        </label>

    </td>

</tr>


<tr>

    <th>
        Show Subscribe Button
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[header_subscribe]"
                value="1"
                <?php checked(
                    $options['header_subscribe'] ?? 1,
                    1
                ); ?>
            >

            Show Subscribe Button

        </label>

    </td>

</tr>
<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<tr>

    <th>
        Footer Description
    </th>

    <td>

        <textarea
            rows="4"
            cols="60"
            name="isago_theme_settings[footer_description]"
        ><?php

            echo esc_textarea(
                $options['footer_description']
                ?? 'Independent journalism, in-depth stories, and diverse perspectives on AI.'
            );

        ?></textarea>

    </td>

</tr>


<tr>

    <th>
        Footer Newsletter
    </th>

    <td>

        <label>

            <input
                type="checkbox"
                name="isago_theme_settings[footer_newsletter]"
                value="1"
                <?php checked(
                    $options['footer_newsletter'] ?? 1,
                    1
                ); ?>
            >

            Enable Newsletter

        </label>

    </td>

</tr>


<tr>

    <th>
        Newsletter Heading
    </th>

    <td>

        <input
            class="regular-text"
            type="text"
            name="isago_theme_settings[footer_newsletter_title]"
            value="<?php echo esc_attr(
                $options['footer_newsletter_title']
                ?? 'Newsletter'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Newsletter Description
    </th>

    <td>

        <textarea
            rows="3"
            cols="60"
            name="isago_theme_settings[footer_newsletter_text]"
        ><?php

            echo esc_textarea(
                $options['footer_newsletter_text']
                ?? 'Get the latest AI stories delivered to your inbox.'
            );

        ?></textarea>

    </td>

</tr>


<tr>

    <th>
        Subscribe Button
    </th>

    <td>

        <input
            class="regular-text"
            type="text"
            name="isago_theme_settings[footer_newsletter_button]"
            value="<?php echo esc_attr(
                $options['footer_newsletter_button']
                ?? 'Subscribe'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Copyright Text
    </th>

    <td>

        <input
            class="regular-text"
            type="text"
            name="isago_theme_settings[copyright]"
            value="<?php echo esc_attr(
                $options['copyright']
                ?? '© Isago. All rights reserved.'
            ); ?>"
        >

    </td>

</tr>
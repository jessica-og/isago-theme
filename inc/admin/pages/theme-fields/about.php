<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<?php $settings = isago_get_option('isago_theme_settings'); ?>
<tr>
    <th colspan="2">
        <hr>
        <h2>About Page</h2>
    </th>
</tr>

<tr>
    <th>About Title</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_title]"
            value="<?php echo esc_attr($settings['about_title'] ?? 'About Isago'); ?>"
            class="regular-text">
    </td>
</tr>

<tr>
    <th>About Description</th>
    <td>
        <textarea rows="4" cols="60"
            name="isago_theme_settings[about_desc]"><?php
            echo esc_textarea($settings['about_desc'] ?? '');
        ?></textarea>
    </td>
</tr>

<tr>
    <th>CTA Text</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_cta_text]"
            value="<?php echo esc_attr($settings['about_cta_text'] ?? 'Explore Now'); ?>">
    </td>
</tr>

<tr>
    <th>CTA Link</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_cta_link]"
            value="<?php echo esc_attr($settings['about_cta_link'] ?? '#'); ?>">
    </td>
</tr>







<tr>
    <th>Story Title</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_story_title]"
            value="<?php echo esc_attr($settings['about_story_title'] ?? 'Our Story'); ?>">
    </td>
</tr>

<tr>
    <th>Story Text</th>
    <td>
        <textarea rows="6" cols="60"
            name="isago_theme_settings[about_story_text]"><?php
            echo esc_textarea($settings['about_story_text'] ?? '');
        ?></textarea>
    </td>
</tr>



<tr>
     <th>Story Image URL</th>
    <td>

        <input type="text"
            class="isago-image-url regular-text"
            name="isago_theme_settings[about_story_image]"
            value="<?php echo esc_attr($settings['about_story_image'] ?? ''); ?>">

        <button class="button isago-upload-button">
            Upload Image
        </button>

        <br><br>

        <img class="isago-preview"
            src="<?php echo esc_url($settings['about_story_image'] ?? ''); ?>"
            style="max-width:200px; display:<?php echo !empty($settings['about_story_image']) ? 'block' : 'none'; ?>; border-radius:6px;">

    </td>
</tr>


<?php
/* =====================================================
HERO IMAGE
===================================================== */
?>

<tr>
    <th>Hero Image</th>
    <td>

        <input type="text"
            class="isago-image-url regular-text"
            name="isago_theme_settings[about_hero_image]"
            value="<?php echo esc_attr($settings['about_hero_image'] ?? ''); ?>">

        <button class="button isago-upload-button">
            Upload Image
        </button>

        <br><br>

        <img class="isago-preview"
            src="<?php echo esc_url($settings['about_hero_image'] ?? ''); ?>"
            style="max-width:200px; display:<?php echo !empty($settings['about_hero_image']) ? 'block' : 'none'; ?>; border-radius:6px;">

    </td>
</tr>

<?php
/* =====================================================
SIDEBAR
===================================================== */
?>

<tr>
    <th>Sidebar Title</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_sidebar_title]"
            value="<?php echo esc_attr($settings['about_sidebar_title'] ?? 'About Isago'); ?>">
    </td>
</tr>

<tr>
    <th>Sidebar Description</th>
    <td>
        <textarea rows="4" cols="60"
            name="isago_theme_settings[about_sidebar_desc]"><?php
            echo esc_textarea($settings['about_sidebar_desc'] ?? '');
        ?></textarea>
    </td>
</tr>

<tr>
    <th>Founded</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_founded]"
            value="<?php echo esc_attr($settings['about_founded'] ?? '2024'); ?>">
    </td>
</tr>

<tr>
    <th>Headquarters</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_hq]"
            value="<?php echo esc_attr($settings['about_hq'] ?? 'Worldwide'); ?>">
    </td>
</tr>

<tr>
    <th>Focus</th>
    <td>
        <input type="text"
            name="isago_theme_settings[about_focus]"
            value="<?php echo esc_attr($settings['about_focus'] ?? 'AI Prompts, Tools'); ?>">
    </td>
</tr>







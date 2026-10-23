<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings =
    isago_get_option(
        'isago_theme_settings'
    );

?>

<tr>

    <th colspan="2">

        <hr>

        <h2>About Page</h2>

    </th>
  
</tr>


<tr>

    <th>
        About Title
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_title]"
            value="<?php echo esc_attr(
                $settings['about_title']
                ?? 'About Isago'
            ); ?>"
            class="regular-text"
        >

    </td>

</tr>


<tr>

    <th>
        About Description
    </th>

    <td>

        <textarea
            rows="4"
            cols="60"
            name="isago_theme_settings[about_desc]"
        ><?php

            echo esc_textarea(
                $settings['about_desc'] ?? ''
            );

        ?></textarea>

    </td>

</tr>


<tr>

    <th>
        CTA Text
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_cta_text]"
            value="<?php echo esc_attr(
                $settings['about_cta_text']
                ?? 'Explore Now'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th> 
        CTA Link
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_cta_link]"
            value="<?php echo esc_attr(
                $settings['about_cta_link']
                ?? '#'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Story Title
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_story_title]"
            value="<?php echo esc_attr(
                $settings['about_story_title']
                ?? 'Our Story'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Story Text
    </th>

    <td>

        <textarea
            rows="6"
            cols="60"
            name="isago_theme_settings[about_story_text]"
        ><?php

            echo esc_textarea(
                $settings['about_story_text'] ?? ''
            );

        ?></textarea>

    </td>

</tr>


<!-- =====================================================
STORY IMAGE
===================================================== -->

<!-- =====================================================
STORY IMAGE
===================================================== -->

<tr>

    <th>
        Story Image
    </th>

    <td>

        <?php

        $about_story_image_id =
            absint(
                $settings['about_story_image'] ?? 0
            );

        $about_story_image_url = '';

        if ($about_story_image_id) {

            /*
             * New Isago/ImageKit media.
             */
            $media =
                isago_media_get(
                    $about_story_image_id
                );

            if ($media) {

                $about_story_image_url =
                    isago_media_get_field_image_url(
                        'theme_settings',
                        1,
                        'about_story_image',
                        'medium'
                    );

            }

            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$about_story_image_url
                &&
                wp_attachment_is_image(
                    $about_story_image_id
                )
            ) {

                $about_story_image_url =
                    wp_get_attachment_image_url(
                        $about_story_image_id,
                        'medium'
                    );

            }

        }


        $about_story_account_id =
            isago_get_imagekit_media_account_id(
                $about_story_image_id
            );

        ?>


        <input
            type="hidden"
            id="isago-about-story-image"
            class="isago-imagekit-media-id"
            name="isago_theme_settings[about_story_image]"
            value="<?php echo esc_attr(
                $about_story_image_id
            ); ?>"
        >


        <?php

        isago_render_imagekit_account_selector(
            [
                'id'       => 'isago-about-story-image-account',
                'selected' => $about_story_account_id,
                'label'    => 'ImageKit Account',
                'class'    => 'isago-imagekit-account-selector',
                'style'    => 'width:100%; margin-bottom:10px;',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago-about-story-image-folder',
    'selected' => '/Isago-Media/Site-Assets/',
    'label'    => 'Destination folder',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
        ?>


        <div
            id="isago-about-story-image-preview"
            class="isago-media-preview"
            style="
                margin-top:15px;
                <?php
                echo $about_story_image_url
                    ? ''
                    : 'display:none;';
                ?>
            "
        >

            <?php if ($about_story_image_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $about_story_image_url
                    ); ?>"
                    alt="About story image preview"
                    style="
                        max-width:200px;
                        width:100%;
                        height:auto;
                        display:block;
                        border-radius:6px;
                    "
                >

            <?php endif; ?>

        </div>


        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago-about-story-image"
                data-preview="#isago-about-story-image-preview"
                data-folder-selector="#isago-about-story-image-folder"
                data-type="image"
                data-account-selector="#isago-about-story-image-account"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_story_image"
            >

                <?php

                echo $about_story_image_id
                    ? 'Change Story Image'
                    : 'Upload Story Image';

                ?>

            </button>

        </p>


<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-about-story-image"
    data-preview="#isago-about-story-image-preview"
    data-type="image"
    data-account-selector="#isago-about-story-image-account"
    data-folder-selector="#isago-about-story-image-folder"
>
    Select Existing ImageKit Image
</button>

        <p>

            <button
                type="button"
                class="button isago-imagekit-remove-button"
                data-target="#isago-about-story-image"
                data-preview="#isago-about-story-image-preview"
                data-type="image"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_story_image"
                <?php if (!$about_story_image_id) : ?>
                    disabled
                <?php endif; ?>
            >

                Remove Story Image

            </button>

        </p>


        <p class="description">

            Upload the image displayed in the About story
            section directly to ImageKit.

        </p>

    </td>

</tr>


<!-- =====================================================
ABOUT HERO IMAGES
===================================================== -->

<tr>

    <th colspan="2">

        <hr>

        <h3>
            About Hero Images
        </h3>

        <p class="description">

            Use the first image for large screens and the second image
            for tablets and mobile devices.

        </p>

    </th>

</tr>


<!-- =====================================================
DESKTOP HERO IMAGE
===================================================== -->

<!-- =====================================================
DESKTOP HERO IMAGE
===================================================== -->

<tr>

    <th>
        Desktop Hero Image
    </th>

    <td>

        <?php

        $about_hero_image_id =
            absint(
                $settings['about_hero_image'] ?? 0
            );

        $about_hero_image_url = '';

        if ($about_hero_image_id) {

            /*
             * New Isago/ImageKit media.
             */
            $media =
                isago_media_get(
                    $about_hero_image_id
                );

            if ($media) {

                $about_hero_image_url =
                    isago_media_get_field_image_url(
                        'theme_settings',
                        1,
                        'about_hero_image',
                        'large'
                    );

            }

            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$about_hero_image_url
                &&
                wp_attachment_is_image(
                    $about_hero_image_id
                )
            ) {

                $about_hero_image_url =
                    wp_get_attachment_image_url(
                        $about_hero_image_id,
                        'large'
                    );

            }

        }


        $about_hero_account_id =
            isago_get_imagekit_media_account_id(
                $about_hero_image_id
            );

        ?>


        <input
            type="hidden"
            id="isago-about-hero-image"
            class="isago-imagekit-media-id"
            name="isago_theme_settings[about_hero_image]"
            value="<?php echo esc_attr(
                $about_hero_image_id
            ); ?>"
        >


        <?php

        isago_render_imagekit_account_selector(
            [
                'id'       => 'isago-about-hero-image-account',
                'selected' => $about_hero_account_id,
                'label'    => 'ImageKit Account',
                'class'    => 'isago-imagekit-account-selector',
                'style'    => 'width:100%; margin-bottom:10px;',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago-about-hero-image-folder',
    'selected' => '/Isago-Media/Site-Assets/',
    'label'    => 'Destination folder',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
        ?>


        <div
            id="isago-about-hero-image-preview"
            class="isago-media-preview"
            style="
                margin-top:15px;
                <?php
                echo $about_hero_image_url
                    ? ''
                    : 'display:none;';
                ?>
            "
        >

            <?php if ($about_hero_image_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $about_hero_image_url
                    ); ?>"
                    alt="Desktop About hero preview"
                    style="
                        max-width:320px;
                        width:100%;
                        height:auto;
                        display:block;
                        border-radius:6px;
                    "
                >

            <?php endif; ?>

        </div>


        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago-about-hero-image"
                data-preview="#isago-about-hero-image-preview"
                data-folder-selector="#isago-about-hero-image-folder"
                data-type="image"
                data-account-selector="#isago-about-hero-image-account"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_hero_image"
            >

                <?php

                echo $about_hero_image_id
                    ? 'Change Desktop Hero Image'
                    : 'Upload Desktop Hero Image';

                ?>

            </button>

        </p>


<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-about-hero-image"
    data-preview="#isago-about-hero-image-preview"
    data-type="image"
    data-account-selector="#isago-about-hero-image-account"
    data-folder-selector="#isago-about-hero-image-folder"
>
    Select Existing ImageKit Image
</button>

        <p>

            <button
                type="button"
                class="button isago-imagekit-remove-button"
                data-target="#isago-about-hero-image"
                data-preview="#isago-about-hero-image-preview"
                data-type="image"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_hero_image"
                <?php if (!$about_hero_image_id) : ?>
                    disabled
                <?php endif; ?>
            >

                Remove Desktop Hero Image

            </button>

        </p>


        <p class="description">

            Used on desktop and larger screens.

        </p>

    </td>

</tr>


<!-- =====================================================
MOBILE HERO IMAGE
===================================================== -->
<!-- =====================================================
MOBILE HERO IMAGE
===================================================== -->

<tr>

    <th>
        Mobile Hero Image
    </th>

    <td>

        <?php

        $about_hero_image_mobile_id =
            absint(
                $settings['about_hero_image_mobile'] ?? 0
            );

        $about_hero_image_mobile_url = '';

        if ($about_hero_image_mobile_id) {

            /*
             * New Isago/ImageKit media.
             */
            $media =
                isago_media_get(
                    $about_hero_image_mobile_id
                );

            if ($media) {

                $about_hero_image_mobile_url =
                    isago_media_get_field_image_url(
                        'theme_settings',
                        1,
                        'about_hero_image_mobile',
                        'large'
                    );

            }

            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$about_hero_image_mobile_url
                &&
                wp_attachment_is_image(
                    $about_hero_image_mobile_id
                )
            ) {

                $about_hero_image_mobile_url =
                    wp_get_attachment_image_url(
                        $about_hero_image_mobile_id,
                        'large'
                    );

            }

        }


        $about_hero_mobile_account_id =
            isago_get_imagekit_media_account_id(
                $about_hero_image_mobile_id
            );

        ?>


        <input
            type="hidden"
            id="isago-about-hero-image-mobile"
            class="isago-imagekit-media-id"
            name="isago_theme_settings[about_hero_image_mobile]"
            value="<?php echo esc_attr(
                $about_hero_image_mobile_id
            ); ?>"
        >


        <?php

        isago_render_imagekit_account_selector(
            [
                'id'       => 'isago-about-hero-image-mobile-account',
                'selected' => $about_hero_mobile_account_id,
                'label'    => 'ImageKit Account',
                'class'    => 'isago-imagekit-account-selector',
                'style'    => 'width:100%; margin-bottom:10px;',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago-about-hero-image-mobile-folder',
    'selected' => '/Isago-Media/Site-Assets/',
    'label'    => 'Destination folder',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
        ?>


        <div
            id="isago-about-hero-image-mobile-preview"
            class="isago-media-preview"
            style="
                margin-top:15px;
                <?php
                echo $about_hero_image_mobile_url
                    ? ''
                    : 'display:none;';
                ?>
            "
        >

            <?php if ($about_hero_image_mobile_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $about_hero_image_mobile_url
                    ); ?>"
                    alt="Mobile About hero preview"
                    style="
                        max-width:320px;
                        width:100%;
                        height:auto;
                        display:block;
                        border-radius:6px;
                    "
                >

            <?php endif; ?>

        </div>


        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago-about-hero-image-mobile"
                data-preview="#isago-about-hero-image-mobile-preview"
                data-folder-selector="#isago-about-hero-image-mobile-folder"
                data-type="image"
                data-account-selector="#isago-about-hero-image-mobile-account"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_hero_image_mobile"
            >

                <?php

                echo $about_hero_image_mobile_id
                    ? 'Change Mobile Hero Image'
                    : 'Upload Mobile Hero Image';

                ?>

            </button>

        </p>



<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-about-hero-image-mobile"
    data-preview="#isago-about-hero-image-mobile-preview"
    data-type="image"
    data-account-selector="#isago-about-hero-image-mobile-account"
    data-folder-selector="#isago-about-hero-image-mobile-folder"
>
    Select Existing ImageKit Image
</button>

        <p>

            <button
                type="button"
                class="button isago-imagekit-remove-button"
                data-target="#isago-about-hero-image-mobile"
                data-preview="#isago-about-hero-image-mobile-preview"
                data-type="image"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="about_hero_image_mobile"
                <?php if (!$about_hero_image_mobile_id) : ?>
                    disabled
                <?php endif; ?>
            >

                Remove Mobile Hero Image

            </button>

        </p>


        <p class="description">

            Used on tablets and mobile screens when the hero
            changes to a single-column layout.

        </p>

    </td>

</tr>


<!-- =====================================================
SIDEBAR
===================================================== -->

<tr>

    <th>
        Sidebar Title
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_sidebar_title]"
            value="<?php echo esc_attr(
                $settings['about_sidebar_title']
                ?? 'About Isago'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Sidebar Description
    </th>

    <td>

        <textarea
            rows="4"
            cols="60"
            name="isago_theme_settings[about_sidebar_desc]"
        ><?php

            echo esc_textarea(
                $settings['about_sidebar_desc'] ?? ''
            );

        ?></textarea>

    </td>

</tr>


<tr>

    <th>
        Founded
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_founded]"
            value="<?php echo esc_attr(
                $settings['about_founded']
                ?? '2024'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Headquarters
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_hq]"
            value="<?php echo esc_attr(
                $settings['about_hq']
                ?? 'Worldwide'
            ); ?>"
        >

    </td>

</tr>


<tr>

    <th>
        Focus
    </th>

    <td>

        <input
            type="text"
            name="isago_theme_settings[about_focus]"
            value="<?php echo esc_attr(
                $settings['about_focus']
                ?? 'AI Prompts, Tools'
            ); ?>"
        >

    </td>

</tr>
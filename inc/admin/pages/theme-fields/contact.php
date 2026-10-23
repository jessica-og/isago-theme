<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_option('isago_theme_settings');

?>

<tr>
  
    <th colspan="2">
  
        <hr>

        <h2>Contact Page</h2>

    </th>

</tr>


<!-- =====================================================
CONTACT HERO IMAGE
===================================================== -->

<!-- =====================================================
CONTACT HERO IMAGE
===================================================== -->

<tr>

    <th>
        Contact Hero Image
    </th>

    <td>

        <?php

        /*
         * --------------------------------------------------------
         * CONTACT HERO IMAGE
         * --------------------------------------------------------
         *
         * New images use the central Isago media system.
         *
         * Object:
         * theme_settings / 1 / contact_hero_image
         */

        $contact_hero_image_id =
            absint(
                $settings['contact_hero_image'] ?? 0
            );


        /*
         * --------------------------------------------------------
         * IMAGE URL
         * --------------------------------------------------------
         */

        $contact_hero_image_url = '';


        if ($contact_hero_image_id) {

            /*
             * New central Isago media.
             */
            $media =
                isago_media_get(
                    $contact_hero_image_id
                );


            if ($media) {

                $contact_hero_image_url =
                    isago_media_get_field_image_url(
                        'theme_settings',
                        1,
                        'contact_hero_image',
                        'medium_large'
                    );

            }


            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$contact_hero_image_url
                &&
                wp_attachment_is_image(
                    $contact_hero_image_id
                )
            ) {

                $contact_hero_image_url =
                    wp_get_attachment_image_url(
                        $contact_hero_image_id,
                        'medium_large'
                    );

            }

        }


        /*
         * --------------------------------------------------------
         * IMAGEKIT ACCOUNT
         * --------------------------------------------------------
         */

        $contact_account_id =
            isago_get_imagekit_media_account_id(
                $contact_hero_image_id
            );

        ?>


        <!--=================================================
        CENTRAL MEDIA ID
        =================================================-->

        <input
            type="hidden"
            id="isago-contact-hero-image"
            class="isago-imagekit-media-id"
            name="isago_theme_settings[contact_hero_image]"
            value="<?php echo esc_attr(
                $contact_hero_image_id
            ); ?>"
        >


        <!--=================================================
        IMAGEKIT ACCOUNT
        =================================================-->

        <?php

        isago_render_imagekit_account_selector(
            [
                'id'       => 'isago-contact-hero-image-account',
                'selected' => $contact_account_id,
                'label'    => 'ImageKit Account',
                'class'    => 'isago-imagekit-account-selector',
                'style'    => 'width:100%; margin-bottom:10px;',
            ]
        );
isago_render_imagekit_folder_selector(
    [
        'id'       => 'isago-contact-hero-image-folder',
        'selected' => '/Isago-Media/Site-Assets/',
        'label'    => 'Destination folder',
        'class'    => 'isago-imagekit-folder-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);
        ?>


        <!--=================================================
        PREVIEW
        =================================================-->

        <div
            id="isago-contact-hero-image-preview"
            class="isago-media-preview"
            style="
                margin-top:15px;
                <?php
                echo $contact_hero_image_url
                    ? ''
                    : 'display:none;';
                ?>
            "
        >

            <?php if ($contact_hero_image_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $contact_hero_image_url
                    ); ?>"
                    alt="Contact hero preview"
                    style="
              
                        width:200px;
                        height:200px;
                        display:block;
                        border-radius:10px;
                        border:1px solid #ddd;
                    "
                >

            <?php endif; ?>

        </div>


        <!--=================================================
        UPLOAD
        =================================================-->

        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago-contact-hero-image"
                data-preview="#isago-contact-hero-image-preview"
                data-folder-selector="#isago-contact-hero-image-folder"
                data-type="image"
                data-account-selector="#isago-contact-hero-image-account"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="contact_hero_image"
            >

                <?php

                echo $contact_hero_image_id
                    ? 'Change Contact Hero Image'
                    : 'Upload Contact Hero Image';

                ?>

            </button>

        </p>

<p>
    <button
        type="button"
        class="button isago-imagekit-select-existing-button"
        data-target="#isago-contact-hero-image"
        data-preview="#isago-contact-hero-image-preview"
        data-type="image"
        data-account-selector="#isago-contact-hero-image-account"
        data-folder-selector="#isago-contact-hero-image-folder"
    >
        Select Existing ImageKit Image
    </button>
</p>
        <!--=================================================
        REMOVE
        =================================================-->

        <p>

            <button
                type="button"
                class="button isago-imagekit-remove-button"
                data-target="#isago-contact-hero-image"
                data-preview="#isago-contact-hero-image-preview"
                data-type="image"
                data-object-type="theme_settings"
                data-object-id="1"
                data-field-key="contact_hero_image"
                <?php
                if (!$contact_hero_image_id) :
                    ?>
                    disabled
                    <?php
                endif;
                ?>
            >

                Remove Contact Hero Image

            </button>

        </p>


        <p class="description">

            Upload the illustration displayed in the Contact
            page hero directly to ImageKit.

        </p>

    </td>

</tr>

<!-- =====================================================
EMAIL
===================================================== -->

<tr>

    <th>
        Email
    </th>

    <td>

        <input
            type="email"
            class="regular-text"
            name="isago_theme_settings[contact_email]"
            value="<?php echo esc_attr(
                $settings['contact_email'] ?? ''
            ); ?>"
        >

    </td>

</tr>


<!-- =====================================================
PHONE
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
ADDRESS
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
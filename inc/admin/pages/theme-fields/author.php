<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<tr>

    <th scope="row">

        Author Name

    </th>

    <td>

        <input
            type="text"
            class="regular-text"
            name="isago_theme_settings[author_name]"
            value="<?php echo esc_attr(
                $options['author_name'] ?? 'Isago'
            ); ?>"
        >

        <p class="description">

            Enter the author name displayed on posts.

        </p>

    </td>

</tr>


<tr>

    <th scope="row">

        Author Bio

    </th>

    <td>

        <textarea
            rows="4"
            cols="60"
            name="isago_theme_settings[author_bio]"
        ><?php

            echo esc_textarea(
                $options['author_bio']
                ?? 'Isago publishes practical insights, guides, prompts, and resources for creators exploring AI.'
            );

        ?></textarea>

        <p class="description">

            Enter the author bio displayed on post pages.

        </p>

    </td>

</tr>


<tr>

    <th scope="row">

        Author Image

    </th>

    <td>

        <?php

        /*
         * --------------------------------------------------------
         * AUTHOR IMAGE
         * --------------------------------------------------------
         *
         * New images are stored in the central Isago media system.
         *
         * Object:
         * theme_settings / 1 / author_image
         *
         * Existing WordPress attachment IDs are still supported
         * as a legacy fallback during migration.
         */

        $author_image_id =
            absint(
                $options['author_image'] ?? 0
            );


        /*
         * --------------------------------------------------------
         * IMAGE URL
         * --------------------------------------------------------
         */

        $author_image_url = '';


        if ($author_image_id) {

            /*
             * New central Isago media.
             */
            $media =
                isago_media_get(
                    $author_image_id
                );


            if ($media) {

                $author_image_url =
                    isago_media_get_field_image_url(
                        'theme_settings',
                        1,
                        'author_image',
                        'medium'
                    );

            }


            /*
             * Legacy WordPress attachment fallback.
             */
            if (
                !$author_image_url
                &&
                wp_attachment_is_image(
                    $author_image_id
                )
            ) {

                $author_image_url =
                    wp_get_attachment_image_url(
                        $author_image_id,
                        'medium'
                    );

            }

        }


        /*
         * --------------------------------------------------------
         * IMAGEKIT ACCOUNT
         * --------------------------------------------------------
         */

        $author_account_id =
            isago_get_imagekit_media_account_id(
                $author_image_id
            );

        ?>


        <div
            class="isago-media-field isago-author-image-field"
        >


            <!--=================================================
            CENTRAL MEDIA ID
            =================================================-->

            <input
                type="hidden"
                id="isago-author-image"
                class="isago-imagekit-media-id"
                name="isago_theme_settings[author_image]"
                value="<?php echo esc_attr(
                    $author_image_id
                ); ?>"
            >


            <!--=================================================
            IMAGEKIT ACCOUNT
            =================================================-->

            <?php

            isago_render_imagekit_account_selector(
                [
                    'id'       => 'isago-author-image-account',
                    'selected' => $author_account_id,
                    'label'    => 'ImageKit Account',
                    'class'    => 'isago-imagekit-account-selector',
                    'style'    => 'width:100%; margin-bottom:10px;',
                ]
            );
isago_render_imagekit_folder_selector(
    [
        'id'       => 'isago-author-image-folder',
        'selected' => '/Isago-Media/Site-Assets/',
        'label'    => 'Destination folder',
        'class'    => 'isago-imagekit-folder-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);
            ?>


            <!--=================================================
            IMAGE PREVIEW
            =================================================-->

            <div
                id="isago-author-image-preview"
                class="isago-media-preview"
                style="<?php
                    echo $author_image_url
                        ? ''
                        : 'display:none;';
                ?>"
            >

                <?php if ($author_image_url) : ?>

                    <img
                        src="<?php echo esc_url(
                            $author_image_url
                        ); ?>"
                        alt="Author preview"
                        style="
                            max-width:200px;
                            width:100%;
                            height:auto;
                            display:block;
                            border-radius:50%;
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
                    data-target="#isago-author-image"
                    data-preview="#isago-author-image-preview"
                      data-folder-selector="#isago-author-image-folder"
                    data-type="image"
                    data-account-selector="#isago-author-image-account"
                    data-object-type="theme_settings"
                    data-object-id="1"
                    data-field-key="author_image"
                >

                    <?php

                    echo $author_image_id
                        ? 'Change Author Image'
                        : 'Upload Author Image';

                    ?>

                </button>

            </p>

<p>
    <button
        type="button"
        class="button isago-imagekit-select-existing-button"
        data-target="#isago-author-image"
        data-preview="#isago-author-image-preview"
        data-type="image"
        data-account-selector="#isago-author-image-account"
        data-folder-selector="#isago-author-image-folder"
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
                    data-target="#isago-author-image"
                    data-preview="#isago-author-image-preview"
                    data-type="image"
                    data-object-type="theme_settings"
                    data-object-id="1"
                    data-field-key="author_image"
                    <?php
                    if (!$author_image_id) :
                        ?>
                        disabled
                        <?php
                    endif;
                    ?>
                >

                    Remove Author Image

                </button>

            </p>


        </div>


        <p class="description">

            Upload the author image directly to ImageKit.

        </p>

    </td>

</tr>
/*=========================================================
ISAGO TAXONOMY HERO IMAGES
=========================================================*/

jQuery(function ($) {

    "use strict";


    /*=========================================================
    IMAGEKIT UPLOAD
    =========================================================*/

    /*
     * The actual ImageKit upload is handled by:
     *
     *     assets/js/admin/imagekit.js
     *
     * This file only handles taxonomy-specific UI.
     *
     * Therefore we no longer use:
     *
     *     wp.media()
     *
     * here.
     */


    /*
     * When ImageKit upload completes, make sure the
     * taxonomy card reflects the new image.
     */
    $(document).on(
        "isago:imagekit-uploaded",
        function (event, data) {

            if (!data) {
                return;
            }


            const mediaId =
                parseInt(
                    data.media_id || data.mediaId || 0,
                    10
                ) || 0;


            const url =
                data.url || "";


            if (!mediaId || !url) {
                return;
            }


            /*
             * Find the field that contains the uploaded
             * media ID.
             */
            const input =
                $(
                    '.isago-taxonomy-hero-input[value="' +
                    mediaId +
                    '"]'
                );


            if (!input.length) {
                return;
            }


            const card =
                input.closest(
                    ".isago-card"
                );


            if (!card.length) {
                return;
            }


            const preview =
                card.find(
                    ".isago-taxonomy-hero-preview"
                );


            const image =
                preview.find(
                    "img"
                );


            const removeButton =
                card.find(
                    ".isago-taxonomy-hero-remove"
                );


            /*
             * Update preview.
             */
            if (image.length) {

                image.attr(
                    "src",
                    url
                );

            }


            preview.show();


            /*
             * Enable Remove.
             */
            removeButton
                .prop(
                    "disabled",
                    false
                )
                .show();

        }
    );


    /*=========================================================
    REMOVE TAXONOMY HERO IMAGE
    =========================================================*/

    $(document).on(
        "click",
        ".isago-taxonomy-hero-remove",
        function (e) {

            e.preventDefault();
            e.stopPropagation();


            const button =
                $(this);


            const card =
                button.closest(
                    ".isago-card"
                );


            if (!card.length) {
                return;
            }


            const input =
                card.find(
                    ".isago-taxonomy-hero-input"
                );


            const preview =
                card.find(
                    ".isago-taxonomy-hero-preview"
                );


            const image =
                preview.find(
                    "img"
                );


            const uploadButton =
                card.find(
                    ".isago-taxonomy-hero-upload"
                );


            const mediaId =
                parseInt(
                    input.val(),
                    10
                ) || 0;


            /*
             * Nothing to remove.
             */
            if (!mediaId) {

                input.val("");

                image.attr(
                    "src",
                    ""
                );

                preview.hide();

                button.hide();

                button.prop(
                    "disabled",
                    true
                );

                return;

            }


            /*
             * Prevent double-clicking.
             */
            if (
                button.data(
                    "removing"
                )
            ) {

                return;

            }


            button.data(
                "removing",
                true
            );


            button.prop(
                "disabled",
                true
            );


            /*
             * Taxonomy hero images are global settings.
             *
             * Category:
             *
             *     taxonomy_hero / 1 / category_hero_image
             *
             * Tag:
             *
             *     taxonomy_hero / 1 / tag_hero_image
             */
            const fieldKey =
                button.attr(
                    "data-field-key"
                );


            if (!fieldKey) {

                console.error(
                    "Isago: Taxonomy hero field key is missing."
                );

                button.prop(
                    "disabled",
                    false
                );

                button.data(
                    "removing",
                    false
                );

                return;

            }


            /*
             * Remove the central media reference.
             *
             * The server will then:
             *
             * 1. Remove the reference.
             * 2. Check whether the media is still used.
             * 3. Delete the ImageKit file if unused.
             * 4. Delete the Isago media record.
             */
            $.ajax({

                url:
                    ajaxurl,

                type:
                    "POST",

                data: {

                    action:
                        "isago_imagekit_remove_media",

                    nonce:
                        isagoImageKit.nonce,

                    media_id:
                        mediaId,

                    object_type:
                        "taxonomy_hero",

                    object_id:
                        1,

                    field_key:
                        fieldKey

                },

                success:
                    function (response) {

                        if (
                            response &&
                            response.success
                        ) {

                            /*
                             * Clear central media ID.
                             */
                            input.val("");


                            /*
                             * Clear preview.
                             */
                            image.attr(
                                "src",
                                ""
                            );


                            preview.hide();


                            /*
                             * Hide/disable remove.
                             */
                            button.hide();

                            button.prop(
                                "disabled",
                                true
                            );


                            /*
                             * Reset upload button.
                             */
                            const target =
                                button.attr(
                                    "data-target"
                                );


                            uploadButton.text(

                                target === "category"

                                    ? "Upload Category Hero Image"

                                    : "Upload Tag Hero Image"

                            );

                        } else {

                            let message =
                                "Could not remove the hero image.";


                            if (
                                response &&
                                response.data &&
                                response.data.message
                            ) {

                                message =
                                    response.data.message;

                            }


                            window.alert(
                                message
                            );


                            button.prop(
                                "disabled",
                                false
                            );

                        }

                    },

                error:
                    function () {

                        window.alert(
                            "Could not remove the hero image. Please try again."
                        );


                        button.prop(
                            "disabled",
                            false
                        );

                    },

                complete:
                    function () {

                        button.data(
                            "removing",
                            false
                        );

                    }

            });

        }
    );


});
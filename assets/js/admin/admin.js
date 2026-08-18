jQuery(function ($) {

    "use strict";

    /*=========================================================
    Media Uploader
    =========================================================*/

    let mediaFrame = null;

    $(document).on("click", ".isago-upload-button", function (e) {

        e.preventDefault();

        const button = $(this);

        if (mediaFrame) {
            mediaFrame.close();
        }

        mediaFrame = wp.media({

            title: "Select Image",

            button: {

                text: "Use Image"

            },

            multiple: false

        });

        mediaFrame.on("select", function () {

            const attachment = mediaFrame.state()

                .get("selection")

                .first()

                .toJSON();

            button

                .siblings(".isago-image-url")

                .val(attachment.url)

                .trigger("change");

            button

                .siblings(".isago-preview")

                .attr("src", attachment.url)

                .show();

        });

        mediaFrame.open();

    });

    /*=========================================================
    WordPress Color Picker
    =========================================================*/

    if ($.fn.wpColorPicker) {

        $(".isago-color-picker").wpColorPicker();

    }

    /*=========================================================
    Reset Confirmation
    =========================================================*/

    $(document).on("submit", ".isago-reset-form", function () {

        return confirm(

            "Are you sure you want to reset these settings?\n\nThis action cannot be undone."

        );

    });

    /*=========================================================
    Toggle Visibility
    =========================================================*/

    $(document).on("change", "[data-toggle]", function () {

        const target = $(this).data("toggle");

        if ($(this).is(":checked")) {

            $(target).slideDown(200);

        } else {

            $(target).slideUp(200);

        }

    });

    /*=========================================================
    Live Image Preview
    =========================================================*/

    $(document).on("change", ".isago-image-url", function () {

        $(this)

            .siblings(".isago-preview")

            .attr("src", $(this).val());

    });

});
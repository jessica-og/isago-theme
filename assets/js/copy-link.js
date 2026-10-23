jQuery(function ($) {

    "use strict";

    /*
    =========================================================
    COPY POST LINK
    =========================================================
    */

    $(document).on("click", ".copy-post-link", async function (e) {

        e.preventDefault();

        const button = $(this);

        const link = button.attr("data-link");

        if (!link) {

            console.error(
                "Isago: No data-link found on copy button."
            );

            return;

        }

        try {

            /*
            -------------------------------------------------
            Modern Clipboard API
            -------------------------------------------------
            */

            await navigator.clipboard.writeText(link);

            isagoCopied(button);

        } catch (error) {

            /*
            -------------------------------------------------
            Fallback for browsers / HTTP environments
            -------------------------------------------------
            */

            const textarea =
                document.createElement("textarea");

            textarea.value = link;

            textarea.setAttribute(
                "readonly",
                ""
            );

            textarea.style.position = "fixed";

            textarea.style.left = "-9999px";

            document.body.appendChild(textarea);

            textarea.select();

            try {

                document.execCommand("copy");

                isagoCopied(button);

            } catch (fallbackError) {

                console.error(
                    "Isago: Copy failed.",
                    fallbackError
                );

            }

            document.body.removeChild(textarea);

        }

    });


    /*
    =========================================================
    COPIED STATE
    =========================================================
    */

    function isagoCopied(button) {

        const icon = button.find("i");

        const originalIcon =
            icon.attr("class");

        const originalAria =
            button.attr("aria-label") ||
            "Copy article link";

        button.addClass("is-copied");

        icon.attr(
            "class",
            "fa-solid fa-check"
        );

        button.attr(
            "aria-label",
            "Link copied"
        );

        button.attr(
            "title",
            "Link copied"
        );

        /*
        -----------------------------------------------------
        Optional text feedback
        -----------------------------------------------------
        */

        let feedback =
            button.find(".copy-feedback");

        if (!feedback.length) {

            feedback = $("<span>", {

                class: "copy-feedback",

                text: "Copied!"

            });

            button.append(feedback);

        }

        feedback.stop(true, true).fadeIn(150);

        /*
        -----------------------------------------------------
        Restore Button
        -----------------------------------------------------
        */

        setTimeout(function () {

            button.removeClass("is-copied");

            icon.attr(
                "class",
                originalIcon
            );

            button.attr(
                "aria-label",
                originalAria
            );

            button.removeAttr("title");

            feedback.fadeOut(
                150,
                function () {

                    $(this).remove();

                }
            );

        }, 1800);

    }

});
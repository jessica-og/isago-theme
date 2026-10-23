jQuery(function ($) {

    "use strict";



    /*=========================================================
ISAGO MEDIA UPLOADER
Reusable Image + Video System
=========================================================*/

let isagoMediaFrame = null;


/*=========================================================
GET MEDIA TARGET
=========================================================*/

function isagoGetMediaTarget($button) {

    const target =
        $button.attr("data-target");


    if (!target) {

        console.error(
            "Isago: Media button has no data-target.",
            $button
        );

        return $();

    }


    const $input =
        $(target);


    if (!$input.length) {

        console.error(
            "Isago: Media target not found:",
            target
        );

        return $();

    }


    return $input;

}


/*=========================================================
GET MEDIA FIELD
=========================================================*/

function isagoGetMediaField($button) {

    const $field =
        $button.closest(
            ".isago-media-field"
        );


    if ($field.length) {

        return $field;

    }


    /*
     * Fallback for existing fields that have
     * not yet been wrapped in .isago-media-field.
     */
    return $button.closest(
        "td, .form-field, .isago-builder-card"
    );

}


/*=========================================================
GET MEDIA PREVIEW
=========================================================*/

function isagoGetMediaPreview($field) {

    if (!$field.length) {

        return $();

    }


    return $field
        .find(
            ".isago-media-preview"
        )
        .first();

}


/*=========================================================
UPDATE IMAGE PREVIEW
=========================================================*/

function isagoUpdateImagePreview(
    $field,
    imageUrl
) {

    const $preview =
        isagoGetMediaPreview(
            $field
        );


    if (!$preview.length) {

        return;

    }


    let $image =
        $preview
            .find("img")
            .first();


    /*
     * Create image element when needed.
     */
    if (
        !$image.length &&
        imageUrl
    ) {

        $image =
            $("<img>", {

                alt: "",

                style:
                    "max-width:300px;height:auto;display:block;"

            });


        $preview.append(
            $image
        );

    }


    if (!$image.length) {

        return;

    }


    if (imageUrl) {

        $image
            .attr(
                "src",
                imageUrl
            )
            .show();


        $preview.show();

    } else {

        $image
            .attr(
                "src",
                ""
            )
            .hide();


        $preview.hide();

    }

}


/*=========================================================
UPDATE VIDEO PREVIEW
=========================================================*/

function isagoUpdateVideoPreview(
    $field,
    videoUrl
) {

    const $preview =
        isagoGetMediaPreview(
            $field
        );


    if (!$preview.length) {

        return;

    }


    let $video =
        $preview
            .find("video")
            .first();


    /*
     * Create video element when needed.
     */
    if (
        !$video.length &&
        videoUrl
    ) {

        $video =
            $("<video>", {

                controls: true,

                preload: "metadata",

                style:
                    "max-width:500px;width:100%;height:auto;display:block;"

            });


        $preview.append(
            $video
        );

    }


    if (!$video.length) {

        return;

    }


    if (videoUrl) {

        $video
            .attr(
                "src",
                videoUrl
            )
            .show();


        $preview.show();


        const video =
            $video.get(0);


        if (video) {

            video.load();

        }

    } else {

        $video
            .attr(
                "src",
                ""
            )
            .hide();


        $preview.hide();


        const video =
            $video.get(0);


        if (video) {

            video.load();

        }

    }

}


/*=========================================================
CLEAR MEDIA
=========================================================*/

function isagoClearMedia(
    $button
) {

    const $input =
        isagoGetMediaTarget(
            $button
        );


    if (!$input.length) {

        return;

    }


    const $field =
        isagoGetMediaField(
            $button
        );


    const type =
        $button.attr(
            "data-type"
        ) || "image";


    $input
        .val("")
        .trigger("change");


    if (type === "video") {

        isagoUpdateVideoPreview(
            $field,
            ""
        );

    } else {

        isagoUpdateImagePreview(
            $field,
            ""
        );

    }

}


/*=========================================================
UPLOAD MEDIA
=========================================================*/

$(document).on(
    "click",
    ".isago-upload-button",
    function (e) {

        e.preventDefault();
        e.stopPropagation();


        const $button =
            $(this);


        const type =
            $button.attr(
                "data-type"
            ) || "image";


        const $input =
            isagoGetMediaTarget(
                $button
            );


        if (!$input.length) {

            return;

        }


        /*
         * WordPress Media Library must be available.
         */
        if (
            typeof wp === "undefined" ||
            typeof wp.media === "undefined"
        ) {

            console.error(
                "Isago: wp.media is not available."
            );


            alert(
                "The WordPress Media Library could not be loaded. Please refresh the page and try again."
            );


            return;

        }


        /*
         * Close previous media frame.
         */
        if (isagoMediaFrame) {

            try {

                isagoMediaFrame.close();

            } catch (error) {

                console.warn(
                    "Isago: Could not close previous media frame.",
                    error
                );

            }

        }


        /*
         * Create reusable media frame.
         */
        isagoMediaFrame =
            wp.media({

                title:
                    type === "video"
                        ? "Select Video"
                        : "Select Image",

                button: {

                    text:
                        type === "video"
                            ? "Use Video"
                            : "Use Image"

                },

                library: {

                    type: type

                },

                multiple: false

            });


        /*
         * Selected media.
         */
        isagoMediaFrame.on(
            "select",
            function () {

                const selection =
                    isagoMediaFrame
                        .state()
                        .get("selection");


                if (
                    !selection ||
                    !selection.length
                ) {

                    return;

                }


                const attachment =
                    selection
                        .first()
                        .toJSON();


                if (!attachment.id) {

                    console.error(
                        "Isago: Selected media has no attachment ID.",
                        attachment
                    );

                    return;

                }


                const $field =
                    isagoGetMediaField(
                        $button
                    );


                /*
                 * Save attachment ID.
                 */
                $input
                    .val(
                        attachment.id
                    )
                    .trigger("change");


                /*
                 * Update preview.
                 */
                if (type === "video") {

                    isagoUpdateVideoPreview(
                        $field,
                        attachment.url || ""
                    );

                } else {

                    isagoUpdateImagePreview(
                        $field,
                        attachment.url || ""
                    );

                }

            }
        );


        /*
         * Open WordPress Media Library.
         */
        isagoMediaFrame.open();

    }
);


/*=========================================================
REMOVE IMAGE
=========================================================*/

$(document).on(
    "click",
    ".isago-remove-image",
    function (e) {

        e.preventDefault();


        const $button =
            $(this);


        $button.attr(
            "data-type",
            "image"
        );


        isagoClearMedia(
            $button
        );

    }
);


/*=========================================================
REMOVE VIDEO
=========================================================*/

$(document).on(
    "click",
    ".isago-remove-video-source",
    function (e) {

        e.preventDefault();


        const $button =
            $(this);


        $button.attr(
            "data-type",
            "video"
        );


        isagoClearMedia(
            $button
        );

    }
);


    /*=========================================================
    RESET CONFIRMATION
    =========================================================*/

    $(document).on(
        "submit",
        ".isago-reset-form",
        function () {

            return confirm(

                "Are you sure you want to reset these settings?\n\nThis action cannot be undone."

            );

        }
    );


    /*=========================================================
    TOGGLE VISIBILITY
    =========================================================*/

    $(document).on(
        "change",
        "[data-toggle]",
        function () {

            const target =
                $(this).data("toggle");


            if ($(this).is(":checked")) {

                $(target)
                    .stop(true, true)
                    .slideDown(200);

            } else {

                $(target)
                    .stop(true, true)
                    .slideUp(200);

            }

        }
    );


    /*=========================================================
    ISAGO TAXONOMY ACCORDION
    =========================================================*/

    $(document).on(
        "click",
        ".isago-accordion-toggle",
        function (e) {

            e.preventDefault();
            e.stopPropagation();


            const $button =
                $(this);


            const $accordion =
                $button.closest(
                    ".isago-accordion"
                );


            if (!$accordion.length) {

                return;

            }


            const $panel =
                $accordion.children(
                    ".isago-accordion-panel"
                );


            if (!$panel.length) {

                return;

            }


            const $icon =
                $button.children(
                    ".dashicons"
                );


            const isOpen =
                $accordion.hasClass(
                    "is-open"
                );


            /*
            |--------------------------------------------------------------------------
            | Close siblings only
            |--------------------------------------------------------------------------
            */

            $accordion
                .siblings(".isago-accordion")
                .each(function () {

                    const $other =
                        $(this);


                    const $otherButton =
                        $other.children(
                            ".isago-accordion-toggle"
                        );


                    const $otherPanel =
                        $other.children(
                            ".isago-accordion-panel"
                        );


                    const $otherIcon =
                        $otherButton.children(
                            ".dashicons"
                        );


                    $other
                        .removeClass(
                            "is-open"
                        );


                    $otherButton
                        .removeClass(
                            "is-open"
                        )
                        .attr(
                            "aria-expanded",
                            "false"
                        );


                    $otherIcon
                        .removeClass(
                            "is-open"
                        );


                    $otherPanel
                        .stop(
                            true,
                            true
                        )
                        .slideUp(200);

                });


            /*
            |--------------------------------------------------------------------------
            | Close current
            |--------------------------------------------------------------------------
            */

            if (isOpen) {

                $accordion
                    .removeClass(
                        "is-open"
                    );


                $button
                    .removeClass(
                        "is-open"
                    )
                    .attr(
                        "aria-expanded",
                        "false"
                    );


                $icon
                    .removeClass(
                        "is-open"
                    );


                $panel
                    .stop(
                        true,
                        true
                    )
                    .slideUp(200);


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Open current
            |--------------------------------------------------------------------------
            */

            $accordion
                .addClass(
                    "is-open"
                );


            $button
                .addClass(
                    "is-open"
                )
                .attr(
                    "aria-expanded",
                    "true"
                );


            $icon
                .addClass(
                    "is-open"
                );


            $panel
                .stop(
                    true,
                    true
                )
                .slideDown(200);

        }
    );


    /*=========================================================
    INITIALIZE TAXONOMY ACCORDIONS
    =========================================================*/

    function isagoInitializeAccordions() {

        $(".isago-accordion").each(
            function () {

                const $accordion =
                    $(this);


                const $button =
                    $accordion.children(
                        ".isago-accordion-toggle"
                    );


                const $panel =
                    $accordion.children(
                        ".isago-accordion-panel"
                    );


                const $icon =
                    $button.children(
                        ".dashicons"
                    );


                if (!$button.length) {

                    return;

                }


                if (!$panel.length) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Existing open state
                |--------------------------------------------------------------------------
                */

                if (
                    $accordion.hasClass(
                        "is-open"
                    )
                ) {

                    $button
                        .addClass(
                            "is-open"
                        )
                        .attr(
                            "aria-expanded",
                            "true"
                        );


                    $icon.addClass(
                        "is-open"
                    );


                    $panel.show();


                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Closed state
                |--------------------------------------------------------------------------
                */

                $accordion
                    .removeClass(
                        "is-open"
                    );


                $button
                    .removeClass(
                        "is-open"
                    )
                    .attr(
                        "aria-expanded",
                        "false"
                    );


                $icon.removeClass(
                    "is-open"
                );


                $panel.hide();

            }
        );

    }


    isagoInitializeAccordions();


    /*=========================================================
    ADMIN VIDEO THUMBNAIL PREVIEW
    =========================================================*/

    $(document).on(
        "loadedmetadata",
        ".isago-admin-video-thumbnail",
        function () {

            const video =
                this;


            try {

                video.currentTime = 0;

            } catch (error) {

                console.warn(
                    "Isago: Could not seek admin video thumbnail.",
                    error
                );

            }

        }
    );


/*=========================================================
AI PROMPT CATEGORY FONT AWESOME ICON PREVIEW
=========================================================*/

function isagoUpdateAiPromptCategoryIconPreview() {

    var $input =
        $("#isago_ai_prompt_category_icon");


    var $preview =
        $("#isago-ai-prompt-category-icon-preview");


    if (
        !$input.length ||
        !$preview.length
    ) {
        return;
    }


    var value =
        $.trim(
            $input.val()
        );


    $preview.empty();


    if (!value) {
        return;
    }


    var classes =
        value.split(/\s+/);


    var validClasses = [];


    $.each(
        classes,
        function(index, className) {

            if (
                /^fa-[a-z0-9-]+$/i.test(
                    className
                )
            ) {

                validClasses.push(
                    className
                );

            }

        }
    );


    if (!validClasses.length) {
        return;
    }


    var $icon =
        $("<i>", {
            "class":
                validClasses.join(" "),
            "aria-hidden":
                "true"
        });


    $preview.append(
        $icon
    );
}


/*=========================================================
LIVE PREVIEW
=========================================================*/

$(document).on(
    "input",
    "#isago_ai_prompt_category_icon",
    function() {

        isagoUpdateAiPromptCategoryIconPreview();

    }
);


/*=========================================================
CHANGE PREVIEW
=========================================================*/

$(document).on(
    "change",
    "#isago_ai_prompt_category_icon",
    function() {

        isagoUpdateAiPromptCategoryIconPreview();

    }
);


/*=========================================================
INITIAL PREVIEW
=========================================================*/

$(document).ready(
    function() {

        isagoUpdateAiPromptCategoryIconPreview();

    }
);


});












/*=========================================================
ISAGO GUTENBERG RIGHT SIDEBAR EXPANDER
=========================================================*/

(function ($) {

    'use strict';


    const ISAGO_SIDEBAR_WIDTH = '500px';

    const ISAGO_SIDEBAR_STORAGE =
        'isago_editor_sidebar_expanded';


    /*
     * Get the Gutenberg right sidebar.
     */
    function isagoGetEditorSidebar() {

        return $(
            '.interface-interface-skeleton__sidebar'
        ).first();

    }


    /*
     * Get the Gutenberg editor skeleton.
     */
    function isagoGetEditorSkeleton() {

        return $(
            '.interface-interface-skeleton'
        ).first();

    }


    /*
     * Apply the expanded width directly.
     *
     * Gutenberg uses flex/grid sizing on the editor
     * skeleton, so width alone is not sufficient.
     */
    function isagoSetSidebarWidth(
        sidebar,
        expanded
    ) {

        if (!sidebar.length) {
            return;
        }


        const complementaryArea =
            sidebar.find(
                '.interface-complementary-area'
            ).first();


        if (expanded) {

            /*
             * Sidebar itself.
             */
            sidebar.css({
                width:
                    ISAGO_SIDEBAR_WIDTH,

                minWidth:
                    ISAGO_SIDEBAR_WIDTH,

                maxWidth:
                    ISAGO_SIDEBAR_WIDTH,

                flex:
                    '0 0 ' +
                    ISAGO_SIDEBAR_WIDTH,

                flexBasis:
                    ISAGO_SIDEBAR_WIDTH
            });


            /*
             * Gutenberg complementary area.
             */
            if (
                complementaryArea.length
            ) {

                complementaryArea.css({

                    width:
                        ISAGO_SIDEBAR_WIDTH,

                    minWidth:
                        ISAGO_SIDEBAR_WIDTH,

                    maxWidth:
                        ISAGO_SIDEBAR_WIDTH
                });

            }


            /*
             * Make the Gutenberg panels use the
             * entire expanded area.
             */
            sidebar
                .find(
                    '.interface-complementary-area__fill'
                )
                .css({
                    width: '100%',
                    maxWidth: 'none'
                });


        } else {

            /*
             * Remove our inline sizing and allow
             * Gutenberg to return to its normal width.
             */
            sidebar.css({

                width: '',
                minWidth: '',
                maxWidth: '',
                flex: '',
                flexBasis: ''

            });


            if (
                complementaryArea.length
            ) {

                complementaryArea.css({

                    width: '',
                    minWidth: '',
                    maxWidth: ''

                });

            }


            sidebar
                .find(
                    '.interface-complementary-area__fill'
                )
                .css({
                    width: '',
                    maxWidth: ''
                });

        }

    }


    /*
     * Update the button appearance.
     */
    function isagoUpdateSidebarButton(
        button,
        expanded
    ) {

        if (!button.length) {
            return;
        }


        button.attr(
            'aria-expanded',
            expanded ? 'true' : 'false'
        );


        button.attr(
            'aria-label',
            expanded
                ? 'Collapse editor sidebar'
                : 'Expand editor sidebar'
        );


        button.attr(
            'title',
            expanded
                ? 'Collapse editor sidebar'
                : 'Expand editor sidebar'
        );


        button.find(
            '.isago-editor-sidebar-toggle-text'
        ).text(
            expanded
                ? 'Collapse'
                : 'Expand'
        );


        const icon =
            button.find(
                '.dashicons'
            );


        if (expanded) {

            icon
                .removeClass(
                    'dashicons-editor-expand'
                )
                .addClass(
                    'dashicons-editor-contract'
                );

        } else {

            icon
                .removeClass(
                    'dashicons-editor-contract'
                )
                .addClass(
                    'dashicons-editor-expand'
                );

        }

    }


    /*
     * Expand.
     */
    function isagoExpandEditorSidebar(
        sidebar,
        button,
        saveState = true
    ) {

        sidebar.addClass(
            'isago-editor-sidebar-expanded'
        );


        isagoSetSidebarWidth(
            sidebar,
            true
        );


        isagoUpdateSidebarButton(
            button,
            true
        );


        if (saveState) {

            localStorage.setItem(
                ISAGO_SIDEBAR_STORAGE,
                '1'
            );

        }

    }


    /*
     * Collapse.
     */
    function isagoCollapseEditorSidebar(
        sidebar,
        button,
        saveState = true
    ) {

        sidebar.removeClass(
            'isago-editor-sidebar-expanded'
        );


        isagoSetSidebarWidth(
            sidebar,
            false
        );


        isagoUpdateSidebarButton(
            button,
            false
        );


        if (saveState) {

            localStorage.setItem(
                ISAGO_SIDEBAR_STORAGE,
                '0'
            );

        }

    }


    /*
     * Create the toggle button.
     */
    function isagoCreateSidebarToggle() {

        const sidebar =
            isagoGetEditorSidebar();


        if (!sidebar.length) {
            return false;
        }


        /*
         * Already created.
         */
        if (
            sidebar.find(
                '.isago-editor-sidebar-toggle'
            ).length
        ) {

            return true;

        }


        /*
         * Gutenberg sidebar header.
         */
        const header =
            sidebar.find(
                '.interface-complementary-area-header'
            ).first();


        if (!header.length) {

            return false;

        }


        /*
         * Create button.
         */
        const button =
            $('<button>', {

                type: 'button',

                class:
                    'components-button ' +
                    'isago-editor-sidebar-toggle',

                'aria-expanded':
                    'false',

                'aria-label':
                    'Expand editor sidebar',

                title:
                    'Expand editor sidebar'

            });


        /*
         * Icon.
         */
        const icon =
            $('<span>', {

                class:
                    'dashicons dashicons-editor-expand',

                'aria-hidden':
                    'true'

            });


        /*
         * Text.
         */
        const text =
            $('<span>', {

                class:
                    'isago-editor-sidebar-toggle-text',

                text:
                    'Expand'

            });


        button.append(icon);
        button.append(text);


        /*
         * Add button to header.
         */
        header.append(button);


        /*
         * Restore saved state.
         */
        const expanded =
            localStorage.getItem(
                ISAGO_SIDEBAR_STORAGE
            ) === '1';


        if (expanded) {

            isagoExpandEditorSidebar(
                sidebar,
                button,
                false
            );

        }


        return true;

    }


    /*
     * Toggle.
     */
    $(document).on(
        'click',
        '.isago-editor-sidebar-toggle',
        function (event) {

            event.preventDefault();
            event.stopPropagation();


            const button =
                $(this);


            const sidebar =
                button.closest(
                    '.interface-interface-skeleton__sidebar'
                );


            if (!sidebar.length) {
                return;
            }


            if (
                sidebar.hasClass(
                    'isago-editor-sidebar-expanded'
                )
            ) {

                isagoCollapseEditorSidebar(
                    sidebar,
                    button
                );

            } else {

                isagoExpandEditorSidebar(
                    sidebar,
                    button
                );

            }

        }
    );


    /*
     * Gutenberg can rebuild its sidebar.
     *
     * Therefore keep looking for the HEADER,
     * not merely the sidebar.
     */
    $(document).ready(
        function () {

            let attempts = 0;

            const maxAttempts = 60;


            const interval =
                setInterval(
                    function () {

                        const created =
                            isagoCreateSidebarToggle();


                        attempts++;


                        if (
                            created
                            ||
                            attempts >= maxAttempts
                        ) {

                            clearInterval(
                                interval
                            );

                        }

                    },
                    500
                );

        }
    );


})(jQuery);



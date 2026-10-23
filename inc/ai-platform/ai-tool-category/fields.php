<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO AI TOOL CATEGORY FIELDS
=========================================================*/


/*=========================================================
ADD CATEGORY FIELDS
=========================================================*/

function isago_ai_tool_category_add_fields()
{
    ?>

    <!--=====================================================
    FONT AWESOME ICON
    =====================================================-->

    <div class="form-field">

        <label for="isago_ai_tool_category_icon">

            <?php
            esc_html_e(
                'Font Awesome Icon',
                'isago'
            );
            ?>

        </label>


        <input
            type="text"
            name="isago_ai_tool_category_icon"
            id="isago_ai_tool_category_icon"
            value=""
            class="regular-text"
            placeholder="fa-solid fa-pen"
        >


        <div
            id="isago-ai-tool-category-icon-preview"
            style="
                margin:15px 0;
                min-height:40px;
                font-size:32px;
                line-height:1;
                display:flex;
                align-items:center;
            "
        ></div>


        <p class="description">

            <?php
            esc_html_e(
                'Enter a Font Awesome icon class. Example: fa-solid fa-pen',
                'isago'
            );
            ?>

        </p>


        <p class="description">

            <strong>
                <?php
                esc_html_e(
                    'Examples:',
                    'isago'
                );
                ?>
            </strong>

            <code>fa-solid fa-pen</code>,
            <code>fa-solid fa-image</code>,
            <code>fa-solid fa-video</code>,
            <code>fa-solid fa-code</code>,
            <code>fa-solid fa-robot</code>

        </p>

    </div>

    <?php
}

add_action(
    'ai_tool_category_add_form_fields',
    'isago_ai_tool_category_add_fields'
);


/*=========================================================
EDIT CATEGORY FIELDS
=========================================================*/

function isago_ai_tool_category_edit_fields($term)
{
    $term_id = absint(
        $term->term_id
    );


    $icon = get_term_meta(
        $term_id,
        '_isago_ai_tool_category_icon',
        true
    );

    ?>

    <!--=====================================================
    FONT AWESOME ICON
    =====================================================-->

    <tr class="form-field">

        <th scope="row">

            <label for="isago_ai_tool_category_icon">

                <?php
                esc_html_e(
                    'Font Awesome Icon',
                    'isago'
                );
                ?>

            </label>

        </th>


        <td>

            <input
                type="text"
                name="isago_ai_tool_category_icon"
                id="isago_ai_tool_category_icon"
                value="<?php echo esc_attr($icon); ?>"
                class="regular-text"
                placeholder="fa-solid fa-pen"
            >


            <div
                id="isago-ai-tool-category-icon-preview"
                style="
                    margin:15px 0;
                    min-height:40px;
                    font-size:32px;
                    line-height:1;
                    display:flex;
                    align-items:center;
                "
            >

                <?php if ($icon) : ?>

                    <i
                        class="<?php echo esc_attr($icon); ?>"
                        aria-hidden="true"
                    ></i>

                <?php endif; ?>

            </div>


            <p class="description">

                <?php
                esc_html_e(
                    'Enter a Font Awesome icon class. Example: fa-solid fa-pen',
                    'isago'
                );
                ?>

            </p>


            <p class="description">

                <strong>
                    <?php
                    esc_html_e(
                        'Examples:',
                        'isago'
                    );
                    ?>
                </strong>

                <code>fa-solid fa-pen</code>,
                <code>fa-solid fa-image</code>,
                <code>fa-solid fa-video</code>,
                <code>fa-solid fa-code</code>,
                <code>fa-solid fa-robot</code>

            </p>

        </td>

    </tr>


    <?php
}

add_action(
    'ai_tool_category_edit_form_fields',
    'isago_ai_tool_category_edit_fields'
);


/*=========================================================
SAVE CATEGORY FIELDS
=========================================================*/

function isago_save_ai_tool_category_fields($term_id)
{
    $term_id = absint($term_id);

    if (!$term_id) {
        return;
    }


    if (
        !current_user_can(
            'manage_categories'
        )
    ) {
        return;
    }


    /*-----------------------------------------------------
    FONT AWESOME ICON
    -----------------------------------------------------*/

    if (
        isset(
            $_POST['isago_ai_tool_category_icon']
        )
    ) {

        $icon = sanitize_text_field(
            wp_unslash(
                $_POST['isago_ai_tool_category_icon']
            )
        );


        if ($icon !== '') {

            update_term_meta(
                $term_id,
                '_isago_ai_tool_category_icon',
                $icon
            );

        } else {

            delete_term_meta(
                $term_id,
                '_isago_ai_tool_category_icon'
            );
        }
    }


    /*-----------------------------------------------------
    REMOVE OLD RICH INTRODUCTION
    -----------------------------------------------------*/

    delete_term_meta(
        $term_id,
        '_isago_ai_tool_category_intro'
    );


   
}

add_action(
    'created_ai_tool_category',
    'isago_save_ai_tool_category_fields'
);

add_action(
    'edited_ai_tool_category',
    'isago_save_ai_tool_category_fields'
);


/*=========================================================
LOAD FONT AWESOME ON AI TOOL CATEGORY ADMIN SCREEN
=========================================================*/

function isago_ai_tool_category_admin_font_awesome()
{
    $screen = get_current_screen();

    if (
        !$screen ||
        $screen->taxonomy !== 'ai_tool_category'
    ) {
        return;
    }


    wp_enqueue_style(
        'isago-font-awesome-admin',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        array(),
        '6.7.2'
    );
}

add_action(
    'admin_enqueue_scripts',
    'isago_ai_tool_category_admin_font_awesome'
);


/*=========================================================
FONT AWESOME ADMIN PREVIEW
=========================================================*/

function isago_ai_tool_category_admin_icon_script()
{
    $screen = get_current_screen();

    if (
        !$screen ||
        $screen->taxonomy !== 'ai_tool_category'
    ) {
        return;
    }

    ?>

    <script>
    jQuery(function($) {

        "use strict";


        function isagoUpdateAiToolCategoryIconPreview() {

            var $input =
                $("#isago_ai_tool_category_icon");


            var $preview =
                $("#isago-ai-tool-category-icon-preview");


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


        $(document).on(
            "input",
            "#isago_ai_tool_category_icon",
            function() {

                isagoUpdateAiToolCategoryIconPreview();

            }
        );


        $(document).on(
            "change",
            "#isago_ai_tool_category_icon",
            function() {

                isagoUpdateAiToolCategoryIconPreview();

            }
        );


        isagoUpdateAiToolCategoryIconPreview();

    });
    </script>

    <?php
}

add_action(
    'admin_footer',
    'isago_ai_tool_category_admin_icon_script'
);
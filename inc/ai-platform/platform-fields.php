<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO AI TOOL CATEGORIES
=========================================================*/

function isago_get_ai_platform_categories()
{
    return [

        'llms' => 'LLMs',

        'image' => 'Image',

        'video' => 'Video',

        'audio' => 'Audio',

        'coding' => 'Coding',
        
        'automation' => 'Automation',

        'research' => 'Research',

        'productivity' => 'Productivity',

        'design' => 'Design',

        'creative-studio' => 'Creative Studio',

        'ai-tool' => 'AI Tool',

    ];
}


/*=========================================================
FIELD DEFINITIONS
=========================================================*/

function isago_get_ai_platform_field_definitions()
{
    return [

        'website' => [

            'label' =>
                'Platform Website',

            'description' =>
                'The official website for this AI Tool.'

        ],

        'short_description' => [

            'label' =>
                'Short Description',

            'description' =>
                'A short editorial description displayed beneath the platform name.'

        ],

        'how_to_use' => [

            'label' =>
                'How to use this platform',

            'description' =>
                'Explain how people can use this AI Tool, including its main workflow, common tasks, and useful ways to get started.'

        ],

        'best_for' => [

            'label' =>
                'Best for',

            'description' =>
                'Describe the types of work, tasks, or users this platform is suited to.'

        ],

        'behavior' => [

            'label' =>
                'How it works',

            'description' =>
                'Describe the platform experience, workflow, or notable characteristics.'

        ],

        'tips' => [

            'label' =>
                'Tips',

            'description' =>
                'Useful editorial tips for getting better results from the platform.'

        ],

        'pricing' => [

            'label' =>
                'Pricing',

            'description' =>
                'Provide accurate pricing information or a concise pricing overview.'

        ]

    ];
}


/*=========================================================
RICH TEXT EDITOR SETTINGS
=========================================================*/

function isago_ai_platform_editor_settings()
{
    return [

        'textarea_name' => '',

        'media_buttons' => true,

        'textarea_rows' => 10,

        'teeny' => false,

        'quicktags' => true,

        'tinymce' => [

            'forced_root_block' => 'p',

            'wpautop' => true,

            'menubar' => false,

            'toolbar1' =>
                'formatselect,bold,italic,bullist,numlist,blockquote,link,unlink,undo,redo',

            'toolbar2' =>
                'alignleft,aligncenter,alignright,removeformat,pastetext',

            'block_formats' =>
                'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Quote=blockquote'

        ]

    ];
}


/*=========================================================
FORCE TINYMCE PARAGRAPH SETTINGS
=========================================================*/

add_filter(
    'tiny_mce_before_init',
    'isago_ai_platform_tiny_mce_before_init',
    10,
    2
);

function isago_ai_platform_tiny_mce_before_init(
    $mce_init,
    $editor_id
) {

    if (
        strpos(
            $editor_id,
            'isago_ai_platform_'
        ) !== 0
    ) {

        return $mce_init;

    }


    $mce_init['forced_root_block'] =
        'p';


    $mce_init['wpautop'] =
        true;


    return $mce_init;

}


/*=========================================================
SYNC TINYMCE BEFORE WORDPRESS TAXONOMY SAVE
=========================================================*/

add_action(
    'admin_footer-edit-tags.php',
    'isago_ai_platform_editor_submit_sync'
);

add_action(
    'admin_footer-term.php',
    'isago_ai_platform_editor_submit_sync'
);

function isago_ai_platform_editor_submit_sync()
{
    $screen =
        get_current_screen();


    if (
        !$screen
        ||
        $screen->taxonomy !== 'ai_platform'
    ) {

        return;

    }

    ?>

    <script>
    (function () {

        'use strict';

        var isagoAIPlatformEditorIds = [

            'isago_ai_platform_short_description',

            'isago_ai_platform_how_to_use',

            'isago_ai_platform_best_for',

            'isago_ai_platform_behavior',

            'isago_ai_platform_tips',

            'isago_ai_platform_pricing'

        ];


        function isagoSyncAIPlatformEditors()
        {

            if (
                typeof window.tinymce === 'undefined'
            ) {

                return;

            }


            if (
                typeof window.tinymce.triggerSave === 'function'
            ) {

                window.tinymce.triggerSave();

            }


            isagoAIPlatformEditorIds.forEach(
                function (editorId) {

                    var editor =
                        window.tinymce.get(editorId);


                    if (
                        editor
                        &&
                        typeof editor.save === 'function'
                    ) {

                        editor.save();

                    }

                }
            );

        }


        /*
         * ADD FORM
         */

        document.addEventListener(
            'click',
            function (event) {

                var target =
                    event.target;


                if (!target) {

                    return;

                }


                var submitButton =
                    target.closest('#submit');


                if (!submitButton) {

                    return;

                }


                var form =
                    submitButton.closest('form');


                if (!form) {

                    return;

                }


                if (
                    form.id !== 'addtag'
                ) {

                    return;

                }


                var taxonomyField =
                    form.querySelector(
                        'input[name="taxonomy"]'
                    );


                if (
                    taxonomyField
                    &&
                    taxonomyField.value !== 'ai_platform'
                ) {

                    return;

                }


                isagoSyncAIPlatformEditors();

            },
            true
        );


        /*
         * EDIT FORM
         */

        document.addEventListener(
            'submit',
            function (event) {

                var form =
                    event.target;


                if (!form) {

                    return;

                }


                if (
                    form.id !== 'edittag'
                ) {

                    return;

                }


                var taxonomyField =
                    form.querySelector(
                        'input[name="taxonomy"]'
                    );


                if (
                    taxonomyField
                    &&
                    taxonomyField.value !== 'ai_platform'
                ) {

                    return;

                }


                isagoSyncAIPlatformEditors();

            },
            true
        );


        /*
         * ADDITIONAL EDIT BUTTON SAFETY
         */

        document.addEventListener(
            'click',
            function (event) {

                var target =
                    event.target;


                if (!target) {

                    return;

                }


                var submitButton =
                    target.closest(
                        '#edittag #submit'
                    );


                if (!submitButton) {

                    return;

                }


                isagoSyncAIPlatformEditors();

            },
            true
        );

    })();
    </script>

    <?php
}


/*=========================================================
AI TOOL CATEGORIES
=========================================================*/

add_action(
    'ai_platform_add_form_fields',
    'isago_ai_platform_add_tool_categories'
);

function isago_ai_platform_add_tool_categories()
{

    $categories =
        isago_get_ai_tool_categories(
            [
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]
        );

    ?>

    <div class="isago-accordion">

        <button
            type="button"
            class="isago-accordion-toggle"
            aria-expanded="false"
        >

            <span>
                AI Tool Categories
            </span>

            <span
                class="dashicons dashicons-arrow-down-alt2"
                aria-hidden="true"
            ></span>

        </button>


        <div class="isago-accordion-panel">

            <div class="form-field">

                <?php if (!empty($categories)) : ?>

                    <div
                        class="isago-ai-platform-tool-categories"
                        style="
                            max-height:320px;
                            overflow:auto;
                            padding:12px;
                            border:1px solid #dcdcde;
                            background:#fff;
                        "
                    >

                        <?php foreach (
                            $categories as $category
                        ) : ?>

                            <label
                                style="
                                    display:block;
                                    margin-bottom:8px;
                                "
                            >

                                <input
                                    type="checkbox"
                                    name="isago_ai_tool_categories[]"
                                    value="<?php echo esc_attr(
                                        $category->term_id
                                    ); ?>"
                                >

                                <?php
                                echo esc_html(
                                    $category->name
                                );
                                ?>

                            </label>

                        <?php endforeach; ?>

                    </div>

                <?php else : ?>

                    <p class="description">

                        No AI Tool Categories have been
                        created yet.

                    </p>

                <?php endif; ?>


                <p class="description">

                    Select every category that accurately
                    describes what this AI Tool can help
                    users accomplish.

                </p>

            </div>

        </div>

    </div>

    <?php
}


/*=========================================================
ADD FORM
=========================================================*/

add_action(
    'ai_platform_add_form_fields',
    'isago_ai_platform_add_fields'
);

function isago_ai_platform_add_fields()
{
    $categories =
        isago_get_ai_platform_categories();


    $flags =
        isago_get_ai_platform_flags();

    ?>

    <!--=====================================================
    AI TOOL CURATION
    ======================================================-->

    <div class="isago-accordion">

        <button
            type="button"
            class="isago-accordion-toggle"
            aria-expanded="false"
        >

            <span>
                AI Tool Curation
            </span>

            <span
                class="dashicons dashicons-arrow-down-alt2"
                aria-hidden="true"
            ></span>

        </button>


        <div class="isago-accordion-panel">

            <div class="form-field">

                <p class="description">

                    Use these flags to organize and feature AI
                    tools across Isago.

                </p>


                <?php foreach (
                    $flags as $key => $flag
                ) : ?>

                    <p
                        style="
                            margin:0 0 10px;
                        "
                    >

                        <label>

                            <input
                                type="checkbox"
                                name="isago_ai_platform_flags[]"
                                value="<?php echo esc_attr(
                                    $key
                                ); ?>"
                            >

                            <strong>

                                <?php
                                echo esc_html(
                                    $flag['label']
                                );
                                ?>

                            </strong>

                        </label>

                    </p>

                <?php endforeach; ?>

            </div>

        </div>

    </div>


    <!--=====================================================
    PLATFORM TYPE
    ======================================================-->

    <div class="form-field">

        <label for="isago_ai_platform_category">

            Platform Type

        </label>


        <select
            name="isago_ai_platform_category"
            id="isago_ai_platform_category"
        >

            <option value="">

                Select a category

            </option>


            <?php foreach (
                $categories as $slug => $label
            ) : ?>

                <option
                    value="<?php echo esc_attr($slug); ?>"
                >

                    <?php echo esc_html($label); ?>

                </option>

            <?php endforeach; ?>

        </select>


        <p class="description">

            Select the primary platform type for this AI Tool.

        </p>

    </div>


    <!--=====================================================
    PLATFORM IMAGE
    ======================================================-->


<div class="form-field isago-ai-platform-image-field">

 <?php

$imagekit_account_selector_id =
    isago_render_imagekit_account_selector(
        [
            'id' =>
                'isago_ai_platform_imagekit_account',

            'label' =>
                'ImageKit Account',
        ]
    );


    isago_render_imagekit_folder_selector([
    'id'       => 'isago_ai_platform_imagekit_folder',
    'selected' => '/Isago-Media/AI-Tools/',
    'label'    => 'Destination folder',
    'class'    => 'isago-imagekit-folder-selector',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
?>
  <br>

    <label for="isago_ai_platform_image">

        Platform Image

    </label>


    <input
        type="hidden"
        id="isago_ai_platform_image"
        name="isago_ai_platform_image"
        class="isago-platform-image-id"
        value=""
    >


    <p>



<p>



    <br>


    <button
        type="button"
        class="button button-primary isago-imagekit-upload-button"
        data-target="#isago_ai_platform_image"
        data-preview="#isago_ai_platform_image-preview"
        data-type="image"
        data-account-selector="<?php echo esc_attr(
        '#' . $imagekit_account_selector_id
    ); ?>"
       data-folder-selector="#isago_ai_platform_imagekit_folder"
    >

        Upload to ImageKit

    </button>


<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago_ai_platform_image"
    data-preview="#isago_ai_platform_image-preview"
    data-type="image"
    data-account-selector="<?php echo esc_attr(
        '#' . $imagekit_account_selector_id
    ); ?>"
    data-folder-selector="#isago_ai_platform_imagekit_folder"
>
    Select Existing ImageKit Image
</button>

        

    </p>


    <div
        id="isago_ai_platform_image-preview"
        class="isago-platform-image-preview"
        style="
            display:none;
            width:180px;
            height:180px;
            margin-top:12px;
            padding:10px;
            box-sizing:border-box;
            border:1px solid #dcdcde;
            border-radius:10px;
            background:#f6f7f7;
        "
    >

        <img
            class="isago-preview"
            src=""
            alt=""
            style="
                display:block;
                width:100%;
                height:100%;
                object-fit:contain;
            "
        >

    </div>


    <p class="description">

          Select the ImageKit account where this image should be
    stored, then upload the image directly to ImageKit.
    </p>

</div>





    <!--=====================================================
    WEBSITE
    ======================================================-->

    <div class="form-field">

        <label for="isago_ai_platform_website">

            Platform Website

        </label>


        <input
            type="url"
            name="isago_ai_platform_website"
            id="isago_ai_platform_website"
            class="regular-text"
            placeholder="https://example.com/"
        >


        <p class="description">

            The official website used by the Visit Site button.

        </p>

    </div>


    <!--=====================================================
    RICH TEXT CONTENT
    ======================================================-->

    <?php

    isago_ai_platform_add_editor_field(
        'short_description',
        'Short Description'
    );


    isago_ai_platform_add_editor_field(
        'how_to_use',
        'How to use this platform'
    );


    isago_ai_platform_add_editor_field(
        'best_for',
        'Best for'
    );


    isago_ai_platform_add_editor_field(
        'behavior',
        'How it works'
    );


    isago_ai_platform_add_editor_field(
        'tips',
        'Tips'
    );


    isago_ai_platform_add_editor_field(
        'pricing',
        'Pricing'
    );

}



/*=========================================================
ADD RICH TEXT EDITOR
=========================================================*/

function isago_ai_platform_add_editor_field(
    $key,
    $label
) {

    $definitions = isago_get_ai_platform_field_definitions();

    $editor_id = 'isago_ai_platform_' . $key;
    ?>

    <div class="form-field isago-ai-platform-editor-field">

        <label for="<?php echo esc_attr($editor_id); ?>">
            <?php echo esc_html($label); ?>
        </label>

        <div class="isago-ai-platform-editor">
            <?php
            isago_render_rich_text_editor(
                $editor_id,
                ''
            );
            ?>
        </div>

        <?php if (isset($definitions[$key]['description'])) : ?>

            <p class="description">
                <?php
                echo esc_html(
                    $definitions[$key]['description']
                );
                ?>
            </p>

        <?php endif; ?>

    </div>

    <?php
}



/*=========================================================
EDIT FORM
=========================================================*/

add_action(
    'ai_platform_edit_form_fields',
    'isago_ai_platform_edit_fields',
    10,
    2
);

function isago_ai_platform_edit_fields(
    $term,
    $taxonomy
) {

    if (
        !$term
        ||
        is_wp_error($term)
        ||
        $taxonomy !== 'ai_platform'
    ) {

        return;

    }


    $flags =
        isago_get_ai_platform_flags();


    $categories =
        isago_get_ai_platform_categories();

    ?>


    <!--=====================================================
    AI TOOL CURATION
    ======================================================-->

    <tr class="form-field">

        <th
            colspan="2"
            style="padding:0;"
        >

            <div class="isago-accordion">

                <button
                    type="button"
                    class="isago-accordion-toggle"
                    aria-expanded="false"
                >

                    <span>
                        AI Tool Curation
                    </span>

                    <span
                        class="dashicons dashicons-arrow-down-alt2"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-accordion-panel">

                    <div
                        style="
                            padding:20px;
                        "
                    >

                        <?php foreach (
                            $flags as $key => $flag
                        ) : ?>

                            <?php

                            $enabled =
                                get_term_meta(
                                    $term->term_id,
                                    $flag['meta_key'],
                                    true
                                ) === '1';

                            ?>

                            <p
                                style="
                                    margin:0 0 12px;
                                "
                            >

                                <label>

                                    <input
                                        type="checkbox"
                                        name="isago_ai_platform_flags[]"
                                        value="<?php echo esc_attr(
                                            $key
                                        ); ?>"
                                        <?php checked(
                                            $enabled,
                                            true
                                        ); ?>
                                    >

                                    <strong>

                                        <?php
                                        echo esc_html(
                                            $flag['label']
                                        );
                                        ?>

                                    </strong>

                                </label>


                                <span
                                    class="description"
                                    style="
                                        display:block;
                                        margin-left:25px;
                                    "
                                >

                                    <?php
                                    echo esc_html(
                                        $flag['description']
                                    );
                                    ?>

                                </span>

                            </p>

                        <?php endforeach; ?>


                        <p class="description">

                            These flags control how this AI Tool
                            can be curated throughout Isago.

                        </p>

                    </div>

                </div>

            </div>

        </th>

    </tr>


    <!--=====================================================
    PLATFORM TYPE
    ======================================================-->

    <tr class="form-field">

        <th scope="row">

            <label for="isago_ai_platform_category">

                Platform Type

            </label>

        </th>

        <td>

            <?php

            $selected =
                get_term_meta(
                    $term->term_id,
                    '_isago_ai_platform_category',
                    true
                );

            ?>

            <select
                name="isago_ai_platform_category"
                id="isago_ai_platform_category"
            >

                <option value="">

                    Select a category

                </option>


                <?php foreach (
                    $categories as $slug => $label
                ) : ?>

                    <option
                        value="<?php echo esc_attr($slug); ?>"
                        <?php selected(
                            $selected,
                            $slug
                        ); ?>
                    >

                        <?php echo esc_html($label); ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <p class="description">

                Select the primary platform type for this AI Tool.

            </p>

        </td>

    </tr>


    <!--=====================================================
    PLATFORM IMAGE
    ======================================================-->
<?php

/*
 * Get the media ID currently assigned to this AI Platform.
 *
 * First use the central Isago media reference.
 * If no central reference exists yet, fall back
 * to the legacy term meta value.
 */
$image_media_id =
    isago_media_get_field(
        'term',
        $term->term_id,
        'ai_platform_image'
    );

if (!$image_media_id) {

    $image_media_id =
        absint(
            get_term_meta(
                $term->term_id,
                '_isago_ai_platform_image',
                true
            )
        );
}


/*
 * Resolve the actual image URL.
 */
$image_url = '';

if ($image_media_id) {

    $image_url =
        isago_get_media_image_url(
            $image_media_id,
            'medium'
        );

    /*
     * Legacy WordPress attachment fallback.
     */
    if (
        !$image_url
        &&
        wp_attachment_is_image($image_media_id)
    ) {

        $image_url =
            wp_get_attachment_image_url(
                $image_media_id,
                'medium'
            );

    }

}


/*
 * ---------------------------------------------------------
 * IMAGEKIT ACCOUNT
 * ---------------------------------------------------------
 *
 * Get the account belonging to the current media.
 *
 * If this is a new/empty image field, the reusable selector
 * will automatically use the first configured account.
 */
$current_imagekit_account =
    isago_get_imagekit_media_account_id(
        $image_media_id
    );




?>

<tr
    class="form-field isago-ai-platform-image-field"
>

    <th scope="row">

        <label for="isago_ai_platform_image">

            Platform Image

        </label>

    </th>

    <td>

<?php

    $imagekit_account_selector_id =
        isago_render_imagekit_account_selector(
            [
                'id' =>
                    'isago_ai_platform_imagekit_account',

                'selected' =>
                    $current_imagekit_account,

                'label' =>
                    'ImageKit Account',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago_ai_platform_imagekit_folder',
    'selected' => '/Isago-Media/AI-Tools/',
    'label'    => 'Destination folder',
    'class'    => 'isago-imagekit-folder-selector',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
    ?>


        <input
            type="hidden"
            id="isago_ai_platform_image"
            name="isago_ai_platform_image"
            class="isago-platform-image-id"
            value="<?php echo esc_attr(
                $image_media_id
            ); ?>"
        >


        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago_ai_platform_image"
                data-preview="#isago_ai_platform_image-preview"
                data-type="image"
                data-account-selector="#isago_ai_platform_imagekit_account"
                data-folder-selector="#isago_ai_platform_imagekit_folder"
            >

                <?php echo $image_url
                    ? 'Change Image'
                    : 'Upload to ImageKit';
                ?>

            </button>

        </p>

        <button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago_ai_platform_image"
    data-preview="#isago_ai_platform_image-preview"
    data-type="image"
    data-account-selector="#isago_ai_platform_imagekit_account"
    data-folder-selector="#isago_ai_platform_imagekit_folder"
>
    Select Existing ImageKit Image
</button>


        <div
            id="isago_ai_platform_image-preview"
            class="isago-platform-image-preview"
            style="
                <?php echo $image_url
                    ? 'display:block;'
                    : 'display:none;';
                ?>
                width:180px;
                height:180px;
                margin-top:12px;
                padding:10px;
                box-sizing:border-box;
                border:1px solid #dcdcde;
                border-radius:10px;
                background:#f6f7f7;
            "
        >

            <img
                class="isago-preview"
                src="<?php echo esc_url(
                    $image_url
                ); ?>"
                alt="<?php echo esc_attr(
                    $term->name
                ); ?>"
                style="
                    display:block;
                    width:100%;
                    height:100%;
                    object-fit:contain;
                "
            >

        </div>


        <p class="description">

            Upload a square logo or image representing this platform.

        </p>

    </td>

</tr>


    <!--=====================================================
    WEBSITE
    ======================================================-->

    <?php

    isago_ai_platform_edit_text_field(
        $term,
        'website',
        'Platform Website',
        'url'
    );

    ?>





    <!--=====================================================
    RICH TEXT CONTENT
    ======================================================-->

    <?php

    isago_ai_platform_edit_editor_field(
        $term,
        'short_description',
        'Short Description'
    );


    isago_ai_platform_edit_editor_field(
        $term,
        'how_to_use',
        'How to use ' . $term->name
    );


    isago_ai_platform_edit_editor_field(
        $term,
        'best_for',
        'Best for'
    );


    isago_ai_platform_edit_editor_field(
        $term,
        'behavior',
        'How it works'
    );


    isago_ai_platform_edit_editor_field(
        $term,
        'tips',
        'Tips'
    );


    isago_ai_platform_edit_editor_field(
        $term,
        'pricing',
        'Pricing'
    );

}





/*=========================================================
EDIT AI TOOL CATEGORIES
=========================================================*/

function isago_ai_platform_edit_tool_categories(
    $term,
    $taxonomy
) {

    if (
        !$term
        ||
        is_wp_error($term)
        ||
        $taxonomy !== 'ai_platform'
    ) {

        return;

    }


    $categories =
        isago_get_ai_tool_categories(
            [
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]
        );


    $selected =
        get_term_meta(
            $term->term_id,
            '_isago_ai_tool_categories',
            true
        );


    if (!is_array($selected)) {

        $selected = [];

    }


    $selected =
        array_map(
            'absint',
            $selected
        );

    ?>

    <!--=====================================================
    AI TOOL CATEGORIES ACCORDION
    ======================================================-->

    <tr class="form-field">

        <th
            colspan="2"
            style="padding:0;"
        >

            <div class="isago-accordion">

                <button
                    type="button"
                    class="isago-accordion-toggle"
                    aria-expanded="false"
                >

                    <span>
                        AI Tool Categories
                    </span>

                    <span
                        class="dashicons dashicons-arrow-down-alt2"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-accordion-panel">

                    <div
                        style="
                            padding:20px;
                        "
                    >

                        <?php if (!empty($categories)) : ?>

                            <div
                                class="isago-ai-platform-tool-categories"
                                style="
                                    max-height:320px;
                                    overflow:auto;
                                    padding:12px;
                                    border:1px solid #dcdcde;
                                    background:#fff;
                                "
                            >

                                <?php foreach (
                                    $categories as $category
                                ) : ?>

                                    <label
                                        style="
                                            display:block;
                                            margin-bottom:8px;
                                        "
                                    >

                                        <input
                                            type="checkbox"
                                            name="isago_ai_tool_categories[]"
                                            value="<?php echo esc_attr(
                                                $category->term_id
                                            ); ?>"
                                            <?php checked(
                                                in_array(
                                                    $category->term_id,
                                                    $selected,
                                                    true
                                                )
                                            ); ?>
                                        >

                                        <?php
                                        echo esc_html(
                                            $category->name
                                        );
                                        ?>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                        <?php else : ?>

                            <p class="description">

                                No AI Tool Categories have been
                                created yet.

                            </p>

                        <?php endif; ?>


                        <p class="description">

                            Select all categories that accurately
                            describe this AI Tool.

                        </p>

                    </div>

                </div>

            </div>

        </th>

    </tr>

    <?php
}

add_action(
    'ai_platform_edit_form_fields',
    'isago_ai_platform_edit_tool_categories',
    10,
    2
);


/*=========================================================
EDIT TEXT FIELD
=========================================================*/

function isago_ai_platform_edit_text_field(
    $term,
    $key,
    $label,
    $type = 'text'
) {

    $value =
        get_term_meta(
            $term->term_id,
            '_isago_ai_platform_' . $key,
            true
        );

    ?>

    <tr class="form-field">

        <th scope="row">

            <label
                for="isago_ai_platform_<?php echo esc_attr($key); ?>"
            >

                <?php echo esc_html($label); ?>

            </label>

        </th>

        <td>

            <input
                type="<?php echo esc_attr($type); ?>"
                name="isago_ai_platform_<?php echo esc_attr($key); ?>"
                id="isago_ai_platform_<?php echo esc_attr($key); ?>"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text"
            >


            <?php if ($key === 'website') : ?>

                <p class="description">

                    The official website used by the Visit Site button.

                </p>

            <?php endif; ?>

        </td>

    </tr>

    <?php
}


/*=========================================================
EDIT RICH TEXT EDITOR
=========================================================*/

function isago_ai_platform_edit_editor_field(
    $term,
    $key,
    $label
) {

    $value =
        get_term_meta(
            $term->term_id,
            '_isago_ai_platform_' . $key,
            true
        );


    /*
     * Backward compatibility.
     */

    if (
        $key === 'how_to_use'
        &&
        $value === ''
    ) {

        $legacy_value =
            get_term_meta(
                $term->term_id,
                '_isago_ai_platform_what_is_it',
                true
            );


        if ($legacy_value !== '') {

            $value =
                $legacy_value;

        }

    }


    $editor_id =
        'isago_ai_platform_' . $key;


    $settings =
        isago_ai_platform_editor_settings();


    $settings['textarea_name'] =
        $editor_id;

    ?>

    <tr class="form-field isago-ai-platform-editor-field">

        <th scope="row">

            <label
                for="<?php echo esc_attr($editor_id); ?>"
            >

                <?php echo esc_html($label); ?>

            </label>

        </th>

        <td>

            <div class="isago-ai-platform-editor">

                <?php

               
isago_render_rich_text_editor(
    $editor_id,
    $value
);


                ?>

            </div>


            <?php

            $definitions =
                isago_get_ai_platform_field_definitions();

            ?>


            <?php if (
                isset(
                    $definitions[$key]['description']
                )
            ) : ?>

                <p class="description">

                    <?php echo esc_html(
                        $definitions[$key]['description']
                    ); ?>

                </p>

            <?php endif; ?>

        </td>

    </tr>

    <?php
}


/*=========================================================
AI TOOL CURATION FLAGS
=========================================================*/

function isago_get_ai_platform_flags()
{
    return [

        'hot' => [

            'meta_key' =>
                '_isago_ai_platform_hot',

            'label' =>
                'Hot Tool',

            'description' =>
                'Mark this AI Tool as a hot/trending tool.'

        ],

        'new' => [

            'meta_key' =>
                '_isago_ai_platform_new',

            'label' =>
                'New Tool',

            'description' =>
                'Mark this AI Tool as a new tool.'

        ],

        'free' => [

            'meta_key' =>
                '_isago_ai_platform_free',

            'label' =>
                'Free Tool',

            'description' =>
                'Mark this AI Tool as a free tool.'

        ],

        'popular' => [

            'meta_key' =>
                '_isago_ai_platform_popular',

            'label' =>
                'Most Popular Tool',

            'description' =>
                'Mark this AI Tool as one of the most popular tools.'

        ],

        'featured' => [

            'meta_key' =>
                '_isago_ai_platform_featured',

            'label' =>
                'Featured',

            'description' =>
                'Feature this AI Tool in Isago platform sections.'

        ]

    ];
}


/*=========================================================
SAVE AI TOOL FIELDS
=========================================================*/

add_action(
    'created_ai_platform',
    'isago_save_ai_platform_fields'
);

add_action(
    'edited_ai_platform',
    'isago_save_ai_platform_fields'
);

function isago_save_ai_platform_fields(
    $term_id
) {

    $term_id =
        absint($term_id);


    if (!$term_id) {

        return;

    }


    /*
     * SECURITY:
     *
     * Check capability before saving ANY custom
     * AI Tool term data.
     */

    if (
        !current_user_can(
            'manage_categories'
        )
    ) {

        return;

    }


    /*-----------------------------------------------------
    CURATION FLAGS
    -----------------------------------------------------*/

    $flags =
        isago_get_ai_platform_flags();


    $selected_flags = [];


    if (
        isset(
            $_POST['isago_ai_platform_flags']
        )
        &&
        is_array(
            $_POST['isago_ai_platform_flags']
        )
    ) {

        $selected_flags =
            array_map(
                'sanitize_key',
                wp_unslash(
                    $_POST[
                        'isago_ai_platform_flags'
                    ]
                )
            );

    }


    foreach (
        $flags as $key => $flag
    ) {

        if (
            in_array(
                $key,
                $selected_flags,
                true
            )
        ) {

            update_term_meta(
                $term_id,
                $flag['meta_key'],
                '1'
            );

        } else {

            delete_term_meta(
                $term_id,
                $flag['meta_key']
            );

        }

    }


    /*-----------------------------------------------------
    PLATFORM TYPE
    -----------------------------------------------------*/

    if (
        isset(
            $_POST['isago_ai_platform_category']
        )
    ) {

        $category =
            sanitize_key(
                wp_unslash(
                    $_POST[
                        'isago_ai_platform_category'
                    ]
                )
            );


        $categories =
            isago_get_ai_platform_categories();


        if (
            $category
            &&
            isset(
                $categories[$category]
            )
        ) {

            update_term_meta(
                $term_id,
                '_isago_ai_platform_category',
                $category
            );

        } else {

            delete_term_meta(
                $term_id,
                '_isago_ai_platform_category'
            );

        }

    }


 /*-----------------------------------------------------
PLATFORM IMAGE
-----------------------------------------------------*/

if (
    isset(
        $_POST['isago_ai_platform_image']
    )
) {

    $media_id =
        absint(
            wp_unslash(
                $_POST[
                    'isago_ai_platform_image'
                ]
            )
        );


    /*
     * -----------------------------------------------------
     * NEW ISAGO MEDIA ID
     * -----------------------------------------------------
     */
    if ($media_id) {

        $media =
            isago_get_media(
                $media_id
            );


        if ($media) {

            /*
             * If this is a new ImageKit/Isago media record,
             * attach it to this AI Platform term.
             */
            isago_media_set_field(
                'term',
                $term_id,
                'ai_platform_image',
                $media_id
            );


            update_term_meta(
                $term_id,
                '_isago_ai_platform_image',
                $media_id
            );

        }

    } else {

        /*
         * No image selected.
         *
         * Remove the reference and let the cleanup system
         * determine whether the actual ImageKit file can
         * safely be deleted.
         */
        isago_media_remove_field_and_cleanup(
            'term',
            $term_id,
            'ai_platform_image'
        );


        delete_term_meta(
            $term_id,
            '_isago_ai_platform_image'
        );

    }

}


    /*-----------------------------------------------------
    WEBSITE
    -----------------------------------------------------*/

    if (
        isset(
            $_POST['isago_ai_platform_website']
        )
    ) {

        $website =
            esc_url_raw(
                wp_unslash(
                    $_POST[
                        'isago_ai_platform_website'
                    ]
                )
            );


        if ($website) {

            update_term_meta(
                $term_id,
                '_isago_ai_platform_website',
                $website
            );

        } else {

            delete_term_meta(
                $term_id,
                '_isago_ai_platform_website'
            );

        }

    }



    /*-----------------------------------------------------
    AI TOOL CATEGORIES
    -----------------------------------------------------*/

    if (
        isset(
            $_POST['isago_ai_tool_categories']
        )
    ) {

        $category_ids =
            array_map(
                'absint',
                (array) wp_unslash(
                    $_POST['isago_ai_tool_categories']
                )
            );


        $category_ids =
            array_values(
                array_unique(
                    array_filter(
                        $category_ids
                    )
                )
            );


        if (!empty($category_ids)) {

            $valid_category_ids = [];


            foreach (
                $category_ids as $category_id
            ) {

                $category =
                    get_term(
                        $category_id,
                        'ai_tool_category'
                    );


                if (
                    $category
                    &&
                    !is_wp_error($category)
                ) {

                    $valid_category_ids[] =
                        $category_id;

                }

            }


            $valid_category_ids =
                array_values(
                    array_unique(
                        $valid_category_ids
                    )
                );


            if (!empty($valid_category_ids)) {

                update_term_meta(
                    $term_id,
                    '_isago_ai_tool_categories',
                    $valid_category_ids
                );

            } else {

                delete_term_meta(
                    $term_id,
                    '_isago_ai_tool_categories'
                );

            }

        } else {

            delete_term_meta(
                $term_id,
                '_isago_ai_tool_categories'
            );

        }

    } else {

        delete_term_meta(
            $term_id,
            '_isago_ai_tool_categories'
        );

    }


    /*-----------------------------------------------------
    RICH TEXT CONTENT
    -----------------------------------------------------*/

    $editor_fields = [

        'short_description',

        'how_to_use',

        'best_for',

        'behavior',

        'tips',

        'pricing'

    ];


    foreach (
        $editor_fields as $field
    ) {

        $post_key =
            'isago_ai_platform_' . $field;


        $meta_key =
            '_isago_ai_platform_' . $field;


        if (
            !array_key_exists(
                $post_key,
                $_POST
            )
        ) {

            continue;

        }


        $raw_value =
            wp_unslash(
                $_POST[$post_key]
            );


        $value =
            wp_kses_post(
                $raw_value
            );


        $plain_text =
            trim(
                wp_strip_all_tags(
                    $value
                )
            );


        if (
            $plain_text !== ''
        ) {

            update_term_meta(
                $term_id,
                $meta_key,
                $value
            );

        } else {

            delete_term_meta(
                $term_id,
                $meta_key
            );

        }


        /*
         * Remove legacy How to use field.
         */

        if (
            $field === 'how_to_use'
        ) {

            delete_term_meta(
                $term_id,
                '_isago_ai_platform_what_is_it'
            );

        }

    }

}


/*=========================================================
AI TOOL MODIFICATION TIMESTAMP
=========================================================*/

/*
 * A newly created AI Tool also receives a modification
 * timestamp so it can appear in Recently Updated.
 */

add_action(
    'created_ai_platform',
    'isago_record_ai_platform_modified_at',
    20,
    1
);


/*
 * Update the timestamp whenever an existing AI Tool
 * is edited.
 */

add_action(
    'edited_ai_platform',
    'isago_record_ai_platform_modified_at',
    20,
    1
);


function isago_record_ai_platform_modified_at($term_id)
{
    $term_id = absint($term_id);

    if (!$term_id) {
        return;
    }

    update_term_meta(
        $term_id,
        '_isago_ai_platform_modified_at',
        time()
    );
}



/*=========================================================
CATEGORY COLUMN
=========================================================*/

add_filter(
    'manage_edit-ai_platform_columns',
    'isago_ai_platform_category_column'
);

function isago_ai_platform_category_column(
    $columns
) {

    $columns[
        'isago_platform_category'
    ] =
        'Category';


    return $columns;

}


add_filter(
    'manage_ai_platform_custom_column',
    'isago_ai_platform_category_column_content',
    10,
    3
);

function isago_ai_platform_category_column_content(
    $content,
    $column_name,
    $term_id
) {

    if (
        $column_name !==
        'isago_platform_category'
    ) {

        return $content;

    }


    $category =
        get_term_meta(
            $term_id,
            '_isago_ai_platform_category',
            true
        );


    $categories =
        isago_get_ai_platform_categories();


    if (
        $category
        &&
        isset(
            $categories[$category]
        )
    ) {

        return esc_html(
            $categories[$category]
        );

    }


    return '—';

}
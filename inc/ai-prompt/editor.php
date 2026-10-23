<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI PROMPT — GUTENBERG EDITOR
VARIABLE INSERTION
=========================================================*/

function isago_ai_prompt_enqueue_editor_assets($hook)
{
    /*
     * Only post editor screens.
     */

    if (
        $hook !== 'post.php' &&
        $hook !== 'post-new.php'
    ) {
        return;
    }


    /*
     * Current screen.
     */

    $screen =
        get_current_screen();


    if (!$screen) {
        return;
    }


    /*
     * Only AI Prompt.
     */

    if (
        $screen->post_type !== 'ai_prompt'
    ) {
        return;
    }


/*
 * Ensure the WordPress rich-text editor API
 * is available for Isago's custom editor fields.
 */
wp_enqueue_editor();



    /*
     * Current post ID.
     */

    $post_id = 0;


    if (
        isset($_GET['post'])
    ) {

        $post_id =
            absint(
                $_GET['post']
            );

    }


    /*=====================================================
    GET SAVED VARIABLES
    =====================================================*/

    $variables = [];


    if ($post_id) {

        $variables =
            get_post_meta(
                $post_id,
                '_isago_ai_prompt_variables',
                true
            );

    }


    if (!is_array($variables)) {
        $variables = [];
    }


    $variables =
        array_values(
            array_filter(
                array_map(
                    'sanitize_text_field',
                    $variables
                )
            )
        );


    /*=====================================================
    REGISTER SCRIPT HANDLE
    =====================================================*/

    wp_register_script(
        'isago-ai-prompt-editor',
        false,
       [
    'wp-block-editor',
    'wp-rich-text',
    'wp-compose',
    'wp-element',
    'wp-components',
    'wp-data',
    'wp-hooks',
],
        ISAGO_VERSION,
        true
    );


    wp_enqueue_script(
        'isago-ai-prompt-editor'
    );


    /*=====================================================
    PASS VARIABLES
    =====================================================*/

    wp_add_inline_script(
        'isago-ai-prompt-editor',

        'window.isagoAIPromptEditor = ' .
        wp_json_encode(
            [
                'variables' => $variables,
                'postId'    => $post_id,
            ]
        ) .
        ';',

        'before'
    );


    /*=====================================================
    GUTENBERG JAVASCRIPT
    =====================================================*/

    $script = <<<'JS'

(function () {

    'use strict';


    console.log(
        'ISAGO: AI Prompt Gutenberg editor loaded.'
    );


    /*=====================================================
    CHECK GUTENBERG
    =====================================================*/

    if (
        typeof wp === 'undefined'
    ) {

        console.error(
            'ISAGO: Gutenberg unavailable.'
        );

        return;

    }


    if (
        !wp.data ||
        !wp.hooks ||
        !wp.compose ||
        !wp.element ||
        !wp.components ||
        !wp.blockEditor
    ) {

        console.error(
            'ISAGO: Required Gutenberg APIs unavailable.'
        );

        return;

    }


    /*=====================================================
    VARIABLES
    =====================================================*/

    var variables = [];


    if (
        window.isagoAIPromptEditor &&
        Array.isArray(
            window.isagoAIPromptEditor.variables
        )
    ) {

        variables =
            window.isagoAIPromptEditor.variables
                .map(
                    function (variable) {

                        return String(
                            variable || ''
                        )
                        .trim()
                        .toLowerCase();

                    }
                )
                .filter(
                    function (variable) {

                        return variable !== '';

                    }
                );

    }


    console.log(
        'ISAGO: Saved variables:',
        variables
    );


    /*=====================================================
    NORMALIZE VARIABLE
    =====================================================*/

    function normalizeVariable(
        value
    )
    {

        value =
            String(
                value || ''
            )
            .trim()
            .toLowerCase();


        value =
            value.replace(
                /[^a-z0-9_-]+/g,
                '-'
            );


        value =
            value.replace(
                /^-+|-+$/g,
                ''
            );


        return value;

    }


    /*=====================================================
    GET EDITOR STORE
    =====================================================*/

    function getEditor()
    {

        return wp.data.select(
            'core/block-editor'
        );

    }


    function getDispatch()
    {

        return wp.data.dispatch(
            'core/block-editor'
        );

    }


    /*=====================================================
    GET CURRENT SELECTION
    =====================================================*/

    function getCodeSelection()
    {

        var editor =
            getEditor();


        if (!editor) {
            return null;
        }


        if (
            typeof editor.getSelectionStart !==
            'function'
        ) {

            console.error(
                'ISAGO: getSelectionStart() unavailable.'
            );

            return null;

        }


        var start =
            editor.getSelectionStart();


        var end =
            typeof editor.getSelectionEnd ===
            'function'
                ? editor.getSelectionEnd()
                : start;


        console.log(
            'ISAGO: Gutenberg selection start:',
            start
        );


        console.log(
            'ISAGO: Gutenberg selection end:',
            end
        );


        if (!start) {
            return null;
        }


        /*
         * Selection must belong to a block.
         */

        if (!start.clientId) {
            return null;
        }


        /*
         * We only support the Code block.
         */

        var block =
            editor.getBlock(
                start.clientId
            );


        if (
            !block ||
            block.name !== 'core/code'
        ) {

            return null;

        }


        /*
         * The Code block stores its text
         * in the "content" attribute.
         */

        if (
            start.attributeKey &&
            start.attributeKey !== 'content'
        ) {

            console.warn(
                'ISAGO: Selection is not in Code content:',
                start.attributeKey
            );

            return null;

        }


        /*
         * Make sure the selection end is
         * the same Code block.
         */

        if (
            end &&
            end.clientId &&
            end.clientId !== start.clientId
        ) {

            console.warn(
                'ISAGO: Selection spans multiple blocks.'
            );

            return null;

        }


        /*
         * Gutenberg selection objects use
         * an offset for text position.
         */

        var startOffset =
            typeof start.offset === 'number'
                ? start.offset
                : null;


        var endOffset =
            end &&
            typeof end.offset === 'number'
                ? end.offset
                : startOffset;


        if (
            startOffset === null
        ) {

            console.error(
                'ISAGO: Gutenberg did not provide a text offset.',
                start
            );

            return null;

        }


        return {

            clientId:
                start.clientId,

            start:
                startOffset,

            end:
                endOffset

        };

    }


   



/*=====================================================
INSERT VARIABLE
=====================================================*/
function insertVariable(variable)
{

    'use strict';


    /*=====================================================
    NORMALIZE VARIABLE
    =====================================================*/

    variable =
        String(
            variable || ''
        )
        .trim()
        .toLowerCase();


    variable =
        variable.replace(
            /[^a-z0-9_-]+/g,
            '-'
        );


    variable =
        variable.replace(
            /^-+|-+$/g,
            ''
        );


    if (!variable) {

        alert(
            'Please enter a valid variable name.'
        );

        return;

    }


    /*=====================================================
    GET GUTENBERG STORES
    =====================================================*/

    var editor =
        wp.data.select(
            'core/block-editor'
        );


    var dispatch =
        wp.data.dispatch(
            'core/block-editor'
        );


    if (
        !editor ||
        !dispatch
    ) {

        alert(
            'Gutenberg editor is unavailable.'
        );

        return;

    }


    /*=====================================================
    GET CURRENT SELECTION
    =====================================================*/

    var selection =
        editor.getSelectionStart();


    if (
        !selection ||
        !selection.clientId
    ) {

        alert(
            'Please place your cursor inside the prompt Code block first.'
        );

        return;

    }


    /*=====================================================
    GET SELECTED BLOCK
    =====================================================*/

    var clientId =
        selection.clientId;


    var block =
        editor.getBlock(
            clientId
        );


    if (
        !block ||
        block.name !== 'core/code'
    ) {

        alert(
            'Please place your cursor inside the prompt Code block first.'
        );

        return;

    }


    console.log(
        'ISAGO: Selected Code block:',
        clientId
    );


    /*=====================================================
    FIND EXACT BLOCK DOM
    =====================================================*/

    function findBlockElement(root)
    {

        if (!root) {
            return null;
        }


        var selector =
            '[data-block="' +
            clientId +
            '"]';


        /*
         * Normal document.
         */

        var element =
            root.querySelector(
                selector
            );


        if (element) {
            return element;
        }


        /*
         * Gutenberg iframe document.
         */

        var frames =
            root.querySelectorAll(
                'iframe'
            );


        for (
            var i = 0;
            i < frames.length;
            i++
        ) {

            try {

                var frameDocument =
                    frames[i].contentDocument;


                if (!frameDocument) {
                    continue;
                }


                element =
                    frameDocument.querySelector(
                        selector
                    );


                if (element) {
                    return element;
                }

            } catch (error) {

                console.warn(
                    'ISAGO: Could not inspect editor iframe.',
                    error
                );

            }

        }


        return null;

    }


    var blockElement =
        findBlockElement(
            document
        );


    if (!blockElement) {

        console.error(
            'ISAGO: Selected Gutenberg block element not found:',
            clientId
        );


        alert(
            'The selected Code block could not be located.'
        );

        return;

    }


    console.log(
        'ISAGO: Exact Code block element found:',
        blockElement
    );


    /*=====================================================
    FIND CODE TEXTAREA
    =====================================================*/

    var codeEditor =
        blockElement.querySelector(
            'textarea.wp-block-code__content'
        );


    /*
     * Fallback for WordPress versions where the
     * textarea class differs.
     */

    if (!codeEditor) {

        codeEditor =
            blockElement.querySelector(
                'textarea'
            );

    }


    /*
     * Another fallback for the Code block editor.
     */

    if (!codeEditor) {

        codeEditor =
            blockElement.querySelector(
                '[contenteditable="true"]'
            );

    }


    if (!codeEditor) {

        console.error(
            'ISAGO: Code editor input was not found.',
            blockElement
        );


        alert(
            'The Code block editor could not be located.'
        );

        return;

    }


    console.log(
        'ISAGO: Code editor found:',
        codeEditor
    );


    /*=====================================================
    GET ORIGINAL CONTENT
    =====================================================*/

    var content = '';


    if (
        typeof codeEditor.value ===
        'string'
    ) {

        content =
            codeEditor.value;

    } else {

        content =
            codeEditor.textContent ||
            '';

    }


    console.log(
        'ISAGO: Original Code content:',
        content
    );


    /*=====================================================
    GET NATIVE CURSOR
    =====================================================*/

    var start =
        typeof codeEditor.selectionStart ===
        'number'
            ? codeEditor.selectionStart
            : null;


    var end =
        typeof codeEditor.selectionEnd ===
        'number'
            ? codeEditor.selectionEnd
            : start;


    console.log(
        'ISAGO: Native cursor:',
        start,
        end
    );


    /*=====================================================
    MAKE SURE CODE EDITOR HAS NATIVE SELECTION
    =====================================================*/

    if (
        start === null ||
        end === null
    ) {

        /*
         * Gutenberg may have moved focus to the
         * toolbar when the button was clicked.
         *
         * In that situation, use Gutenberg's
         * selection offset as a fallback.
         */

        if (
            typeof selection.offset ===
            'number'
        ) {

            start =
                selection.offset;

            end =
                start;


            console.log(
                'ISAGO: Using Gutenberg selection offset:',
                start
            );

        } else {

            alert(
                'Please place your cursor inside the prompt Code block first.'
            );

            return;

        }

    }


    /*=====================================================
    NORMALIZE RANGE
    =====================================================*/

    if (
        end < start
    ) {

        var temporary =
            start;

        start =
            end;

        end =
            temporary;

    }


    /*=====================================================
    VALIDATE RANGE
    =====================================================*/

    if (
        start < 0 ||
        start > content.length
    ) {

        console.error(
            'ISAGO: Invalid cursor position.',
            {
                start:
                    start,

                end:
                    end,

                contentLength:
                    content.length,

                content:
                    content
            }
        );


        alert(
            'The Code block cursor position is invalid.'
        );

        return;

    }


    if (
        end > content.length
    ) {

        end =
            content.length;

    }


    /*=====================================================
    CREATE VARIABLE
    =====================================================*/

    var placeholder =
        '{{' +
        variable +
        '}}';


    /*=====================================================
    INSERT INTO ORIGINAL STRING
    =====================================================*/

    var newContent =
        content.substring(
            0,
            start
        ) +
        placeholder +
        content.substring(
            end
        );


    console.log(
        'ISAGO: New Code content:',
        newContent
    );


    /*=====================================================
    UPDATE GUTENBERG BLOCK
    =====================================================*/

    dispatch.updateBlockAttributes(
        clientId,
        {
            content:
                newContent
        }
    );


    /*=====================================================
    NEW CURSOR
    =====================================================*/

    var newPosition =
        start +
        placeholder.length;


    /*=====================================================
    RESTORE CURSOR
    =====================================================*/

    setTimeout(
        function () {

            /*
             * Re-find the exact block because Gutenberg
             * may have re-rendered it.
             */

            var refreshedBlock =
                findBlockElement(
                    document
                );


            if (!refreshedBlock) {

                console.warn(
                    'ISAGO: Could not find Code block after update.'
                );

                return;

            }


            var refreshedEditor =
                refreshedBlock.querySelector(
                    'textarea.wp-block-code__content'
                );


            if (!refreshedEditor) {

                refreshedEditor =
                    refreshedBlock.querySelector(
                        'textarea'
                    );

            }


            if (refreshedEditor) {

                try {

                    refreshedEditor.focus();


                    refreshedEditor.setSelectionRange(
                        newPosition,
                        newPosition
                    );


                    console.log(
                        'ISAGO: Cursor restored:',
                        newPosition
                    );

                } catch (error) {

                    console.warn(
                        'ISAGO: Native cursor restoration failed:',
                        error
                    );

                }

            }


            /*
             * Synchronize Gutenberg selection as well.
             */

            if (
                typeof dispatch.selectionChange ===
                'function'
            ) {

                try {

                    dispatch.selectionChange(
                        clientId,
                        'content',
                        newPosition,
                        newPosition
                    );

                } catch (error) {

                    console.warn(
                        'ISAGO: Gutenberg selection restoration failed:',
                        error
                    );

                }

            }


            console.log(
                'ISAGO: Variable inserted successfully:',
                placeholder
            );

        },
        100
    );

}
    





    /*=====================================================
    TOOLBAR COMPONENTS
    =====================================================*/

    var createHigherOrderComponent =
        wp.compose.createHigherOrderComponent;


    var createElement =
        wp.element.createElement;


    var Fragment =
        wp.element.Fragment;


    var BlockControls =
        wp.blockEditor.BlockControls;


    var ToolbarDropdownMenu =
        wp.components.ToolbarDropdownMenu;


    var ToolbarButton =
        wp.components.ToolbarButton;


    if (
        !createHigherOrderComponent ||
        !BlockControls ||
        !ToolbarDropdownMenu ||
        !ToolbarButton
    ) {

        console.error(
            'ISAGO: Required toolbar components unavailable.'
        );

        return;

    }


    /*=====================================================
    CREATE TOOLBAR
    =====================================================*/

    var withIsagoPromptVariableToolbar =
        createHigherOrderComponent(

            function (BlockEdit) {

                return function (props) {

                    /*
                     * Only Code blocks.
                     */

                    if (
                        props.name !==
                        'core/code'
                    ) {

                        return createElement(
                            BlockEdit,
                            props
                        );

                    }


                    /*-----------------------------------------
                    VARIABLES AVAILABLE
                    -----------------------------------------*/

                    var controls =
                        variables.map(
                            function (variable) {

                                return {

                                    title:
                                        '{{' +
                                        variable +
                                        '}}',

                                    onClick:
                                        function () {

                                            insertVariable(
                                                variable
                                            );

                                        }

                                };

                            }
                        );


                    /*-----------------------------------------
                    NO SAVED VARIABLES
                    -----------------------------------------*/

                    if (
                        !controls.length
                    ) {

                        return createElement(

                            Fragment,

                            null,

                            createElement(
                                BlockEdit,
                                props
                            ),

                            createElement(

                                BlockControls,

                                {
                                    group:
                                        'block'
                                },

                                createElement(

                                    ToolbarButton,

                                    {

                                        icon:
                                            'editor-code',

                                        label:
                                            'Insert Variable',

                                        showTooltip:
                                            true,

                                        onClick:
                                            function () {

                                                var variable =
                                                    window.prompt(
                                                        'Enter variable name:',
                                                        ''
                                                    );


                                                if (
                                                    variable ===
                                                    null
                                                ) {

                                                    return;

                                                }


                                                insertVariable(
                                                    variable
                                                );

                                            }

                                    }

                                )

                            )

                        );

                    }


                    /*-----------------------------------------
                    SAVED VARIABLES DROPDOWN
                    -----------------------------------------*/

                    return createElement(

                        Fragment,

                        null,

                        createElement(
                            BlockEdit,
                            props
                        ),

                        createElement(

                            BlockControls,

                            {
                                group:
                                    'block'
                            },

                            createElement(

                                ToolbarDropdownMenu,

                                {

                                    icon:
                                        'editor-code',

                                    label:
                                        'Insert Variable',

                                    controls:
                                        controls

                                }

                            )

                        )

                    );

                };

            },

            'withIsagoPromptVariableToolbar'

        );


    /*=====================================================
    REGISTER
    =====================================================*/

    wp.hooks.addFilter(

        'editor.BlockEdit',

        'isago/ai-prompt-variable-toolbar',

        withIsagoPromptVariableToolbar

    );


    console.log(
        'ISAGO: Variable toolbar registered.'
    );


})();

JS;


    wp_add_inline_script(
        'isago-ai-prompt-editor',
        $script,
        'after'
    );
}


add_action(
    'admin_enqueue_scripts',
    'isago_ai_prompt_enqueue_editor_assets'
);









/*=========================================================
ISAGO AI PROMPT RICH TEXT EDITOR INITIALIZATION FIX
=========================================================*/

/**
 * Reinitialize the Isago rich text fields after WordPress
 * has mounted the legacy meta boxes in the block editor.
 *
 * This addresses cases where TinyMCE appears on screen
 * but does not accept typing.
 */
function isago_ai_prompt_reinitialize_rich_text_editors()
{
   
$screen = get_current_screen();

if (
    !$screen
    ||
    !in_array(
        $screen->post_type,
        [
            'ai_prompt',
            'isago_video'
        ],
        true
    )
) {
    return;
}  

/*
 * Select the correct editor IDs for the current post type.
 */
if ($screen->post_type === 'isago_video') {

    $editor_ids = [
        'isago_video_ai_prompt'
    ];

} else {

    $editor_ids = [
          'isago_ai_prompt_template_editor',
            'isago_ai_prompt_instructions_editor',
            'isago_ai_prompt_short_description_editor'
    ];

}

    ?>

    <script>
    (function () {
        'use strict';

      var editorIds = <?php echo wp_json_encode($editor_ids); ?>;

        function reinitializeEditors() {
            if (
                !window.wp ||
                !wp.editor ||
                typeof wp.editor.initialize !== 'function'
            ) {
                return;
            }

            var preInit = window.tinyMCEPreInit;

            if (
                !preInit ||
                !preInit.mceInit
            ) {
                return;
            }

            editorIds.forEach(function (editorId) {
                var textarea = document.getElementById(
                    editorId
                );

                var originalSettings =
                    preInit.mceInit[editorId];

                if (
                    !textarea ||
                    !originalSettings
                ) {
                    return;
                }

                /*
                 * Preserve the existing content before
                 * removing the possibly unusable editor.
                 */
                var content = textarea.value || '';

                if (window.tinymce) {
                    var oldEditor = window.tinymce.get(
                        editorId
                    );

                    if (oldEditor) {
                        try {
                            var currentContent =
                                oldEditor.getContent({
                                    format: 'raw'
                                });

                            if (
                                currentContent &&
                                currentContent.trim()
                            ) {
                                content = currentContent;
                            }
                        } catch (error) {
                            // Keep the textarea content.
                        }
                    }
                }

                /*
                 * Remove the old editor instance, if present.
                 */
                try {
                    wp.editor.remove(editorId);
                } catch (error) {
                    // Continue with initialization.
                }

                /*
                 * Restore the field value before rebuilding
                 * TinyMCE and Quicktags.
                 */
                textarea.value = content;

                var tinymceSettings =
                    JSON.parse(
                        JSON.stringify(originalSettings)
                    );

                var quicktagsSettings = false;

                if (
                    preInit.qtInit &&
                    preInit.qtInit[editorId]
                ) {
                    quicktagsSettings =
                        JSON.parse(
                            JSON.stringify(
                                preInit.qtInit[editorId]
                            )
                        );
                }

                wp.editor.initialize(
                    editorId,
                    {
                        tinymce: tinymceSettings,
                        quicktags: quicktagsSettings
                    }
                );
            });
        }

        function scheduleInitialization() {
            /*
             * Allow Gutenberg time to mount its meta boxes.
             */
            window.setTimeout(
                reinitializeEditors,
                1500
            );
        }

        if (
            document.readyState === 'complete'
        ) {
            scheduleInitialization();
        } else {
            window.addEventListener(
                'load',
                scheduleInitialization,
                { once: true }
            );
        }
    })();
    </script>

    <?php
}

add_action(
    'admin_footer-post.php',
    'isago_ai_prompt_reinitialize_rich_text_editors',
    99
);

add_action(
    'admin_footer-post-new.php',
    'isago_ai_prompt_reinitialize_rich_text_editors',
    99
);





/*=========================================================
VIDEO POST RICH TEXT EDITOR ASSETS
=========================================================*/

function isago_video_enqueue_rich_text_editor_assets($hook)
{
    if (
        !in_array(
            $hook,
            [
                'post.php',
                'post-new.php'
            ],
            true
        )
    ) {
        return;
    }

    $screen = get_current_screen();

    if (
        !$screen
        ||
        $screen->post_type !== 'isago_video'
    ) {
        return;
    }

    wp_enqueue_editor();
}

add_action(
    'admin_enqueue_scripts',
    'isago_video_enqueue_rich_text_editor_assets'
);

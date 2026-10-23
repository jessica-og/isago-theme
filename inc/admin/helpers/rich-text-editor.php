
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * ISAGO SHARED RICH TEXT EDITOR
 * =========================================================
 *
 * Reusable TinyMCE configuration and rendering.
 * Individual features remain responsible for their own
 * field IDs, labels, metadata, and saving.
 */

/**
 * Return the shared TinyMCE editor settings.
 */

function isago_rich_text_editor_settings($editor_id)
{
    return [
        'textarea_name' => $editor_id,
        'media_buttons' => true,
        'textarea_rows' => 10,
        'teeny'         => false,
        'quicktags'     => true,
        'tinymce'       => [
            'wpautop'            => true,
            'forced_root_block'  => 'p',
            'menubar'            => false,
            'toolbar1'           => 'formatselect,bold,italic,bullist,numlist,blockquote,link,unlink,undo,redo',
            'toolbar2'           => 'alignleft,aligncenter,alignright,removeformat,pastetext',
            'block_formats'      => 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Quote=blockquote',
        ],
    ];
}

/**
 * Apply consistent paragraph settings to Isago editors.
 */
add_filter(
    'tiny_mce_before_init',
    'isago_rich_text_editor_before_init',
    10,
    2
);

function isago_rich_text_editor_before_init($mce_init, $editor_id)
{
    if (strpos($editor_id, 'isago_') !== 0) {
        return $mce_init;
    }

    $mce_init['forced_root_block'] = 'p';
    $mce_init['wpautop'] = true;

    return $mce_init;
}


/**
 * Render a reusable TinyMCE editor.
 *
 * @param string $editor_id    Unique editor ID.
 * @param string $value        Existing editor content.
 * @param string $textarea_name Submitted form field name.
 */
function isago_render_rich_text_editor(
    $editor_id,
    $value = '',
    $textarea_name = ''
) {
    $editor_id = sanitize_key($editor_id);

    if ($editor_id === '') {
        return;
    }

    $settings = isago_rich_text_editor_settings(
        $editor_id
    );

    // Allow the submitted field name to differ from the editor ID.
    if ($textarea_name !== '') {
        $settings['textarea_name'] = $textarea_name;
    }

    wp_editor(
        $value,
        $editor_id,
        $settings
    );
}
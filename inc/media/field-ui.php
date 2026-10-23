<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO IMAGEKIT FIELD UI
 * ---------------------------------------------------------
 *
 * Renders a reusable ImageKit upload field.
 *
 * This does not decide where the field is used.
 * Existing Isago modules will call this later.
 */


/**
 * Render an ImageKit media field.
 *
 * @param array $args
 *
 * @return void
 */
function isago_imagekit_field($args = [])
{
    $defaults = [
        'id'          => '',
        'media_id'    => 0,
        'type'        => 'image',
        'account'     => 'account_1',
        'label'       => 'Image',
        'button_text' => 'Upload to ImageKit',
       'preview'     => true,
'object_type' => '',
'object_id'   => 0,
'field_key'   => '',
    ];

    $args = wp_parse_args(
        $args,
        $defaults
    );


    $field_id =
        sanitize_html_class(
            $args['id']
        );


    if (!$field_id) {
        return;
    }


    $media_id =
        absint(
            $args['media_id']
        );


    $type =
        in_array(
            $args['type'],
            ['image', 'video'],
            true
        )
            ? $args['type']
            : 'image';


    $account =
        sanitize_key(
            $args['account']
        );


    $label =
        sanitize_text_field(
            $args['label']
        );


    $button_text =
        sanitize_text_field(
            $args['button_text']
        );


    $preview_url = '';


    if ($media_id) {

        if ($type === 'image') {

            $preview_url =
                isago_get_media_image_url(
                    $media_id,
                    'medium'
                );

        } else {

            $preview_url =
                isago_get_media_url(
                    $media_id
                );

        }
    }

    ?>

    <div
        class="isago-imagekit-field"
        data-media-type="<?php echo esc_attr($type); ?>"
    >

        <?php if ($label) : ?>

            <p>
                <strong>
                    <?php echo esc_html($label); ?>
                </strong>
            </p>

        <?php endif; ?>


        <input
            type="hidden"
            id="<?php echo esc_attr($field_id); ?>"
            name="<?php echo esc_attr($field_id); ?>"
            value="<?php echo esc_attr($media_id); ?>"
            class="isago-imagekit-media-id"
        >


        <div
             id="<?php echo esc_attr($field_id); ?>-preview"
    class="isago-imagekit-preview"
            <?php
            if (!$preview_url) {
                echo 'style="display:none;"';
            }
            ?>
        >

            <?php if ($type === 'image') : ?>

                <img
                    src="<?php echo esc_url($preview_url); ?>"
                    alt=""
                >

            <?php else : ?>

                <video
                    src="<?php echo esc_url($preview_url); ?>"
                    controls
                ></video>

            <?php endif; ?>

        </div>


        <p>

            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#<?php echo esc_attr($field_id); ?>"
               data-preview="#<?php echo esc_attr($field_id); ?>-preview"
                data-type="<?php echo esc_attr($type); ?>"
                data-account="<?php echo esc_attr($account); ?>"
            >
                <?php echo esc_html($button_text); ?>
            </button>


            <?php if ($media_id) : ?>

            <button
    type="button"
    class="button isago-imagekit-remove-button"
    data-target="#<?php echo esc_attr($field_id); ?>"
    data-preview="#<?php echo esc_attr($field_id); ?>-preview"
    data-object-type="<?php echo esc_attr($args['object_type']); ?>"
    data-object-id="<?php echo esc_attr($args['object_id']); ?>"
    data-field-key="<?php echo esc_attr($args['field_key']); ?>"
>
    Remove
</button>

            <?php endif; ?>

        </p>

    </div>

    <?php
}
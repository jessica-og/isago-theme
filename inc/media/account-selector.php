<?php

if (!defined('ABSPATH')) {
    exit;
}


/**
 * =========================================================
 * ISAGO IMAGEKIT ACCOUNT SELECTOR
 * =========================================================
 *
 * Reusable admin UI for selecting an ImageKit account.
 *
 * The selector does NOT save the account itself.
 *
 * It simply tells the ImageKit uploader which account
 * should receive a new upload.
 *
 */


/**
 * ---------------------------------------------------------
 * GET IMAGEKIT ACCOUNT FOR MEDIA
 * ---------------------------------------------------------
 *
 * Returns the ImageKit account ID stored on an existing
 * Isago media record.
 *
 * Example:
 *
 *     account_1
 *     account_2
 *
 * If the media does not belong to ImageKit, an empty
 * string is returned.
 *
 * @param int    $media_id
 * @param string $fallback
 *
 * @return string
 */
function isago_get_imagekit_media_account_id(
    $media_id,
    $fallback = ''
) {

    $media_id =
        absint(
            $media_id
        );


    if (!$media_id) {

        return sanitize_key(
            $fallback
        );

    }


    if (
        !function_exists(
            'isago_get_media'
        )
    ) {

        return sanitize_key(
            $fallback
        );

    }


    $media =
        isago_get_media(
            $media_id
        );


    if (
        !$media
        ||
        empty($media->account_id)
        ||
        empty($media->source)
        ||
        $media->source !== 'imagekit'
    ) {

        return sanitize_key(
            $fallback
        );

    }


    return sanitize_key(
        $media->account_id
    );

}


/**
 * ---------------------------------------------------------
 * RENDER IMAGEKIT ACCOUNT SELECTOR
 * ---------------------------------------------------------
 *
 * Outputs a reusable ImageKit account selector.
 *
 * @param array $args
 *
 * Supported arguments:
 *
 *     id
 *     selected
 *     label
 *     class
 *     style
 *     disabled
 *
 * @return string
 */
function isago_render_imagekit_account_selector(
    $args = []
) {

    $defaults = [

        'id' =>
            'isago_imagekit_account',

        'selected' =>
            '',

        'label' =>
            'ImageKit Account',

        'class' =>
            'isago-imagekit-account-selector',

        'style' =>
            'min-width:240px;',

        'disabled' =>
            false,

    ];


    $args =
        wp_parse_args(
            $args,
            $defaults
        );


    $accounts =
        isago_get_imagekit_accounts();


    /*
     * If no account was explicitly selected,
     * use the first configured account.
     */
    $selected =
        sanitize_key(
            $args['selected']
        );


    if (
        !$selected
        &&
        !empty($accounts)
    ) {

        $selected =
            array_key_first(
                $accounts
            );

    }


    /*
     * Make sure the selected account actually exists.
     */
    if (
        $selected
        &&
        !isset(
            $accounts[$selected]
        )
    ) {

        $selected = '';

    }


    $id =
        sanitize_html_class(
            $args['id']
        );


    $class =
        sanitize_html_class(
            $args['class']
        );


    $style =
        esc_attr(
            $args['style']
        );


    $disabled =
        !empty(
            $args['disabled']
        )
        ||
        empty($accounts);


    ?>

    <div class="isago-imagekit-account-selector">

        <?php if (!empty($args['label'])) : ?>

            <label
                for="<?php echo esc_attr($id); ?>"
                style="
                    display:block;
                    margin-bottom:6px;
                "
            >

                <strong>
                    <?php echo esc_html(
                        $args['label']
                    ); ?>
                </strong>

            </label>

        <?php endif; ?>


        <select
            id="<?php echo esc_attr($id); ?>"
            class="<?php echo esc_attr($class); ?>"
            style="<?php echo $style; ?>"
            <?php disabled($disabled); ?>
        >

            <?php if (!empty($accounts)) : ?>

                <?php foreach (
                    $accounts as $account_id => $account
                ) : ?>

                    <option
                        value="<?php echo esc_attr(
                            $account_id
                        ); ?>"
                        <?php selected(
                            $selected,
                            $account_id
                        ); ?>
                    >

                        <?php echo esc_html(
                            $account['name']
                        ); ?>

                    </option>

                <?php endforeach; ?>

            <?php else : ?>

                <option value="">

                    No ImageKit accounts configured

                </option>

            <?php endif; ?>

        </select>

    </div>

    <?php


    /*
     * Return the selector ID so the calling field can
     * connect its ImageKit upload button to this selector.
     */
    return $id;

}
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO IMAGEKIT UPLOAD ENDPOINTS
 * ---------------------------------------------------------
 *
 * Provides secure WordPress AJAX endpoints for the admin.
 *
 * The browser receives only:
 *
 * - ImageKit public key
 * - upload token
 * - signature
 * - expiry
 * - account ID
 * - URL endpoint
 *
 * The private ImageKit key NEVER leaves PHP.
 */


/**
 * ---------------------------------------------------------
 * IMAGEKIT UPLOAD NONCE
 * ---------------------------------------------------------
 */

function isago_imagekit_upload_nonce_action()
{
    return 'isago_imagekit_upload';
}


/**
 * ---------------------------------------------------------
 * AUTHENTICATION ENDPOINT
 * ---------------------------------------------------------
 *
 * The browser calls this before uploading.
 *
 * WordPress verifies:
 *
 * 1. User is logged in.
 * 2. User can upload files.
 * 3. Nonce is valid.
 * 4. Requested ImageKit account exists.
 *
 * Then it generates the temporary ImageKit
 * authentication parameters.
 */

add_action(
    'wp_ajax_isago_imagekit_auth',
    'isago_imagekit_ajax_auth'
);

function isago_imagekit_ajax_auth()
{
    /*
     * Make sure this is an authenticated WordPress
     * administrator/editor request.
     */
    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' =>
                    'You must be logged in.'
            ],
            401
        );
    }


    /*
     * Only users who can upload files may use
     * the ImageKit uploader.
     */
    if (
        !current_user_can(
            'upload_files'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to upload files.'
            ],
            403
        );
    }


    /*
     * Verify WordPress nonce.
     */
    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );


    /*
     * Determine requested ImageKit account.
     *
     * We do NOT trust the account blindly.
     * isago_get_imagekit_account() verifies that
     * it actually exists in our configured accounts.
     */
    $account_id =
        isset($_POST['account_id'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['account_id']
                )
            )
            : 'account_1';


    $account =
        isago_get_imagekit_account(
            $account_id
        );


    if (!$account) {

        wp_send_json_error(
            [
                'message' =>
                    'The selected ImageKit account does not exist.'
            ],
            400
        );
    }


    /*
     * Generate temporary ImageKit credentials.
     */
    $auth =
        isago_imagekit_get_upload_auth(
            $account_id
        );


    if (
        is_wp_error($auth)
    ) {

        wp_send_json_error(
            [
                'message' =>
                    $auth->get_error_message()
            ],
            500
        );
    }


    /*
     * Return ONLY information the browser needs.
     *
     * IMPORTANT:
     *
     * private_key is deliberately absent.
     */
    wp_send_json_success(
        [

            'account_id' =>
                $account_id,

            'public_key' =>
                $auth['publicKey'],

            'url_endpoint' =>
                $account['url'],

            'token' =>
                $auth['token'],

            'signature' =>
                $auth['signature'],

            'expire' =>
                $auth['expire'],

        ]
    );
}





/**
 * ---------------------------------------------------------
 * SAVE IMAGEKIT UPLOAD
 * ---------------------------------------------------------
 *
 * Receives the successful ImageKit upload response and
 * creates the corresponding Isago media record.
 *
 * The browser never sends the ImageKit private key.
 */
add_action(
    'wp_ajax_isago_imagekit_save_upload',
    'isago_imagekit_ajax_save_upload'
);

function isago_imagekit_ajax_save_upload()
{
    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' => 'You must be logged in.'
            ],
            401
        );

    }


    if (!current_user_can('upload_files')) {

        wp_send_json_error(
            [
                'message' => 'You do not have permission to upload files.'
            ],
            403
        );

    }


    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );


    /*
     * Account.
     */
    $account_id =
        isset($_POST['account_id'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['account_id']
                )
            )
            : '';


    if (!$account_id) {

        wp_send_json_error(
            [
                'message' => 'ImageKit account is missing.'
            ],
            400
        );

    }


    if (
        !isago_get_imagekit_account(
            $account_id
        )
    ) {

        wp_send_json_error(
            [
                'message' => 'ImageKit account does not exist.'
            ],
            400
        );

    }


    /*
     * ImageKit response values.
     */
    $file_id =
        isset($_POST['file_id'])
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['file_id']
                )
            )
            : '';


    $file_path =
        isset($_POST['file_path'])
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['file_path']
                )
            )
            : '';


    $url =
        isset($_POST['url'])
            ? esc_url_raw(
                wp_unslash(
                    $_POST['url']
                )
            )
            : '';


    $thumbnail_url =
        isset($_POST['thumbnail_url'])
            ? esc_url_raw(
                wp_unslash(
                    $_POST['thumbnail_url'])
            )
            : '';


    $file_name =
        isset($_POST['file_name'])
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['file_name']
                )
            )
            : '';


    $file_type =
        isset($_POST['file_type'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['file_type'])
            )
            : 'image';


    $mime_type =
        isset($_POST['mime_type'])
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['mime_type'])
            )
            : '';


    $width =
        isset($_POST['width'])
            ? absint(
                $_POST['width']
            )
            : 0;


    $height =
        isset($_POST['height'])
            ? absint(
                $_POST['height']
            )
            : 0;


    $file_size =
        isset($_POST['file_size'])
            ? absint(
                $_POST['file_size']
            )
            : 0;


    /*
     * File ID and URL are essential.
     */
    if (
        empty($file_id)
        ||
        empty($url)
    ) {

        wp_send_json_error(
            [
                'message' => 'ImageKit upload response is incomplete.'
            ],
            400
        );

    }


    /*
     * Only allow media types supported by our
     * central media system.
     */
    if (
        !in_array(
            $file_type,
            [
                'image',
                'video'
            ],
            true
        )
    ) {

        wp_send_json_error(
            [
                'message' => 'Unsupported ImageKit media type.'
            ],
            400
        );

    }


    /*
     * Check whether this exact ImageKit file has already
     * been registered.
     *
     * This prevents accidental duplicate database records.
     */
    $existing =
        isago_media_find_imagekit(
            $account_id,
            $file_id
        );


    if ($existing) {

        wp_send_json_success(
            [
                'media_id' =>
                    absint(
                        $existing->id
                    ),

                'url' =>
                    esc_url(
                        $existing->url
                    ),

                'thumbnail_url' =>
                    !empty($existing->thumbnail_url)
                        ? esc_url(
                            $existing->thumbnail_url
                        )
                        : esc_url(
                            $existing->url
                        ),

                'existing' => true,
            ]
        );

    }


    /*
     * Create the central Isago media record.
     */
    $media_id =
        isago_media_create(
            [
                'source' => 'imagekit',

                'media_type' => $file_type,

                'mime_type' => $mime_type,

                'attachment_id' => 0,

                'account_id' => $account_id,

                'file_id' => $file_id,

                'file_path' => $file_path,

                'url' => $url,

                'thumbnail_url' => $thumbnail_url,

                'width' => $width,

                'height' => $height,

                'file_size' => $file_size,

                'original_name' => $file_name,

                'created_by' =>
                    get_current_user_id(),
            ]
        );


    if (!$media_id) {

        wp_send_json_error(
            [
                'message' =>
                    'The ImageKit file uploaded successfully, but Isago could not save its media record.'
            ],
            500
        );

    }


    wp_send_json_success(
        [
            'media_id' =>
                absint($media_id),

            'url' =>
                esc_url($url),

            'thumbnail_url' =>
                $thumbnail_url
                    ? esc_url($thumbnail_url)
                    : esc_url($url),

            'existing' => false,
        ]
    );
}



/**
 * ---------------------------------------------------------
 * REMOVE MEDIA FIELD
 * ---------------------------------------------------------
 *
 * Removes the media reference from an Isago field.
 *
 * If the ImageKit file has no other references, the
 * cleanup layer deletes it from ImageKit.
 */
add_action(
    'wp_ajax_isago_imagekit_remove_media',
    'isago_imagekit_ajax_remove_media'
);

function isago_imagekit_ajax_remove_media()
{
    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' => 'You must be logged in.'
            ],
            401
        );

    }

    if (!current_user_can('upload_files')) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to remove media.'
            ],
            403
        );

    }

    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );


    $object_type =
        isset($_POST['object_type'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['object_type']
                )
            )
            : '';

    $object_id =
        isset($_POST['object_id'])
            ? absint(
                $_POST['object_id']
            )
            : 0;

    $field_key =
        isset($_POST['field_key'])
            ? sanitize_key(
                wp_unslash(
                    $_POST['field_key']
                )
            )
            : '';


    if (
        !$object_type
        ||
        !$field_key
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'Media field information is missing.'
            ],
            400
        );

    }


    $result =
        isago_media_remove_field_and_cleanup(
            $object_type,
            $object_id,
            $field_key
        );


    if (is_wp_error($result)) {

        wp_send_json_error(
            [
                'message' =>
                    $result->get_error_message()
            ],
            500
        );

    }


    if (!$result) {

        wp_send_json_error(
            [
                'message' =>
                    'The media could not be removed.'
            ],
            500
        );

    }


    wp_send_json_success(
        [
            'message' =>
                'Media removed successfully.'
        ]
    );
}




/**
 * ---------------------------------------------------------
 * DELETE UNREFERENCED MEDIA
 * ---------------------------------------------------------
 *
 * Used when an ImageKit file was uploaded but has not yet
 * been attached to a saved Isago object.
 *
 * This prevents abandoned uploads from remaining forever.
 */
add_action(
    'wp_ajax_isago_imagekit_delete_unreferenced',
    'isago_imagekit_ajax_delete_unreferenced'
);

function isago_imagekit_ajax_delete_unreferenced()
{
    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' => 'You must be logged in.'
            ],
            401
        );
    }


    if (!current_user_can('upload_files')) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to remove media.'
            ],
            403
        );
    }


    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );


    $media_id =
        isset($_POST['media_id'])
            ? absint(
                $_POST['media_id']
            )
            : 0;


    if (!$media_id) {

        wp_send_json_error(
            [
                'message' =>
                    'Media ID is missing.'
            ],
            400
        );
    }


    $media =
        isago_media_get(
            $media_id
        );


    if (!$media) {

        /*
         * The desired final state is already reached.
         */
        wp_send_json_success(
            [
                'message' =>
                    'Media record no longer exists.'
            ]
        );
    }


    /*
     * NEVER allow this endpoint to delete referenced media.
     */
    $reference_count =
        isago_media_reference_count(
            $media_id
        );


    if ($reference_count > 0) {

        wp_send_json_error(
            [
                'message' =>
                    'This media is currently being used.'
            ],
            409
        );
    }


    /*
     * Delete the unreferenced media.
     */
    $result =
        isago_media_delete_if_unused(
            $media_id
        );


    if (is_wp_error($result)) {

        wp_send_json_error(
            [
                'message' =>
                    $result->get_error_message()
            ],
            500
        );
    }


    if (!$result) {

        wp_send_json_error(
            [
                'message' =>
                    'The media could not be deleted.'
            ],
            500
        );
    }


    wp_send_json_success(
        [
            'message' =>
                'Unreferenced media deleted successfully.'
        ]
    );
}




/**
 * ---------------------------------------------------------
 * GET MEDIA RECORD
 * ---------------------------------------------------------
 *
 * Temporary diagnostic endpoint.
 */
add_action(
    'wp_ajax_isago_imagekit_get_media',
    'isago_imagekit_ajax_get_media'
);

function isago_imagekit_ajax_get_media()
{
    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' => 'You must be logged in.'
            ],
            401
        );

    }


    if (!current_user_can('upload_files')) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to view media.'
            ],
            403
        );

    }


    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );


    $media_id =
        isset($_POST['media_id'])
            ? absint(
                $_POST['media_id']
            )
            : 0;


    if (!$media_id) {

        wp_send_json_error(
            [
                'message' =>
                    'Media ID is missing.'
            ],
            400
        );

    }


    $media =
        isago_get_media(
            $media_id
        );


    if (!$media) {

        wp_send_json_error(
            [
                'message' =>
                    'Media record could not be found.'
            ],
            404
        );

    }


    $reference_count =
        isago_media_reference_count(
            $media_id
        );


    $media_data =
        (array) $media;


    $media_data['reference_count'] =
        absint(
            $reference_count
        );


    wp_send_json_success(
        [
            'media' =>
                $media_data
        ]
    );
}


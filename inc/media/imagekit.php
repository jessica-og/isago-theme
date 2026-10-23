<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO IMAGEKIT API
 * ---------------------------------------------------------
 *
 * Low-level ImageKit API communication.
 *
 * This file is responsible for:
 *
 * - Upload authentication
 * - Server-side API requests
 * - ImageKit file deletion
 *
 * It does NOT decide where media is used.
 * That belongs to the media layer.
 */


/**
 * ---------------------------------------------------------
 * API REQUEST
 * ---------------------------------------------------------
 *
 * Make an authenticated request to ImageKit's REST API.
 *
 * ImageKit uses HTTP Basic Authentication where the
 * private API key is the username and the password is empty.
 *
 * @param string $account_id
 * @param string $method
 * @param string $endpoint
 * @param array  $body
 *
 * @return array|WP_Error
 */
function isago_imagekit_api_request(
    $account_id,
    $method,
    $endpoint,
    $body = null
) {

    $account =
        isago_get_imagekit_account(
            $account_id
        );

    if (!$account) {

        return new WP_Error(
            'isago_imagekit_account_missing',
            'ImageKit account could not be found.'
        );

    }


    if (
        empty($account['private_key'])
    ) {

        return new WP_Error(
            'isago_imagekit_private_key_missing',
            'ImageKit private API key is not configured.'
        );

    }


    $url =
        'https://api.imagekit.io'
        . '/'
        . ltrim(
            $endpoint,
            '/'
        );


    $args = [

        'method' => strtoupper($method),

        'timeout' => 30,

        'headers' => [

            'Authorization' =>
                'Basic '
                .
                base64_encode(
                    $account['private_key'] . ':'
                ),

            'Content-Type' =>
                'application/json',

            'Accept' =>
                'application/json',

        ],

    ];


    if ($body !== null) {

        $args['body'] =
            wp_json_encode(
                $body
            );

    }


    $response =
        wp_remote_request(
            $url,
            $args
        );


    if (
        is_wp_error($response)
    ) {

        return $response;

    }


    $status =
        wp_remote_retrieve_response_code(
            $response
        );


    $raw_body =
        wp_remote_retrieve_body(
            $response
        );


    $data =
        json_decode(
            $raw_body,
            true
        );


    if (
        $status < 200
        ||
        $status >= 300
    ) {

        $message =
            'ImageKit API request failed.';


        if (
            is_array($data)
            &&
            !empty($data['message'])
        ) {

            $message =
                sanitize_text_field(
                    $data['message']
                );

        }


        return new WP_Error(

            'isago_imagekit_api_error',

            $message,

            [

                'status' =>
                    $status,

                'response' =>
                    $data,

            ]

        );

    }


    return [

        'status' =>
            $status,

        'data' =>
            is_array($data)
                ? $data
                : [],

    ];
}


/**
 * ---------------------------------------------------------
 * UPLOAD AUTHENTICATION
 * ---------------------------------------------------------
 *
 * Generate the authentication parameters required by
 * ImageKit browser uploads.
 *
 * ImageKit requires:
 *
 * token
 * signature
 * expire
 *
 * The signature is HMAC-SHA1 of:
 *
 * token + expire
 *
 * using the ImageKit private key.
 *
 * @param string $account_id
 *
 * @return array|WP_Error
 */
function isago_imagekit_get_upload_auth(
    $account_id
) {

    $account =
        isago_get_imagekit_account(
            $account_id
        );

    if (!$account) {

        return new WP_Error(
            'isago_imagekit_account_missing',
            'ImageKit account could not be found.'
        );

    }


    if (
        empty($account['private_key'])
    ) {

        return new WP_Error(
            'isago_imagekit_private_key_missing',
            'ImageKit private API key is not configured.'
        );

    }


    /*
     * Generate a unique upload token.
     */
    $token =
        wp_generate_uuid4();


    /*
     * ImageKit requires the expiry timestamp
     * to be less than one hour in the future.
     *
     * We use 30 minutes.
     */
    $expire =
        time() + (30 * MINUTE_IN_SECONDS);


    /*
     * ImageKit signature:
     *
     * HMAC-SHA1(
     *     token + expire,
     *     private_key
     * )
     */
    $signature =
        hash_hmac(

            'sha1',

            $token . $expire,

            $account['private_key']

        );


    return [

        'token' =>
            $token,

        'expire' =>
            $expire,

        'signature' =>
            $signature,

        'publicKey' =>
            $account['public_key'],

        'accountId' =>
            $account_id,

    ];
}


/**
 * ---------------------------------------------------------
 * DELETE FILE
 * ---------------------------------------------------------
 *
 * Permanently delete an ImageKit file by fileId.
 *
 * If the file has already been deleted from ImageKit,
 * that condition is treated as success because the desired
 * final state has already been reached.
 *
 * @param string $account_id
 * @param string $file_id
 *
 * @return true|WP_Error
 */
function isago_imagekit_delete_file(
    $account_id,
    $file_id
) {

    $file_id =
        sanitize_text_field(
            $file_id
        );


    if (!$file_id) {

        return new WP_Error(
            'isago_imagekit_file_missing',
            'ImageKit file ID is missing.'
        );

    }


    $response =
        isago_imagekit_api_request(

            $account_id,

            'DELETE',

            '/v1/files/'
            .
            rawurlencode(
                $file_id
            )

        );


    /*
     * If ImageKit returned an error, check whether
     * the file simply no longer exists.
     */
    if (
        is_wp_error($response)
    ) {

        $error_data =
            $response->get_error_data();


        $status =
            is_array($error_data)
                ? absint(
                    $error_data['status'] ?? 0
                )
                : 0;


        /*
         * HTTP 404 means the ImageKit file is already gone.
         *
         * That is acceptable for cleanup.
         */
        if ($status === 404) {

            return true;
        }


        /*
         * Any other API error is a real failure.
         */
        return $response;
    }


    return true;
}
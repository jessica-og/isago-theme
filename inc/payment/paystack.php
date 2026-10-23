<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
PAYSTACK API
=========================================================*/

/**
 * Paystack API base URL.
 */
function isago_get_paystack_api_url()
{
    return 'https://api.paystack.co';
}


/**
 * Convert an NGN decimal amount to kobo.
 *
 * Example:
 *
 * 100.00  -> 10000
 * 2500.50 -> 250050
 *
 * @param mixed $amount
 * @return int
 */
function isago_paystack_amount_to_kobo($amount)
{
    if (!is_numeric($amount)) {
        return 0;
    }

    $amount = (float) $amount;

    if ($amount <= 0) {
        return 0;
    }

    return (int) round(
        $amount * 100
    );
}


/**
 * Make a request to Paystack.
 *
 * Secret key is obtained only from the
 * server-side payment settings.
 *
 * @param string $method
 * @param string $endpoint
 * @param array  $body
 * @return array
 */
function isago_paystack_request(
    $method,
    $endpoint,
    $body = array()
) {
    $secret_key =
        isago_get_paystack_secret_key();

    if ($secret_key === '') {

        return array(
            'success' => false,
            'message' => 'Paystack secret key is not configured.',
            'data'    => array(),
        );

    }


    $method =
        strtoupper(
            sanitize_key($method)
        );


    $endpoint =
        '/' .
        ltrim(
            $endpoint,
            '/'
        );


    $url =
        isago_get_paystack_api_url() .
        $endpoint;


    $headers = array(
        'Authorization' =>
            'Bearer ' . $secret_key,

        'Content-Type' =>
            'application/json',

        'Cache-Control' =>
            'no-cache',
    );


    $args = array(
        'method'  => $method,
        'timeout' => 30,
        'headers' => $headers,
    );


    if (
        in_array(
            $method,
            array(
                'POST',
                'PUT',
                'PATCH',
            ),
            true
        )
    ) {

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
        is_wp_error(
            $response
        )
    ) {

        return array(
            'success' => false,
            'message' =>
                $response->get_error_message(),
            'data'    => array(),
        );

    }


    $http_code =
        (int) wp_remote_retrieve_response_code(
            $response
        );


    $raw_body =
        wp_remote_retrieve_body(
            $response
        );


    $decoded =
        json_decode(
            $raw_body,
            true
        );


    if (
        !is_array(
            $decoded
        )
    ) {

        return array(
            'success' => false,
            'message' =>
                'Invalid response received from Paystack.',
            'data'    => array(),
            'http_code' => $http_code,
        );

    }


    /*
     * Paystack's top-level "status" represents
     * whether the API request itself succeeded.
     *
     * The actual transaction status is inside
     * data.status when verifying a transaction.
     */
    if (
        empty(
            $decoded['status']
        )
    ) {

        $message =
            isset(
                $decoded['message']
            )
                ? sanitize_text_field(
                    $decoded['message']
                )
                : 'Paystack API request failed.';


        return array(
            'success'   => false,
            'message'   => $message,
            'data'      =>
                isset(
                    $decoded['data']
                ) &&
                is_array(
                    $decoded['data']
                )
                    ? $decoded['data']
                    : array(),
            'http_code' => $http_code,
        );

    }


    return array(
        'success'   => true,
        'message'   =>
            isset(
                $decoded['message']
            )
                ? sanitize_text_field(
                    $decoded['message']
                )
                : '',
        'data'      =>
            isset(
                $decoded['data']
            ) &&
            is_array(
                $decoded['data']
            )
                ? $decoded['data']
                : array(),
        'http_code' => $http_code,
    );
}


/*=========================================================
INITIALIZE TRANSACTION
=========================================================*/

/**
 * Initialize a Paystack transaction.
 *
 * IMPORTANT:
 * The caller must provide the amount calculated
 * by Isago's server.
 *
 * Never accept a customer-supplied amount as
 * the authoritative payment amount.
 *
 * @param string $email
 * @param int    $amount_kobo
 * @param string $reference
 * @param string $callback_url
 * @param array  $metadata
 * @return array
 */
function isago_paystack_initialize_transaction(
    $email,
    $amount_kobo,
    $reference,
    $callback_url = '',
    $metadata = array()
) {
    $email =
        sanitize_email(
            $email
        );


    if (
        !is_email(
            $email
        )
    ) {

        return array(
            'success' => false,
            'message' => 'A valid customer email is required.',
            'data'    => array(),
        );

    }


    $amount_kobo =
        absint(
            $amount_kobo
        );


    if ($amount_kobo <= 0) {

        return array(
            'success' => false,
            'message' => 'Invalid payment amount.',
            'data'    => array(),
        );

    }


    $reference =
        sanitize_text_field(
            $reference
        );


    if ($reference === '') {

        return array(
            'success' => false,
            'message' => 'Payment reference is required.',
            'data'    => array(),
        );

    }


    /*
     * Paystack references should contain only
     * safe characters.
     */
 if (
    !preg_match(
        '/^[A-Za-z0-9\-\.\=]+$/',
        $reference
    )
) {

    return array(
        'success' => false,
        'message' => 'Invalid payment reference.',
        'data'    => array(),
    );

}


    $payload = array(
        'email'    => $email,
        'amount'   => $amount_kobo,
        'currency' => 'NGN',
        'reference'=> $reference,
    );


    if (
        $callback_url !== ''
    ) {

        $payload['callback_url'] =
            esc_url_raw(
                $callback_url
            );

    }


    if (
        !empty(
            $metadata
        ) &&
        is_array(
            $metadata
        )
    ) {

        $payload['metadata'] =
            $metadata;

    }


    return isago_paystack_request(
        'POST',
        '/transaction/initialize',
        $payload
    );
}


/*=========================================================
VERIFY TRANSACTION
=========================================================*/

/**
 * Verify a Paystack transaction.
 *
 * @param string $reference
 * @return array
 */
function isago_paystack_verify_transaction(
    $reference
) {
    $reference =
        sanitize_text_field(
            $reference
        );


    if (
        $reference === ''
    ) {

        return array(
            'success' => false,
            'message' => 'Payment reference is required.',
            'data'    => array(),
        );

    }


  if (
    !preg_match(
        '/^[A-Za-z0-9\-\.\=]+$/',
        $reference
    )
) {

    return array(
        'success' => false,
        'message' => 'Invalid payment reference.',
        'data'    => array(),
    );

}


    return isago_paystack_request(
        'GET',
        '/transaction/verify/' .
        rawurlencode(
            $reference
        )
    );
}


/*=========================================================
VERIFY SUCCESSFUL PAYMENT
=========================================================*/

/**
 * Check whether a Paystack verification response
 * represents a valid successful NGN payment.
 *
 * This function does NOT grant access.
 *
 * The order/entitlement system will do that later.
 *
 * @param array $verification
 * @param int   $expected_amount_kobo
 * @param string $expected_reference
 * @return array
 */
function isago_paystack_validate_payment(
    $verification,
    $expected_amount_kobo,
    $expected_reference
) {
    if (
        !is_array(
            $verification
        )
    ) {

        return array(
            'valid'   => false,
            'status'  => 'invalid',
            'message' => 'Invalid Paystack verification response.',
            'data'    => array(),
        );

    }


    if (
        empty(
            $verification['success']
        )
    ) {

        return array(
            'valid'   => false,
            'status'  => 'invalid',
            'message' =>
                isset(
                    $verification['message']
                )
                    ? $verification['message']
                    : 'Paystack verification failed.',
            'data'    => array(),
        );

    }


    $data =
        isset(
            $verification['data']
        ) &&
        is_array(
            $verification['data']
        )
            ? $verification['data']
            : array();


    $transaction_status =
        isset(
            $data['status']
        )
            ? sanitize_key(
                $data['status']
            )
            : 'unknown';


    $reference =
        isset(
            $data['reference']
        )
            ? sanitize_text_field(
                $data['reference']
            )
            : '';


    $amount =
        isset(
            $data['amount']
        )
            ? absint(
                $data['amount']
            )
            : 0;


    $currency =
        isset(
            $data['currency']
        )
            ? strtoupper(
                sanitize_text_field(
                    $data['currency']
                )
            )
            : '';


    /*
     * The Paystack transaction status is inside
     * data.status, not the top-level API status.
     */
    if (
        $transaction_status !== 'success'
    ) {

        return array(
            'valid'   => false,
            'status'  => $transaction_status,
            'message' =>
                'Payment has not been confirmed as successful.',
            'data'    => $data,
        );

    }


    /*
     * Verify the reference.
     */
    if (
        $expected_reference !== '' &&
        !hash_equals(
            $expected_reference,
            $reference
        )
    ) {

        return array(
            'valid'   => false,
            'status'  => 'reference_mismatch',
            'message' => 'Payment reference does not match.',
            'data'    => $data,
        );

    }


    /*
     * Verify the amount.
     */
    if (
        absint(
            $expected_amount_kobo
        ) !== $amount
    ) {

        return array(
            'valid'   => false,
            'status'  => 'amount_mismatch',
            'message' => 'Payment amount does not match the order.',
            'data'    => $data,
        );

    }


    /*
     * Verify currency.
     */
    if (
        $currency !== 'NGN'
    ) {

        return array(
            'valid'   => false,
            'status'  => 'currency_mismatch',
            'message' => 'Payment currency does not match the order.',
            'data'    => $data,
        );

    }


    return array(
        'valid'   => true,
        'status'  => 'paid',
        'message' => 'Payment verified successfully.',
        'data'    => $data,
    );
}
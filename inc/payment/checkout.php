<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PAYMENT CHECKOUT
=========================================================*/


/**
 * Get the URL to which Paystack should return the customer.
 *
 * This is only for the customer's browser flow.
 *
 * It is NOT considered proof of payment.
 *
 * @param string $order_id
 * @return string
 */
function isago_get_payment_callback_url(
    $order_id
) {
    $order_id =
        sanitize_text_field(
            $order_id
        );


    return add_query_arg(
        array(
            'isago_payment' => 'callback',
            'order'         => $order_id,
        ),
        home_url('/')
    );
}


/*=========================================================
CREATE CHECKOUT
=========================================================*/

/**
 * Start checkout for an AI Prompt.
 *
 * IMPORTANT:
 * The prompt price is always read from WordPress.
 * A browser-supplied price is never trusted.
 *
 * @param int $prompt_id
 * @return array
 */
function isago_start_prompt_checkout(
    $prompt_id
) {
    $prompt_id =
        absint(
            $prompt_id
        );


    /*
     * User must be logged in.
     */
    if (
        !is_user_logged_in()
    ) {

        return array(
            'success'  => false,
            'code'     => 'login_required',
            'message'  => 'You must be signed in to purchase this AI Prompt.',
            'checkout' => null,
        );

    }


    $user_id =
        get_current_user_id();


    if (
        !$user_id
    ) {

        return array(
            'success'  => false,
            'code'     => 'invalid_user',
            'message'  => 'Unable to identify the current user.',
            'checkout' => null,
        );

    }


    /*
     * Validate prompt.
     */
    if (
        !$prompt_id ||
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return array(
            'success'  => false,
            'code'     => 'invalid_prompt',
            'message'  => 'Invalid AI Prompt.',
            'checkout' => null,
        );

    }


    /*
     * Make sure the post actually exists and is published.
     */
    $prompt =
        get_post(
            $prompt_id
        );


    if (
        !$prompt ||
        $prompt->post_status !== 'publish'
    ) {

        return array(
            'success'  => false,
            'code'     => 'prompt_unavailable',
            'message'  => 'This AI Prompt is not available for purchase.',
            'checkout' => null,
        );

    }


    /*
     * The prompt must be paid.
     */
    if (
        !isago_is_ai_prompt_paid(
            $prompt_id
        )
    ) {

        return array(
            'success'  => false,
            'code'     => 'prompt_is_free',
            'message'  => 'This AI Prompt is free.',
            'checkout' => null,
        );

    }


    /*
     * Get the CURRENT price directly from the prompt.
     *
     * Never accept a price from JavaScript.
     */
    $prompt_price =
        isago_get_ai_prompt_price(
            $prompt_id
        );


    if (
        $prompt_price === '0.00' ||
        (float) $prompt_price <= 0
    ) {

        return array(
            'success'  => false,
            'code'     => 'invalid_price',
            'message'  => 'This AI Prompt does not have a valid price.',
            'checkout' => null,
        );

    }


    /*
     * If the user already owns the prompt, there is
     * no reason to create another checkout.
     */
    if (
        isago_user_has_prompt_entitlement(
            $user_id,
            $prompt_id
        )
    ) {

        return array(
            'success'  => false,
            'code'     => 'already_owned',
            'message'  => 'You already own this AI Prompt.',
            'checkout' => null,
        );

    }


    /*
     * Get customer email from the WordPress account.
     */
    $user =
        get_userdata(
            $user_id
        );


    if (
        !$user ||
        !is_email(
            $user->user_email
        )
    ) {

        return array(
            'success'  => false,
            'code'     => 'invalid_email',
            'message'  => 'Your account does not have a valid email address.',
            'checkout' => null,
        );

    }


    /*
     * Create the Isago order first.
     *
     * This creates the pending payment record before
     * contacting Paystack.
     */
    $order_result =
        isago_create_order(
            $user_id,
            $prompt_id,
            $prompt_price
        );


    if (
        empty(
            $order_result['success']
        ) ||
        empty(
            $order_result['order']
        )
    ) {

        return array(
            'success'  => false,
            'code'     => 'order_creation_failed',
            'message'  =>
                isset(
                    $order_result['message']
                )
                    ? $order_result['message']
                    : 'Unable to create payment order.',
            'checkout' => null,
        );

    }


    $order =
        $order_result['order'];


    /*
     * Convert the server-side NGN amount to kobo.
     */
    $amount_kobo =
        isago_paystack_amount_to_kobo(
            $order['amount']
        );


    if (
        $amount_kobo <= 0
    ) {

        isago_mark_order_failed(
            $order['order_id']
        );


        return array(
            'success'  => false,
            'code'     => 'invalid_amount',
            'message'  => 'Unable to calculate the payment amount.',
            'checkout' => null,
        );

    }


    /*
     * Paystack callback URL.
     *
     * Remember:
     *
     * callback = customer experience
     * webhook/verification = payment authority
     */
    $callback_url =
        isago_get_payment_callback_url(
            $order['order_id']
        );


    /*
     * Metadata helps us identify the Isago order
     * when processing the Paystack response.
     */
    $metadata = array(

        'isago_order_id' =>
            $order['order_id'],

        'isago_user_id' =>
            (string) $user_id,

        'isago_prompt_id' =>
            (string) $prompt_id,

        'isago_product_type' =>
            'ai_prompt',

    );


    /*
     * Initialize Paystack transaction.
     */
    $paystack =
        isago_paystack_initialize_transaction(
            $user->user_email,
            $amount_kobo,
            $order['provider_reference'],
            $callback_url,
            $metadata
        );


    if (
        empty(
            $paystack['success']
        )
    ) {

        isago_mark_order_failed(
            $order['order_id']
        );


        return array(
            'success'  => false,
            'code'     => 'paystack_initialization_failed',
            'message'  =>
                isset(
                    $paystack['message']
                )
                    ? $paystack['message']
                    : 'Unable to initialize Paystack checkout.',
            'checkout' => null,
        );

    }


    /*
     * Paystack should return authorization data.
     */
    $paystack_data =
        isset(
            $paystack['data']
        ) &&
        is_array(
            $paystack['data']
        )
            ? $paystack['data']
            : array();


    $authorization_url =
        isset(
            $paystack_data['authorization_url']
        )
            ? esc_url_raw(
                $paystack_data['authorization_url']
            )
            : '';


    $access_code =
        isset(
            $paystack_data['access_code']
        )
            ? sanitize_text_field(
                $paystack_data['access_code']
            )
            : '';


    $reference =
        isset(
            $paystack_data['reference']
        )
            ? sanitize_text_field(
                $paystack_data['reference']
            )
            : '';


    /*
     * We must have a usable checkout URL.
     */
    if (
        $authorization_url === ''
    ) {

        isago_mark_order_failed(
            $order['order_id']
        );


        return array(
            'success'  => false,
            'code'     => 'missing_authorization_url',
            'message'  => 'Paystack did not return a checkout URL.',
            'checkout' => null,
        );

    }


    /*
     * Save the Paystack reference if one was returned.
     *
     * The reference should normally match the reference
     * we supplied during initialization.
     */
    if (
        $reference !== '' &&
        $reference !==
            $order['provider_reference']
    ) {

        isago_mark_order_failed(
            $order['order_id']
        );


        return array(
            'success'  => false,
            'code'     => 'reference_mismatch',
            'message'  => 'Paystack returned an unexpected payment reference.',
            'checkout' => null,
        );

    }


    /*
     * Save the Paystack transaction ID when available.
     *
     * At initialization this may not yet exist, so this
     * is intentionally optional.
     */
    if (
        isset(
            $paystack_data['id']
        ) &&
        $paystack_data['id'] !== ''
    ) {

        isago_update_order_provider_transaction(
            $order['order_id'],
            (string) $paystack_data['id']
        );

    }


    return array(
        'success' => true,
        'code'    => 'checkout_ready',
        'message' => 'Paystack checkout initialized.',
        'checkout' => array(

            'authorization_url' =>
                $authorization_url,

            'access_code' =>
                $access_code,

            'reference' =>
                $order['provider_reference'],

            'order_id' =>
                $order['order_id'],

            'amount' =>
                $order['amount'],

            'currency' =>
                'NGN',

        ),
    );
}


/*=========================================================
CHECKOUT AJAX
=========================================================*/

/**
 * AJAX endpoint for logged-in users.
 */
function isago_ajax_start_prompt_checkout()
{
    /*
     * Verify WordPress AJAX nonce.
     */
    check_ajax_referer(
        'isago_prompt_checkout',
        'nonce'
    );


    /*
     * Only authenticated users may purchase
     * through this endpoint.
     */
    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'code' =>
                    'login_required',

                'message' =>
                    'Please sign in before purchasing this AI Prompt.',
            ),
            401
        );

    }


    $prompt_id =
        isset(
            $_POST['prompt_id']
        )
            ? absint(
                $_POST['prompt_id']
            )
            : 0;


    if (
        !$prompt_id
    ) {

        wp_send_json_error(
            array(
                'code' =>
                    'invalid_prompt',

                'message' =>
                    'Invalid AI Prompt.',
            ),
            400
        );

    }


    $result =
        isago_start_prompt_checkout(
            $prompt_id
        );


    if (
        empty(
            $result['success']
        )
    ) {

        wp_send_json_error(
            array(

                'code' =>
                    isset(
                        $result['code']
                    )
                        ? $result['code']
                        : 'checkout_failed',

                'message' =>
                    isset(
                        $result['message']
                    )
                        ? $result['message']
                        : 'Unable to start checkout.',

            ),
            400
        );

    }


    wp_send_json_success(
        $result['checkout']
    );
}

add_action(
    'wp_ajax_isago_start_prompt_checkout',
    'isago_ajax_start_prompt_checkout'
);
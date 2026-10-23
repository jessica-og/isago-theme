<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PAYSTACK WEBHOOK
=========================================================*/


/**
 * Register the Paystack webhook REST route.
 *
 * URL:
 *
 * /wp-json/isago/v1/paystack/webhook
 */
function isago_register_paystack_webhook_route()
{
    register_rest_route(
        'isago/v1',
        '/paystack/webhook',
        array(

            'methods' =>
                WP_REST_Server::CREATABLE,

            'callback' =>
                'isago_handle_paystack_webhook',

            'permission_callback' =>
                '__return_true',

        )
    );
}

add_action(
    'rest_api_init',
    'isago_register_paystack_webhook_route'
);


/*=========================================================
WEBHOOK SIGNATURE
=========================================================*/


/**
 * Validate Paystack webhook signature.
 *
 * Paystack signs the raw request body using HMAC-SHA512
 * and the Paystack secret key.
 *
 * @param string $raw_body
 * @param string $signature
 * @return bool
 */
function isago_verify_paystack_webhook_signature(
    $raw_body,
    $signature
) {
    $secret_key =
        isago_get_paystack_secret_key();


    if (
        $secret_key === '' ||
        $raw_body === '' ||
        $signature === ''
    ) {

        return false;

    }


    $expected_signature =
        hash_hmac(
            'sha512',
            $raw_body,
            $secret_key
        );


    return hash_equals(
        $expected_signature,
        $signature
    );
}


/*=========================================================
READ WEBHOOK BODY
=========================================================*/


/**
 * Get the raw webhook request body.
 *
 * @return string
 */
function isago_get_paystack_webhook_raw_body()
{
    return file_get_contents(
        'php://input'
    );
}


/*=========================================================
FIND HEADER
=========================================================*/


/**
 * Get Paystack signature header.
 *
 * WordPress/server configurations can expose HTTP
 * headers differently, so we check the common locations.
 *
 * @return string
 */
function isago_get_paystack_signature()
{
    $signature = '';


    if (
        isset(
            $_SERVER['HTTP_X_PAYSTACK_SIGNATURE']
        )
    ) {

        $signature =
            sanitize_text_field(
                wp_unslash(
                    $_SERVER[
                        'HTTP_X_PAYSTACK_SIGNATURE'
                    ]
                )
            );

    }


    /*
     * Some server configurations expose headers
     * through getallheaders().
     */
    if (
        $signature === '' &&
        function_exists(
            'getallheaders'
        )
    ) {

        $headers =
            getallheaders();


        if (
            is_array(
                $headers
            )
        ) {

            foreach (
                $headers
                as $name => $value
            ) {

                if (
                    strtolower(
                        $name
                    ) ===
                    'x-paystack-signature'
                ) {

                    $signature =
                        sanitize_text_field(
                            $value
                        );

                    break;

                }

            }

        }

    }


    return $signature;
}


/*=========================================================
HANDLE WEBHOOK
=========================================================*/


/**
 * Handle Paystack webhook.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function isago_handle_paystack_webhook(
    WP_REST_Request $request
) {
    /*
     * IMPORTANT:
     *
     * We verify the RAW body.
     *
     * Do not reconstruct the JSON before calculating
     * the signature.
     */
    $raw_body =
        isago_get_paystack_webhook_raw_body();


    $signature =
        isago_get_paystack_signature();


    /*
     * Reject unsigned/invalid requests.
     */
    if (
        !isago_verify_paystack_webhook_signature(
            $raw_body,
            $signature
        )
    ) {

        return new WP_REST_Response(
            array(
                'success' => false,
                'message' => 'Invalid webhook signature.',
            ),
            401
        );

    }


    /*
     * Decode JSON.
     */
    $payload =
        json_decode(
            $raw_body,
            true
        );


    if (
        !is_array(
            $payload
        )
    ) {

        return new WP_REST_Response(
            array(
                'success' => false,
                'message' => 'Invalid webhook payload.',
            ),
            400
        );

    }


    $event =
        isset(
            $payload['event']
        )
            ? sanitize_text_field(
                $payload['event']
            )
            : '';


    $data =
        isset(
            $payload['data']
        ) &&
        is_array(
            $payload['data']
        )
            ? $payload['data']
            : array();


    if (
        $event === ''
    ) {

        return new WP_REST_Response(
            array(
                'success' => false,
                'message' => 'Missing webhook event.',
            ),
            400
        );

    }


    /*
     * Process successful transactions.
     */
    if (
        $event === 'charge.success'
    ) {

        isago_process_paystack_charge_success(
            $data
        );


        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => 'Webhook received.',
            ),
            200
        );

    }


    /*
     * Refund lifecycle.
     */
    switch ($event) {

        case 'refund.pending':

            isago_process_paystack_refund_event(
                'pending',
                $data
            );

            break;


        case 'refund.processing':

            isago_process_paystack_refund_event(
                'processing',
                $data
            );

            break;


        case 'refund.needs-attention':

            isago_process_paystack_refund_event(
                'needs_attention',
                $data
            );

            break;


        case 'refund.failed':

            isago_process_paystack_refund_event(
                'failed',
                $data
            );

            break;


        case 'refund.processed':

            isago_process_paystack_refund_event(
                'processed',
                $data
            );

            break;

    }


    /*
     * We acknowledge events that Isago does not
     * currently need to process.
     */
    return new WP_REST_Response(
        array(
            'success' => true,
            'message' => 'Webhook received.',
        ),
        200
    );
}


/*=========================================================
SUCCESSFUL CHARGE
=========================================================*/


/**
 * Process charge.success.
 *
 * We DO NOT trust the webhook payload by itself.
 *
 * The transaction is verified directly with Paystack
 * before the Isago order is marked paid.
 *
 * @param array $data
 * @return bool
 */
function isago_process_paystack_charge_success(
    $data
) {
    if (
        !is_array(
            $data
        )
    ) {

        return false;

    }


    $reference =
        isset(
            $data['reference']
        )
            ? sanitize_text_field(
                $data['reference']
            )
            : '';


    if (
        $reference === ''
    ) {

        return false;

    }


    /*
     * Find the Isago order using the Paystack reference.
     */
    $order =
        isago_get_order_by_provider_reference(
            $reference
        );


    if (
        !$order
    ) {

        /*
         * Do not create an order from an unknown webhook.
         */
        return false;

    }


    /*
     * Idempotency:
     *
     * If the order is already paid, do not grant another
     * entitlement or perform the payment process again.
     */
    if (
        $order['status'] === 'paid'
    ) {

        /*
         * Make sure the entitlement exists.
         */
        if (
            !isago_user_has_prompt_entitlement(
                absint(
                    $order['user_id']
                ),
                absint(
                    $order['prompt_id']
                )
            )
        ) {

            isago_grant_prompt_entitlement(
                absint(
                    $order['user_id']
                ),
                absint(
                    $order['prompt_id']
                ),
                $order['order_id']
            );

        }


        return true;

    }


    /*
     * Never process a refunded or reversed order
     * as a fresh payment.
     */
    if (
        in_array(
            $order['status'],
            array(
                'refunded',
                'reversed',
            ),
            true
        )
    ) {

        return false;

    }


    /*
     * Calculate the exact amount expected by Isago.
     */
    $expected_amount_kobo =
        isago_paystack_amount_to_kobo(
            $order['amount']
        );


    /*
     * Verify the transaction directly with Paystack.
     */
    $verification =
        isago_paystack_verify_transaction(
            $reference
        );


    /*
     * Validate:
     *
     * - transaction status
     * - reference
     * - amount
     * - currency
     */
    $validated =
        isago_paystack_validate_payment(
            $verification,
            $expected_amount_kobo,
            $reference
        );


    if (
        empty(
            $validated['valid']
        )
    ) {

        /*
         * A webhook saying charge.success does not
         * override failed server-side verification.
         */
        return false;

    }


    $verified_data =
        isset(
            $validated['data']
        ) &&
        is_array(
            $validated['data']
        )
            ? $validated['data']
            : array();


/*
 * Save the payment method used.
 *
 * This comes from Paystack's server-side
 * verified transaction data.
 */
if (
    isset(
        $verified_data['channel']
    ) &&
    $verified_data['channel'] !== ''
) {

    isago_update_order_payment_method(
        $order['order_id'],
        $verified_data['channel']
    );

}


    /*
     * Save Paystack's transaction ID.
     */
    if (
        isset(
            $verified_data['id']
        ) &&
        $verified_data['id'] !== ''
    ) {

        isago_update_order_provider_transaction(
            $order['order_id'],
            (string) $verified_data['id']
        );

    }


    /*
     * Mark the Isago order as paid.
     */
    $updated =
        isago_update_order_status(
            $order['order_id'],
            'paid'
        );


    if (
        !$updated
    ) {

        return false;

    }


    /*
     * Re-read the order after updating it.
     */
    $order =
        isago_get_order_by_id(
            $order['order_id']
        );


    if (
        !$order ||
        $order['status'] !== 'paid'
    ) {

        return false;

    }


    /*
     * Grant the actual prompt entitlement.
     */
    $entitlement =
        isago_grant_prompt_entitlement(
            absint(
                $order['user_id']
            ),
            absint(
                $order['prompt_id']
            ),
            $order['order_id']
        );


    return !empty(
        $entitlement['success']
    );
}


/*=========================================================
REFUNDS
=========================================================*/


/**
 * Process a Paystack refund event.
 *
 * Paystack provides these refund states:
 *
 * pending
 * processing
 * needs_attention
 * failed
 * processed
 *
 * @param string $refund_status
 * @param array  $data
 * @return bool
 */
function isago_process_paystack_refund_event(
    $refund_status,
    $data
) {
    if (
        !is_array(
            $data
        )
    ) {

        return false;

    }


    if (
        !isago_is_valid_refund_status(
            $refund_status
        )
    ) {

        return false;

    }


    /*
     * Paystack refund events identify the original
     * transaction through transaction_reference.
     */
    $reference =
        isset(
            $data['transaction_reference']
        )
            ? sanitize_text_field(
                $data['transaction_reference']
            )
            : '';


    /*
     * Some payloads may expose a transaction reference
     * differently. Fall back to reference if available.
     */
    if (
        $reference === '' &&
        isset(
            $data['reference']
        )
    ) {

        $reference =
            sanitize_text_field(
                $data['reference']
            );

    }


    if (
        $reference === ''
    ) {

        return false;

    }


    $order =
        isago_get_order_by_provider_reference(
            $reference
        );


    if (
        !$order
    ) {

        return false;

    }


    /*
     * Store the refund lifecycle state.
     */
    $updated =
        isago_update_order_refund_status(
            $order['order_id'],
            $refund_status
        );


    if (
        !$updated
    ) {

        return false;

    }


    /*
     * Only a successfully processed refund revokes
     * access.
     *
     * A failed refund means the transaction remains
     * successful according to Paystack's refund
     * lifecycle.
     */
    if (
        $refund_status === 'processed'
    ) {

        isago_revoke_entitlement_by_order(
            $order['order_id']
        );

    }


    return true;
}
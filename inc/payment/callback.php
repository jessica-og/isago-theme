<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PAYMENT CALLBACK
=========================================================*/


/**
 * Register the customer payment callback.
 *
 * Paystack redirects the customer here after checkout.
 *
 * Example:
 *
 * /?isago_payment=callback&order=ISG-...&reference=...
 */
function isago_register_payment_callback()
{
    /*
     * WordPress does not need a separate rewrite rule for
     * this simple callback.
     *
     * We handle it during template_redirect.
     */
}
add_action(
    'init',
    'isago_register_payment_callback'
);


/*=========================================================
HANDLE CALLBACK
=========================================================*/


/**
 * Handle Paystack customer callback.
 *
 * IMPORTANT:
 *
 * Visiting this URL does NOT automatically grant access.
 *
 * We verify the transaction directly with Paystack.
 * The webhook remains the authoritative fulfillment path.
 *
 * @return void
 */
function isago_handle_payment_callback()
{
    if (
        !isset(
            $_GET['isago_payment']
        ) ||
        $_GET['isago_payment'] !== 'callback'
    ) {

        return;

    }


    /*
     * Get the Isago order ID.
     */
    $order_id =
        isset(
            $_GET['order']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $_GET['order']
                )
            )
            : '';


    /*
     * Get Paystack reference.
     *
     * Paystack appends the reference to the callback URL.
     */
    $reference =
        isset(
            $_GET['reference']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $_GET['reference']
                )
            )
            : '';


    /*
     * We need at least the Paystack reference.
     */
    if (
        $reference === ''
    ) {

        isago_render_payment_result_page(
            'error',
            'Payment could not be verified.',
            'We could not find the Paystack transaction reference.'
        );

        exit;

    }


    /*
     * Find the order using the Paystack reference.
     */
    $order =
        isago_get_order_by_provider_reference(
            $reference
        );


    if (
        !$order
    ) {

        isago_render_payment_result_page(
            'error',
            'Payment order not found.',
            'We could not find the Isago order associated with this payment.'
        );

        exit;

    }


    /*
     * If an order ID was supplied, make sure it matches
     * the Paystack reference we found.
     */
    if (
        $order_id !== '' &&
        $order['order_id'] !== $order_id
    ) {

        isago_render_payment_result_page(
            'error',
            'Payment information does not match.',
            'The payment reference and Isago order do not match.'
        );

        exit;

    }


    /*
     * If the visitor is logged in, make sure the order
     * belongs to that user.
     *
     * We do not require login here because a customer
     * may return through a browser session that changed.
     *
     * The actual order itself remains tied to the user
     * stored when checkout was created.
     */
    if (
        is_user_logged_in()
    ) {

        $current_user_id =
            get_current_user_id();


        if (
            absint(
                $order['user_id']
            ) !==
            absint(
                $current_user_id
            )
        ) {

            isago_render_payment_result_page(
                'error',
                'Payment account mismatch.',
                'This payment belongs to a different Isago account.'
            );

            exit;

        }

    }


    /*
     * If the order is already paid, we do not verify and
     * fulfill it again.
     *
     * This prevents duplicate fulfillment.
     */
   if (
    $order['status'] === 'paid'
) {

    $prompt_url =
        get_permalink(
            absint(
                $order['prompt_id']
            )
        );

    isago_render_payment_result_page(
        'success',
        'Payment successful.',
        'Your AI Prompt purchase has been confirmed and access is active.',
        $prompt_url,
        $reference
    );

    exit;

}


    /*
     * Do not allow a refunded or reversed order to
     * become paid again from a browser callback.
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

        isago_render_payment_result_page(
            'error',
            'This payment is no longer active.',
            'The payment associated with this order is no longer valid.'
        );

        exit;

    }


    /*
     * Calculate the expected amount from the Isago order.
     *
     * We do NOT use an amount supplied by the browser.
     */
    $expected_amount_kobo =
        isago_paystack_amount_to_kobo(
            $order['amount']
        );


    /*
     * Verify directly with Paystack.
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


    /*
     * Payment is NOT successful.
     */
    if (
        empty(
            $validated['valid']
        )
    ) {

        $status =
            isset(
                $validated['status']
            )
                ? sanitize_key(
                    $validated['status']
                )
                : 'unknown';


        /*
         * Keep the order pending for states that may
         * still complete later.
         */
        if (
            in_array(
                $status,
                array(
                    'pending',
                    'ongoing',
                    'processing',
                ),
                true
            )
        ) {

            isago_render_payment_result_page(
                'pending',
                'Payment is still processing.',
                'Your payment has not been confirmed yet. Please wait a moment and check your account again.'
            );

            exit;

        }


        /*
         * Paystack reports abandoned checkout.
         */
        if (
            $status === 'abandoned'
        ) {

            isago_mark_order_abandoned(
                $order['order_id']
            );


            isago_render_payment_result_page(
                'cancelled',
                'Payment was not completed.',
                'The payment checkout was abandoned before the transaction was completed.'
            );

            exit;

        }


        /*
         * Failed transaction.
         */
        if (
            $status === 'failed'
        ) {

            isago_mark_order_failed(
                $order['order_id']
            );


            isago_render_payment_result_page(
                'failed',
                'Payment failed.',
                'The payment was not completed. You can try again.'
            );

            exit;

        }


        /*
         * Reversed transaction.
         */
        if (
            $status === 'reversed'
        ) {

            isago_mark_order_reversed(
                $order['order_id']
            );


            isago_revoke_entitlement_by_order(
                $order['order_id']
            );


            isago_render_payment_result_page(
                'error',
                'Payment was reversed.',
                'This transaction is no longer valid.'
            );

            exit;

        }


        /*
         * Reference, amount or currency mismatch.
         *
         * Never grant access.
         */
        isago_render_payment_result_page(
            'error',
            'Payment could not be confirmed.',
            'The transaction details did not match the Isago order.'
        );

        exit;

    }


    /*
     * Payment is verified successfully.
     *
     * Save Paystack's transaction ID.
     */
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
 * Paystack provides this as the verified
 * transaction channel.
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
     * Mark the order paid.
     */
    $updated =
        isago_update_order_status(
            $order['order_id'],
            'paid'
        );


    if (
        !$updated
    ) {

        /*
         * The webhook may have processed the payment
         * at almost the same time.
         *
         * Re-read the order before treating this as
         * an error.
         */
        $order =
            isago_get_order_by_id(
                $order['order_id']
            );


        if (
            !$order ||
            $order['status'] !== 'paid'
        ) {

            isago_render_payment_result_page(
                'error',
                'Payment was verified but could not be recorded.',
                'Please contact Isago support with your payment reference.'
            );

            exit;

        }

    }


    /*
     * Re-read the order.
     */
    $order =
        isago_get_order_by_id(
            $order['order_id']
        );


    if (
        !$order ||
        $order['status'] !== 'paid'
    ) {

        isago_render_payment_result_page(
            'error',
            'Payment could not be completed.',
            'The payment was verified but the order could not be updated.'
        );

        exit;

    }


    /*
     * Grant entitlement.
     *
     * This is idempotent. If the webhook already granted
     * access, this will simply find the existing active
     * entitlement.
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


    if (
        empty(
            $entitlement['success']
        )
    ) {

        isago_render_payment_result_page(
            'error',
            'Payment verified but access was not activated.',
            'Please contact Isago support with your payment reference.'
        );

        exit;

    }


  /*
 * Success.
 */
$prompt_url =
    get_permalink(
        absint(
            $order['prompt_id']
        )
    );

isago_render_payment_result_page(
    'success',
    'Payment successful.',
    'Your AI Prompt purchase has been confirmed and access has been activated.',
    $prompt_url,
    $reference
);

exit;
}

add_action(
    'template_redirect',
    'isago_handle_payment_callback',
    1
);



/*=========================================================
PAYMENT RESULT STYLES
=========================================================*/

/**
 * Enqueue the Isago payment result stylesheet.
 *
 * The payment callback exits through template_redirect,
 * so the stylesheet must be queued before wp_head()
 * is printed by the standalone result page.
 */
function isago_enqueue_payment_result_styles()
{
    wp_enqueue_style(
        'isago-payment-result',
        ISAGO_URI . '/assets/css/payment-result.css',
        array(),
        function_exists(
            'isago_asset_version'
        )
            ? isago_asset_version(
                '/assets/css/payment-result.css'
            )
            : ISAGO_VERSION
    );
}


/*=========================================================
RESULT PAGE
=========================================================*/


/**
 * Render a simple payment result page.
 *
 * This is intentionally independent of the Isago theme
 * templates so the callback can work reliably.
 *
 * @param string $type
 * @param string $title
 * @param string $message
 * @return void
 */
function isago_render_payment_result_page(
    $type,
    $title,
    $message,
    $prompt_url = '',
    $reference = ''
) {

  /*
     * Load the payment result stylesheet before
     * wp_head() is printed below.
     */
    isago_enqueue_payment_result_styles();

    $type =
        sanitize_key(
            $type
        );


    if (
        !in_array(
            $type,
            array(
                'success',
                'pending',
                'failed',
                'cancelled',
                'error',
            ),
            true
        )
    ) {

        $type = 'error';

    }


    $title =
        sanitize_text_field(
            $title
        );


    $message =
        sanitize_text_field(
            $message
        );


    $account_url =
        function_exists(
            'isago_get_account_url'
        )
            ? isago_get_account_url()
            : wp_login_url();


    $home_url =
        home_url('/');


    status_header(
        200
    );


    nocache_headers();


    ?>
    <!doctype html>

    <html
        <?php
        language_attributes();
        ?>
    >

    <head>

        <meta
            charset="<?php
            bloginfo('charset');
            ?>"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >

        <title>
            <?php
            echo esc_html(
                $title
            );
            ?>
            -
            <?php
            bloginfo('name');
            ?>
        </title>

        <?php
        wp_head();
        ?>

    </head>


    <body
        <?php
        body_class(
            'isago-payment-result'
        );
        ?>
    >

        <?php
        wp_body_open();
        ?>


    <main
    class="isago-payment-result-page"
>

    <div
        class="isago-payment-result-card"
    >

        <div
            class="isago-payment-result-icon <?php
            echo esc_attr(
                'is-' . $type
            );
            ?>"
        >

            <?php

            if (
                $type === 'success'
            ) {

                echo '✓';

            } elseif (
                $type === 'pending'
            ) {

                echo '…';

            } else {

                echo '!';

            }

            ?>

        </div>


        <h1
            class="isago-payment-result-title"
        >

            <?php
            echo esc_html(
                $title
            );
            ?>

        </h1>


        <p
            class="isago-payment-result-message"
        >

            <?php
            echo esc_html(
                $message
            );
            ?>

        </p>


        <?php if (
            $type === 'success' &&
            !empty($reference)
        ) : ?>

            <p
                class="isago-payment-result-reference"
            >

                Transaction reference:

                <strong>
                    <?php
                    echo esc_html(
                        $reference
                    );
                    ?>
                </strong>

            </p>

        <?php endif; ?>


        <div
            class="isago-payment-result-actions"
        >

            <a
                href="<?php
                echo esc_url(
                    $account_url
                );
                ?>"
                class="isago-payment-result-button"
            >
                My Account
            </a>


            <a
                href="<?php
                echo esc_url(
                    $home_url
                );
                ?>"
                class="
                    isago-payment-result-button
                    isago-payment-result-button-secondary
                "
            >
                Return Home
            </a>

        </div>

    </div>

</main>

        <?php
        wp_footer();
        ?>

    </body>

    </html>

    <?php
}
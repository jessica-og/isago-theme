<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PAYMENT ORDERS
=========================================================*/

/**
 * Get the database table name for Isago orders.
 *
 * @return string
 */
function isago_get_orders_table_name()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_orders';
}


/*=========================================================
ORDER STATUSES
=========================================================*/

/**
 * Get supported Isago order statuses.
 *
 * These are Isago's internal statuses.
 *
 * @return array
 */
function isago_get_order_statuses()
{
    return array(

        'pending' => array(
            'label' => 'Pending',
        ),

        'paid' => array(
            'label' => 'Paid',
        ),

        'failed' => array(
            'label' => 'Failed',
        ),

        'cancelled' => array(
            'label' => 'Cancelled',
        ),

        'abandoned' => array(
            'label' => 'Abandoned',
        ),

        'reversed' => array(
            'label' => 'Reversed',
        ),

        'refunded' => array(
            'label' => 'Refunded',
        ),

    );
}


/**
 * Check whether an order status is valid.
 *
 * @param string $status
 * @return bool
 */
function isago_is_valid_order_status($status)
{
    $statuses =
        isago_get_order_statuses();

    return isset(
        $statuses[$status]
    );
}


/*=========================================================
REFUND STATUSES
=========================================================*/

/**
 * Get supported refund statuses.
 *
 * Refund status is kept separately from
 * the main order status.
 *
 * @return array
 */
function isago_get_refund_statuses()
{
    return array(

        'none' => array(
            'label' => 'No Refund',
        ),

        'pending' => array(
            'label' => 'Refund Pending',
        ),

        'processing' => array(
            'label' => 'Refund Processing',
        ),

        'processed' => array(
            'label' => 'Refund Processed',
        ),

        'failed' => array(
            'label' => 'Refund Failed',
        ),

        'needs_attention' => array(
            'label' => 'Refund Needs Attention',
        ),

    );
}





/**
 * Check whether a refund status is valid.
 *
 * @param string $status
 * @return bool
 */
function isago_is_valid_refund_status($status)
{
    $statuses =
        isago_get_refund_statuses();

    return isset(
        $statuses[$status]
    );
}



/*=========================================================
PAYMENT METHODS
=========================================================*/

/**
 * Get a human-readable payment method label.
 *
 * Paystack returns the actual payment channel used
 * for a transaction.
 *
 * @param string $payment_method
 * @return string
 */
function isago_get_payment_method_label(
    $payment_method
) {

    $payment_method =
        sanitize_key(
            $payment_method
        );


    if (
        $payment_method === ''
    ) {

        return '—';

    }


    $labels = array(

        'card' => 'Card',

        'bank' => 'Bank',

        'bank_transfer' => 'Bank Transfer',

        'ussd' => 'USSD',

        'qr' => 'QR',

        'mobile_money' => 'Mobile Money',

        'apple_pay' => 'Apple Pay',

        'google_pay' => 'Google Pay',

        'eft' => 'EFT',

        'capitec_pay' => 'Capitec Pay',

        'payattitude' => 'Payattitude',

    );


    if (
        isset(
            $labels[
                $payment_method
            ]
        )
    ) {

        return $labels[
            $payment_method
        ];

    }


    /*
     * Graceful fallback for any future
     * Paystack payment channel.
     */
    return ucwords(
        str_replace(
            '_',
            ' ',
            $payment_method
        )
    );
}


/**
 * Save the payment method used for an order.
 *
 * This stores only the payment channel, such as:
 *
 * card
 * bank_transfer
 * ussd
 *
 * It does NOT store card details.
 *
 * @param string $order_id
 * @param string $payment_method
 * @return bool
 */
function isago_update_order_payment_method(
    $order_id,
    $payment_method
) {

    global $wpdb;


    $order_id =
        sanitize_text_field(
            $order_id
        );


    $payment_method =
        sanitize_key(
            $payment_method
        );


    if (
        $order_id === ''
        ||
        $payment_method === ''
    ) {

        return false;

    }


    $result =
        $wpdb->update(

            isago_get_orders_table_name(),

            array(

                'payment_method' =>
                    $payment_method,

                'updated_at' =>
                    current_time(
                        'mysql',
                        true
                    ),

            ),

            array(

                'order_id' =>
                    $order_id,

            ),

            array(

                '%s',
                '%s',

            ),

            array(

                '%s',

            )

        );


    return false !== $result;
}


/*=========================================================
CREATE ORDERS TABLE
=========================================================*/

/**
 * Create or update the Isago orders table.
 *
 * @return void
 */
function isago_create_orders_table()
{
    global $wpdb;

    $table_name =
        isago_get_orders_table_name();

    $charset_collate =
        $wpdb->get_charset_collate();

    require_once ABSPATH .
        'wp-admin/includes/upgrade.php';


    $sql = "
        CREATE TABLE {$table_name} (

            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

            order_id varchar(64) NOT NULL,

            user_id bigint(20) unsigned NOT NULL,

            prompt_id bigint(20) unsigned NOT NULL,

            amount decimal(20,2) NOT NULL DEFAULT 0.00,

            currency varchar(3) NOT NULL DEFAULT 'NGN',

            provider varchar(32) NOT NULL DEFAULT 'paystack',

            provider_reference varchar(128) NOT NULL DEFAULT '',

            provider_transaction_id varchar(128) NOT NULL DEFAULT '',

            payment_method varchar(64) NOT NULL DEFAULT '',

            status varchar(32) NOT NULL DEFAULT 'pending',

            refund_status varchar(32) NOT NULL DEFAULT 'none',

            created_at datetime NOT NULL,

            paid_at datetime NULL,

            failed_at datetime NULL,

            cancelled_at datetime NULL,

            refunded_at datetime NULL,

            updated_at datetime NOT NULL,

            PRIMARY KEY  (id),

            UNIQUE KEY order_id (order_id),

            UNIQUE KEY provider_reference (provider_reference),

            KEY user_id (user_id),

            KEY prompt_id (prompt_id),

            KEY status (status),

            KEY refund_status (refund_status),

            KEY provider_transaction_id (provider_transaction_id),

            KEY user_prompt (user_id,prompt_id)

        ) {$charset_collate};
    ";


    dbDelta(
        $sql
    );
}


/**
 * Register payment database installation.
 */
function isago_install_payment_database()
{
    isago_create_orders_table();

    update_option(
        'isago_payment_database_version',
    '1.1.0'
    );
}


/*
 * Create the table when WordPress initializes.
 *
 * The version check prevents dbDelta() from running
 * unnecessarily on every request.
 */
function isago_maybe_install_payment_database()
{
    $current_version =
        get_option(
            'isago_payment_database_version',
            ''
        );


    if (
           $current_version !== '1.1.0'
    ) {

        isago_install_payment_database();

    }
}
add_action(
    'init',
    'isago_maybe_install_payment_database',
    5
);


/*=========================================================
GENERATE ORDER ID
=========================================================*/

/**
 * Generate a unique internal Isago order ID.
 *
 * Example:
 *
 * ISG-20260929-ABC123XYZ
 *
 * @return string
 */
function isago_generate_order_id()
{
    global $wpdb;

    $table_name =
        isago_get_orders_table_name();


    do {

        $order_id =
            'ISG-' .
            gmdate('Ymd-His') .
            '-' .
            strtoupper(
                wp_generate_password(
                    8,
                    false,
                    false
                )
            );


        $exists =
            $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT id
                    FROM {$table_name}
                    WHERE order_id = %s
                    LIMIT 1
                    ",
                    $order_id
                )
            );

    } while (
        $exists
    );


    return $order_id;
}


/*=========================================================
CREATE ORDER
=========================================================*/

/**
 * Create a pending Isago order.
 *
 * IMPORTANT:
 * The amount passed to this function should have already
 * been calculated from the current AI Prompt price on
 * the server.
 *
 * @param int    $user_id
 * @param int    $prompt_id
 * @param string $amount
 * @return array
 */
function isago_create_order(
    $user_id,
    $prompt_id,
    $amount
) {
    global $wpdb;


    $user_id =
        absint(
            $user_id
        );

    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id ||
        !$prompt_id
    ) {

        return array(
            'success' => false,
            'message' => 'Invalid user or prompt.',
            'order'   => null,
        );

    }


    /*
     * Only AI Prompts can be purchased.
     */
    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return array(
            'success' => false,
            'message' => 'Invalid AI Prompt.',
            'order'   => null,
        );

    }


    /*
     * The prompt must actually be paid.
     */
    if (
        !isago_is_ai_prompt_paid(
            $prompt_id
        )
    ) {

        return array(
            'success' => false,
            'message' => 'This AI Prompt is free.',
            'order'   => null,
        );

    }


    /*
     * Never trust a price supplied by the browser.
     *
     * Read the current price directly from the
     * AI Prompt.
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
            'success' => false,
            'message' => 'This AI Prompt does not have a valid price.',
            'order'   => null,
        );

    }


    /*
     * The $amount argument is deliberately ignored as
     * the authoritative price.
     *
     * The database order receives the current prompt price.
     */
    $amount =
        isago_normalize_currency_amount(
            $prompt_price
        );


    $currency =
        'NGN';


    $provider =
        'paystack';


    $order_id =
        isago_generate_order_id();


    /*
     * Paystack reference is generated separately from
     * the internal Isago order ID.
     */
$provider_reference =
    'isago-' .
    strtolower(
        $order_id
    );


    $now =
        current_time(
            'mysql',
            true
        );


    $table_name =
        isago_get_orders_table_name();


    $inserted =
        $wpdb->insert(
            $table_name,
            array(

                'order_id' =>
                    $order_id,

                'user_id' =>
                    $user_id,

                'prompt_id' =>
                    $prompt_id,

                'amount' =>
                    $amount,

                'currency' =>
                    $currency,

                'provider' =>
                    $provider,

                'provider_reference' =>
                    $provider_reference,

                'provider_transaction_id' =>
                    '',

                'status' =>
                    'pending',

                'refund_status' =>
                    'none',

                'created_at' =>
                    $now,

                'paid_at' =>
                    null,

                'failed_at' =>
                    null,

                'cancelled_at' =>
                    null,

                'refunded_at' =>
                    null,

                'updated_at' =>
                    $now,

            ),

            array(

                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',

            )
        );


    if (
        false === $inserted
    ) {

        return array(
            'success' => false,
            'message' => 'Unable to create payment order.',
            'order'   => null,
        );

    }


    $order =
        isago_get_order_by_id(
            $order_id
        );


    return array(
        'success' => true,
        'message' => 'Payment order created.',
        'order'   => $order,
    );
}


/*=========================================================
GET ORDER
=========================================================*/

/**
 * Get an order by internal Isago order ID.
 *
 * @param string $order_id
 * @return array|null
 */
function isago_get_order_by_id(
    $order_id
) {
    global $wpdb;

    $order_id =
        sanitize_text_field(
            $order_id
        );


    if (
        $order_id === ''
    ) {

        return null;

    }


    $table_name =
        isago_get_orders_table_name();


    $order =
        $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table_name}
                WHERE order_id = %s
                LIMIT 1
                ",
                $order_id
            ),
            ARRAY_A
        );


    if (
        !is_array(
            $order
        )
    ) {

        return null;

    }


    return $order;
}


/**
 * Get an order by Paystack reference.
 *
 * @param string $reference
 * @return array|null
 */
function isago_get_order_by_provider_reference(
    $reference
) {
    global $wpdb;

    $reference =
        sanitize_text_field(
            $reference
        );


    if (
        $reference === ''
    ) {

        return null;

    }


    $table_name =
        isago_get_orders_table_name();


    $order =
        $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table_name}
                WHERE provider_reference = %s
                LIMIT 1
                ",
                $reference
            ),
            ARRAY_A
        );


    if (
        !is_array(
            $order
        )
    ) {

        return null;

    }


    return $order;
}


/*=========================================================
UPDATE ORDER STATUS
=========================================================*/

/**
 * Update an order status.
 *
 * @param string $order_id
 * @param string $status
 * @return bool
 */
function isago_update_order_status(
    $order_id,
    $status
) {
    global $wpdb;


    $order_id =
        sanitize_text_field(
            $order_id
        );


    $status =
        sanitize_key(
            $status
        );


    if (
        $order_id === '' ||
        !isago_is_valid_order_status(
            $status
        )
    ) {

        return false;

    }


    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    $now =
        current_time(
            'mysql',
            true
        );


    $data = array(
        'status' =>
            $status,

        'updated_at' =>
            $now,
    );


    $formats = array(
        '%s',
        '%s',
    );


    /*
     * Record important status timestamps.
     */
    if (
        $status === 'paid'
    ) {

        $data['paid_at'] =
            $now;

        $formats[] =
            '%s';

    }


    if (
        $status === 'failed'
    ) {

        $data['failed_at'] =
            $now;

        $formats[] =
            '%s';

    }


    if (
        $status === 'cancelled'
    ) {

        $data['cancelled_at'] =
            $now;

        $formats[] =
            '%s';

    }


    if (
        $status === 'refunded'
    ) {

        $data['refunded_at'] =
            $now;

        $formats[] =
            '%s';

    }


    $result =
        $wpdb->update(
            isago_get_orders_table_name(),
            $data,
            array(
                'order_id' =>
                    $order_id,
            ),
            $formats,
            array(
                '%s',
            )
        );


    return false !== $result;
}


/*=========================================================
UPDATE PROVIDER TRANSACTION
=========================================================*/

/**
 * Save the Paystack transaction ID.
 *
 * @param string $order_id
 * @param string $transaction_id
 * @return bool
 */
function isago_update_order_provider_transaction(
    $order_id,
    $transaction_id
) {
    global $wpdb;


    $order_id =
        sanitize_text_field(
            $order_id
        );


    $transaction_id =
        sanitize_text_field(
            $transaction_id
        );


    if (
        $order_id === '' ||
        $transaction_id === ''
    ) {

        return false;

    }


    $result =
        $wpdb->update(
            isago_get_orders_table_name(),
            array(

                'provider_transaction_id' =>
                    $transaction_id,

                'updated_at' =>
                    current_time(
                        'mysql',
                        true
                    ),

            ),
            array(

                'order_id' =>
                    $order_id,

            ),
            array(
                '%s',
                '%s',
            ),
            array(
                '%s',
            )
        );


    return false !== $result;
}


/*=========================================================
REFUND STATUS
=========================================================*/

/**
 * Update an order's refund status.
 *
 * @param string $order_id
 * @param string $refund_status
 * @return bool
 */
function isago_update_order_refund_status(
    $order_id,
    $refund_status
) {
    global $wpdb;


    $order_id =
        sanitize_text_field(
            $order_id
        );


    $refund_status =
        sanitize_key(
            $refund_status
        );


    if (
        $order_id === '' ||
        !isago_is_valid_refund_status(
            $refund_status
        )
    ) {

        return false;

    }


    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    $now =
        current_time(
            'mysql',
            true
        );


    $data = array(

        'refund_status' =>
            $refund_status,

        'updated_at' =>
            $now,

    );


    $formats = array(
        '%s',
        '%s',
    );


    if (
        $refund_status === 'processed'
    ) {

        $data['refunded_at'] =
            $now;

        $formats[] =
            '%s';


        /*
         * A successfully processed refund means
         * the original order is no longer a paid
         * purchase.
         */
        $data['status'] =
            'refunded';

        $formats[] =
            '%s';

    }


    $result =
        $wpdb->update(
            isago_get_orders_table_name(),
            $data,
            array(
                'order_id' =>
                    $order_id,
            ),
            $formats,
            array(
                '%s',
            )
        );


    return false !== $result;
}


/*=========================================================
ORDER OWNERSHIP
=========================================================*/

/**
 * Check whether a user owns a prompt through a paid order.
 *
 * This is intentionally separate from the entitlement
 * system. The entitlement layer will eventually become
 * the authoritative access check.
 *
 * @param int $user_id
 * @param int $prompt_id
 * @return bool
 */
function isago_has_paid_order_for_prompt(
    $user_id,
    $prompt_id
) {
    global $wpdb;


    $user_id =
        absint(
            $user_id
        );

    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id ||
        !$prompt_id
    ) {

        return false;

    }


    $table_name =
        isago_get_orders_table_name();


    $order_id =
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT order_id
                FROM {$table_name}
                WHERE user_id = %d
                AND prompt_id = %d
                AND status = 'paid'
                AND refund_status = 'none'
                LIMIT 1
                ",
                $user_id,
                $prompt_id
            )
        );


    return !empty(
        $order_id
    );
}


/*=========================================================
CANCEL ORDER
=========================================================*/

/**
 * Cancel a pending order.
 *
 * Only orders that have not already been paid
 * or refunded can be cancelled this way.
 *
 * @param string $order_id
 * @return bool
 */
function isago_cancel_order(
    $order_id
) {
    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    if (
        !in_array(
            $order['status'],
            array(
                'pending',
                'abandoned',
            ),
            true
        )
    ) {

        return false;

    }


    return isago_update_order_status(
        $order_id,
        'cancelled'
    );
}


/*=========================================================
MARK ORDER FAILED
=========================================================*/

/**
 * Mark an order as failed.
 *
 * @param string $order_id
 * @return bool
 */
function isago_mark_order_failed(
    $order_id
) {
    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    /*
     * Never overwrite a successful payment with
     * a later failure notification.
     */
    if (
        in_array(
            $order['status'],
            array(
                'paid',
                'refunded',
                'reversed',
            ),
            true
        )
    ) {

        return false;

    }


    return isago_update_order_status(
        $order_id,
        'failed'
    );
}


/*=========================================================
MARK ORDER ABANDONED
=========================================================*/

/**
 * Mark an order as abandoned.
 *
 * @param string $order_id
 * @return bool
 */
function isago_mark_order_abandoned(
    $order_id
) {
    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    /*
     * Never turn a completed payment into abandoned.
     */
    if (
        in_array(
            $order['status'],
            array(
                'paid',
                'refunded',
                'reversed',
            ),
            true
        )
    ) {

        return false;

    }


    return isago_update_order_status(
        $order_id,
        'abandoned'
    );
}


/*=========================================================
MARK ORDER REVERSED
=========================================================*/

/**
 * Mark an order as reversed.
 *
 * @param string $order_id
 * @return bool
 */
function isago_mark_order_reversed(
    $order_id
) {
    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return false;

    }


    return isago_update_order_status(
        $order_id,
        'reversed'
    );
}
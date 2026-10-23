<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PROMPT ENTITLEMENTS
=========================================================*/

/**
 * Get the database table name for Isago entitlements.
 *
 * @return string
 */
function isago_get_entitlements_table_name()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_entitlements';
}


/*=========================================================
ENTITLEMENT STATUSES
=========================================================*/

/**
 * Get supported entitlement statuses.
 *
 * @return array
 */
function isago_get_entitlement_statuses()
{
    return array(

        'active' => array(
            'label' => 'Active',
        ),

        'revoked' => array(
            'label' => 'Revoked',
        ),

    );
}


/**
 * Check whether an entitlement status is valid.
 *
 * @param string $status
 * @return bool
 */
function isago_is_valid_entitlement_status($status)
{
    $statuses =
        isago_get_entitlement_statuses();

    return isset(
        $statuses[$status]
    );
}


/*=========================================================
CREATE ENTITLEMENTS TABLE
=========================================================*/

/**
 * Create or update the Isago entitlements table.
 *
 * @return void
 */
function isago_create_entitlements_table()
{
    global $wpdb;

    $table_name =
        isago_get_entitlements_table_name();

    $charset_collate =
        $wpdb->get_charset_collate();

    require_once ABSPATH .
        'wp-admin/includes/upgrade.php';


    $sql = "
        CREATE TABLE {$table_name} (

            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

            user_id bigint(20) unsigned NOT NULL,

            prompt_id bigint(20) unsigned NOT NULL,

            order_id varchar(64) NOT NULL,

            status varchar(32) NOT NULL DEFAULT 'active',

            created_at datetime NOT NULL,

            revoked_at datetime NULL,

            updated_at datetime NOT NULL,

            PRIMARY KEY  (id),

            UNIQUE KEY user_prompt (user_id,prompt_id),

            KEY user_id (user_id),

            KEY prompt_id (prompt_id),

            KEY order_id (order_id),

            KEY status (status)

        ) {$charset_collate};
    ";


    dbDelta(
        $sql
    );
}


/**
 * Install entitlement database.
 *
 * @return void
 */
function isago_install_entitlement_database()
{
    isago_create_entitlements_table();

    update_option(
        'isago_entitlement_database_version',
        '1.0.0'
    );
}


/**
 * Check whether entitlement database needs installation.
 *
 * @return void
 */
function isago_maybe_install_entitlement_database()
{
    $current_version =
        get_option(
            'isago_entitlement_database_version',
            ''
        );


    if (
        $current_version !== '1.0.0'
    ) {

        isago_install_entitlement_database();

    }
}

add_action(
    'init',
    'isago_maybe_install_entitlement_database',
    6
);


/*=========================================================
GET ENTITLEMENT
=========================================================*/

/**
 * Get an entitlement for a user and prompt.
 *
 * @param int $user_id
 * @param int $prompt_id
 * @return array|null
 */
function isago_get_entitlement(
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

        return null;

    }


    $table_name =
        isago_get_entitlements_table_name();


    $entitlement =
        $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table_name}
                WHERE user_id = %d
                AND prompt_id = %d
                LIMIT 1
                ",
                $user_id,
                $prompt_id
            ),
            ARRAY_A
        );


    if (
        !is_array(
            $entitlement
        )
    ) {

        return null;

    }


    return $entitlement;
}


/**
 * Get entitlement by order ID.
 *
 * @param string $order_id
 * @return array|null
 */
function isago_get_entitlement_by_order(
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
        isago_get_entitlements_table_name();


    $entitlement =
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
            $entitlement
        )
    ) {

        return null;

    }


    return $entitlement;
}


/*=========================================================
CREATE / ACTIVATE ENTITLEMENT
=========================================================*/

/**
 * Grant prompt access to a user after a verified payment.
 *
 * IMPORTANT:
 * This function should only be called after the payment
 * has been verified server-side.
 *
 * @param int    $user_id
 * @param int    $prompt_id
 * @param string $order_id
 * @return array
 */
function isago_grant_prompt_entitlement(
    $user_id,
    $prompt_id,
    $order_id
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

    $order_id =
        sanitize_text_field(
            $order_id
        );


    if (
        !$user_id ||
        !$prompt_id ||
        $order_id === ''
    ) {

        return array(
            'success' => false,
            'message' => 'Invalid entitlement information.',
            'entitlement' => null,
        );

    }


    /*
     * Only AI Prompts can have this entitlement.
     */
    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return array(
            'success' => false,
            'message' => 'Invalid AI Prompt.',
            'entitlement' => null,
        );

    }


    /*
     * Confirm that the order exists.
     */
    $order =
        isago_get_order_by_id(
            $order_id
        );


    if (
        !$order
    ) {

        return array(
            'success' => false,
            'message' => 'Payment order not found.',
            'entitlement' => null,
        );

    }


    /*
     * The order must belong to the same user
     * and prompt.
     */
    if (
        absint(
            $order['user_id']
        ) !== $user_id ||
        absint(
            $order['prompt_id']
        ) !== $prompt_id
    ) {

        return array(
            'success' => false,
            'message' => 'Order ownership does not match.',
            'entitlement' => null,
        );

    }


    /*
     * Only a paid order can create an active
     * entitlement.
     */
    if (
        $order['status'] !== 'paid'
    ) {

        return array(
            'success' => false,
            'message' => 'The order has not been marked as paid.',
            'entitlement' => null,
        );

    }


    /*
     * A refunded order cannot provide access.
     */
    if (
        $order['refund_status'] !== 'none'
    ) {

        return array(
            'success' => false,
            'message' => 'This order has a refund associated with it.',
            'entitlement' => null,
        );

    }


    $existing =
        isago_get_entitlement(
            $user_id,
            $prompt_id
        );


    $now =
        current_time(
            'mysql',
            true
        );


    $table_name =
        isago_get_entitlements_table_name();


    /*
     * Existing entitlement.
     */
    if (
        $existing
    ) {

        /*
         * Already active.
         *
         * This is intentionally idempotent.
         * A duplicate webhook/payment event must
         * not create another entitlement.
         */
        if (
            $existing['status'] === 'active'
        ) {

            return array(
                'success' => true,
                'message' => 'Prompt entitlement already active.',
                'entitlement' => $existing,
            );

        }


        /*
         * A revoked entitlement can be restored if
         * a valid paid order is confirmed.
         */
        $updated =
            $wpdb->update(
                $table_name,
                array(

                    'order_id' =>
                        $order_id,

                    'status' =>
                        'active',

                    'revoked_at' =>
                        null,

                    'updated_at' =>
                        $now,

                ),
                array(

                    'id' =>
                        absint(
                            $existing['id']
                        ),

                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                ),
                array(
                    '%d',
                )
            );


        if (
            false === $updated
        ) {

            return array(
                'success' => false,
                'message' => 'Unable to restore prompt entitlement.',
                'entitlement' => null,
            );

        }


        $entitlement =
            isago_get_entitlement(
                $user_id,
                $prompt_id
            );


        return array(
            'success' => true,
            'message' => 'Prompt entitlement restored.',
            'entitlement' => $entitlement,
        );

    }


    /*
     * Create a new entitlement.
     */
    $inserted =
        $wpdb->insert(
            $table_name,
            array(

                'user_id' =>
                    $user_id,

                'prompt_id' =>
                    $prompt_id,

                'order_id' =>
                    $order_id,

                'status' =>
                    'active',

                'created_at' =>
                    $now,

                'revoked_at' =>
                    null,

                'updated_at' =>
                    $now,

            ),
            array(

                '%d',
                '%d',
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

        /*
         * A duplicate webhook may have reached this
         * function simultaneously.
         *
         * Check again before treating it as a failure.
         */
        $existing =
            isago_get_entitlement(
                $user_id,
                $prompt_id
            );


        if (
            $existing &&
            $existing['status'] === 'active'
        ) {

            return array(
                'success' => true,
                'message' => 'Prompt entitlement already active.',
                'entitlement' => $existing,
            );

        }


        return array(
            'success' => false,
            'message' => 'Unable to create prompt entitlement.',
            'entitlement' => null,
        );

    }


    $entitlement =
        isago_get_entitlement(
            $user_id,
            $prompt_id
        );


    return array(
        'success' => true,
        'message' => 'Prompt entitlement created.',
        'entitlement' => $entitlement,
    );
}


/*=========================================================
CHECK ACTIVE ENTITLEMENT
=========================================================*/

/**
 * Check whether a user currently has active access
 * to a prompt.
 *
 * @param int $user_id
 * @param int $prompt_id
 * @return bool
 */
function isago_user_has_prompt_entitlement(
    $user_id,
    $prompt_id
) {
    $entitlement =
        isago_get_entitlement(
            $user_id,
            $prompt_id
        );


    if (
        !$entitlement
    ) {

        return false;

    }


    return (
        $entitlement['status'] === 'active'
    );
}


/*=========================================================
REVOKE ENTITLEMENT
=========================================================*/

/**
 * Revoke prompt access.
 *
 * This is used when a successful refund or reversal
 * removes the user's right to access the prompt.
 *
 * @param int $user_id
 * @param int $prompt_id
 * @return bool
 */
function isago_revoke_prompt_entitlement(
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


    $entitlement =
        isago_get_entitlement(
            $user_id,
            $prompt_id
        );


    if (
        !$entitlement
    ) {

        /*
         * There is nothing to revoke.
         *
         * Treat this as successful so repeated
         * refund/reversal events remain idempotent.
         */
        return true;

    }


    if (
        $entitlement['status'] === 'revoked'
    ) {

        return true;

    }


    $now =
        current_time(
            'mysql',
            true
        );


    $result =
        $wpdb->update(
            isago_get_entitlements_table_name(),
            array(

                'status' =>
                    'revoked',

                'revoked_at' =>
                    $now,

                'updated_at' =>
                    $now,

            ),
            array(

                'id' =>
                    absint(
                        $entitlement['id']
                    ),

            ),
            array(
                '%s',
                '%s',
                '%s',
            ),
            array(
                '%d',
            )
        );


    return false !== $result;
}


/*=========================================================
REVOKE BY ORDER
=========================================================*/

/**
 * Revoke entitlement associated with an order.
 *
 * @param string $order_id
 * @return bool
 */
function isago_revoke_entitlement_by_order(
    $order_id
) {
    $entitlement =
        isago_get_entitlement_by_order(
            $order_id
        );


    if (
        !$entitlement
    ) {

        return true;

    }


    return isago_revoke_prompt_entitlement(
        absint(
            $entitlement['user_id']
        ),
        absint(
            $entitlement['prompt_id']
        )
    );
}


/*=========================================================
ACCESS CONTROL
=========================================================*/

/**
 * Determine whether the current user can access
 * a paid AI Prompt.
 *
 * Free prompts are handled separately.
 *
 * @param int $prompt_id
 * @return bool
 */
function isago_user_can_access_paid_prompt(
    $prompt_id
) {
    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$prompt_id
    ) {

        return false;

    }


    /*
     * The prompt must be paid for this helper.
     */
    if (
        !isago_is_ai_prompt_paid(
            $prompt_id
        )
    ) {

        return true;

    }


    /*
     * Visitors cannot own a paid prompt.
     */
    if (
        !is_user_logged_in()
    ) {

        return false;

    }


    $user_id =
        get_current_user_id();


    return isago_user_has_prompt_entitlement(
        $user_id,
        $prompt_id
    );
}
<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO MY ACCOUNT
=========================================================*/


/**
 * ---------------------------------------------------------
 * ACCOUNT PAGE URL
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_get_account_url()
{
    $page = get_page_by_path('my-account');

    if ($page instanceof WP_Post) {

        return get_permalink($page->ID);
    }

    return home_url('/my-account/');
}


/**
 * ---------------------------------------------------------
 * GET CURRENT USER PURCHASED PROMPTS
 * ---------------------------------------------------------
 *
 * Only active entitlements connected to paid orders
 * are returned.
 *
 * This means refunded/revoked purchases will not appear
 * as active purchased prompts.
 *
 * @param int $user_id
 *
 * @return array
 */
function isago_get_account_purchases(
    $user_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    if (!$user_id) {

        return array();

    }


    /*
     * Payment tables.
     */

    $orders_table =
        $wpdb->prefix
        . 'isago_orders';


    $entitlements_table =
        $wpdb->prefix
        . 'isago_entitlements';


    /*
     * Get active entitlements belonging
     * to the current user.
     *
     * The order must also still be paid.
     */

    $results =
        $wpdb->get_results(

            $wpdb->prepare(

                "
                SELECT

                    e.id AS entitlement_id,

                    e.user_id,

                    e.prompt_id,

                    e.order_id,

                    e.status AS entitlement_status,

                    e.created_at AS entitlement_created_at,

                    o.amount,

                    o.currency,

                    o.provider,

                    o.provider_reference,

                    o.provider_transaction_id,

                    o.status AS order_status,

                    o.created_at AS order_created_at,

                    o.paid_at,
 
                    o.refund_status

                FROM {$entitlements_table} e

              INNER JOIN {$orders_table} o
    ON o.order_id = e.order_id

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = e.prompt_id

                WHERE e.user_id = %d

                    AND e.status = 'active'

                    AND o.status = 'paid'

                    AND (
                        o.refund_status IS NULL
                        OR
                        o.refund_status = ''
                        OR
                        o.refund_status = 'none'
                    )

                    AND p.post_type = 'ai_prompt'

                    AND p.post_status = 'publish'

                ORDER BY
                    COALESCE(
                        o.paid_at,
                        o.created_at
                    ) DESC,

                    e.id DESC
                ",

                $user_id

            )

        );


    if (
        empty($results)
        ||
        !is_array($results)
    ) {

        return array();

    }


    return $results;
}



/**
 * ---------------------------------------------------------
 * GET CURRENT USER ORDER HISTORY
 * ---------------------------------------------------------
 *
 * Returns all orders belonging to the current user.
 *
 * Unlike Purchased Prompts, this does NOT require:
 *
 * - an active entitlement
 * - a paid order
 * - an active prompt
 *
 * This means historical transactions such as failed,
 * cancelled, abandoned, reversed and refunded orders
 * can still appear in the user's order history.
 *
 * @param int $user_id
 * @param int $page
 * @param int $per_page
 *
 * @return array
 */
function isago_get_account_order_history(
    $user_id,
    $page = 1,
    $per_page = 10
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $page =
        max(
            1,
            absint(
                $page
            )
        );


    $per_page =
        max(
            1,
            absint(
                $per_page
            )
        );


    if (!$user_id) {

        return array(
            'orders' =>
                array(),

            'total' =>
                0,

            'pages' =>
                0,

            'page' =>
                $page,

            'per_page' =>
                $per_page,
        );

    }


    $orders_table =
        $wpdb->prefix
        . 'isago_orders';


    $offset =
        (
            $page - 1
        )
        *
        $per_page;


    /*
     * Total order count.
     */

  $total =
    (int)
    $wpdb->get_var(

        $wpdb->prepare(

            "
            SELECT COUNT(*)
            FROM {$orders_table} o

            LEFT JOIN {$wpdb->posts} p
                ON p.ID = o.prompt_id

            WHERE o.user_id = %d

                AND (
                    p.ID IS NOT NULL
                    AND p.post_type = 'ai_prompt'
                )
            ",

            $user_id

        )

    );


    /*
     * Get orders.
     *
     * LEFT JOIN is intentional.
     *
     * A historical order should remain visible even
     * if the prompt is later deleted or unpublished.
     */

    $orders =
        $wpdb->get_results(

            $wpdb->prepare(

                "
                SELECT

                    o.id,

                    o.order_id,

                    o.user_id,

                    o.prompt_id,

                    o.amount,

                    o.currency,

                    o.provider,

                    o.provider_reference,

                    o.provider_transaction_id,

                    o.payment_method,

                    o.status,

                    o.refund_status,

                    o.created_at,

                    o.paid_at,

                    o.failed_at,

                    o.cancelled_at,

                    o.refunded_at,

                    o.updated_at,

                    p.post_title AS prompt_title

                FROM {$orders_table} o

LEFT JOIN {$wpdb->posts} p
    ON p.ID = o.prompt_id

WHERE o.user_id = %d

    AND (
        p.ID IS NOT NULL
        AND p.post_type = 'ai_prompt'
    )

ORDER BY
    o.created_at DESC,
    o.id DESC

                LIMIT %d OFFSET %d
                ",

                $user_id,
                $per_page,
                $offset

            )

        );


    if (
        empty($orders)
        ||
        !is_array($orders)
    ) {

        $orders =
            array();

    }


    $pages =
        $total > 0
            ? (int)
                ceil(
                    $total /
                    $per_page
                )
            : 0;


    return array(

        'orders' =>
            $orders,

        'total' =>
            $total,

        'pages' =>
            $pages,

        'page' =>
            $page,

        'per_page' =>
            $per_page,

    );
}




/**
 * ---------------------------------------------------------
 * ACCOUNT SHORTCODE
 * ---------------------------------------------------------
 */
add_shortcode(
    'isago_my_account',
    'isago_render_my_account'
);


/**
 * ---------------------------------------------------------
 * RENDER MY ACCOUNT
 * ---------------------------------------------------------
 *
 * Routes the My Account page to the requested
 * account screen.
 *
 * Supported screens:
 *
 * /my-account/
 * /my-account/profile/
 * /my-account/purchased-prompts/
 * /my-account/order-history/
 *
 * @return string
 */
function isago_render_my_account()
{
    /*
     * Account pages require authentication.
     */
    if (!is_user_logged_in()) {

        $sign_in_url =
            isago_get_sign_in_url(
                isago_get_account_url()
            );

        ob_start();

        ?>

        <section
            class="isago-account-wrapper"
        >

            <div
                class="isago-account-card isago-account-guest"
            >

                <div
                    class="isago-account-brand"
                >
                    Isago
                </div>


                <h1
                    class="isago-account-title"
                >
                    Sign in to your account
                </h1>


                <p
                    class="isago-account-description"
                >

                    Sign in to view your account
                    and purchases.

                </p>


                <a
                    href="<?php echo esc_url(
                        $sign_in_url
                    ); ?>"
                    class="isago-account-primary-button"
                >
                    Sign In
                </a>


                <p
                    class="isago-account-secondary-text"
                >

                    Don't have an account?

                    <a
                        href="<?php echo esc_url(
                            isago_get_sign_up_url(
                                isago_get_account_url()
                            )
                        ); ?>"
                    >
                        Create an account
                    </a>

                </p>

            </div>

        </section>

        <?php

        return ob_get_clean();
    }


    /*
     * Get the requested account screen.
     *
     * We intentionally use a query parameter rather
     * than creating separate WordPress Pages.
     */
    $account_screen =
        isset(
            $_GET['account_screen']
        )
            ? sanitize_key(
                wp_unslash(
                    $_GET['account_screen']
                )
            )
            : 'profile';


    /*
     * Only allow known account screens.
     */
   $allowed_screens =
    array(
        'profile',
        'purchased-prompts',
        'order-history',
        'saved-prompts',
    );


    if (
        !in_array(
            $account_screen,
            $allowed_screens,
            true
        )
    ) {

        $account_screen =
            'profile';

    }


    /*
     * Render the requested screen.
     */
    switch ($account_screen) {

        case 'purchased-prompts':

            if (
                function_exists(
                    'isago_render_account_purchased_prompts'
                )
            ) {

                return
                    isago_render_account_purchased_prompts();

            }

            break;

            

        case 'order-history':

            if (
                function_exists(
                    'isago_render_account_order_history'
                )
            ) {

                return
                    isago_render_account_order_history();

            }

            break;


case 'saved-prompts':

    if (
        function_exists(
            'isago_render_account_saved_prompts'
        )
    ) {

        return
            isago_render_account_saved_prompts();

    }

    break;


        case 'profile':

        default:

            if (
                function_exists(
                    'isago_render_account_profile'
                )
            ) {

                return
                    isago_render_account_profile();

            }

            break;

    }


    /*
     * Fallback.
     */
    return '';
}


/**
 * ---------------------------------------------------------
 * ENQUEUE ACCOUNT STYLES
 * ---------------------------------------------------------
 */
function isago_account_enqueue_styles()
{
    wp_enqueue_style(
        'isago-account',
        ISAGO_URI . '/assets/css/account.css',
        [
            'isago-auth',
        ],
        function_exists('isago_asset_version')
            ? isago_asset_version(
                '/assets/css/account.css'
            )
            : ISAGO_VERSION
    );
}


/**
 * ---------------------------------------------------------
 * LOAD ACCOUNT STYLES ONLY WHERE NEEDED
 * ---------------------------------------------------------
 */
function isago_account_enqueue_shortcode_styles()
{
    global $post;


    if (
        !$post instanceof WP_Post
    ) {
        return;
    }


    if (
        !has_shortcode(
            $post->post_content,
            'isago_my_account'
        )
    ) {
        return;
    }


    isago_account_enqueue_styles();
}

add_action(
    'wp_enqueue_scripts',
    'isago_account_enqueue_shortcode_styles'
);
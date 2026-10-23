<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ADMIN ORDERS
=========================================================*/


/**
 * ---------------------------------------------------------
 * ADMIN MENU
 * ---------------------------------------------------------
 */

function isago_add_admin_orders_page()
{
    add_submenu_page(
        'isago-home',
        'Orders',
        'Orders',
        'manage_options',
        'isago-orders',
        'isago_render_admin_orders_page'
    );
}

add_action(
    'admin_menu',
    'isago_add_admin_orders_page',
    35
);


/**
 * ---------------------------------------------------------
 * GET ADMIN ORDERS
 * ---------------------------------------------------------
 *
 * @param int    $page
 * @param int    $per_page
 * @param string $status
 * @param string $search
 *
 * @return array
 */

function isago_get_admin_orders(
    $page = 1,
    $per_page = 20,
    $status = '',
    $search = ''
) {

    global $wpdb;


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


    $status =
        sanitize_key(
            $status
        );


    $search =
        is_string(
            $search
        )
            ? trim(
                $search
            )
            : '';


    $orders_table =
        $wpdb->prefix
        . 'isago_orders';


    $where =
        array(
            '1 = 1',
        );


    $values =
        array();


    /*
     * Status filter.
     */

    if (
        $status !== ''
        &&
        function_exists(
            'isago_is_valid_order_status'
        )
        &&
        isago_is_valid_order_status(
            $status
        )
    ) {

        $where[] =
            'o.status = %s';

        $values[] =
            $status;

    }


    /*
     * Search.
     *
     * Search:
     *
     * - Order ID
     * - Paystack reference
     * - Transaction ID
     * - User email
     * - User display name
     * - Prompt title
     */

    if (
        $search !== ''
    ) {

        $like =
            '%' .
            $wpdb->esc_like(
                $search
            ) .
            '%';


        $where[] =
            '(
                o.order_id LIKE %s
                OR
                o.provider_reference LIKE %s
                OR
                o.provider_transaction_id LIKE %s
                OR
                u.user_email LIKE %s
                OR
                u.display_name LIKE %s
                OR
                p.post_title LIKE %s
            )';


        $values[] = $like;
        $values[] = $like;
        $values[] = $like;
        $values[] = $like;
        $values[] = $like;
        $values[] = $like;

    }


    $where_sql =
        implode(
            ' AND ',
            $where
        );


    /*
     * Total orders.
     */

    $count_sql =
        "
        SELECT COUNT(*)

        FROM {$orders_table} o

        LEFT JOIN {$wpdb->users} u
            ON u.ID = o.user_id

        LEFT JOIN {$wpdb->posts} p
            ON p.ID = o.prompt_id

        WHERE {$where_sql}
        ";


    $total =
        !empty(
            $values
        )
            ? (int)
                $wpdb->get_var(
                    $wpdb->prepare(
                        $count_sql,
                        $values
                    )
                )
            : (int)
                $wpdb->get_var(
                    $count_sql
                );


    /*
     * Pagination.
     */

    $offset =
        (
            $page - 1
        )
        *
        $per_page;


    /*
     * Get orders.
     */

    $orders_sql =
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

            u.display_name AS user_name,

            u.user_email AS user_email,

            p.post_title AS prompt_title

        FROM {$orders_table} o

        LEFT JOIN {$wpdb->users} u
            ON u.ID = o.user_id

        LEFT JOIN {$wpdb->posts} p
            ON p.ID = o.prompt_id

        WHERE {$where_sql}

        ORDER BY
            o.created_at DESC,
            o.id DESC

        LIMIT %d OFFSET %d
        ";


    $order_values =
        $values;


    $order_values[] =
        $per_page;


    $order_values[] =
        $offset;


    $orders =
        $wpdb->get_results(
            $wpdb->prepare(
                $orders_sql,
                $order_values
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

    );
}


/**
 * ---------------------------------------------------------
 * ADMIN ORDERS PAGE
 * ---------------------------------------------------------
 */

function isago_render_admin_orders_page()
{
    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        return;

    }


    $page =
        isset(
            $_GET['paged']
        )
            ? absint(
                $_GET['paged']
            )
            : 1;


    $status =
        isset(
            $_GET['status']
        )
            ? sanitize_key(
                $_GET['status']
            )
            : '';


    $search =
        isset(
            $_GET['s']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $_GET['s']
                )
            )
            : '';


    $data =
        isago_get_admin_orders(
            $page,
            20,
            $status,
            $search
        );


    $orders =
        $data['orders'];


    $total =
        $data['total'];


    $pages =
        $data['pages'];


    $statuses =
        function_exists(
            'isago_get_order_statuses'
        )
            ? isago_get_order_statuses()
            : array();

    ?>

    <div class="wrap isago-admin">

        <h1 class="wp-heading-inline">
            Isago Orders
        </h1>


        <p>
            View all Isago customer transactions and
            payment history.
        </p>


        <hr class="wp-header-end">


        <!-- =============================================
             FILTERS
        ============================================== -->

        <form
            method="get"
            action="<?php echo esc_url(
                admin_url('admin.php')
            ); ?>"
            style="
                margin:20px 0;
                display:flex;
                flex-wrap:wrap;
                gap:10px;
                align-items:center;
            "
        >

            <input
                type="hidden"
                name="page"
                value="isago-orders"
            >


            <input
                type="search"
                name="s"
                value="<?php echo esc_attr(
                    $search
                ); ?>"
                placeholder="Search orders, users or prompts..."
                style="min-width:280px;"
            >


            <select
                name="status"
            >

                <option value="">
                    All statuses
                </option>


                <?php foreach (
                    $statuses
                    as $status_key =>
                    $status_data
                ) : ?>

                    <option
                        value="<?php echo esc_attr(
                            $status_key
                        ); ?>"
                        <?php selected(
                            $status,
                            $status_key
                        ); ?>
                    >

                        <?php
                        echo esc_html(
                            $status_data['label']
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <button
                type="submit"
                class="button button-primary"
            >
                Filter
            </button>


            <?php if (
                $search !== ''
                ||
                $status !== ''
            ) : ?>

                <a
                    href="<?php echo esc_url(
                        admin_url(
                            'admin.php?page=isago-orders'
                        )
                    ); ?>"
                    class="button"
                >
                    Clear
                </a>

            <?php endif; ?>

        </form>


        <!-- =============================================
             SUMMARY
        ============================================== -->

        <p>
            <strong>
                <?php
                echo esc_html(
                    number_format_i18n(
                        $total
                    )
                );
                ?>
            </strong>

            <?php
            echo esc_html(
                $total === 1
                    ? ' order'
                    : ' orders'
            );
            ?>
        </p>


        <!-- =============================================
             ORDERS TABLE
        ============================================== -->

        <div
            style="
                overflow-x:auto;
                background:#fff;
                border:1px solid #dcdcde;
            "
        >

            <table
                class="wp-list-table widefat fixed striped"
               style="min-width:1350px;"
            >

                <thead>

                    <tr>

                        <th>
                            Order
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Prompt
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Provider
                        </th>

<th>
    Payment Method
</th>


                        <th>
                            Status
                        </th>

                        <th>
                            Refund
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Paystack Reference
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (
                        !empty($orders)
                    ) : ?>


                        <?php foreach (
                            $orders
                            as $order
                        ) : ?>


                            <?php

                            $order_status =
                                sanitize_key(
                                    $order->status
                                );


                            $order_status_label =
                                isset(
                                    $statuses[
                                        $order_status
                                    ]['label']
                                )
                                    ? $statuses[
                                        $order_status
                                    ]['label']
                                    : ucfirst(
                                        $order_status
                                    );


                            $refund_status =
                                sanitize_key(
                                    $order->refund_status
                                );


                            $refund_statuses =
                                function_exists(
                                    'isago_get_refund_statuses'
                                )
                                    ? isago_get_refund_statuses()
                                    : array();


                            $refund_status_label =
                                isset(
                                    $refund_statuses[
                                        $refund_status
                                    ]['label']
                                )
                                    ? $refund_statuses[
                                        $refund_status
                                    ]['label']
                                    : ucfirst(
                                        $refund_status
                                    );


                            $amount =
                                function_exists(
                                    'isago_format_currency'
                                )
                                    ? isago_format_currency(
                                        $order->amount
                                    )
                                    : '₦'
                                    . number_format(
                                        (float)
                                        $order->amount,
                                        2
                                    );


                                    $payment_method =
    isset(
        $order->payment_method
    )
        ? $order->payment_method
        : '';


$payment_method_label =
    function_exists(
        'isago_get_payment_method_label'
    )
        ? isago_get_payment_method_label(
            $payment_method
        )
        : '—';

                            $date =
                                !empty(
                                    $order->paid_at
                                )
                                    ? $order->paid_at
                                    : $order->created_at;


                            ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?php
                                        echo esc_html(
                                            $order->order_id
                                        );
                                        ?>
                                    </strong>

                                </td>


                                <td>

                                    <?php
                                    echo esc_html(
                                        $order->user_name
                                            ?: 'Unknown user'
                                    );
                                    ?>

                                    <br>

                                    <small>

                                        <?php
                                        echo esc_html(
                                            $order->user_email
                                                ?: '—'
                                        );
                                        ?>

                                    </small>

                                </td>


                                <td>

                                    <?php if (
                                        !empty(
                                            $order->prompt_title
                                        )
                                    ) : ?>

                                        <?php
                                        echo esc_html(
                                            $order->prompt_title
                                        );
                                        ?>

                                    <?php else : ?>

                                        <em>
                                            Prompt unavailable
                                        </em>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <strong>
                                        <?php
                                        echo esc_html(
                                            $amount
                                        );
                                        ?>
                                    </strong>

                                </td>


                             <td>

    <?php
    echo esc_html(
        ucfirst(
            $order->provider
        )
    );
    ?>

</td>


<td>

    <?php
    echo esc_html(
        $payment_method_label
    ); ?>

</td>


<td>

    <strong>
        <?php
        echo esc_html(
            $order_status_label
        );
        ?>
    </strong>

</td>


                                <td>

                                    <?php
                                    echo esc_html(
                                        $refund_status_label
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo esc_html(
                                        mysql2date(
                                            'M j, Y g:i a',
                                            $date
                                        )
                                    );
                                    ?>

                                </td>


                                <td>

                                    <code>
                                        <?php
                                        echo esc_html(
                                            $order->provider_reference
                                        );
                                        ?>
                                    </code>

                                </td>

                            </tr>


                        <?php endforeach; ?>


                    <?php else : ?>

                        <tr>

                            <td
                               colspan="10"
                                style="padding:30px;text-align:center;"
                            >

                                No orders found.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- =============================================
             PAGINATION
        ============================================== -->

        <?php if (
            $pages > 1
        ) : ?>

            <div
                style="
                    margin-top:20px;
                "
            >

                <?php

                $pagination_base =
                    add_query_arg(
                        array(
                            'page' =>
                                'isago-orders',

                            'paged' =>
                                '%#%',

                            'status' =>
                                $status,

                            's' =>
                                $search,
                        ),
                        admin_url(
                            'admin.php'
                        )
                    );


                echo paginate_links(
                    array(
                        'base' =>
                            $pagination_base,

                        'format' =>
                            '',

                        'current' =>
                            $page,

                        'total' =>
                            $pages,

                        'type' =>
                            'plain',

                        'prev_text' =>
                            '&laquo; Previous',

                        'next_text' =>
                            'Next &raquo;',
                    )
                );

                ?>

            </div>

        <?php endif; ?>

    </div>

    <?php
}
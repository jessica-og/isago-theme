<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ACCOUNT — ORDER HISTORY
=========================================================*/


/**
 * ---------------------------------------------------------
 * RENDER ORDER HISTORY SCREEN
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_render_account_order_history()
{
    if (!is_user_logged_in()) {
        return '';
    }


    $user =
        wp_get_current_user();


    if (
        !$user
        ||
        !$user->ID
    ) {
        return '';
    }


    $account_order_page =
        isset(
            $_GET['order_page']
        )
            ? absint(
                $_GET['order_page']
            )
            : 1;


    $account_order_history =
        isago_get_account_order_history(
            $user->ID,
            $account_order_page,
            10
        );


    $account_orders =
        $account_order_history['orders'];


    $account_order_count =
        $account_order_history['total'];


    $account_order_pages =
        $account_order_history['pages'];


    ob_start();

    ?>

    <section
        class="isago-account-wrapper"
    >

        <div
            class="isago-account-container"
        >


            <div
                class="isago-account-heading"
            >

                <div>

                    <span
                        class="isago-account-eyebrow"
                    >
                        ISAGO ACCOUNT
                    </span>


                    <h1
                        class="isago-account-title"
                    >
                        Order History
                    </h1>


                    <p
                        class="isago-account-description"
                    >
                        View your Isago payment transactions.
                    </p>

                </div>

            </div>


            <section
                id="order-history"
                class="isago-account-card isago-account-order-history-card"
            >

                <div
                    class="isago-account-card-heading"
                >

                    <div>

                        <span
                            class="isago-account-card-label"
                        >
                            TRANSACTIONS
                        </span>



                    </div>


                    <?php if ($account_order_count > 0) : ?>

                        <span
                            class="isago-account-purchase-count"
                        >

                            <?php
                            echo esc_html(
                                $account_order_count
                            );
                            ?>

                            <?php
                            echo esc_html(
                                $account_order_count === 1
                                    ? ' Order'
                                    : ' Orders'
                            );
                            ?>

                        </span>

                    <?php endif; ?>

                </div>


                <?php if (!empty($account_orders)) : ?>


                    <div
                        class="isago-account-order-history"
                    >

                        <div
                            class="isago-account-order-history-table-wrap"
                        >

                            <table
                                class="isago-account-order-history-table"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Order
                                        </th>

                                        <th>
                                            Prompt
                                        </th>

                                        <th>
                                            Date
                                        </th>

                                        <th>
                                            Amount
                                        </th>

                                        <th>
                                            Payment Method
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach (
                                        $account_orders
                                        as $account_order
                                    ) : ?>


                                        <?php

                                        $order_id =
                                            isset(
                                                $account_order->order_id
                                            )
                                                ? $account_order->order_id
                                                : '';


                                        $order_prompt_id =
                                            isset(
                                                $account_order->prompt_id
                                            )
                                                ? absint(
                                                    $account_order->prompt_id
                                                )
                                                : 0;


                                        $order_prompt_title =
                                            !empty(
                                                $account_order->prompt_title
                                            )
                                                ? $account_order->prompt_title
                                                : 'Prompt unavailable';


                                        $order_prompt_url =
                                            $order_prompt_id
                                            &&
                                            get_post_status(
                                                $order_prompt_id
                                            ) === 'publish'
                                                ? get_permalink(
                                                    $order_prompt_id
                                                )
                                                : '';


                                        $order_date =
                                            !empty(
                                                $account_order->paid_at
                                            )
                                                ? $account_order->paid_at
                                                : $account_order->created_at;


                                        $order_date_display =
                                            $order_date
                                                ? mysql2date(
                                                    'M j, Y',
                                                    $order_date
                                                )
                                                : '';


                                        $order_amount =
                                            isset(
                                                $account_order->amount
                                            )
                                                ? $account_order->amount
                                                : '0.00';


                                        if (
                                            function_exists(
                                                'isago_format_currency'
                                            )
                                        ) {

                                            $order_price =
                                                isago_format_currency(
                                                    $order_amount
                                                );

                                        } else {

                                            $order_price =
                                                '₦'
                                                . number_format(
                                                    (float)
                                                    $order_amount,
                                                    2
                                                );

                                        }


                                        $order_status =
                                            isset(
                                                $account_order->status
                                            )
                                                ? sanitize_key(
                                                    $account_order->status
                                                )
                                                : 'pending';


                                        $order_statuses =
                                            function_exists(
                                                'isago_get_order_statuses'
                                            )
                                                ? isago_get_order_statuses()
                                                : array();


                                        $order_status_label =
                                            isset(
                                                $order_statuses[
                                                    $order_status
                                                ]['label']
                                            )
                                                ? $order_statuses[
                                                    $order_status
                                                ]['label']
                                                : ucfirst(
                                                    $order_status
                                                );


                                        $order_status_class =
                                            sanitize_html_class(
                                                $order_status
                                            );


                                        $order_payment_method =
                                            isset(
                                                $account_order->payment_method
                                            )
                                                ? $account_order->payment_method
                                                : '';


                                        $order_payment_method_label =
                                            function_exists(
                                                'isago_get_payment_method_label'
                                            )
                                                ? isago_get_payment_method_label(
                                                    $order_payment_method
                                                )
                                                : '—';

                                        ?>


                                        <tr>

                                            <td
                                                class="isago-account-order-id"
                                            >

                                                <?php
                                                echo esc_html(
                                                    $order_id
                                                );
                                                ?>

                                            </td>


                                            <td
                                                class="isago-account-order-prompt"
                                            >

                                                <?php if (
                                                    $order_prompt_url
                                                ) : ?>

                                                    <a
                                                        href="<?php echo esc_url(
                                                            $order_prompt_url
                                                        ); ?>"
                                                    >

                                                        <?php
                                                        echo esc_html(
                                                            $order_prompt_title
                                                        );
                                                        ?>

                                                    </a>

                                                <?php else : ?>

                                                    <span>
                                                        <?php
                                                        echo esc_html(
                                                            $order_prompt_title
                                                        );
                                                        ?>
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php
                                                echo esc_html(
                                                    $order_date_display
                                                );
                                                ?>

                                            </td>


                                            <td
                                                class="isago-account-order-amount"
                                            >

                                                <?php
                                                echo esc_html(
                                                    $order_price
                                                );
                                                ?>

                                            </td>


                                            <td
                                                class="isago-account-order-payment-method"
                                            >

                                                <?php
                                                echo esc_html(
                                                    $order_payment_method_label
                                                );
                                                ?>

                                            </td>


                                            <td>

                                                <span
                                                    class="isago-account-order-status is-<?php echo esc_attr(
                                                        $order_status_class
                                                    ); ?>"
                                                >

                                                    <?php
                                                    echo esc_html(
                                                        $order_status_label
                                                    );
                                                    ?>

                                                </span>

                                            </td>

                                        </tr>


                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>


                        <?php if (
                            $account_order_pages > 1
                        ) : ?>

                            <div
                                class="isago-account-order-pagination"
                            >

                                <?php

                                for (
                                    $order_page_number = 1;
                                    $order_page_number <= $account_order_pages;
                                    $order_page_number++
                                ) :

                                    $order_page_url =
                                        add_query_arg(
                                            'order_page',
                                            $order_page_number,
                                            isago_get_account_url()
                                        );

                                    $order_page_url .=
                                        '#order-history';

                                ?>

                                    <a
                                        href="<?php echo esc_url(
                                            $order_page_url
                                        ); ?>"
                                        class="<?php echo $order_page_number === $account_order_page
                                            ? 'is-active'
                                            : ''; ?>"
                                    >

                                        <?php
                                        echo esc_html(
                                            $order_page_number
                                        );
                                        ?>

                                    </a>

                                <?php endfor; ?>

                            </div>

                        <?php endif; ?>


                    </div>


                <?php else : ?>


                    <div
                        class="isago-account-empty"
                    >

                        <div
                            class="isago-account-empty-icon"
                        >

                            <i
                                class="fa-regular fa-receipt"
                                aria-hidden="true"
                            ></i>

                        </div>


                        <h3>
                            No orders yet
                        </h3>


                        <p>
                            Your payment transactions will appear here
                            after you make a purchase.
                        </p>

                    </div>


                <?php endif; ?>


            </section>


            <div
                class="isago-account-footer"
            >

                <a
                    href="<?php echo esc_url(
                        isago_get_account_url()
                    ); ?>"
                    class="isago-account-footer-link"
                >
                    Account
                </a>


                <a
                    href="<?php echo esc_url(
                        isago_get_logout_url(
                            home_url('/')
                        )
                    ); ?>"
                    class="isago-account-footer-link isago-account-logout"
                >

                    <i
                        class="fa-solid fa-right-from-bracket"
                        aria-hidden="true"
                    ></i>

                    Logout

                </a>

            </div>


        </div>

    </section>

    <?php

    return ob_get_clean();
}
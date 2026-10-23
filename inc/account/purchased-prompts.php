<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ACCOUNT — PURCHASED PROMPTS
=========================================================*/


/**
 * ---------------------------------------------------------
 * RENDER PURCHASED PROMPTS SCREEN
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_render_account_purchased_prompts()
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


    $account_purchases =
        isago_get_account_purchases(
            $user->ID
        );


    $account_purchase_count =
        count(
            $account_purchases
        );


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
                        Purchased Prompts
                    </h1>


                    <p
                        class="isago-account-description"
                    >
                        View and access the AI prompts you have purchased.
                    </p>

                </div>

            </div>


            <section
                id="purchased-prompts"
                class="isago-account-card isago-account-purchases-card"
            >

                <div
                    class="isago-account-card-heading"
                >

                   


                    <?php if ($account_purchase_count > 0) : ?>

                        <span
                            class="isago-account-purchase-count"
                        >

                            <?php
                            echo esc_html(
                                $account_purchase_count
                            );
                            ?>

                            <?php
                            echo esc_html(
                                $account_purchase_count === 1
                                    ? ' Prompt'
                                    : ' Prompts'
                            );
                            ?>

                        </span>

                    <?php endif; ?>

                </div>


                <?php if (!empty($account_purchases)) : ?>


                    <div
                        class="isago-account-purchases-list"
                    >


                        <?php foreach (
                            $account_purchases
                            as $purchase
                        ) : ?>


                            <?php

                            $purchase_prompt_id =
                                absint(
                                    $purchase->prompt_id
                                );


                            if (!$purchase_prompt_id) {
                                continue;
                            }


                            $purchase_prompt =
                                get_post(
                                    $purchase_prompt_id
                                );


                            if (
                                !$purchase_prompt
                                ||
                                $purchase_prompt->post_type !== 'ai_prompt'
                                ||
                                $purchase_prompt->post_status !== 'publish'
                            ) {
                                continue;
                            }


                            $purchase_prompt_url =
                                get_permalink(
                                    $purchase_prompt_id
                                );


                            $purchase_prompt_title =
                                get_the_title(
                                    $purchase_prompt_id
                                );


                            /*
                             * Prompt retirement.
                             */

                            $purchase_prompt_removal_scheduled =
                                false;


                            $purchase_prompt_removal_at =
                                0;


                            if (
                                function_exists(
                                    'isago_is_ai_prompt_removal_scheduled'
                                )
                            ) {

                                $purchase_prompt_removal_scheduled =
                                    isago_is_ai_prompt_removal_scheduled(
                                        $purchase_prompt_id
                                    );

                            }


                            if (
                                $purchase_prompt_removal_scheduled
                                &&
                                function_exists(
                                    'isago_get_ai_prompt_removal_at'
                                )
                            ) {

                                $purchase_prompt_removal_at =
                                    (int)
                                    isago_get_ai_prompt_removal_at(
                                        $purchase_prompt_id
                                    );

                            }


                            /*
                             * Scheduled removal date.
                             */

                            $purchase_prompt_removal_date_display =
                                '';


                            if (
                                $purchase_prompt_removal_scheduled
                                &&
                                $purchase_prompt_removal_at > 0
                            ) {

                                $purchase_prompt_removal_date_display =
                                    wp_date(
                                        get_option(
                                            'date_format'
                                        ),
                                        $purchase_prompt_removal_at,
                                        wp_timezone()
                                    );

                            }


                            /*
                             * Purchase date.
                             */

                            $purchase_date =
                                !empty(
                                    $purchase->paid_at
                                )
                                    ? $purchase->paid_at
                                    : $purchase->order_created_at;


                            $purchase_date_display =
                                $purchase_date
                                    ? mysql2date(
                                        'F j, Y',
                                        $purchase_date
                                    )
                                    : '';


                            /*
                             * Price.
                             */

                            $purchase_amount =
                                isset(
                                    $purchase->amount
                                )
                                    ? $purchase->amount
                                    : '0.00';


                            $purchase_currency =
                                !empty(
                                    $purchase->currency
                                )
                                    ? $purchase->currency
                                    : 'NGN';


                            if (
                                function_exists(
                                    'isago_format_currency'
                                )
                            ) {

                                $purchase_price =
                                    isago_format_currency(
                                        $purchase_amount
                                    );

                            } else {

                                $purchase_price =
                                    $purchase_currency
                                    . ' '
                                    . number_format(
                                        (float)
                                        $purchase_amount,
                                        2
                                    );

                            }


                            /*
                             * Prompt image.
                             */

                            $purchase_image =
                                '';


                            if (
                                function_exists(
                                    'isago_get_post_thumbnail'
                                )
                            ) {

                                $purchase_image =
                                    isago_get_post_thumbnail(
                                        $purchase_prompt_id,
                                        'thumbnail',
                                        array(
                                            'loading' =>
                                                'lazy',

                                            'alt' =>
                                                $purchase_prompt_title,
                                        )
                                    );

                            }


                            if (
                                empty(
                                    $purchase_image
                                )
                                &&
                                has_post_thumbnail(
                                    $purchase_prompt_id
                                )
                            ) {

                                $purchase_image =
                                    get_the_post_thumbnail(
                                        $purchase_prompt_id,
                                        'thumbnail',
                                        array(
                                            'loading' =>
                                                'lazy',

                                            'alt' =>
                                                $purchase_prompt_title,
                                        )
                                    );

                            }


                            if (
                                empty(
                                    $purchase_image
                                )
                            ) {

                                $purchase_image =
                                    sprintf(
                                        '<img src="%s" alt="%s" loading="lazy">',

                                        esc_url(
                                            get_template_directory_uri()
                                            . '/assets/images/placeholder.png'
                                        ),

                                        esc_attr(
                                            $purchase_prompt_title
                                        )
                                    );

                            }

                            ?>


                            <article
                                class="isago-account-purchase"
                            >


                                <a
                                    href="<?php echo esc_url(
                                        $purchase_prompt_url
                                    ); ?>"
                                    class="isago-account-purchase-image"
                                >

                                    <?php
                                    echo $purchase_image;
                                    ?>

                                </a>


                                <div
                                    class="isago-account-purchase-content"
                                >


                                    <div
                                        class="isago-account-purchase-top"
                                    >

                                        <div>

                                            <span
                                                class="isago-account-purchase-label"
                                            >
                                                AI PROMPT
                                            </span>


                                            <h3>
                                                <?php
                                                echo esc_html(
                                                    $purchase_prompt_title
                                                );
                                                ?>
                                            </h3>


                                            <?php if (
                                                $purchase_prompt_removal_scheduled
                                                &&
                                                $purchase_prompt_removal_date_display !== ''
                                            ) : ?>

                                                <div
                                                    class="isago-account-prompt-removal-notice"
                                                    role="status"
                                                >

                                                    <div
                                                        class="isago-account-prompt-removal-notice-icon"
                                                        aria-hidden="true"
                                                    >

                                                        <i
                                                            class="fa-solid fa-triangle-exclamation"
                                                        ></i>

                                                    </div>


                                                    <div
                                                        class="isago-account-prompt-removal-notice-content"
                                                    >

                                                        <strong>
                                                            Scheduled for removal
                                                        </strong>


                                                        <p>

                                                            This prompt will be permanently removed
                                                            from Isago on

                                                            <strong>
                                                                <?php
                                                                echo esc_html(
                                                                    $purchase_prompt_removal_date_display
                                                                );
                                                                ?>
                                                            </strong>.

                                                            Please copy and securely store the prompt
                                                            before the removal date.

                                                        </p>

                                                    </div>

                                                </div>

                                            <?php endif; ?>

                                        </div>


                                        <span
                                            class="isago-account-purchase-price"
                                        >

                                            <?php
                                            echo esc_html(
                                                $purchase_price
                                            );
                                            ?>

                                        </span>

                                    </div>


                                    <div
                                        class="isago-account-purchase-meta"
                                    >

                                        <?php if (
                                            !empty(
                                                $purchase_date_display
                                            )
                                        ) : ?>

                                            <span>

                                                <i
                                                    class="fa-regular fa-calendar"
                                                    aria-hidden="true"
                                                ></i>

                                                Purchased
                                                <?php
                                                echo esc_html(
                                                    $purchase_date_display
                                                );
                                                ?>

                                            </span>

                                        <?php endif; ?>


                                        <span
                                            class="isago-account-purchase-status"
                                        >

                                            <i
                                                class="fa-solid fa-circle-check"
                                                aria-hidden="true"
                                            ></i>

                                            Active

                                        </span>

                                    </div>


                                    <div
                                        class="isago-account-purchase-actions"
                                    >

                                        <a
                                            href="<?php echo esc_url(
                                                $purchase_prompt_url
                                            ); ?>"
                                            class="isago-account-primary-button"
                                        >

                                            View Prompt

                                            <i
                                                class="fa-solid fa-arrow-right"
                                                aria-hidden="true"
                                            ></i>

                                        </a>

                                    </div>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php else : ?>


                    <div
                        class="isago-account-empty"
                    >

                        <div
                            class="isago-account-empty-icon"
                        >

                            <i
                                class="fa-regular fa-bookmark"
                                aria-hidden="true"
                            ></i>

                        </div>


                        <h3>
                            No purchased prompts yet
                        </h3>


                        <p>
                            When you purchase an AI prompt,
                            it will appear here and remain available
                            from your account.
                        </p>


                        <a
                            href="<?php echo esc_url(
                                home_url(
                                    '/ai-prompts-library/'
                                )
                            ); ?>"
                            class="isago-account-primary-button"
                        >

                            Browse AI Prompts

                            <i
                                class="fa-solid fa-arrow-right"
                                aria-hidden="true"
                            ></i>

                        </a>

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
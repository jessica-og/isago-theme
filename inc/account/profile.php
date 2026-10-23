<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ACCOUNT — PROFILE
=========================================================*/


/**
 * ---------------------------------------------------------
 * RENDER PROFILE SCREEN
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_render_account_profile()
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


    $display_name =
        $user->display_name;


    $email =
        $user->user_email;


    $registered =
        mysql2date(
            'F j, Y',
            $user->user_registered
        );


    $logout_url =
        isago_get_logout_url(
            home_url('/')
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
                        Profile
                    </h1>


                    <p
                        class="isago-account-description"
                    >
                        Manage your Isago account details.
                    </p>

                </div>

            </div>


            <section
                class="isago-account-card"
            >



                <div
                    class="isago-account-profile"
                >


                    <div
                        class="isago-account-profile-row"
                    >

                        <span
                            class="isago-account-profile-label"
                        >
                            Name
                        </span>


                        <span
                            class="isago-account-profile-value"
                        >

                            <?php
                            echo esc_html(
                                $display_name
                            );
                            ?>

                        </span>

                    </div>


                    <div
                        class="isago-account-profile-row"
                    >

                        <span
                            class="isago-account-profile-label"
                        >
                            Email
                        </span>


                        <span
                            class="isago-account-profile-value"
                        >

                            <?php
                            echo esc_html(
                                $email
                            );
                            ?>

                        </span>

                    </div>


                    <div
                        class="isago-account-profile-row"
                    >

                        <span
                            class="isago-account-profile-label"
                        >
                            Member since
                        </span>


                        <span
                            class="isago-account-profile-value"
                        >

                            <?php
                            echo esc_html(
                                $registered
                            );
                            ?>

                        </span>

                    </div>


                </div>


                <div
                    class="isago-account-card-actions"
                >

                    <a
                        href="<?php echo esc_url(
                            isago_get_lost_password_url()
                        ); ?>"
                        class="isago-account-secondary-button"
                    >
                        Change Password
                    </a>

                </div>


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
                        $logout_url
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
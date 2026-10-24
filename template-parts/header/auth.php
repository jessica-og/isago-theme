<?php

if (!defined('ABSPATH')) {
    exit;
}


 
/*
|------------------------------------------------------------------
| CURRENT USER
|------------------------------------------------------------------
*/

$is_logged_in = is_user_logged_in();

$current_user = null;

if ($is_logged_in) {

    $current_user =
        wp_get_current_user();

}


/*
|------------------------------------------------------------------
| LOGOUT URL
|------------------------------------------------------------------
*/

$logout_url = '';

if ($is_logged_in) {

    $logout_url =
        isago_get_logout_url(
            home_url('/')
        );

}




?>

<div class="isago-header-social-auth">


    <?php
    /*=====================================================
    AUTHENTICATION
    =====================================================*/
    ?>

    <?php if (!$is_logged_in) : ?>


        <div class="isago-header-auth">


            <a
                class="isago-header-auth-link isago-header-sign-in"
                href="<?php echo esc_url(
    isago_get_sign_in_url(
        isago_get_account_url()
    )
); ?>"
            >
                Sign In
            </a>


            <a
                class="isago-header-auth-link isago-header-sign-up"
               href="<?php echo esc_url(
    isago_get_sign_up_url(
        isago_get_account_url()
    )
); ?>"
            >
                Sign Up
            </a>


        </div>


    <?php else : ?>


        <div class="isago-header-user">

           <button 
    type="button" 
    class="isago-header-user-toggle" 
    aria-expanded="false" 
    aria-controls="isago-header-user-dropdown"
    aria-haspopup="true" 
>

                <span class="isago-header-user-greeting">
                    Hello,
                </span>

                <span class="isago-header-user-name">

                    <?php echo esc_html(
                        $current_user->display_name
                    ); ?>

                </span>

                <i
                    class="fa-solid fa-chevron-down"
                    aria-hidden="true"
                ></i>

            </button>


         <div
    class="isago-header-user-dropdown"
    id="isago-header-user-dropdown"
>


                <div class="isago-header-user-dropdown-name">

                    <?php echo esc_html(
                        $current_user->display_name
                    ); ?>

                </div>


                <?php if (!empty($current_user->user_email)) : ?>

                    <div class="isago-header-user-dropdown-email">

                        <?php echo esc_html(
                            $current_user->user_email
                        ); ?>

                    </div>

                <?php endif; ?>

<div class="isago-header-user-dropdown-divider"></div>


<div
    class="isago-header-user-account-links"
>


    <a
        href="<?php echo esc_url(
            add_query_arg(
                'account_screen',
                'profile',
                isago_get_account_url()
            )
        ); ?>"
        class="isago-header-user-account"
    >

        <i
            class="fa-regular fa-user"
            aria-hidden="true"
        ></i>

        <span>
            Profile
        </span>

    </a>


    <a
        href="<?php echo esc_url(
            add_query_arg(
                'account_screen',
                'purchased-prompts',
                isago_get_account_url()
            )
        ); ?>"
        class="isago-header-user-account"
    >

        <i
            class="fa-regular fa-bookmark"
            aria-hidden="true"
        ></i>

        <span>
            Purchased Prompts
        </span>

    </a>


<a
    href="<?php echo esc_url(
        add_query_arg(
            'account_screen',
            'saved-prompts',
            isago_get_account_url()
        )
    ); ?>"
    class="isago-header-user-account"
>

    <i
        class="fa-regular fa-bookmark"
        aria-hidden="true"
    ></i>

    <span>
        Saved Prompts
    </span>

</a>



    <a
        href="<?php echo esc_url(
            add_query_arg(
                'account_screen',
                'order-history',
                isago_get_account_url()
            )
        ); ?>"
        class="isago-header-user-account"
    >

        <i
            class="fa-regular fa-receipt"
            aria-hidden="true"
        ></i>

        <span>
            Order History
        </span>

    </a>


</div>





<div class="isago-header-user-dropdown-divider"></div>


<a
    href="<?php echo esc_url(
        $logout_url
    ); ?>"
    class="isago-header-user-logout"
>

    <i
        class="fa-solid fa-right-from-bracket"
        aria-hidden="true"
    ></i>

    <span>
        Logout
    </span>

</a>


            </div>

        </div>


    <?php endif; ?>


</div>
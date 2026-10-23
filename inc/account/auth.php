<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO ACCOUNT / AUTHENTICATION
=========================================================*/


/**
 * ---------------------------------------------------------
 * AUTH PAGE SLUGS
 * ---------------------------------------------------------
 */
function isago_auth_page_slugs()
{
    return [
        'sign-in',
        'sign-up',
        'lost-password',
        'reset-password',
    ];
}


/**
 * ---------------------------------------------------------
 * CHECK WHETHER CURRENT PAGE IS AN ISAGO AUTH PAGE
 * ---------------------------------------------------------
 */
function isago_is_auth_page()
{
    if (!is_page()) {
        return false;
    }

    $page = get_queried_object();

    if (!$page instanceof WP_Post) {
        return false;
    }

    return in_array(
        $page->post_name,
        isago_auth_page_slugs(),
        true
    );
}


/**
 * ---------------------------------------------------------
 * GET SIGN IN PAGE URL
 * ---------------------------------------------------------
 *
 * @param string $redirect_to Optional redirect destination.
 *
 * @return string
 */
function isago_get_sign_in_url($redirect_to = '')
{
    $page = get_page_by_path('sign-in');

    if ($page instanceof WP_Post) {

        $url = get_permalink($page->ID);

    } else {

        $url = home_url('/sign-in/');
    }


    if (!empty($redirect_to)) {

        $redirect_to = isago_auth_validate_redirect(
            $redirect_to
        );

        $url = add_query_arg(
            'redirect_to',
            $redirect_to,
            $url
        );
    }


    return $url;
}


/**
 * ---------------------------------------------------------
 * GET SIGN UP PAGE URL
 * ---------------------------------------------------------
 *
 * @param string $redirect_to Optional redirect destination.
 *
 * @return string
 */
function isago_get_sign_up_url($redirect_to = '')
{
    $page = get_page_by_path('sign-up');

    if ($page instanceof WP_Post) {

        $url = get_permalink($page->ID);

    } else {

        $url = home_url('/sign-up/');
    }


    if (!empty($redirect_to)) {

        $redirect_to = isago_auth_validate_redirect(
            $redirect_to
        );

        $url = add_query_arg(
            'redirect_to',
            $redirect_to,
            $url
        );
    }


    return $url;
}


/**
 * ---------------------------------------------------------
 * GET LOST PASSWORD PAGE URL
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_get_lost_password_url()
{
    $page = get_page_by_path('lost-password');

    if ($page instanceof WP_Post) {

        return get_permalink($page->ID);
    }

    return home_url('/lost-password/');
}


/**
 * ---------------------------------------------------------
 * GET RESET PASSWORD PAGE URL
 * ---------------------------------------------------------
 *
 * @param string $key
 * @param string $login
 *
 * @return string
 */
function isago_get_reset_password_url(
    $key = '',
    $login = ''
) {
    $page = get_page_by_path('reset-password');

    if ($page instanceof WP_Post) {

        $url = get_permalink($page->ID);

    } else {

        $url = home_url('/reset-password/');
    }


    if (
        !empty($key)
        &&
        !empty($login)
    ) {

        $url = add_query_arg(
            [
                'key'   => $key,
                'login' => $login,
            ],
            $url
        );
    }


    return $url;
}


/**
 * ---------------------------------------------------------
 * GET LOGOUT URL
 * ---------------------------------------------------------
 *
 * @param string $redirect_to Destination after logout.
 *
 * @return string
 */
function isago_get_logout_url($redirect_to = '')
{
    if (empty($redirect_to)) {

        $redirect_to = home_url('/');
    }


    $redirect_to = isago_auth_validate_redirect(
        $redirect_to
    );


    return wp_logout_url(
        $redirect_to
    );
}


/**
 * ---------------------------------------------------------
 * VALIDATE REDIRECT
 * ---------------------------------------------------------
 *
 * Never trust a redirect URL supplied by the browser.
 *
 * @param string $redirect_to
 *
 * @return string
 */
function isago_auth_validate_redirect($redirect_to = '')
{
    if (empty($redirect_to)) {

        return home_url('/');
    }


    return wp_validate_redirect(
        $redirect_to,
        home_url('/')
    );
}


/**
 * ---------------------------------------------------------
 * AUTH ERROR URL
 * ---------------------------------------------------------
 *
 * @param string $url
 * @param string $error_code
 *
 * @return string
 */
function isago_auth_error_url(
    $url,
    $error_code
) {
    return add_query_arg(
        [
            'isago_auth_error' =>
                sanitize_key($error_code),
        ],
        $url
    );
}


/**
 * ---------------------------------------------------------
 * AUTH SUCCESS URL
 * ---------------------------------------------------------
 *
 * @param string $url
 * @param string $success_code
 *
 * @return string
 */
function isago_auth_success_url(
    $url,
    $success_code
) {
    return add_query_arg(
        [
            'isago_auth_success' =>
                sanitize_key($success_code),
        ],
        $url
    );
}


/**
 * ---------------------------------------------------------
 * GET CURRENT AUTH REDIRECT
 * ---------------------------------------------------------
 *
 * @return string
 */
function isago_get_auth_redirect_value()
{
    if (
        empty($_GET['redirect_to'])
    ) {
        return '';
    }


    return isago_auth_validate_redirect(
        esc_url_raw(
            wp_unslash(
                $_GET['redirect_to']
            )
        )
    );
}


/**
 * ---------------------------------------------------------
 * GET EMAIL USER
 * ---------------------------------------------------------
 *
 * Resolve the frontend email address to the native
 * WordPress user.
 *
 * @param string $email
 *
 * @return WP_User|false
 */
function isago_get_user_by_email($email)
{
    if (
        empty($email)
        ||
        !is_email($email)
    ) {
        return false;
    }


    $user = get_user_by(
        'email',
        $email
    );


    if (!$user instanceof WP_User) {
        return false;
    }


    return $user;
}


/**
 * ---------------------------------------------------------
 * PROCESS SIGN IN
 * ---------------------------------------------------------
 */
function isago_process_sign_in()
{
    if (
        empty($_POST['isago_auth_action'])
        ||
        $_POST['isago_auth_action'] !== 'sign_in'
    ) {
        return;
    }


    /*
     * Verify nonce.
     */

    if (
        empty($_POST['isago_sign_in_nonce'])
        ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['isago_sign_in_nonce']
                )
            ),
            'isago_sign_in'
        )
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_in_url(),
                'security'
            )
        );

        exit;
    }


    /*
     * Get email.
     */

    $email = '';

    if (
        isset($_POST['user_email'])
    ) {

        $email = sanitize_email(
            wp_unslash(
                $_POST['user_email']
            )
        );
    }


    /*
     * Get password.
     *
     * Never sanitize passwords because sanitizing can
     * modify the password the user actually entered.
     */

    $password = '';

    if (
        isset($_POST['user_password'])
    ) {

        $password = (string) wp_unslash(
            $_POST['user_password']
        );
    }


    /*
     * Remember Me.
     */

    $remember = (
        isset($_POST['remember_me'])
        &&
        wp_unslash($_POST['remember_me']) === '1'
    );


    /*
     * Safe redirect.
     */

    $redirect_to = home_url('/');

    if (
        isset($_POST['redirect_to'])
    ) {

        $redirect_to =
            isago_auth_validate_redirect(
                esc_url_raw(
                    wp_unslash(
                        $_POST['redirect_to']
                    )
                )
            );
    }


    /*
     * Basic validation.
     */

    if (
        empty($email)
        ||
        !is_email($email)
        ||
        empty($password)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_in_url(
                    $redirect_to
                ),
                'login_failed'
            )
        );

        exit;
    }


    /*
     * Resolve email to the native WordPress login.
     *
     * The Isago frontend uses email addresses, while
     * WordPress maintains user_login internally.
     */

    $user = isago_get_user_by_email(
        $email
    );


    /*
     * Do not reveal whether the account exists.
     */

    if (!$user instanceof WP_User) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_in_url(
                    $redirect_to
                ),
                'login_failed'
            )
        );

        exit;
    }


    /*
     * Authenticate through WordPress.
     */

    $authenticated_user = wp_signon(
        [
            'user_login'    => $user->user_login,
            'user_password' => $password,
            'remember'      => $remember,
        ],
        is_ssl()
    );


    if (
        is_wp_error(
            $authenticated_user
        )
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_in_url(
                    $redirect_to
                ),
                'login_failed'
            )
        );

        exit;
    }


    /*
     * Successful login.
     */

    wp_safe_redirect(
        $redirect_to
    );

    exit;
}


/**
 * ---------------------------------------------------------
 * GENERATE UNIQUE USERNAME
 * ---------------------------------------------------------
 *
 * WordPress requires user_login even though Isago uses
 * email addresses for authentication.
 *
 * @param string $email
 *
 * @return string
 */
function isago_generate_username_from_email($email)
{
    $parts = explode(
        '@',
        $email
    );


    $base = $parts[0] ?? 'user';


    $base = sanitize_user(
        $base,
        true
    );


    if (empty($base)) {

        $base = 'user';
    }


    /*
     * WordPress user_login maximum length is 60 characters.
     */

    $base = substr(
        $base,
        0,
        45
    );


    $username = $base;

    $counter = 1;


    while (
        username_exists($username)
    ) {

        $suffix = '-' . $counter;


        $username = substr(
            $base,
            0,
            60 - strlen($suffix)
        ) . $suffix;


        $counter++;


        if ($counter > 9999) {

            $username =
                'user-' .
                wp_generate_password(
                    10,
                    false,
                    false
                );

            break;
        }
    }


    return $username;
}


/**
 * ---------------------------------------------------------
 * PROCESS SIGN UP
 * ---------------------------------------------------------
 */
function isago_process_sign_up()
{
    if (
        empty($_POST['isago_auth_action'])
        ||
        $_POST['isago_auth_action'] !== 'sign_up'
    ) {
        return;
    }


    /*
     * Verify nonce.
     */

    if (
        empty($_POST['isago_sign_up_nonce'])
        ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['isago_sign_up_nonce']
                )
            ),
            'isago_sign_up'
        )
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(),
                'security'
            )
        );

        exit;
    }


    /*
     * Name.
     */

    $name = '';

    if (
        isset($_POST['display_name'])
    ) {

        $name = sanitize_text_field(
            wp_unslash(
                $_POST['display_name']
            )
        );
    }


    /*
     * Email.
     */

    $email = '';

    if (
        isset($_POST['user_email'])
    ) {

        $email = sanitize_email(
            wp_unslash(
                $_POST['user_email']
            )
        );
    }


    /*
     * Password.
     *
     * Do NOT sanitize passwords.
     */

    $password = '';

    if (
        isset($_POST['user_password'])
    ) {

        $password = (string) wp_unslash(
            $_POST['user_password']
        );
    }


    /*
     * Password confirmation.
     */

    $password_confirmation = '';

    if (
        isset(
            $_POST['user_password_confirmation']
        )
    ) {

        $password_confirmation =
            (string) wp_unslash(
                $_POST[
                    'user_password_confirmation'
                ]
            );
    }


    /*
     * Safe redirect.
     */

    $redirect_to = home_url('/');

    if (
        isset($_POST['redirect_to'])
    ) {

        $redirect_to =
            isago_auth_validate_redirect(
                esc_url_raw(
                    wp_unslash(
                        $_POST['redirect_to']
                    )
                )
            );
    }


    /*
     * Validate name.
     */

    if (
        empty($name)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'name_required'
            )
        );

        exit;
    }


    /*
     * Validate email.
     */

    if (
        empty($email)
        ||
        !is_email($email)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'invalid_email'
            )
        );

        exit;
    }


    /*
     * Existing email.
     */

    if (
        email_exists($email)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'email_exists'
            )
        );

        exit;
    }


    /*
     * Password length.
     */

    if (
        strlen($password) < 10
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'password_short'
            )
        );

        exit;
    }


    /*
     * Password confirmation.
     */

    if (
        !hash_equals(
            $password,
            $password_confirmation
        )
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'password_mismatch'
            )
        );

        exit;
    }


    /*
     * Generate internal WordPress username.
     */

    $username =
        isago_generate_username_from_email(
            $email
        );


    /*
     * Create normal subscriber account.
     *
     * WordPress handles password hashing.
     */

    $user_id = wp_insert_user(
        [
            'user_login'   => $username,
            'user_pass'    => $password,
            'user_email'   => $email,
            'display_name' => $name,
            'nickname'     => $name,
            'role'         => 'subscriber',
        ]
    );


    if (
        is_wp_error($user_id)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_sign_up_url(
                    $redirect_to
                ),
                'registration_failed'
            )
        );

        exit;
    }


    /*
     * Log the user in.
     *
     * Registration does not expose a Remember Me
     * checkbox, so use a normal session cookie.
     */

    wp_set_auth_cookie(
        $user_id,
        false,
        is_ssl()
    );


    /*
     * Set current user immediately.
     */

    wp_set_current_user(
        $user_id
    );


    /*
     * Redirect.
     */

    wp_safe_redirect(
        $redirect_to
    );

    exit;
}


/**
 * ---------------------------------------------------------
 * PROCESS LOST PASSWORD
 * ---------------------------------------------------------
 */
function isago_process_lost_password()
{
    if (
        empty($_POST['isago_auth_action'])
        ||
        $_POST['isago_auth_action'] !== 'lost_password'
    ) {
        return;
    }


    /*
     * Verify nonce.
     */

    if (
        empty($_POST['isago_lost_password_nonce'])
        ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['isago_lost_password_nonce']
                )
            ),
            'isago_lost_password'
        )
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_lost_password_url(),
                'security'
            )
        );

        exit;
    }


    /*
     * Get email.
     */

    $email = '';

    if (
        isset($_POST['user_email'])
    ) {

        $email = sanitize_email(
            wp_unslash(
                $_POST['user_email']
            )
        );
    }


    /*
     * Invalid email format.
     */

    if (
        empty($email)
        ||
        !is_email($email)
    ) {

        wp_safe_redirect(
            isago_auth_error_url(
                isago_get_lost_password_url(),
                'invalid_email'
            )
        );

        exit;
    }


    /*
     * Temporarily replace the default WordPress
     * password reset message.
     */

    add_filter(
        'retrieve_password_message',
        'isago_password_reset_email',
        10,
        4
    );


    /*
     * Ask WordPress to generate the secure reset key
     * and send the email.
     */

    $result = retrieve_password($email);


    /*
     * Remove our custom email filter immediately.
     */

    remove_filter(
        'retrieve_password_message',
        'isago_password_reset_email',
        10
    );


    /*
     * WordPress returns WP_Error when something failed.
     *
     * We deliberately do NOT reveal whether an account
     * exists to the visitor.
     */

    if (is_wp_error($result)) {

        /*
         * Optional server-side logging for debugging.
         *
         * This does NOT expose the error to the user.
         */

        if (
            defined('WP_DEBUG')
            &&
            WP_DEBUG
        ) {

            error_log(
                'Isago password reset failed: '
                .
                implode(
                    ', ',
                    $result->get_error_codes()
                )
            );
        }


        /*
         * Keep the public response generic.
         */

        wp_safe_redirect(
            add_query_arg(
                [
                    'isago_password_reset' => 'failed',
                ],
                isago_get_lost_password_url()
            )
        );

        exit;
    }


    /*
     * Successful request.
     */

    wp_safe_redirect(
        add_query_arg(
            'isago_password_reset',
            'sent',
            isago_get_lost_password_url()
        )
    );

    exit;
}


/**
 * ---------------------------------------------------------
 * PASSWORD RESET EMAIL
 * ---------------------------------------------------------
 *
 * WordPress generates the secure reset key.
 * Isago only changes the destination URL.
 *
 * @param string  $message
 * @param string  $key
 * @param string  $user_login
 * @param WP_User $user_data
 *
 * @return string
 */
function isago_password_reset_email(
    $message,
    $key,
    $user_login,
    $user_data
) {
    $reset_url =
        isago_get_reset_password_url(
            $key,
            $user_login
        );


    $site_name =
        wp_specialchars_decode(
            get_option('blogname'),
            ENT_QUOTES
        );


    $message =
        "Someone requested a password reset for your "
        . $site_name
        . " account."
        . "\r\n\r\n";


    $message .=
        "If you made this request, use the link below "
        . "to create a new password:"
        . "\r\n\r\n";


    $message .=
        $reset_url
        . "\r\n\r\n";


    $message .=
        "If you did not request a password reset, "
        . "you can safely ignore this email."
        . "\r\n\r\n";


    $message .=
        "This password reset link will expire "
        . "according to WordPress password reset "
        . "security rules.";


    return $message;
}


/**
 * ---------------------------------------------------------
 * PROCESS RESET PASSWORD
 * ---------------------------------------------------------
 */
function isago_process_reset_password()
{
    if (
        empty($_POST['isago_auth_action'])
        ||
        $_POST['isago_auth_action'] !== 'reset_password'
    ) {
        return;
    }


    /*
     * Verify nonce.
     */

    if (
        empty(
            $_POST[
                'isago_reset_password_nonce'
            ]
        )
        ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_reset_password_nonce'
                    ]
                )
            ),
            'isago_reset_password'
        )
    ) {

        wp_safe_redirect(
            add_query_arg(
                'isago_auth_error',
                'security',
                isago_get_reset_password_url()
            )
        );

        exit;
    }


    /*
     * Read reset credentials.
     */

    $key = '';

    if (
        isset($_POST['key'])
    ) {

        $key =
            sanitize_text_field(
                wp_unslash(
                    $_POST['key']
                )
            );
    }


    $login = '';

    if (
        isset($_POST['login'])
    ) {

        $login =
            sanitize_text_field(
                wp_unslash(
                    $_POST['login']
                )
            );
    }


    /*
     * Require both values.
     */

    if (
        empty($key)
        ||
        empty($login)
    ) {

        wp_safe_redirect(
            add_query_arg(
                'isago_auth_error',
                'invalid_key',
                isago_get_reset_password_url()
            )
        );

        exit;
    }


    /*
     * Validate the reset key through WordPress.
     */

    $user =
        check_password_reset_key(
            $key,
            $login
        );


    if (
        is_wp_error($user)
    ) {

        wp_safe_redirect(
            add_query_arg(
                'isago_auth_error',
                'invalid_key',
                isago_get_reset_password_url()
            )
        );

        exit;
    }


    /*
     * Get new password.
     */

    $password = '';

    if (
        isset($_POST['user_password'])
    ) {

        $password =
            (string) wp_unslash(
                $_POST['user_password']
            );
    }


    /*
     * Confirmation.
     */

    $password_confirmation = '';

    if (
        isset(
            $_POST[
                'user_password_confirmation'
            ]
        )
    ) {

        $password_confirmation =
            (string) wp_unslash(
                $_POST[
                    'user_password_confirmation'
                ]
            );
    }


    /*
     * Password length.
     */

    if (
        strlen($password) < 10
    ) {

        wp_safe_redirect(
            add_query_arg(
                [
                    'key' =>
                        rawurlencode($key),

                    'login' =>
                        rawurlencode($login),

                    'isago_auth_error' =>
                        'password_short',
                ],
                isago_get_reset_password_url()
            )
        );

        exit;
    }


    /*
     * Password confirmation.
     */

    if (
        !hash_equals(
            $password,
            $password_confirmation
        )
    ) {

        wp_safe_redirect(
            add_query_arg(
                [
                    'key' =>
                        rawurlencode($key),

                    'login' =>
                        rawurlencode($login),

                    'isago_auth_error' =>
                        'password_mismatch',
                ],
                isago_get_reset_password_url()
            )
        );

        exit;
    }


    /*
     * Reset password through WordPress.
     *
     * WordPress handles the password update.
     */

    reset_password(
        $user,
        $password
    );


    /*
     * Redirect to Sign In.
     */

    wp_safe_redirect(
        add_query_arg(
            'isago_auth_success',
            'password_reset',
            isago_get_sign_in_url()
        )
    );

    exit;
}


/**
 * ---------------------------------------------------------
 * AUTH FORM REQUEST HANDLER
 * ---------------------------------------------------------
 */
add_action(
    'init',
    'isago_auth_handle_request',
    20
);

function isago_auth_handle_request()
{
    if (
        strtoupper(
            $_SERVER['REQUEST_METHOD'] ?? ''
        ) !== 'POST'
    ) {
        return;
    }


    if (
        empty($_POST['isago_auth_action'])
    ) {
        return;
    }


    $action =
        sanitize_key(
            wp_unslash(
                $_POST['isago_auth_action']
            )
        );


    switch ($action) {

        case 'sign_in':

            isago_process_sign_in();

            break;


        case 'sign_up':

            isago_process_sign_up();

            break;


        case 'lost_password':

            isago_process_lost_password();

            break;


        case 'reset_password':

            isago_process_reset_password();

            break;
    }
}


/**
 * ---------------------------------------------------------
 * AUTH STATUS MESSAGES
 * ---------------------------------------------------------
 *
 * @return array
 */
function isago_get_auth_messages()
{
    $messages = [];


    /*
     * Registration success.
     */

    if (
        isset($_GET['registered'])
        &&
        $_GET['registered'] === '1'
    ) {

        $messages[] = [
            'type' =>
                'success',

            'message' =>
                'Your account has been created successfully.',
        ];
    }


    /*
     * Password reset success.
     */

    if (
        isset(
            $_GET['isago_auth_success']
        )
        &&
        sanitize_key(
            wp_unslash(
                $_GET['isago_auth_success']
            )
        ) === 'password_reset'
    ) {

        $messages[] = [
            'type' =>
                'success',

            'message' =>
                'Your password has been reset successfully. You can now sign in.',
        ];
    }


    /*
     * No error.
     */

    if (
        empty($_GET['isago_auth_error'])
    ) {

        return $messages;
    }


    $error =
        sanitize_key(
            wp_unslash(
                $_GET['isago_auth_error']
            )
        );


    $error_messages = [

        'security' => [

            'type' =>
                'error',

            'message' =>
                'Your request could not be verified. Please try again.',
        ],


        'login_failed' => [

            'type' =>
                'error',

            'message' =>
                'The email or password you entered is incorrect.',
        ],


        'name_required' => [

            'type' =>
                'error',

            'message' =>
                'Please enter your name.',
        ],


        'invalid_email' => [

            'type' =>
                'error',

            'message' =>
                'Please enter a valid email address.',
        ],


        'email_exists' => [

            'type' =>
                'error',

            'message' =>
                'An account with that email already exists. Please sign in instead.',
        ],


        'password_short' => [

            'type' =>
                'error',

            'message' =>
                'Your password must be at least 10 characters long.',
        ],


        'password_mismatch' => [

            'type' =>
                'error',

            'message' =>
                'Your passwords do not match.',
        ],


        'registration_failed' => [

            'type' =>
                'error',

            'message' =>
                'We could not create your account. Please try again.',
        ],


        'invalid_key' => [

            'type' =>
                'error',

            'message' =>
                'This password reset link is invalid or has expired. Please request a new reset link.',
        ],

    ];


    if (
        isset(
            $error_messages[$error]
        )
    ) {

        $messages[] =
            $error_messages[$error];
    }


    return $messages;
}


/**
 * ---------------------------------------------------------
 * AUTH MESSAGE RENDERER
 * ---------------------------------------------------------
 */
function isago_render_auth_messages()
{
    $messages =
        isago_get_auth_messages();


    if (
        empty($messages)
    ) {

        return;
    }


    foreach (
        $messages
        as $message
    ) :

        $type =
            !empty($message['type'])
                ? $message['type']
                : 'error';


        $text =
            !empty($message['message'])
                ? $message['message']
                : '';


        ?>

        <div
            class="isago-auth-message isago-auth-message-<?php echo esc_attr($type); ?>"
            role="<?php echo $type === 'error' ? 'alert' : 'status'; ?>"
        >

            <?php echo esc_html($text); ?>

        </div>

        <?php

    endforeach;
}


/**
 * ---------------------------------------------------------
 * ENQUEUE AUTH STYLES
 * ---------------------------------------------------------
 */
function isago_auth_enqueue_styles()
{
    wp_enqueue_style(
        'isago-auth',
        ISAGO_URI . '/assets/css/auth.css',
        [],
        function_exists(
            'isago_asset_version'
        )
            ? isago_asset_version(
                '/assets/css/auth.css'
            )
            : ISAGO_VERSION
    );
}


/**
 * ---------------------------------------------------------
 * LOAD AUTH STYLES ON AUTH PAGES
 * ---------------------------------------------------------
 */
function isago_auth_enqueue_page_styles()
{
    if (
        !isago_is_auth_page()
    ) {
        return;
    }


    isago_auth_enqueue_styles();
}

add_action(
    'wp_enqueue_scripts',
    'isago_auth_enqueue_page_styles'
);


/**
 * ---------------------------------------------------------
 * AUTH PAGE TEMPLATE
 * ---------------------------------------------------------
 *
 * Authentication pages deliberately bypass the normal
 * Isago header and footer.
 */
function isago_auth_page_template($template)
{
    if (
        !isago_is_auth_page()
    ) {
        return $template;
    }


    $auth_template =
        ISAGO_DIR . '/page-auth.php';


    if (
        file_exists($auth_template)
    ) {

        return $auth_template;
    }


    return $template;
}

add_filter(
    'template_include',
    'isago_auth_page_template'
);


/**
 * ---------------------------------------------------------
 * SIGN IN SHORTCODE
 * ---------------------------------------------------------
 */
add_shortcode(
    'isago_sign_in',
    'isago_render_sign_in'
);

function isago_render_sign_in()
{
    isago_auth_enqueue_styles();


    /*
     * Already logged in.
     */

    if (
        is_user_logged_in()
    ) {

        $user =
            wp_get_current_user();


        ob_start();

        ?>

        <section class="isago-auth-wrapper">

            <div class="isago-auth-card">

                <h1 class="isago-auth-title">
                    You're already signed in
                </h1>


                <p class="isago-auth-description">

                    You are signed in as
                    <strong>
                        <?php echo esc_html(
                            $user->display_name
                        ); ?>
                    </strong>.

                </p>


                <div class="isago-auth-actions">

                    <a
                        href="<?php echo esc_url(
                            isago_get_account_url()
                        ); ?>"
                        class="isago-auth-button"
                    >
                        My Account
                    </a>

                </div>

            </div>

        </section>

        <?php

        return ob_get_clean();
    }


    $redirect_to =
        isago_get_auth_redirect_value();


    ob_start();

    ?>

    <section class="isago-auth-wrapper">

        <div class="isago-auth-card">

            <h1 class="isago-auth-title">
                Welcome back
            </h1>


            <p class="isago-auth-description">
                Sign in to your Isago account.
            </p>


            <?php
            isago_render_auth_messages();
            ?>


            <form
                class="isago-auth-form"
                method="post"
                action=""
            >

                <input
                    type="hidden"
                    name="isago_auth_action"
                    value="sign_in"
                >


                <?php

                wp_nonce_field(
                    'isago_sign_in',
                    'isago_sign_in_nonce'
                );

                ?>


                <?php if (
                    !empty($redirect_to)
                ) : ?>

                    <input
                        type="hidden"
                        name="redirect_to"
                        value="<?php echo esc_attr(
                            $redirect_to
                        ); ?>"
                    >

                <?php endif; ?>


                <div class="isago-auth-field">

                    <label
                        for="isago-user-email"
                    >
                        Email address
                    </label>

                    <input
                        id="isago-user-email"
                        type="email"
                        name="user_email"
                        autocomplete="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="isago-auth-field">

                    <label
                        for="isago-user-password"
                    >
                        Password
                    </label>

                    <input
                        id="isago-user-password"
                        type="password"
                        name="user_password"
                        autocomplete="current-password"
                         placeholder="Enter your password"
                        required
                    >

                </div>


                <div class="isago-auth-row">

                    <label class="isago-auth-checkbox">

                        <input
                            type="checkbox"
                            name="remember_me"
                            value="1"
                        >

                        <span>
                            Remember me
                        </span>

                    </label>


                    <a
                        href="<?php echo esc_url(
                            isago_get_lost_password_url()
                        ); ?>"
                        class="isago-auth-link"
                    >
                        Forgot password?
                    </a>

                </div>


                <button
                    type="submit"
                    class="isago-auth-button"
                >
                    Sign In
                </button>

            </form>


            <div class="isago-auth-switch">

                <span>
                    Don't have an account?
                </span>

                <a
                    href="<?php echo esc_url(
                        isago_get_sign_up_url(
                            $redirect_to
                        )
                    ); ?>"
                >
                    Create an account
                </a>

            </div>

        </div>

    </section>

    <?php

    return ob_get_clean();
}


/**
 * ---------------------------------------------------------
 * SIGN UP SHORTCODE
 * ---------------------------------------------------------
 */
add_shortcode(
    'isago_sign_up',
    'isago_render_sign_up'
);

function isago_render_sign_up()
{
    isago_auth_enqueue_styles();


    /*
     * Already logged in.
     */

    if (
        is_user_logged_in()
    ) {

        $user =
            wp_get_current_user();


        ob_start();

        ?>

        <section class="isago-auth-wrapper">

            <div class="isago-auth-card">

                <h1 class="isago-auth-title">
                    You're already signed in
                </h1>


                <p class="isago-auth-description">

                    You are signed in as
                    <strong>
                        <?php echo esc_html(
                            $user->display_name
                        ); ?>
                    </strong>.

                </p>


                <div class="isago-auth-actions">

                    <a
                        href="<?php echo esc_url(
                            isago_get_account_url()
                        ); ?>"
                        class="isago-auth-button"
                    >
                        My Account
                    </a>

                </div>

            </div>

        </section>

        <?php

        return ob_get_clean();
    }


    $redirect_to =
        isago_get_auth_redirect_value();


    ob_start();

    ?>

    <section class="isago-auth-wrapper">

        <div class="isago-auth-card">

            <h1 class="isago-auth-title">
                Create your account
            </h1>


            <p class="isago-auth-description">
                Create an Isago account to access your purchases and account features.
            </p>


            <?php
            isago_render_auth_messages();
            ?>


            <form
                class="isago-auth-form"
                method="post"
                action=""
            >

                <input
                    type="hidden"
                    name="isago_auth_action"
                    value="sign_up"
                >


                <?php

                wp_nonce_field(
                    'isago_sign_up',
                    'isago_sign_up_nonce'
                );

                ?>


                <?php if (
                    !empty($redirect_to)
                ) : ?>

                    <input
                        type="hidden"
                        name="redirect_to"
                        value="<?php echo esc_attr(
                            $redirect_to
                        ); ?>"
                    >

                <?php endif; ?>


                <div class="isago-auth-field">

                    <label
                        for="isago-display-name"
                    >
                        Name
                    </label>

                    <input
                        id="isago-display-name"
                        type="text"
                        name="display_name"
                        autocomplete="name"
                        maxlength="100"
                         placeholder="Enter your name"
                        required
                    >

                </div>


                <div class="isago-auth-field">

                    <label
                        for="isago-sign-up-email"
                    >
                        Email address
                    </label>

                    <input
                        id="isago-sign-up-email"
                        type="email"
                        name="user_email"
                        autocomplete="email"
                         placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="isago-auth-field">

                    <label
                        for="isago-sign-up-password"
                    >
                        Password
                    </label>

                    <input
                        id="isago-sign-up-password"
                        type="password"
                        name="user_password"
                        autocomplete="new-password"
                        minlength="10"
                         placeholder="Enter your password"
                        required
                    >

                    <span class="isago-auth-help">
                        Use at least 10 characters.
                    </span>

                </div>


                <div class="isago-auth-field">

                    <label
                        for="isago-sign-up-password-confirmation"
                    >
                        Confirm password
                    </label>

                    <input
                        id="isago-sign-up-password-confirmation"
                        type="password"
                        name="user_password_confirmation"
                        autocomplete="new-password"
                        minlength="10"
                         placeholder="Confirm your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="isago-auth-button"
                >
                    Create Account
                </button>

            </form>


            <div class="isago-auth-switch">

                <span>
                    Already have an account?
                </span>

                <a
                    href="<?php echo esc_url(
                        isago_get_sign_in_url(
                            $redirect_to
                        )
                    ); ?>"
                >
                    Sign In
                </a>

            </div>

        </div>

    </section>

    <?php

    return ob_get_clean();
}


/**
 * ---------------------------------------------------------
 * LOST PASSWORD SHORTCODE
 * ---------------------------------------------------------
 */
add_shortcode(
    'isago_lost_password',
    'isago_render_lost_password'
);

function isago_render_lost_password()
{
    isago_auth_enqueue_styles();


    ob_start();

    ?>

    <section class="isago-auth-wrapper">

        <div class="isago-auth-card">

            <h1 class="isago-auth-title">
                Forgot your password?
            </h1>


            <p class="isago-auth-description">

                Enter your email address and we'll send
                you a link to reset your password.

            </p>


       <?php

$password_reset_status = '';

if (
    isset($_GET['isago_password_reset'])
) {

    $password_reset_status =
        sanitize_key(
            wp_unslash(
                $_GET['isago_password_reset']
            )
        );
}

?>

<?php if ($password_reset_status === 'sent') : ?>

    <div
        class="isago-auth-message isago-auth-message-success"
        role="status"
    >
        If an account exists for that email
        address, a password reset link has
        been sent.
    </div>

<?php elseif ($password_reset_status === 'failed') : ?>

    <div
        class="isago-auth-message isago-auth-message-error"
        role="alert"
    >
        We could not send the password reset email
        right now. Please try again later.
    </div>

<?php else : ?>

    <?php
    isago_render_auth_messages();
    ?>

    <form
        class="isago-auth-form"
        method="post"
        action=""
    >

        <input
            type="hidden"
            name="isago_auth_action"
            value="lost_password"
        >

        <?php

        wp_nonce_field(
            'isago_lost_password',
            'isago_lost_password_nonce'
        );

        ?>

        <div class="isago-auth-field">

            <label for="isago-lost-password-email">
                Email address
            </label>

            <input
                id="isago-lost-password-email"
                type="email"
                name="user_email"
                autocomplete="email"
                 placeholder="Enter your email"
                required
            >

        </div>

        <button
            type="submit"
            class="isago-auth-button"
        >
            Send Reset Link
        </button>

    </form>

<?php endif; ?>


            <div class="isago-auth-switch">

                <span>
                    Remember your password?
                </span>

                <a
                    href="<?php echo esc_url(
                        isago_get_sign_in_url()
                    ); ?>"
                >
                    Sign In
                </a>

            </div>

        </div>

    </section>

    <?php

    return ob_get_clean();
}


/**
 * ---------------------------------------------------------
 * RESET PASSWORD SHORTCODE
 * ---------------------------------------------------------
 */
add_shortcode(
    'isago_reset_password',
    'isago_render_reset_password'
);

function isago_render_reset_password()
{
    isago_auth_enqueue_styles();


    /*
     * Read reset key.
     */

    $key = '';

    if (
        isset($_GET['key'])
    ) {

        $key =
            sanitize_text_field(
                wp_unslash(
                    $_GET['key']
                )
            );
    }


    /*
     * Read login.
     */

    $login = '';

    if (
        isset($_GET['login'])
    ) {

        $login =
            sanitize_text_field(
                wp_unslash(
                    $_GET['login']
                )
            );
    }


    /*
     * Validate key before showing the password form.
     */

    $user = false;

    if (
        !empty($key)
        &&
        !empty($login)
    ) {

        $user =
            check_password_reset_key(
                $key,
                $login
            );
    }


    /*
     * Invalid / expired key.
     */

    if (
        is_wp_error($user)
    ) {

        ob_start();

        ?>

        <section class="isago-auth-wrapper">

            <div class="isago-auth-card">

                <h1 class="isago-auth-title">
                    Reset link unavailable
                </h1>


                <p class="isago-auth-description">

                    This password reset link is invalid
                    or has expired.

                </p>


                <div class="isago-auth-actions">

                    <a
                        href="<?php echo esc_url(
                            isago_get_lost_password_url()
                        ); ?>"
                        class="isago-auth-button"
                    >
                        Request a New Reset Link
                    </a>

                </div>


                <div class="isago-auth-switch">

                    <span>
                        Remember your password?
                    </span>

                    <a
                        href="<?php echo esc_url(
                            isago_get_sign_in_url()
                        ); ?>"
                    >
                        Sign In
                    </a>

                </div>

            </div>

        </section>

        <?php

        return ob_get_clean();
    }


    /*
     * No valid reset credentials.
     */

    if (
        !is_string($user)
        &&
        !$user instanceof WP_User
    ) {

        ob_start();

        ?>

        <section class="isago-auth-wrapper">

            <div class="isago-auth-card">

                <h1 class="isago-auth-title">
                    Reset your password
                </h1>


                <p class="isago-auth-description">

                    To reset your password, please use
                    the secure link sent to your email.

                </p>


                <div class="isago-auth-actions">

                    <a
                        href="<?php echo esc_url(
                            isago_get_lost_password_url()
                        ); ?>"
                        class="isago-auth-button"
                    >
                        Request Reset Link
                    </a>

                </div>

            </div>

        </section>

        <?php

        return ob_get_clean();
    }


    /*
     * Valid reset request.
     */

    ob_start();

    ?>

    <section class="isago-auth-wrapper">

        <div class="isago-auth-card">

            <h1 class="isago-auth-title">
                Create a new password
            </h1>


            <p class="isago-auth-description">

                Choose a new password for your
                Isago account.

            </p>


            <?php
            isago_render_auth_messages();
            ?>


            <form
                class="isago-auth-form"
                method="post"
                action=""
            >

                <input
                    type="hidden"
                    name="isago_auth_action"
                    value="reset_password"
                >


                <?php

                wp_nonce_field(
                    'isago_reset_password',
                    'isago_reset_password_nonce'
                );

                ?>


                <input
                    type="hidden"
                    name="key"
                    value="<?php echo esc_attr(
                        $key
                    ); ?>"
                >


                <input
                    type="hidden"
                    name="login"
                    value="<?php echo esc_attr(
                        $login
                    ); ?>"
                >


                <div class="isago-auth-field">

                    <label
                        for="isago-reset-password"
                    >
                        New password
                    </label>

                    <input
                        id="isago-reset-password"
                        type="password"
                        name="user_password"
                        autocomplete="new-password"
                        minlength="10"
                         placeholder="Enter your password"
                        required
                    >

                    <span class="isago-auth-help">
                        Use at least 10 characters.
                    </span>

                </div>


                <div class="isago-auth-field">

                    <label
                        for="isago-reset-password-confirmation"
                    >
                        Confirm new password
                    </label>

                    <input
                        id="isago-reset-password-confirmation"
                        type="password"
                        name="user_password_confirmation"
                        autocomplete="new-password"
                        minlength="10"
                         placeholder="Confirm your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="isago-auth-button"
                >
                    Reset Password
                </button>

            </form>


            <div class="isago-auth-switch">

                <span>
                    Remember your password?
                </span>

                <a
                    href="<?php echo esc_url(
                        isago_get_sign_in_url()
                    ); ?>"
                >
                    Sign In
                </a>

            </div>

        </div>

    </section>

    <?php

    return ob_get_clean();
}
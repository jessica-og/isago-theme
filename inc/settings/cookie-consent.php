<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ISAGO COOKIE CONSENT
=========================================================*/

/**
 * Return cookie consent settings.
 */
function isago_get_cookie_settings()
{
    $options = isago_get_option(
        'isago_cookie_settings'
    );

    return [

        'enabled' => !isset($options['enabled'])
            ? true
            : !empty($options['enabled']),

        'title' => sanitize_text_field(
            $options['title']
            ?? 'We use cookies'
        ),

        'description' => sanitize_textarea_field(
            $options['description']
            ?? 'Isago uses cookies to improve your experience, understand site usage, and support relevant advertising.'
        ),

        'accept_text' => sanitize_text_field(
            $options['accept_text']
            ?? 'Accept All'
        ),

        'reject_text' => sanitize_text_field(
            $options['reject_text']
            ?? 'Reject Non-Essential'
        ),

        'manage_text' => sanitize_text_field(
            $options['manage_text']
            ?? 'Manage Preferences'
        ),

        'privacy_text' => sanitize_text_field(
            $options['privacy_text']
            ?? 'Privacy Policy'
        ),

        'analytics_label' => sanitize_text_field(
            $options['analytics_label']
            ?? 'Analytics'
        ),

        'analytics_description' => sanitize_textarea_field(
            $options['analytics_description']
            ?? 'Helps us understand how visitors use Isago so we can improve the website.'
        ),

        'marketing_label' => sanitize_text_field(
            $options['marketing_label']
            ?? 'Advertising & Marketing'
        ),

        'marketing_description' => sanitize_textarea_field(
            $options['marketing_description']
            ?? 'Allows advertising and marketing services to provide relevant content and measure advertising performance.'
        ),

        'cookie_days' => absint(
            $options['cookie_days'] ?? 180
        ),

    ];
}


/**
 * Check whether cookie consent is enabled.
 */
function isago_cookie_consent_enabled()
{
    $settings = isago_get_cookie_settings();

    return !empty($settings['enabled']);
}


/**
 * Return the privacy policy URL.
 */
function isago_get_cookie_privacy_url()
{
    $url = get_privacy_policy_url();

    return $url ?: home_url('/privacy-policy/');
}


/**
 * Get stored consent from the current request.
 */
function isago_get_cookie_consent()
{
    if (empty($_COOKIE['isago_cookie_consent'])) {
        return null;
    }

    $raw = wp_unslash(
        $_COOKIE['isago_cookie_consent']
    );

    $data = json_decode(
        $raw,
        true
    );

    if (!is_array($data)) {
        return null;
    }

    return [

        'necessary' => true,

        'analytics' => !empty(
            $data['analytics']
        ),

        'marketing' => !empty(
            $data['marketing']
        ),

        'timestamp' => absint(
            $data['timestamp'] ?? 0
        ),

    ];
}


/**
 * Check whether a cookie category is allowed.
 */
function isago_cookie_allowed($category = 'necessary')
{
    if ($category === 'necessary') {
        return true;
    }

    $consent = isago_get_cookie_consent();

    if (!$consent) {
        return false;
    }

    return !empty(
        $consent[$category]
    );
}


/**
 * Output cookie consent UI.
 */
function isago_render_cookie_consent()
{
    if (!isago_cookie_consent_enabled()) {
        return;
    }

    $settings = isago_get_cookie_settings();

    $privacy_url = isago_get_cookie_privacy_url();

    ?>

    <div
        id="isago-cookie-consent"
        class="isago-cookie-consent"
        hidden
        aria-hidden="true">

        <div
            class="isago-cookie-banner"
            role="dialog"
            aria-modal="false"
            aria-labelledby="isago-cookie-title"
            aria-describedby="isago-cookie-description">

            <div class="isago-cookie-content">

                <div class="isago-cookie-copy">

                    <h2 id="isago-cookie-title">

                        <?php echo esc_html(
                            $settings['title']
                        ); ?>

                    </h2>

                    <p id="isago-cookie-description">

                        <?php echo esc_html(
                            $settings['description']
                        ); ?>

                        <a
                            href="<?php echo esc_url(
                                $privacy_url
                            ); ?>">

                            <?php echo esc_html(
                                $settings['privacy_text']
                            ); ?>

                        </a>

                    </p>

                </div>

                <div class="isago-cookie-actions">

                    <button
                        type="button"
                        class="isago-cookie-button isago-cookie-accept">

                        <?php echo esc_html(
                            $settings['accept_text']
                        ); ?>

                    </button>

                    <button
                        type="button"
                        class="isago-cookie-button isago-cookie-reject">

                        <?php echo esc_html(
                            $settings['reject_text']
                        ); ?>

                    </button>

                    <button
                        type="button"
                        class="isago-cookie-button isago-cookie-manage">

                        <?php echo esc_html(
                            $settings['manage_text']
                        ); ?>

                    </button>

                </div>

            </div>

        </div>


        <!-- Preferences -->

        <div
            class="isago-cookie-preferences"
            role="dialog"
            aria-modal="true"
            aria-labelledby="isago-cookie-preferences-title"
            hidden>

            <div class="isago-cookie-preferences-inner">

                <div class="isago-cookie-preferences-header">

                    <div>

                        <span class="isago-cookie-eyebrow">

                            Privacy

                        </span>

                        <h2 id="isago-cookie-preferences-title">

                            Cookie Preferences

                        </h2>

                    </div>

                    <button
                        type="button"
                        class="isago-cookie-close"
                        aria-label="Close cookie preferences">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                </div>


                <p class="isago-cookie-preferences-intro">

                    Choose which types of cookies you allow.
                    Necessary cookies are required for core
                    website functionality and cannot be disabled.

                </p>


                <!-- Necessary -->

                <div class="isago-cookie-option">

                    <div class="isago-cookie-option-copy">

                        <h3>

                            Necessary

                        </h3>

                        <p>

                            Required for security, accessibility
                            and basic website functionality.

                        </p>

                    </div>

                    <label class="isago-cookie-switch">

                        <input
                            type="checkbox"
                            checked
                            disabled>

                        <span></span>

                    </label>

                </div>


                <!-- Analytics -->

                <div class="isago-cookie-option">

                    <div class="isago-cookie-option-copy">

                        <h3>

                            <?php echo esc_html(
                                $settings['analytics_label']
                            ); ?>

                        </h3>

                        <p>

                            <?php echo esc_html(
                                $settings['analytics_description']
                            ); ?>

                        </p>

                    </div>

                    <label class="isago-cookie-switch">

                        <input
                            type="checkbox"
                            id="isago-cookie-analytics">

                        <span></span>

                    </label>

                </div>


                <!-- Marketing -->

                <div class="isago-cookie-option">

                    <div class="isago-cookie-option-copy">

                        <h3>

                            <?php echo esc_html(
                                $settings['marketing_label']
                            ); ?>

                        </h3>

                        <p>

                            <?php echo esc_html(
                                $settings['marketing_description']
                            ); ?>

                        </p>

                    </div>

                    <label class="isago-cookie-switch">

                        <input
                            type="checkbox"
                            id="isago-cookie-marketing">

                        <span></span>

                    </label>

                </div>


                <div class="isago-cookie-preferences-footer">

                    <button
                        type="button"
                        class="isago-cookie-button isago-cookie-save">

                        Save Preferences

                    </button>

                    <a
                        href="<?php echo esc_url(
                            $privacy_url
                        ); ?>">

                        <?php echo esc_html(
                            $settings['privacy_text']
                        ); ?>

                    </a>

                </div>

            </div>

        </div>

    </div>

    <?php
}


/**
 * Render the cookie UI near the end of the page.
 */
add_action(
    'wp_footer',
    'isago_render_cookie_consent',
    5
);
<?php

if (!defined('ABSPATH')) {
    exit;
}

$theme = isago_get_theme_settings();

if (empty($theme['footer_newsletter'])) {
    return;
}

$status = isset($_GET['newsletter'])
    ? sanitize_key($_GET['newsletter'])
    : '';

$message = isset($_GET['newsletter_message'])
    ? sanitize_text_field(
        rawurldecode($_GET['newsletter_message'])
    )
    : '';

?>

<div class="footer-newsletter">

    <h4>
        <?php echo esc_html(
            $theme['footer_newsletter_title']
        ); ?>
    </h4>


    <p>
        <?php echo esc_html(
            $theme['footer_newsletter_text']
        ); ?>
    </p>


    <?php if ($status === 'success') : ?>

        <div class="newsletter-message newsletter-success">
            <?php echo esc_html($message); ?>
        </div>

    <?php elseif ($status === 'exists') : ?>

        <div class="newsletter-message newsletter-info">
            <?php echo esc_html($message); ?>
        </div>

    <?php elseif ($status === 'error') : ?>

        <div class="newsletter-message newsletter-error">
            <?php echo esc_html($message); ?>
        </div>

    <?php endif; ?>


    <form
        method="post"
        action="<?php echo esc_url(
            admin_url('admin-post.php')
        ); ?>"
        class="isago-newsletter-form"
    >

        <input
            type="hidden"
            name="action"
            value="isago_newsletter_subscribe"
        >

        <?php
        wp_nonce_field(
            'isago_newsletter_subscribe',
            'isago_newsletter_nonce'
        );
        ?>


        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            aria-label="Email address"
            required
        >


        <button type="submit">

            <?php echo esc_html(
                $theme['footer_newsletter_button']
            ); ?>

        </button>

    </form>


    <small>
        No spam. Unsubscribe anytime.
    </small>

</div>
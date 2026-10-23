<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_sidebar_settings();

if (!$settings['newsletter_enable']) {
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

<section class="sidebar-widget newsletter-widget">

    <h3 class="isago-editorial-post-title">

        <?php echo esc_html(
            $settings['newsletter_title']
        ); ?>

    </h3>


    <p class="isago-excerpt">

        <?php echo esc_html(
            $settings['newsletter_text']
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
                $settings['newsletter_button']
            ); ?>

        </button>

    </form>


    <small>
        No spam. Unsubscribe anytime.
    </small>

</section>
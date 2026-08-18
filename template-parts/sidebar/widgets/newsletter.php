<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_sidebar_settings();

if (!$settings['newsletter_enable']) {
    return;
}

?>

<section class="sidebar-widget newsletter-widget">

<h3>

<?php echo esc_html(
$settings['newsletter_title']
); ?>

</h3>

<p>

<?php echo esc_html(
$settings['newsletter_text']
); ?>

</p>

<form>

<input

type="email"

placeholder="Enter your email">

<button>

<?php echo esc_html(
$settings['newsletter_button']
); ?>

</button>

</form>

<small>

No spam. Unsubscribe anytime.

</small>

</section>
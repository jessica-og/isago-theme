<?php

if (!defined('ABSPATH')) {
    exit;
}
$theme = isago_get_theme_settings();

if(empty($theme['footer_newsletter'])){

return;

}
?>

<div class="footer-newsletter">

    <h4><?php echo esc_html($theme['footer_newsletter_title']); ?></h4>

    <p>

    <?php

echo esc_html(

$theme['footer_newsletter_text']

);

?>

    </p>

    <form>

        <input
            type="email"
            placeholder="Enter your email">

        <button type="submit">

    <?php

echo esc_html(

$theme['footer_newsletter_button']

);

?>

        </button>

    </form>

    <small>

        No spam.
        Unsubscribe anytime.

    </small>

</div>
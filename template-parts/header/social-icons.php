<?php

if (!defined('ABSPATH')) {
    exit;
}

$options = isago_get_social_settings();

if (
    empty($options['header'])
) {
    return;
}

$icons = [

    'facebook'  => 'fa-facebook-f',

    'instagram' => 'fa-instagram',

    'youtube'   => 'fa-youtube',

    'tiktok'    => 'fa-tiktok',


];

?>

<div class="isago-social-icons">

<?php foreach ($icons as $key => $icon) : ?>

<?php if (!empty($options[$key])) : ?>

<a

href="<?php echo esc_url($options[$key]); ?>"

<?php if (!empty($options['blank'])) : ?>

target="_blank"

rel="noopener"

<?php endif; ?>

aria-label="<?php echo esc_attr(ucfirst($key)); ?>">

<i class="fa-brands <?php echo esc_attr($icon); ?>"></i>

</a>

<?php endif; ?>

<?php endforeach; ?>

</div>
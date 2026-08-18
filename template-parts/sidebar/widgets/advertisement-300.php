<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_sidebar_settings();

if (
    !$settings['sidebar_ad_enable'] ||
    empty($settings['sidebar_ad_image'])
) {
    return;
}

?>
  
<section class="sidebar-widget">

<a

href="<?php echo esc_url(
$settings['sidebar_ad_link']
); ?>">

<img

src="<?php echo esc_url(
$settings['sidebar_ad_image']
); ?>"

alt="Advertisement"

loading="lazy">

</a>

</section>
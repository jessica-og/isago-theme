<?php

if (!defined('ABSPATH')) {
    exit;
}

$slot = $args['slot'] ?? '';

if (empty($slot)) {
    return;
}

?>

<section class="isago-home-ad">

<div class="ad-box">
        <?php

    echo isago_render_ad(
        $slot,
        'homepage-ad',
        'ad-image'
    );

    ?>
</div>

</section>
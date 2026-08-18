<?php

if (!defined('ABSPATH')) {
    exit;
}

$title = $args['title'] ?? '';

$link = $args['link'] ?? '#';

?>

<div class="section-heading">

    <h2>

        <?php echo esc_html($title); ?>

    </h2>

    <a href="<?php echo esc_url($link); ?>">

        View All

        <i class="fa-solid fa-arrow-right-long"></i>

    </a>

</div>
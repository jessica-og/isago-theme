<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="category-post-grid">

<?php while (have_posts()) : the_post(); ?>

    <?php get_template_part('template-parts/category/cards/post-card'); ?>

<?php endwhile; ?>

</div> 
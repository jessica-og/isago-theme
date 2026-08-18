<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="isago-search-page">

<div class="container">

    <?php
  
    get_template_part('template-parts/search/search-header');
    ?>

    <div class="category-page-grid">
 
        <main class="category-main">

            <?php if (have_posts()) : ?>

                <?php get_template_part('template-parts/search/search-grid'); ?>

                <?php isago_pagination(); ?>

            <?php else : ?>

                <?php get_template_part('template-parts/search/search-no-results'); ?>

            <?php endif; ?>

        </main>

        <?php
        get_template_part(
            'template-parts/category/sidebar/sidebar'
        );
        ?>

    </div>

</div>

</section>
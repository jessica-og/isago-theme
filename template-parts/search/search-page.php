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
    <div class="post-category padding-top">
<?php if (isago_ad_enabled('search')) : ?>
      <div class="ad-box">
          <?php

        echo isago_render_ad(
            'search',
            'ad-image',
            'category-banner',
        
        );

        ?>
      </div>
<?php endif; ?>

    </div>
        </main>

        <?php
        get_template_part(
            'template-parts/category/sidebar/sidebar'
        );
        ?>

    </div>

</div>

</section>




<section class="homepage-video-wrapper">

    <div class="container">

        <?php

        get_template_part(
            'template-parts/sections/video-section'
        );

        ?>

    </div>

</section>
<?php
/*
Template Name: About Page
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header();
$settings = isago_get_theme_settings();
?>

<div class="container isago-page">

    <?php
    get_template_part('template-parts/common/breadcrumb');
    ?>

    <div class="homepage-grid"> <!-- reuse your grid -->

        <!-- LEFT CONTENT -->
        <main class="homepage-main">

            <?php
            get_template_part('template-parts/about/about-hero');
            get_template_part('template-parts/about/about-story');
            get_template_part('template-parts/about/about-what');
            get_template_part('template-parts/about/about-values');
            get_template_part('template-parts/about/about-cta');
            ?>

        </main>

        <!-- RIGHT SIDEBAR -->
      <aside class="homepage-sidebar">
    <?php get_template_part('template-parts/about/about-sidebar'); ?>
</aside>

    </div>

</div>

<?php get_footer(); ?>
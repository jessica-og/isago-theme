<?php
/*
Template Name: Cookies Policy
*/
get_header();
?>



<div class="isago-container cookies-layout">

    <!-- LEFT -->
    <main class="cookies-main">
        <?php get_template_part('template-parts/cookies/cookies-content'); ?>
    </main>

    <!-- RIGHT -->
    <aside class="cookies-sidebar">
        <?php get_template_part('template-parts/cookies/cookies-sidebar'); ?>
    </aside>

</div>

<?php get_footer(); ?>
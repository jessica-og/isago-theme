<?php
/* Template Name: Contact Page */

get_header();
?>

<div class="contact-page">

<main class="contact-main">

<?php
get_template_part('template-parts/contact/contact-hero');
get_template_part('template-parts/contact/contact-cards');
get_template_part('template-parts/contact/contact-form');
get_template_part('template-parts/contact/contact-faq');
get_template_part('template-parts/contact/contact-newsletter');
?>

</main>



</div>

<?php get_footer(); ?>
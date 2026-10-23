<?php

if (!defined('ABSPATH')) {
    exit;
}
$footer_ad = isago_get_ad('footer');
?>

<footer class="isago-footer">

    <div class="container">
 
    
<?php if (isago_ad_enabled('footer')) : ?>
      <div class="ad-box footer-banner">
          <?php

        echo isago_render_ad(
            'footer',
            'ad-image',
            'category-banner',  
        
        );

        ?>
      </div>
<?php endif; ?>

    


        <div class="isago-footer-grid">

            <?php get_template_part('template-parts/footer/footer-brand'); ?>

            <?php get_template_part('template-parts/footer/footer-explore'); ?>

            <?php get_template_part('template-parts/footer/footer-resources'); ?>

            <?php get_template_part('template-parts/footer/footer-support'); ?>

            <?php get_template_part('template-parts/footer/footer-newsletter'); ?>

        </div>

        <?php

        get_template_part(
            'template-parts/footer/footer-bottom'
        );

        ?>
<!-- Back To Top -->
<button
    type="button"
    class="isago-back-to-top"
    aria-label="Back to top"
    title="Back to top">

    <i class="fa-solid fa-arrow-up"></i>

</button>
    </div>

</footer>

<?php

get_template_part(
    'template-parts/lightbox'
);

wp_footer();

?>

</body>
</html>
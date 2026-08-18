<?php

if (!defined('ABSPATH')) {
    exit;
}
$footer_ad = isago_get_ad('footer');
?>

<footer class="isago-footer">

    <div class="container">
 
      <?php if (
    !empty($footer_ad['enable']) &&
    !empty($footer_ad['image'])
) : ?>

    <div class="header-ad-label">
        Advertisement
    </div>

    <div class="header-ad-box">

        <div class="header-banner">

            <?php if (!empty($footer_ad['url'])) : ?>

                <a
                    href="<?php echo esc_url($footer_ad['url']); ?>"
                    <?php
                    if (!empty($footer_ad['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                >

                    <img
                        src="<?php echo esc_url($footer_ad['image']); ?>"
                        alt="<?php echo esc_attr($footer_ad['alt']); ?>"
                    >

                </a>

            <?php else : ?>

                <img
                    src="<?php echo esc_url($footer_ad['image']); ?>"
                    alt="<?php echo esc_attr($footer_ad['alt']); ?>"
                >

            <?php endif; ?>

        </div>

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
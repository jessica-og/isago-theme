<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Isago Author Settings
|--------------------------------------------------------------------------
*/

$settings = isago_get_theme_settings();

$author_name = !empty($settings['author_name'])
    ? $settings['author_name']
    : 'Isago';

$author_image =
    isago_get_author_image_url(
        'medium'
    );

?>

<section class="post-author-box">

    <div class="post-author-avatar">

        <?php if (!empty($author_image)) : ?>

            <img
                src="<?php echo esc_url($author_image); ?>"
                width="90"
                height="90"
                alt="<?php echo esc_attr($author_name); ?>"
                loading="lazy"
            >

        <?php else : ?>

            <div class="post-author-placeholder">

                <i class="fa-solid fa-user"></i>

            </div>

        <?php endif; ?>

    </div>


    <div class="post-author-content">

        <h3>

            Written by  <span class="author-bio">~</span>

            <span>
                <?php echo esc_html($author_name); ?>
            </span>

        </h3>


       <?php if (!empty($settings['author_bio'])) : ?>

    <p>

        <?php echo esc_html(
            $settings['author_bio']
        ); ?>

    </p>

<?php endif; ?>


        <div class="post-author-links">

            <?php

            $social = isago_get_social_settings();

            ?>


            <?php if (!empty($social['facebook'])) : ?>

                <a
                    href="<?php echo esc_url($social['facebook']); ?>"
                    <?php
                    if (!empty($social['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                    aria-label="Facebook"
                >

                    <i class="fa-brands fa-facebook-f"></i>

                </a>

            <?php endif; ?>


            <?php if (!empty($social['x'])) : ?>

                <a
                    href="<?php echo esc_url($social['x']); ?>"
                    <?php
                    if (!empty($social['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                    aria-label="X"
                >

                    <i class="fa-brands fa-x-twitter"></i>

                </a>

            <?php endif; ?>


            <?php if (!empty($social['linkedin'])) : ?>

                <a
                    href="<?php echo esc_url($social['linkedin']); ?>"
                    <?php
                    if (!empty($social['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                    aria-label="LinkedIn"
                >

                    <i class="fa-brands fa-linkedin-in"></i>

                </a>

            <?php endif; ?>


            <?php if (!empty($social['instagram'])) : ?>

                <a
                    href="<?php echo esc_url($social['instagram']); ?>"
                    <?php
                    if (!empty($social['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                    aria-label="Instagram"
                >

                    <i class="fa-brands fa-instagram"></i>

                </a>

            <?php endif; ?>

        </div>

    </div>

</section>
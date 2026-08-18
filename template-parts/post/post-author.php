<?php

if (!defined('ABSPATH')) {
    exit;
}

$author = get_the_author_meta('ID');

?>

<section class="post-author-box">

    <div class="post-author-avatar">

        <?php echo get_avatar($author, 90); ?>

    </div>

    <div class="post-author-content">

        <h3>

            Written by

            <?php the_author(); ?>

        </h3>

        <p>

            <?php

            echo wp_trim_words(

                get_the_author_meta('description'),

                35

            );

            ?>

        </p>

        <div class="post-author-links">

            <?php if (get_the_author_meta('user_url')) : ?>

                <a href="<?php echo esc_url(get_the_author_meta('user_url')); ?>">

                    <i class="fa-solid fa-globe"></i>

                </a>

            <?php endif; ?>

            <?php if (get_the_author_meta('linkedin')) : ?>

                <a href="<?php echo esc_url(get_the_author_meta('linkedin')); ?>">

                    <i class="fa-brands fa-linkedin-in"></i>

                </a>

            <?php endif; ?>

            <?php if (get_the_author_meta('twitter')) : ?>

                <a href="<?php echo esc_url(get_the_author_meta('twitter')); ?>">

                    <i class="fa-brands fa-x-twitter"></i>

                </a>

            <?php endif; ?>

        </div>

    </div>

</section>
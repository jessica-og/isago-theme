<?php

if (!defined('ABSPATH')) {
    exit;
}  
$settings = isago_get_home_video_settings();
?>

<article class="category-post-card">

    <a
        class="category-post-image"
        href="<?php the_permalink(); ?>">
  
        <?php
  echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );
        ?>

    </a>

    <div class="category-post-content center-post-content">

        <div class="category-text">

            <?php

            echo esc_html(

                get_the_category()[0]->name ?? ''

            );

            ?>

        </div>

        <h3 class="sidebar-post-title ">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h3>

        <p class="isago-excerpt">

            <?php

            echo wp_trim_words(

                get_the_excerpt(),

                18

            );

            ?>

        </p>

        <div class="isago-post-meta">

       <?php

$theme = isago_get_theme_settings();

$author_image =
    isago_get_author_image_url(
        'medium'
    );

?>

<div class="category-author">

    <?php if (!empty($author_image)) : ?>

        <img
            src="<?php echo esc_url($author_image); ?>"
            width="32"
            height="32"
            alt="<?php echo esc_attr($author_name); ?>"
            loading="lazy"
        >

    <?php endif; ?>

    <span>

        By <?php echo esc_html($author_name); ?>

    </span>

    <span>
        •
    </span>  

    <span>

        <?php echo esc_html(get_the_date()); ?>

    </span>

</div>

        </div>

    </div>

</article>
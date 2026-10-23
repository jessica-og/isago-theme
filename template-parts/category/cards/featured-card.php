<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="category-featured-card">

    <a
        class="category-featured-image"
        href="<?php the_permalink(); ?>">

        <?php

        echo isago_get_post_thumbnail(

    get_the_ID(),
                'large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

        ?>

    </a>

    <div class="category-featured-content center-big-post-content">

        <div class="category-text">

            <?php

            $category = get_the_category();

            echo !empty($category)
                ? esc_html(strtoupper($category[0]->name))
                : '';

            ?>

        </div>

        <h2 class="isago-big-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h2>

        <p class="isago-excerpt">

            <?php

            echo wp_trim_words(
                get_the_excerpt(),
                32
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
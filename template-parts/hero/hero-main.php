<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = $args['query'] ?? null;

if (!$query || !$query->have_posts()) {
    return;
}

$query->the_post();

$categories = get_the_category();

?>

<article class="hero-main">

    <a
        href="<?php the_permalink(); ?>"
        aria-label="<?php echo esc_attr(get_the_title()); ?>"
    >

        <?php

        if (has_post_thumbnail()) {

            the_post_thumbnail(
                'large',
                [
                    'loading' => 'eager',
                    'fetchpriority' => 'high'
                ]
            );

        }

        ?>

    </a>


    <div class="hero-main-content">

        <?php if (!empty($categories)) : ?>

            <div class="category-text isago-post-category">

                <?php echo esc_html($categories[0]->name); ?>

            </div>

        <?php endif; ?>


        <h2 class='isago-big-post-title'>

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h2>


        <?php

        $excerpt = get_the_excerpt();

        if (!empty($excerpt)) :

        ?>

            <p>

                <?php

                echo esc_html(
                    wp_trim_words(
                        wp_strip_all_tags($excerpt),
                        24
                    )
                );

                ?>

            </p>

        <?php endif; ?>


        <div class="isago-post-meta">

            <?php echo esc_html(get_the_date()); ?>

            <span aria-hidden="true">•</span>

            <?php the_author(); ?>

        </div>

    </div>

</article>

<?php wp_reset_postdata(); ?>
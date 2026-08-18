<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = $args['query'] ?? null;

if (!$query || !$query->have_posts()) {
    return;
}

?>

<div class="hero-left">

    <?php

    $count = 0;

    while ($query->have_posts() && $count < 2) :

        $query->the_post();

        $categories = get_the_category();

    ?>

        <article class="hero-side-card">

            <a
                href="<?php the_permalink(); ?>"
                aria-label="<?php echo esc_attr(get_the_title()); ?>"
            >

                <?php

                if (has_post_thumbnail()) {

                    the_post_thumbnail(
                        'medium_large',
                        [
                            'loading' => $count === 0 ? 'eager' : 'lazy'
                        ]
                    );

                }

                ?>

            </a>


            <div class="hero-side-content">

                <?php if (!empty($categories)) : ?>

                    <div class="category-text isago-post-category">

                        <?php echo esc_html($categories[0]->name); ?>

                    </div>

                <?php endif; ?>


                <h3 class="isago-small-post-title">

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </h3>


                <div class="isago-post-meta">

                   <?php echo esc_html(get_the_date()); ?>

            <span aria-hidden="true">•</span>

            <?php the_author(); ?>

                </div>

            </div>

        </article>

    <?php

        $count++;

    endwhile;

    wp_reset_postdata();

    ?>

</div>
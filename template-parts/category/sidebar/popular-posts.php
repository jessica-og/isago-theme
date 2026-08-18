<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_popular_posts_query(5);

if (!$query->have_posts()) {
    return;
}

?>

<section class="category-widget">

    <h3>

        Popular Articles

    </h3>

    <div class="category-popular-list">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <article class="category-popular-item">

                <a
                    class="category-popular-thumb"
                    href="<?php the_permalink(); ?>">

                    <?php

                    if (has_post_thumbnail()) {

                        the_post_thumbnail('thumbnail');

                    }

                    ?>

                </a>

                <div class="category-popular-content">

                    <h4>

                        <a href="<?php the_permalink(); ?>">

                            <?php the_title(); ?>

                        </a>

                    </h4>

                    <span>

                        <?php echo get_the_date(); ?>

                    </span>

                </div>

            </article>

        <?php endwhile; ?>

    </div>

</section>

<?php wp_reset_postdata(); ?>
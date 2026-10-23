<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_get_sidebar_popular_posts(5);

if (!$query->have_posts()) {
    return;
}

?>  

<section class="category-widget sidebar-popular-card">

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
  echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );
        ?>

                </a>

                <div class="category-popular-content">

                    <h4 class="isago-editorial-post-title">

                        <a href="<?php the_permalink(); ?>">

                            <?php the_title(); ?>

                        </a>

                    </h4>

                    <span class="isago-post-meta">

                        <?php echo get_the_date(); ?>

                    </span>

                </div>

            </article>

        <?php endwhile; ?>

    </div>

</section>

<?php wp_reset_postdata(); ?>
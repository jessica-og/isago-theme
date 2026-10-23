<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_sidebar_settings();

$query = new WP_Query([

    'post_type'              => 'post',
    'posts_per_page'         => absint($settings['latest_posts']),
    'post_status'            => 'publish',
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,

]);

if (!$query->have_posts()) {
    return;
}

?>

<section class="sidebar-widget">

<h3 class="isago-section-title">

LATEST

</h3>

<?php while ($query->have_posts()) : $query->the_post(); ?>

<article class="latest-item">

    <div class="latest-contents">

        <h4 class="latest-sidebar-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h4>

        <?php if ($settings['show_date']) : ?>

            <div class="isago-post-meta">

                <?php echo get_the_date(); ?>

            </div>

        <?php endif; ?>

    </div>

    <?php if ($settings['show_thumb']) : ?>

        <a class="latest-thumb" href="<?php the_permalink(); ?>">

            <?php     echo isago_get_post_thumbnail(

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

    <?php endif; ?>

</article>

<?php endwhile; ?>

</section>

<?php wp_reset_postdata(); ?>
<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_home_video_settings();

$query = isago_get_homepage_videos(
    $settings['category'],
    $settings['posts']
);

if (!$query->have_posts()) {
    return;
}

$category = get_category_by_slug(
    $settings['category']
);

?>

<section class="homepage-video-section">

    <?php

    get_template_part(
        'template-parts/sections/section-header',
        null,
        [
            'title' => $settings['title'],
            'link'  => $category
                ? get_category_link($category)
                : '#'
        ]
    );

    ?>

    <div class="homepage-video-grid">

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <?php  

            $video_url = isago_get_video_url(get_the_ID());

            $duration = get_post_meta(
                get_the_ID(),
                '_isago_video_duration',
                true
            );

            ?>

            <article class="homepage-video-card">

                <div class="homepage-video-thumb">

                    <a
                        class="homepage-video-image"
                        href="<?php the_permalink(); ?>">

                        <?php

                   echo isago_get_video_thumbnail();

                        ?>

                        <div class="homepage-video-overlay"></div>

                    </a>

                    <button
                        type="button"
                        class="homepage-video-play"
                        data-video="<?php echo esc_url($video_url); ?>">

                        <i class="fa-solid fa-play"></i>

                    </button>

                </div>

                <div class="homepage-video-content">
  <span class="homepage-video-label">

                        VIDEO

                    </span>
                    <h3>

                        <a href="<?php the_permalink(); ?>">

                            <?php the_title(); ?>

                        </a>

                    </h3>

                    <div class="homepage-video-meta">

                        <?php echo esc_html(get_the_date()); ?>
                           •

            <?php the_author(); ?>
                  
                    </div>

                </div>

            </article>

        <?php endwhile; ?>

    </div>

</section>

<?php wp_reset_postdata(); ?>
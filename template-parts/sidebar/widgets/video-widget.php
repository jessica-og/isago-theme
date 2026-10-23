<?php

if (!defined('ABSPATH')) {
    exit;
}


$settings = isago_get_sidebar_settings();


/*
|--------------------------------------------------------------------------
| Popular Sidebar Videos
|--------------------------------------------------------------------------
*/

$query = isago_popular_sidebar_videos(
    $settings['video_posts']
);


if (!$query->have_posts()) {
    return;
}
$video_category = get_term_by(
    'name',
    'AI Tutorials',
    'category'
);

$video_link = '#';

if ($video_category) {

    $video_link = get_category_link(
        $video_category->term_id
    );
}
?>

<section class="sidebar-widget">

    <div class="section-heading">

        <h3>
            VIDEO
        </h3>

     <a href="<?php echo esc_url($video_link); ?>">
    More Video
</a>

    </div>


    <?php while ($query->have_posts()) : $query->the_post(); ?>

<article class="sidebar-video-item">

    <div class="sidebar-video-thumb">

        <div class="sidebar-video-image">

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


            <?php
            /*
            |--------------------------------------------------------------------------
            | Video Lightbox Source
            |--------------------------------------------------------------------------
            */

            $video_url = isago_get_video_url(
                get_the_ID()
            );
            ?>  


            <?php if (!empty($video_url)) : ?>

                <button
                    type="button"
                    class="sidebar-video-play homepage-video-play"
                    data-video="<?php echo esc_url($video_url); ?>"
                    aria-label="<?php echo esc_attr(
                        'Play ' . get_the_title()
                    ); ?>"
                >

                    <i class="fa-solid fa-play"></i>

                </button>

            <?php endif; ?>

        </div>

    </div>


    <div class="sidebar-video-content">

        <div class="sidebar-video-label">

            VIDEO

        </div>


        <h4 class="isago-editorial-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h4>


        <div class="isago-post-meta">

            <?php echo esc_html(
                get_the_date()
            ); ?>

        </div>

    </div>

</article>

    <?php endwhile; ?>


</section>

<?php

wp_reset_postdata();
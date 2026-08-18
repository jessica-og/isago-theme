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

?>

<section class="sidebar-widget">

    <div class="sidebar-heading">

        <h3>
            VIDEO
        </h3>

        <a href="<?php echo esc_url(
            get_post_type_archive_link('isago_video')
        ); ?>">
            More Video
        </a>

    </div>


    <?php while ($query->have_posts()) : $query->the_post(); ?>

        <article class="video-item">

            <a
                href="<?php the_permalink(); ?>"
                class="video-thumb"
            >

                <div class="video-image">

                    <?php

                    if (has_post_thumbnail()) {

                        the_post_thumbnail('medium');

                    } else {

                        echo '<img src="' .
                            esc_url(
                                get_template_directory_uri() .
                                '/assets/images/video.png'
                            ) .
                            '" alt="' .
                            esc_attr(get_the_title()) .
                            '">';

                    }

                    ?>

                    <span class="video-play">

                        <i class="fa-solid fa-play"></i>

                    </span>

                </div>

            </a>


            <div class="video-content">

                <span class="video-label">
                    VIDEO
                </span>


                <h4>

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </h4>


                <div class="latest-date">

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
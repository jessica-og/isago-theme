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

        if (has_post_thumbnail()) {

            the_post_thumbnail(
                'medium_large'
            );

        } else{
            $yt_thumb = isago_get_video_url(get_the_ID());

    if ($yt_thumb) {
       echo isago_get_video_thumbnail();
    } else {
        // fallback image
        echo '<img src="' . get_template_directory_uri() . '/assets/images/video.png" alt="No image">';
    }

        }
        ?>

    </a>

    <div class="category-post-content">

        <div class="category-post-category">

            <?php

            echo esc_html(

                get_the_category()[0]->name ?? ''

            );

            ?>

        </div>

        <h3>

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h3>

        <p>

            <?php

            echo wp_trim_words(

                get_the_excerpt(),

                18

            );

            ?>

        </p>

        <div class="category-post-meta">

            <div class="category-author">

                <?php

                echo get_avatar(

                    get_the_author_meta('ID'),

                    26

                );

                ?>

                <span>

                    <?php the_author(); ?>

                </span>

            </div>

            <span>

                <?php echo get_the_date(); ?>

            </span>

        </div>

    </div>

</article>
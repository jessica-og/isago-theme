<?php

if (!defined('ABSPATH')) {
    exit;
}


$author_image =
    isago_get_author_image_url(
        'medium'
    );


$post_id =
    get_the_ID();


$category =
    get_the_category();


$is_video =
    get_post_type($post_id) === 'isago_video';


/*
|--------------------------------------------------------------------------
| Get playable video URL
|--------------------------------------------------------------------------
*/

$video_url =
    isago_get_video_url(
        $post_id
    );

?>

<header class="post-header">


    <div class="post-category">

        <?php if (isago_ad_enabled('post')) : ?>

            <div class="ad-box">

                <?php

                echo isago_render_ad(
                    'post',
                    'ad-image',
                    'category-banner'
                );

                ?>

            </div>

        <?php endif; ?>

    </div>


    <h1 class="post-title">

        <?php the_title(); ?>

    </h1>


    <?php if (has_excerpt()) : ?>

        <div class="post-excerpt">

            <?php the_excerpt(); ?>

        </div>

    <?php endif; ?>


    <div class="post-meta-row">


        <div class="post-meta">


            <div class="post-author">

                <img
                    class="post-author-avatar"
                    src="<?php echo esc_url($author_image); ?>"
                    width="42"
                    height="42"
                    alt="<?php echo esc_attr(
                        isago_get_author_name()
                    ); ?>"
                    loading="lazy"
                >

                <span>

                    <?php echo esc_html(
                        isago_get_author_name()
                    ); ?>

                </span>

            </div>


            <span>•</span>


            <span>

                <?php echo get_the_date(); ?>

            </span>


            <span>•</span>


            <span>

                <?php

                echo ceil(

                    str_word_count(

                        wp_strip_all_tags(

                            get_post_field(
                                'post_content',
                                $post_id
                            )

                        )

                    ) / 220

                );

                ?>

                min read

            </span>


        </div>


        <?php

        get_template_part(
            'template-parts/post/post-share'
        );

        ?>


    </div>


    <?php if (!empty($video_url)) : ?>


        <!-- =====================================================
             FEATURED IMAGE WITH VIDEO
             ===================================================== -->

        <div class="post-featured-image isago-post-video">


            <div class="isago-post-video-thumb">


                <?php

                /*
                |--------------------------------------------------------------------------
                | Featured Image
                |--------------------------------------------------------------------------
                */

                echo isago_get_post_thumbnail(

                    $post_id,

                    'full',

                    [
                        'loading' =>
                            'lazy',

                        'alt' =>
                            the_title_attribute([
                                'echo' => false
                            ])
                    ]

                );

                ?>


                <div
                    class="homepage-video-overlay"
                    aria-hidden="true"
                ></div>


                <button
                    type="button"
                    class="homepage-video-play isago-post-video-play"
                    data-video="<?php echo esc_url(
                        $video_url
                    ); ?>"
                    aria-label="<?php echo esc_attr(
                        'Play ' . get_the_title($post_id)
                    ); ?>"
                >

                    <i
                        class="fa-solid fa-play"
                        aria-hidden="true"
                    ></i>

                </button>


            </div>


        </div>


    <?php else : ?>


        <!-- =====================================================
             NORMAL POST FEATURED IMAGE
             ===================================================== -->

        <div class="post-featured-image">

            <?php

            echo isago_get_post_thumbnail(

                $post_id,

                'full',

                [
                    'loading' =>
                        'lazy',

                    'alt' =>
                        the_title_attribute([
                            'echo' => false
                        ])
                ]

            );

            ?>

        </div>


    <?php endif; ?>


</header>
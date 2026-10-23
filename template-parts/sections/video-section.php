<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Homepage Video Section Settings
|--------------------------------------------------------------------------
*/

$settings =
    isago_get_home_video_settings();


$type =
    $settings['type']
    ?? 'blog';


$source =
    $settings['source']
    ?? '';


$posts =
    absint(
        $settings['posts']
        ?? 3
    );


/*
|--------------------------------------------------------------------------
| Get Videos
|--------------------------------------------------------------------------
*/

$query =
    isago_get_homepage_videos(
        $type,
        $source,
        $posts
    );


/*
|--------------------------------------------------------------------------
| Stop if no videos
|--------------------------------------------------------------------------
*/

if (
    !$query
    ||
    !$query->have_posts()
) {

    return;

}


/*
|--------------------------------------------------------------------------
| Section Header Link
|--------------------------------------------------------------------------
*/

$section_link = '#';


/*
|--------------------------------------------------------------------------
| BLOG LINK
|--------------------------------------------------------------------------
*/

if ($type === 'blog') {

    if (
        !empty($source)
        &&
        $source !== '__all'
    ) {

        $category =
            get_category_by_slug(
                sanitize_title($source)
            );


        if ($category) {

            $section_link =
                get_category_link(
                    $category->term_id
                );

        }

    }

    else {

        /*
         * All Blog Videos.
         *
         * Change this URL if your site uses
         * another dedicated video/blog destination.
         */

        $section_link =
            home_url('/blog/');

    }

}


/*
|--------------------------------------------------------------------------
| AI PROMPT LINK
|--------------------------------------------------------------------------
*/

elseif ($type === 'ai_prompt') {

    if (
        !empty($source)
        &&
        $source !== '__all'
    ) {

        $prompt_category =
            get_term_by(

                'slug',

                sanitize_title($source),

                'ai_prompt_category'

            );


        if (
            $prompt_category
            &&
            !is_wp_error($prompt_category)
        ) {

            $section_link =
                get_term_link(
                    $prompt_category
                );

        }

    }

    else {

        /*
         * All AI Prompt videos.
         */

        $section_link =
            home_url(
                '/ai-prompts-library/'
            );

    }

}


/*
|--------------------------------------------------------------------------
| AI TOOL LINK
|--------------------------------------------------------------------------
*/

elseif ($type === 'ai_tool') {

    $tool_category_id =
        absint($source);


    if ($tool_category_id) {

        /*
         * Use the existing AI Tool Category URL helper
         * when available.
         */

        if (
            function_exists(
                'isago_get_ai_tool_category_url'
            )
        ) {

            $tool_url =
                isago_get_ai_tool_category_url(
                    $tool_category_id
                );


            if ($tool_url) {

                $section_link =
                    $tool_url;

            }

        }

    }

    else {

        /*
         * All AI Tools.
         */

        if (
            function_exists(
                'isago_get_ai_platform_directory_url'
            )
        ) {

            $section_link =
                isago_get_ai_platform_directory_url();

        }

        else {

            $section_link =
                home_url('/ai-tools/');

        }

    }

}


/*
|--------------------------------------------------------------------------
| Render Section
|--------------------------------------------------------------------------
*/

?>

<section class="homepage-video-section">

    <?php

    get_template_part(

        'template-parts/sections/section-header',

        null,

        [

            'title' =>
                $settings['title']
                ?? 'Latest Videos',

            'link' =>
                $section_link

        ]

    );

    ?>


    <div class="homepage-video-grid">


        <?php while ($query->have_posts()) : ?>

            <?php

            $query->the_post();


            /*
            |--------------------------------------------------------------------------
            | Video Data
            |--------------------------------------------------------------------------
            */

            $video_id =
                get_the_ID();


            $video_url =
                isago_get_video_url(
                    $video_id
                );


            $duration =
                get_post_meta(
                    $video_id,
                    '_isago_video_duration',
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            |
            | Use the universal video thumbnail helper.
            |
            | This supports:
            |
            | - Featured image
            | - Custom poster
            | - Generated video thumbnail
            | - YouTube thumbnail
            | - Placeholder
            |
            |--------------------------------------------------------------------------
            */
  
            $thumbnail =
                isago_get_video_thumbnail(
                    $video_id
                );


            /*
            |--------------------------------------------------------------------------
            | Author
            |--------------------------------------------------------------------------
            */

            $theme =
                isago_get_theme_settings();


            $author_name =
                !empty($theme['author_name'])
                ? $theme['author_name']
                : 'Isago';

            ?>


            <article class="homepage-video-card">


                <div class="homepage-video-thumb">


                    <a
                        class="homepage-video-image"
                        href="<?php the_permalink(); ?>"
                    >

                        <?php

                        /*
                         * Video thumbnail.
                         */

                        echo $thumbnail;

                        ?>


                        <div
                            class="homepage-video-overlay"
                        ></div>

                    </a>


                 <?php if (!empty($video_url)) : ?>

    <button
        type="button"
        class="homepage-video-play"
        data-video="<?php echo esc_url(
            $video_url
        ); ?>"
        aria-label="<?php echo esc_attr(
            'Play ' . get_the_title()
        ); ?>"
    >

        <i
            class="fa-solid fa-play"
            aria-hidden="true"
        ></i>

    </button>

<?php endif; ?>

                    <?php if (!empty($duration)) : ?>

                        <span
                            class="homepage-video-duration"
                        >

                            <?php
                            echo esc_html($duration);
                            ?>

                        </span>

                    <?php endif; ?>


                </div>


                <div class="homepage-video-content">


                    <span class="homepage-video-label">
                        VIDEO
                    </span>


                    <h3 class="isago-small-post-title">

                        <a
                            href="<?php the_permalink(); ?>"
                        >

                            <?php the_title(); ?>

                        </a>

                    </h3>


                    <div class="isago-post-meta">

                        <span>

                            <?php
                            echo esc_html(
                                get_the_date()
                            );
                            ?>

                        </span>


                        <span aria-hidden="true">
                            •
                        </span>


                        <span>

                            <?php
                            echo esc_html(
                                $author_name
                            );
                            ?>

                        </span>

                    </div>


                </div>


            </article>


        <?php endwhile; ?>


    </div>

</section>


<?php

wp_reset_postdata();

?>
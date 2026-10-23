<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings =
    isago_get_theme_settings();

  
/*=========================================================
ABOUT STORY IMAGE
=========================================================*/

$about_story_image =
    isago_get_about_story_image_url(
        'full'
    );
 
?>

<section class="about-story">


    <div class="about-story-grid">


        <div class="about-story-image">

            <img
                src="<?php echo esc_url(
                    $about_story_image
                ); ?>"
                alt="<?php echo esc_attr(
                    $settings['about_story_title']
                ); ?>"
            >

        </div>


        <div class="about-story-content">


            <h2>

                <?php echo esc_html(
                    $settings['about_story_title']
                ); ?>

            </h2>


            <p>

                <?php echo nl2br(
                    esc_html(
                        $settings['about_story_text']
                    )
                ); ?>

            </p>


        </div>


    </div>


</section>
<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings =
    isago_get_theme_settings();

  
/*=========================================================
ABOUT HERO IMAGES
=========================================================*/


$about_hero_image =
    isago_get_about_hero_image_url(
        'full'
    );


$about_hero_image_mobile =
    isago_get_about_hero_image_mobile_url(
        'full'
    );
/*
 * If no separate mobile image exists,
 * use the desktop image.
 */
if (!$about_hero_image_mobile) {

    $about_hero_image_mobile =
        $about_hero_image;

}
?>

<section class="about-hero">


    <div class="about-hero-left">


        <h1>

            <?php echo esc_html(
                $settings['about_title']
            ); ?>

        </h1>


        <p class="about-lead">

            <?php echo esc_html(
                $settings['about_desc']
            ); ?>

        </p>


        <div class="about-features">


            <div class="about-feature">

                <div class="icon">

                    <i class="fas fa-lightbulb"></i>

                </div>

                <h4>
                    Practical Knowledge
                </h4>

                <p>
                    Actionable guides and prompts.
                </p>

            </div>


            <div class="about-feature">

                <div class="icon">

                    <i class="fas fa-layer-group"></i>

                </div>

                <h4>
                    Curated Resources
                </h4>

                <p>
                    We review the best AI tools.
                </p>

            </div>


            <div class="about-feature">

                <div class="icon">

                    <i class="fas fa-users"></i>

                </div>

                <h4>
                    Creator Focused
                </h4>

                <p>
                    Built for creators.
                </p>

            </div>


        </div>


    </div>


    <div class="about-hero-right">


        <picture>


            <source
                media="(max-width: 1140px)"
                srcset="<?php echo esc_url(
                    $about_hero_image_mobile
                ); ?>"
            >


            <img
                src="<?php echo esc_url(
                    $about_hero_image
                ); ?>"
                alt="<?php echo esc_attr(
                    $settings['about_title']
                ); ?>"
            >


        </picture>


    </div>


</section>
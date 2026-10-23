<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ABOUT SETTINGS
=========================================================*/

$settings = isago_get_theme_settings();


/*=========================================================
CTA SETTINGS
=========================================================*/

$cta_text = $settings['about_cta_text'] ?? 'Explore Now';

$cta_link = $settings['about_cta_link'] ?? '#';

?>

<section class="about-cta">

    <div class="about-cta-box">


        <!-- CTA LEFT -->
        <div class="cta-left">

            <div class="cta-icon">

                <i class="fas fa-paper-plane"></i>

            </div>


            <div>

                <h3>
                    Join thousands of creators using AI
                </h3>

                <p>
                    Do more, create more, and grow faster.
                </p>

            </div>

        </div>


        <!-- CTA BUTTON -->
        <a
            href="<?php echo esc_url($cta_link); ?>"
            class="isago-animated-button about-box"
        >

            <?php echo esc_html($cta_text); ?>
<span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>

        </a>


    </div>

</section>
<?php

if (!defined('ABSPATH')) {
    exit;
}


$settings =
    isago_get_theme_settings();


/*=========================================================
CONTACT HERO IMAGE
=========================================================*/
$hero_image_url =
    isago_get_contact_hero_image_url(
        'full'
    );

?>


<section class="contact-hero">


    <div class="contact-hero-inner">


        <div class="contact-hero-left">


            <h1>

                Contact Isago

            </h1>


            <p class="contact-lead">

                We’d love to hear from you! Whether you have a question,
                feedback, or need support — we’re here to help.

            </p>


        </div>


        <div class="contact-hero-right">


            <div class="hero-map">


                <img
                    src="<?php echo esc_url($hero_image_url); ?>"
                    alt="Contact Isago"
                >


            </div>


        </div>


    </div>


</section>
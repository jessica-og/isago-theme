<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$categories_url =
    isset(
        $args['categories_url']
    )
        ? $args['categories_url']
        : home_url(
            '/ai-tool-categories/'
        );

?>


<!-- =================================================
BACK TO CATEGORIES
================================================= -->

<section
    class="ai-platform-page-content"
    style="padding-top:0;"
>


    <div class="container">


        <a
            href="<?php echo esc_url(
                $categories_url
            ); ?>"
              class="isago-animated-button"
        >



            <i
                class="fa-solid fa-arrow-left"
                aria-hidden="true"
            ></i>


            Browse all AI tool categories

<span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>
        </a>


    </div>


</section>
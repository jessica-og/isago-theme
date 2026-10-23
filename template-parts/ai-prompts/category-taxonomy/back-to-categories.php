<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$ai_prompts_url =
    isset($args['ai_prompts_url'])
        ? $args['ai_prompts_url']
        : home_url(
            '/ai-prompts-library/'
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
            href="<?php
            echo esc_url(
                $ai_prompts_url
            );
            ?>"
            class="isago-animated-button"
        >

            <i
                class="fa-solid fa-arrow-left"
                aria-hidden="true"
            ></i>

            Browse all AI prompt categories
<span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>
        </a>


    </div>

</section>
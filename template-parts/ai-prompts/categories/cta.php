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
BROWSE ALL CTA
================================================= -->

<section
    class=" container"
>

    <div class="isago-ai-tool-categories-cta">
    <div >


        <span
            class="isago-ai-tool-categories-kicker"
        >

            AI PROMPTS DIRECTORY

        </span>


        <h2>

            Looking for a specific prompt?

        </h2>


        <p>

            Explore the complete Isago AI
            prompt library and discover
            prompts by category.

        </p>


    </div>


    <a
        href="<?php
        echo esc_url(
            $ai_prompts_url
        );
        ?>"
        class="isago-animated-button"
    >

        Browse all AI prompts

        <i
            class="fa-solid fa-arrow-right"
            aria-hidden="true"
        ></i>
  <span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>
    </a>
</div>

</section>
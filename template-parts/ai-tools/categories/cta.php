<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$ai_tools_url =
    isset(
        $args['ai_tools_url']
    )
        ? $args['ai_tools_url']
        : home_url(
            '/ai-tools/'
        );

?>


<section
    class="isago-ai-tool-categories-cta"
>


    <div>


        <span
            class="isago-ai-tool-categories-kicker"
        >

            AI TOOLS DIRECTORY

        </span>


        <h2>

            Looking for a specific AI tool?

        </h2>


        <p>

            Browse the complete Isago AI
            tools directory and discover
            platforms by name.

        </p>


    </div>


    <a
        href="<?php echo esc_url(
            $ai_tools_url
        ); ?>"
         class="isago-animated-button"
    >


        Browse all AI tools


          <i
            class="fa-solid fa-arrow-right"
            aria-hidden="true"
        ></i>
  <span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>
    </a>


</section>
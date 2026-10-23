<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
GET AI PLATFORMS
=========================================================*/

$platforms =
    isago_get_post_ai_platforms(
        get_the_ID()
    );


/*
 * No AI platforms assigned.
 */

if (
    empty($platforms)
) {
    return;
}


/*=========================================================
GET NORMAL POST CATEGORIES
=========================================================*/

$category_ids =
    wp_get_post_categories(
        get_the_ID()
    );


$context = 'general';


/*=========================================================
DETERMINE EDITORIAL CONTEXT
=========================================================*/

foreach (
    $category_ids as $category_id
) {

    $ancestors =
        get_ancestors(
            $category_id,
            'category'
        );


    $category_chain =
        array_merge(
            $ancestors,
            [$category_id]
        );


    foreach (
        $category_chain as $term_id
    ) {

        $term =
            get_category(
                $term_id
            );


        if (!$term) {
            continue;
        }


        /*
         * AI Prompts context.
         */

        if (
            $term->slug === 'ai-prompts'
        ) {

            $context = 'prompt';

            break 2;

        }


        /*
         * AI Tools context.
         */

        if (
            $term->slug === 'ai-tools'
        ) {

            $context = 'tool';

            break 2;

        }

    }

}

?>


<section class="ai-platform-cta">


    <?php foreach (
        $platforms as $platform
    ) : ?>


        <?php

        /*
         * Get AI Platform URL.
         */

        $url =
            isago_get_ai_platform_url(
                $platform
            );


        if (!$url) {
            continue;
        }


        /*
         * Get context-specific CTA text.
         */

        $label =
            isago_get_ai_platform_cta(
                $platform,
                $context
            );


        if (!$label) {
            continue;
        }

        ?>


        <a
            href="<?php echo esc_url($url); ?>"
            class="isago-animated-button"
        >


            <span>

                <?php

                echo esc_html(
                    $label
                );

                ?>

            </span>


            <i
                class="fa-solid fa-arrow-right"
                aria-hidden="true"
            ></i>

    <span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>
        </a>


    <?php endforeach; ?>


</section>
<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
CURRENT POST
=========================================================*/

$post_id =
    get_the_ID();


/*=========================================================
CONTENT TYPE
=========================================================*/

$is_ai_prompt =
    get_post_type($post_id) === 'ai_prompt';


/*=========================================================
CATEGORY / TAXONOMY NAME
=========================================================*/

$category_name = '';


if ($is_ai_prompt) {

    /*
     * AI Prompt category.
     */

    $prompt_categories =
        isago_get_ai_prompt_post_categories(
            $post_id
        );


    if (!empty($prompt_categories)) {

        $category_name =
            $prompt_categories[0]->name;

    }

} else {

    /*
     * Normal WordPress category.
     */

    $categories =
        get_the_category(
            $post_id
        );


    if (!empty($categories)) {

        $category_name =
            $categories[0]->name;

    }

}


/*=========================================================
POST URL
=========================================================*/

$post_url =
    get_permalink(
        $post_id
    );

?>


<article class="grid-card">


    <!-- =================================================
         IMAGE
         ================================================= -->

    <a
        class="grid-image"
        href="<?php echo esc_url($post_url); ?>"
    >

        <?php

        echo isago_get_post_thumbnail(

            $post_id,

            'medium',

            [

                'loading' =>
                    'lazy',

                'alt' =>
                    the_title_attribute([

                        'echo' =>
                            false

                    ])

            ]

        );

        ?>

    </a>


    <!-- =================================================
         CONTENT
         ================================================= -->

    <div class="related-content center-post-content">


        <!-- =================================================
             CATEGORY
             ================================================= -->

        <?php if ($category_name) : ?>

            <div class="category-text">

                <?php
                echo esc_html(
                    $category_name
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             TITLE
             ================================================= -->

        <h3 class="sidebar-post-title">

            <a
                href="<?php echo esc_url($post_url); ?>"
            >

                <?php
                the_title();
                ?>

            </a>

        </h3>


        <!-- =================================================
             DATE
             ================================================= -->

        <div class="isago-post-meta">

            <?php
            echo esc_html(
                get_the_date()
            );
            ?>

        </div>


    </div>


</article>
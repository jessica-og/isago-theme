<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
PROMPT ID
=========================================================*/

$prompt_id = get_the_ID();

if (!$prompt_id) {
    return;
}


/*=========================================================
PROMPT DATE
=========================================================*/

$updated_date = get_the_modified_date(
    'F j, Y',
    $prompt_id
);


/*=========================================================
PROMPT CATEGORIES
=========================================================*/

$prompt_categories = get_the_terms(
    $prompt_id,
    'ai_prompt_category'
);

if (
    is_wp_error($prompt_categories) ||
    empty($prompt_categories)
) {

    $prompt_categories = array();

}


/*=========================================================
PRIMARY CATEGORY
=========================================================*/

$primary_category = null;

if (!empty($prompt_categories)) {

    /*
     * Use the first assigned category
     * as the primary category.
     */

    $primary_category =
        $prompt_categories[0];

}


/*=========================================================
CATEGORY ICON
=========================================================*/

$category_icon = '';

if (
    $primary_category instanceof WP_Term
) {

    /*
     * Get the icon saved on the
     * AI Prompt Category.
     */

    $category_icon =
        isago_get_ai_prompt_category_icon(
            $primary_category->term_id
        );

}


/*
 * Fallback icon if the category
 * does not have an icon assigned.
 */

if (empty($category_icon)) {
 
    $category_icon =
        'fa-solid fa-wand-magic-sparkles';

} 


/*=========================================================
PLATFORMS
=========================================================*/

$prompt_platforms = get_the_terms(
    $prompt_id,
    'ai_platform'
);

if (
    is_wp_error($prompt_platforms) ||
    empty($prompt_platforms)
) {

    $prompt_platforms = array();

}


/*=========================================================
SHORT DESCRIPTION
=========================================================*/

$short_description =
    isago_get_ai_prompt_short_description(
        $prompt_id
    );

?>


<header class="isago-ai-prompt-header">


    <?php if ($primary_category) : ?>


        <div class="isago-ai-prompt-header-category">


            <?php

            $category_link =
                get_term_link(
                    $primary_category,
                    'ai_prompt_category'
                );

            ?>


            <?php if (!is_wp_error($category_link)) : ?>


                <a
                    href="<?php echo esc_url($category_link); ?>"
                    class="ai-platform-page-category"
                >


                    <i
                        class="<?php echo esc_attr($category_icon); ?>"
                        aria-hidden="true"
                    ></i>


                    <?php

                    echo esc_html(
                        $primary_category->name
                    );

                    ?>


                </a>


            <?php endif; ?>


        </div>


    <?php endif; ?>


    <!-- =================================================
         TITLE
         ================================================= -->


    <h1 class="post-title">

        <?php the_title(); ?>

    </h1>


   

 
<!-- =================================================
     SHORT DESCRIPTION — RICH TEXT
     ================================================= -->

<?php
$short_description_text = trim(
    wp_strip_all_tags(
        (string) $short_description
    )
);
?>

<?php if ($short_description_text !== '') : ?>

    <div class="isago-ai-prompt-description">

        <?php
        echo wp_kses_post(
            wpautop($short_description)
        );
        ?>

    </div>

<?php endif; ?>



</header>
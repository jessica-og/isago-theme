<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();


/*=========================================================
CURRENT CATEGORY
=========================================================*/

$category =
    get_queried_object();


if (
    !$category
    ||
    is_wp_error($category)
    ||
    $category->taxonomy !== 'ai_prompt_category'
) {

    get_footer();

    return;

}


/*=========================================================
CATEGORY DATA
=========================================================*/

$category_id =
    absint(
        $category->term_id
    );


$category_name =
    $category->name;


$category_description =
    $category->description;


/*=========================================================
CATEGORY ICON
=========================================================*/

$category_icon =
    get_term_meta(
        $category_id,
        '_isago_ai_prompt_category_icon',
        true
    );


$category_icon =
    sanitize_text_field(
        $category_icon
    );


if (
    empty($category_icon)
) {

    $category_icon =
        'fa-solid fa-wand-magic-sparkles';

}


/*=========================================================
AI PROMPTS LIBRARY URL
=========================================================*/

$ai_prompts_url =
    home_url(
        '/ai-prompts-library/'
    );


/*=========================================================
AI TOOLS URL
=========================================================*/

$ai_tools_url =
    home_url(
        '/ai-tools/'
    );


/*=========================================================
CATEGORY PROMPT COUNT
=========================================================*/

$prompt_count =
    (int)
    $category->count;


/*=========================================================
TOTAL PROMPT CATEGORIES
=========================================================*/

$total_categories =
    wp_count_terms(
        array(
            'taxonomy'   => 'ai_prompt_category',
            'hide_empty' => false,
        )
    );


if (
    is_wp_error(
        $total_categories
    )
) {

    $total_categories = 0;

}


/*=========================================================
PAGINATION
=========================================================*/

$paged =
    max(
        1,
        absint(
            get_query_var('paged')
        ),
        absint(
            get_query_var('page')
        )
    );


/*=========================================================
PROMPT QUERY
=========================================================*/

$prompt_query =
    new WP_Query(
        array(

            'post_type' =>
                'ai_prompt',

            'post_status' =>
                'publish',

            'posts_per_page' =>
                12,

            'paged' =>
                $paged,

            'ignore_sticky_posts' =>
                true,

            'tax_query' =>
                array(

                    array(

                        'taxonomy' =>
                            'ai_prompt_category',

                        'field' =>
                            'term_id',

                        'terms' =>
                            $category_id,

                    ),

                ),

        )
    );


/*=========================================================
HERO
=========================================================*/

get_template_part(
    'template-parts/ai-prompts/category-taxonomy/hero',
    null,
    array(

        'category' =>
            $category,

        'category_id' =>
            $category_id,

        'category_name' =>
            $category_name,

        'category_description' =>
            $category_description,

        'category_icon' =>
            $category_icon,

        'prompt_count' =>
            $prompt_count,

        'total_categories' =>
            $total_categories,

        'ai_prompts_url' =>
            $ai_prompts_url,

        'ai_tools_url' =>
            $ai_tools_url,

    )
);


/*=========================================================
PROMPT GRID
=========================================================*/

get_template_part(
    'template-parts/ai-prompts/category-taxonomy/prompt-grid',
    null,
    array(

        'category' =>
            $category,

        'category_id' =>
            $category_id,

        'category_name' =>
            $category_name,

        'category_icon' =>
            $category_icon,

        'prompt_count' =>
            $prompt_count,

        'prompt_query' =>
            $prompt_query,

        'paged' =>
            $paged,

        'ai_prompts_url' =>
            $ai_prompts_url,

    )
);





/*=========================================================
RELATED BLOG & NEWS ARTICLES
Find posts assigned to the SAME AI Prompt category
=========================================================*/

$related_articles_query = new WP_Query([
    'post_type'           => ['post'],
    'post_status'         => 'publish',
    'posts_per_page'      => -1,
    'ignore_sticky_posts' => true,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'tax_query'           => [
        [
            'taxonomy'         => 'ai_prompt_category',
            'field'            => 'term_id',
            'terms'            => [$category_id],
            'include_children' => true,
            'operator'         => 'IN',
        ],
    ],
]);
?>

<?php if ($related_articles_query->have_posts()) : ?>

    <section class="isago-prompt-related-articles">
        <div class="container">

            <div class="isago-prompt-related-articles-header">
                <div>
                    <span class="isago-prompt-related-articles-kicker">
                        FROM THE ISAGO BLOG
                    </span>

                    <h2>
                           Read more on the topic: 
                   
                    <strong>
                  
                        <?php echo esc_html($category_name); ?>
                    </strong>
 </h2>
                  
                </div>

                <span class="isago-prompt-related-articles-count">
                    <?php
                    echo esc_html(
                        number_format_i18n(
                            $related_articles_query->found_posts
                        )
                    );
                    ?>
                    <?php
                    echo $related_articles_query->found_posts === 1
                        ? 'article'
                        : 'articles';
                    ?>
                </span>
            </div>

            <div class="isago-prompt-related-articles-grid">

                <?php while ($related_articles_query->have_posts()) : ?>
                    <?php $related_articles_query->the_post(); ?>

                    <article class="isago-prompt-related-article">

                        <a
                            class="isago-prompt-related-article-image"
                            href="<?php the_permalink(); ?>"
                        >
                        <?php
                            echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

        ?>
                          
                        </a>

                        <div class="isago-prompt-related-article-body">

                            <span class="isago-prompt-related-article-date">
                                <?php echo esc_html(get_the_date()); ?>
                            </span>

                            <h3>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <p>
                                <?php
                                echo esc_html(
                                    wp_trim_words(
                                        get_the_excerpt(),
                                        22
                                    )
                                );
                                ?>
                            </p>

                            <a
                                class="isago-prompt-related-article-link"
                                href="<?php the_permalink(); ?>"
                            >
                                Read article
                                <i class="fa-solid fa-arrow-right"
                                   aria-hidden="true"></i>
                            </a>

                        </div>
                    </article>

                <?php endwhile; ?>

            </div>
        </div>
    </section>

<?php endif; ?>


<?php




/*=========================================================
BACK TO CATEGORIES
=========================================================*/

get_template_part(
    'template-parts/ai-prompts/category-taxonomy/back-to-categories',
    null,
    array(

        'ai_prompts_url' =>
            $ai_prompts_url,

    )
);


/*=========================================================
RESET POST DATA
=========================================================*/

wp_reset_postdata();



get_footer();

<?php

if (!defined('ABSPATH')) {
    exit;
}

$search_keyword = get_query_var('isago_search_keyword');

if (!$search_keyword) {
    $search_keyword = get_search_query();
}

$search_keyword = sanitize_text_field($search_keyword);
$ai_platform_terms = [];

if (
    $search_keyword !== '' &&
    taxonomy_exists('ai_platform')
) {
    $ai_platform_terms = get_terms([
        'taxonomy'   => 'ai_platform',
        'hide_empty' => false,
        'search'     => $search_keyword,
        'number'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($ai_platform_terms)) {
        $ai_platform_terms = [];
    } else {
        // Keep only platform names containing the search keyword.
        $ai_platform_terms = array_values(
            array_filter(
                $ai_platform_terms,
                function ($term) use ($search_keyword) {
                    return stripos(
                        $term->name,
                        $search_keyword
                    ) !== false;
                }
            )
        );
    }
}

// Get the categories used by your existing AI Tool cards.
$platform_categories = [];

if (function_exists('isago_get_ai_platform_directory')) {
    $directory = isago_get_ai_platform_directory();

    if (
        is_array($directory) &&
        isset($directory['categories'])
    ) {
        $platform_categories = $directory['categories'];
    }
}
?>

<div class="category-post-grid isago-search-results-grid">

    <?php
    /*
     * AI TOOL CARDS
     *
     * These are taxonomy terms, not regular WordPress posts.
     */
    foreach ($ai_platform_terms as $platform) :

        get_template_part(
            'template-parts/ai-tools/browse-ai-tools/card',
            null,
            [
                'platform'            => $platform,
                'platform_categories' => $platform_categories,
                'homepage'            => true,
            ]
        );

    endforeach;
    ?>

    <?php while (have_posts()) : the_post(); ?>

        <?php
        $result_post_type = get_post_type(get_the_ID());

        switch ($result_post_type) {

            case 'ai_prompt':

                get_template_part(
                    'template-parts/ai-prompts/components/card',
                    null,
                    [
                        'prompt' => get_post(),
                    ]
                );

                break;

            case 'post':

                get_template_part(
                    'template-parts/category/cards/post-card'
                );

                break;

            case 'isago_video':

                get_template_part(
                    'template-parts/category/cards/post-card'
                );

                break;

            default:

                do_action(
                    'isago_search_result_card',
                    get_post()
                );

                break;
        }
        ?>

    <?php endwhile; ?>

</div>

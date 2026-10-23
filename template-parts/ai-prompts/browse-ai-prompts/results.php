<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$selected_category =
    $args['selected_category']
    ?? '';

$selected_platform =
    isset(
        $args['selected_platform']
    )
        ? absint(
            $args['selected_platform']
        )
        : 0;

$search =
    $args['search']
    ?? '';

$prompt_categories =
    $args['prompt_categories']
    ?? array();

$prompt_platforms =
    $args['prompt_platforms']
    ?? array();

$prompts =
    $args['prompts']
    ?? array();

$total_prompts =
    isset(
        $args['total_prompts']
    )
        ? (int)
            $args['total_prompts']
        : count(
            $prompts
        );

$prompt_query =
    $args['prompt_query']
    ?? null;


/*=========================================================
RESULT HEADER
=========================================================*/

$results_kicker =
    'LIBRARY';

$results_title =
    'Explore AI Prompts';


/*=========================================================
CATEGORY TITLE
=========================================================*/

if (
    !empty(
        $selected_category
    )
) {

    foreach (
        $prompt_categories
        as $category
    ) {

        if (
            $category->slug ===
            $selected_category
        ) {

            $results_kicker =
                'CATEGORY';

            $results_title =
                $category->name;

            break;

        }

    }

}


/*=========================================================
AI TOOL TITLE
=========================================================*/

if (
    $selected_platform
) {

    foreach (
        $prompt_platforms
        as $platform
    ) {

        if (
            (int)
            $platform->term_id
            ===
            $selected_platform
        ) {

            $results_kicker =
                'AI TOOL';

            $results_title =
                $platform->name;

            break;

        }

    }

}


/*=========================================================
SEARCH TITLE
=========================================================*/

if (
    !empty(
        $search
    )
) {

    $results_kicker =
        'SEARCH';

    $results_title =
        'Search results';

}

?>


<section
    class="ai-platform-directory-results"
>

    <div class="container">

<div
    class="isago-ai-prompts-loading"
    data-isago-ai-prompts-loading
    role="status"
    aria-live="polite"
    aria-atomic="true"
    hidden
>
    <span class="ai-prompt-loading-spinner" aria-hidden="true"></span>
    <span>Fetching AI prompts…</span>
</div>
        <div
            class="ai-platform-results-header"
        >




            <div>


                <span
                    class="ai-platform-results-kicker"
                >

                    <?php echo esc_html(
                        $results_kicker
                    ); ?>

                </span>


                <h2>

                    <?php echo esc_html(
                        $results_title
                    ); ?>

                </h2>


            </div>
  

            <span
                class="ai-platform-results-count"
            >

                <?php echo esc_html(
                    number_format_i18n(
                        $total_prompts
                    )
                ); ?>


                <?php echo (
                    $total_prompts === 1
                )
                    ? 'Prompt'
                    : 'AI Prompts'; ?>

            </span>


        </div>


        <?php if (
            !empty(
                $prompts
            )
        ) : ?>


            <div
                class="ai-platform-directory-grid"
            >


                <?php foreach (
                    $prompts
                    as $prompt
                ) : ?>


                    <?php

                    get_template_part(
                        'template-parts/ai-prompts/components/card',
                        null,
                        array(

                            'prompt' =>
                                $prompt,

                        )
                    );

                    ?>


                <?php endforeach; ?>


            </div>


            <?php if (
                $prompt_query
                &&
                $prompt_query->max_num_pages > 1
            ) : ?>


                <div
                    class="ai-platform-page-pagination"
                >

                    <?php

                    isago_pagination(
                        $prompt_query
                    );

                    ?>

                </div>


            <?php endif; ?>


        <?php else : ?>


            <?php

            get_template_part(
                'template-parts/ai-prompts/browse-ai-prompts/empty'
            );

            ?>


        <?php endif; ?>


    </div>

</section>
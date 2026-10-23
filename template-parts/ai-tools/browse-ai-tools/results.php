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

$search =
    $args['search']
    ?? '';

$platform_categories =
    $args['platform_categories']
    ?? [];

$platforms =
    $args['platforms']
    ?? [];

$total_platforms =
    isset(
        $args['total_platforms']
    )
        ? (int) $args['total_platforms']
        : count($platforms);

$platform_pagination =
    $args['platform_pagination']
    ?? null;


/*=========================================================
RESULT HEADER KICKER
=========================================================*/

if (
    !empty(
        $selected_category
    )
    &&
    isset(
        $platform_categories[
            $selected_category
        ]
    )
) {

    $results_kicker =
        $platform_categories[
            $selected_category
        ];

} elseif (
    !empty($search)
) {

    $results_kicker =
        'SEARCH';

} else {

    $results_kicker =
        'DIRECTORY';

}


/*=========================================================
RESULT HEADER TITLE
=========================================================*/

if (  
    !empty(
        $selected_category
    )
    &&
    isset(
        $platform_categories[
            $selected_category
        ]
    )
) {

    $results_title =
        $platform_categories[
            $selected_category
        ];

} elseif (
    !empty($search)
) {

    $results_title =
        'Search results';

} else {

    $results_title =
        'Explore AI Tools';

}

?>


<!-- =================================================
RESULTS
================================================= -->
<div
    class="ai-platforms-loading"
    data-ai-platforms-loading
    role="status"
    aria-live="polite"
    aria-atomic="true"
    hidden
>
    <span
        class="ai-platforms-loading-spinner"
        aria-hidden="true"
    ></span>

    <span>Fetching AI platforms…</span>
</div>
<section
    class="ai-platform-directory-results"
>

    <div class="container">




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
                        $total_platforms
                    )
                ); ?>


                <?php echo (
                    $total_platforms === 1
                )
                    ? 'Tool'
                    : 'AI Tools'; ?>


            </span>


        </div>


        <?php if (
            !empty($platforms)
        ) : ?>


            <div
                class="ai-platform-directory-grid"
            >


                <?php foreach (
                    $platforms as $platform
                ) : ?>


                    <?php

                    get_template_part(
                        'template-parts/ai-tools/browse-ai-tools/card',
                        null,
                        [

                            'platform' =>
                                $platform,

                            'platform_categories' =>
                                $platform_categories

                        ]
                    );

                    ?>


                <?php endforeach; ?>


            </div>


            <?php if (
                $platform_pagination
                &&
                $platform_pagination->max_num_pages > 1
            ) : ?>
 

                <div
                    class="ai-platform-page-pagination"
                >

                    <?php

                  isago_pagination(
    $platform_pagination,
    $args['current_page'] ?? 1
);

                    ?>

                </div>


            <?php endif; ?>


        <?php else : ?>


            <?php

            get_template_part(
                'template-parts/ai-platforms/browse-ai-tools/empty'
            );

            ?>


        <?php endif; ?>


    </div>

</section>
<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
DATA
=========================================================*/

$platform_name =
    isset($args['platform_name'])
        ? sanitize_text_field(
            $args['platform_name']
        )
        : '';


$platform_id =
    isset($args['platform_id'])
        ? absint(
            $args['platform_id']
        )
        : 0;


$platform_term =
    isset($args['platform_term'])
        ? $args['platform_term']
        : null;


/*=========================================================
GET PLATFORM FROM PLATFORM TERM
=========================================================*/

if (
    !$platform_id
    &&
    $platform_term instanceof WP_Term
) {

    if (
        $platform_term->taxonomy ===
        'ai_platform'
    ) {

        $platform_id =
            absint(
                $platform_term->term_id
            );

    }

}


/*=========================================================
GET PLATFORM FROM CURRENT QUERIED OBJECT
=========================================================*/

if (
    !$platform_id
) {

    $queried_object =
        get_queried_object();


    if (
        $queried_object instanceof WP_Term
        &&
        $queried_object->taxonomy ===
        'ai_platform'
    ) {

        $platform_term =
            $queried_object;

        $platform_id =
            absint(
                $queried_object->term_id
            );

    }

}


/*=========================================================
GET PLATFORM FROM NAME
=========================================================*/

if (
    !$platform_id
    &&
    !empty($platform_name)
) {

    /*
     * Try exact platform name.
     */

    $found_term =
        get_term_by(
            'name',
            $platform_name,
            'ai_platform'
        );


    /*
     * Try platform slug.
     */

    if (
        !$found_term
        ||
        is_wp_error(
            $found_term
        )
    ) {

        $found_term =
            get_term_by(
                'slug',
                sanitize_title(
                    $platform_name
                ),
                'ai_platform'
            );

    }


    if (
        $found_term
        &&
        !is_wp_error(
            $found_term
        )
    ) {

        $platform_term =
            $found_term;

        $platform_id =
            absint(
                $found_term->term_id
            );

    }

}


/*=========================================================
GET PLATFORM TERM BY ID
=========================================================*/

if (
    !$platform_term
    &&
    $platform_id
) {

    $platform_term =
        get_term(
            $platform_id,
            'ai_platform'
        );


    if (
        !$platform_term
        ||
        is_wp_error(
            $platform_term
        )
    ) {

        $platform_term =
            null;

    }

}


/*=========================================================
PLATFORM NAME FALLBACK
=========================================================*/

if (
    empty($platform_name)
    &&
    $platform_term instanceof WP_Term
) {

    $platform_name =
        $platform_term->name;

}


/*=========================================================
VIEW ALL — AI PROMPTS
=========================================================*/

/*
 * IMPORTANT:
 *
 * The Isago AI Prompt directory is:
 *
 * /ai-prompts-library/
 *
 * The directory filtering system uses:
 *
 * prompt_platform
 *
 * with the AI Platform term ID.
 */

$prompts_view_all_url =
    home_url(
        '/ai-prompts-library/'
    );


if (
    $platform_id
) {

    $prompts_view_all_url =
        add_query_arg(
            'prompt_platform',
            $platform_id,
            $prompts_view_all_url
        );

}


/*=========================================================
LATEST PROMPTS QUERY
=========================================================*/

$latest_prompts_query =
    null;


if (
    $platform_id
) {

    $latest_prompts_query =
        new WP_Query([

            'post_type' =>
                'ai_prompt',

            'post_status' =>
                'publish',

            'posts_per_page' =>
                4,

            'orderby' =>
                'date',

            'order' =>
                'DESC',

            'ignore_sticky_posts' =>
                true,

            'no_found_rows' =>
                false,

            'tax_query' => [

                [

                    'taxonomy' =>
                        'ai_platform',

                    'field' =>
                        'term_id',

                    'terms' =>
                        $platform_id,

                    'operator' =>
                        'IN',

                ]

            ],

        ]);

}


/*=========================================================
TOTAL PROMPTS
=========================================================*/

$total_prompts =
    $latest_prompts_query
    ? (int)
        $latest_prompts_query->found_posts
    : 0;


/*=========================================================
HAS PROMPTS
=========================================================*/

$has_prompts =
    $latest_prompts_query
    &&
    !is_wp_error(
        $latest_prompts_query
    )
    &&
    $latest_prompts_query->have_posts();

?>


<!-- =====================================================
     LATEST PROMPTS
     ===================================================== -->

<section
    class="
        ai-platform-page-content
        ai-platform-page-latest-content
    "
>

    <div class="container">


        <!-- =================================================
             HEADER
             ================================================= -->

        <div
            class="ai-platform-page-content-header"
        >

            <div>

                <h2>

                    Latest Prompts

                </h2>
    <!-- =================================================
             VIEW ALL PROMPTS
             ================================================= -->

        <?php if (
            !empty(
                $prompts_view_all_url
            )
        ) : ?>

            <div
                class="ai-platform-page-view-all"
            >

                <a
                    href="<?php
                    echo esc_url(
                        $prompts_view_all_url
                    );
                    ?>"
                    class="ai-platform-card-action"
                >

                    View All Prompts

                    <i
                        class="fa-solid fa-arrow-right"
                        aria-hidden="true"
                    ></i>

                </a>

            </div>

        <?php endif; ?>
            </div>


            <div
                class="ai-platform-page-content-count"
            >

                <span>

                    total

                </span>


                <strong
                    class="ai-platform-page-count-number"
                >

                    <?php

                    echo esc_html(
                        $total_prompts
                    );

                    ?>

                </strong>

            </div>

        </div>


        <!-- =================================================
             PROMPT CONTENT
             ================================================= -->

        <?php if ($has_prompts) : ?>


            <div
                class="ai-platform-page-article-grid"
            >

                <?php

                while (
                    $latest_prompts_query->have_posts()
                ) :

                    $latest_prompts_query->the_post();


                    /*
                     * The AI Prompt card expects
                     * the actual WP_Post object
                     * through:
                     *
                     * $args['prompt']
                     */

                    $prompt =
                        get_post();

                ?>

                    <article
                        class="ai-platform-page-article"
                    >

                        <?php

                        get_template_part(

                            'template-parts/ai-prompts/components/card',

                            null,

                            [

                                'prompt' =>
                                    $prompt

                            ]

                        );

                        ?>

                    </article>

                <?php endwhile; ?>

            </div>


        <?php else : ?>


            <div
                class="ai-platform-page-empty"
            >

                <p>

                    No prompts available yet.

                </p>

            </div>


        <?php endif; ?>


    


    </div>

</section>


<?php

wp_reset_postdata();

?>
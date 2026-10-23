<?php

if (!defined('ABSPATH')) {
    exit;
}


/*========================================================= 
VARIABLES
=========================================================*/

$platform =  
    $args['platform']
    ?? null;

$platform_categories =
    $args['platform_categories']
    ?? [];


if (
    !$platform
    ||
    is_wp_error($platform)
) {

    return;

}


/*=========================================================
PLATFORM URL
=========================================================*/

$url =
    isago_get_ai_platform_url(
        $platform
    );


/*=========================================================
PLATFORM CATEGORY
=========================================================*/

$category =
    get_term_meta(
        $platform->term_id,
        '_isago_ai_platform_category',
        true
    );


/*=========================================================
PLATFORM IMAGE
=========================================================*/

/*
 * The platform image is stored as a
 * WordPress attachment ID.
 */

$image_id =
    absint(
        get_term_meta(
            $platform->term_id,
            '_isago_ai_platform_image',
            true
        )
    );


$image_url = '';


if ($image_id) {

    $image_url =
        isago_get_media_image_url(
            $image_id,
            'medium'
        );

}


/*=========================================================
CATEGORY LABEL
=========================================================*/

$category_label =
    'AI Platform';


if (
    $category
    &&
    isset(
        $platform_categories[
            $category
        ]
    )
) {

    $category_label =
        $platform_categories[
            $category
        ];

}


/*=========================================================
COUNT ASSOCIATED CONTENT
=========================================================*/

$content_query =
    new WP_Query(

        [

            'post_type' => [

                'post',
                'isago_video'

            ],

            'post_status' =>
                'publish',

            'posts_per_page' =>
                1,

            'fields' =>
                'ids',

            'no_found_rows' =>
                false,

            'tax_query' => [

                [

                    'taxonomy' =>
                        'ai_platform',

                    'field' =>
                        'term_id',

                    'terms' =>
                        $platform->term_id

                ]

            ]

        ]

    );



/*=========================================================
DESCRIPTION
=========================================================*/

$description =
    isago_get_ai_platform_description(
        $platform->term_id
    );

?>


<article
    class="ai-platform-card"
>


    <a
        href="<?php echo esc_url(
            $url
        ); ?>"
        class="ai-platform-card-link"
    >


        <div
            class="ai-platform-card-media-ai-tools "
        >


            <?php if (
                !empty(
                    $image_url
                )
            ) : ?>


                <img
                    src="<?php echo esc_url(
                        $image_url
                    ); ?>"
                    alt="<?php echo esc_attr(
                        $platform->name
                    ); ?>"
                    loading="lazy"
                >


            <?php else : ?>


                <span
                    class="ai-platform-card-fallback"
                >

                    <?php echo esc_html(
                        mb_strtoupper(
                            mb_substr(
                                $platform->name,
                                0,
                                1
                            )
                        )
                    ); ?>

                </span>


            <?php endif; ?>


      


        </div>


        <div
            class="ai-platform-card-body"
        >


            <div
                class="ai-platform-card-top"
            >


                <span
                    class="ai-platform-card-category"
                >

                    <?php echo esc_html(
                        $category_label
                    ); ?>

                </span>


            </div>


            <h3
                class="ai-platform-card-title"
            >

                <?php echo esc_html(
                    $platform->name
                ); ?>

            </h3>


            <?php if (
                !empty(
                    $description
                )
            ) : ?>


                <p
                    class="ai-platform-card-description"
                >

                    <?php echo esc_html(
                        wp_trim_words(
                            wp_strip_all_tags(
                                $description
                            ),
                            22
                        )
                    ); ?>

                </p>


            <?php else : ?>


                <p
                    class="ai-platform-card-description"
                >

                    Explore articles,
                    tutorials and
                    resources about
                    <?php echo esc_html(
                        $platform->name
                    ); ?>.

                </p>


            <?php endif; ?>


            <span
                class="ai-platform-card-action"
            >

                Explore platform


                <i
                    class="fa-solid fa-arrow-right"
                    aria-hidden="true"
                ></i>

            </span>


        </div>


    </a>


</article>
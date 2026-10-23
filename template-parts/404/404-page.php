<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| HOME URL
|--------------------------------------------------------------------------
*/

$home_url = home_url('/');


/*
|--------------------------------------------------------------------------
| 404 BROWSE CATEGORIES
|--------------------------------------------------------------------------
|
| These are the four primary editorial destinations shown on the
| 404 page.
|
*/

$browse_category_slugs = [

    'ai-news',
    'ai-prompts',
    'ai-tools',
    'blog'

];

$browse_categories = [];


/*
|--------------------------------------------------------------------------
| GET BROWSE CATEGORIES
|--------------------------------------------------------------------------
*/

foreach ($browse_category_slugs as $category_slug) {

    $category = get_category_by_slug(
        $category_slug
    );

    if (
        !$category
        ||
        is_wp_error($category)
    ) {
        continue;
    }

    $browse_categories[] = $category;

}


/*
|--------------------------------------------------------------------------
| CATEGORY ARTICLE COUNT
|--------------------------------------------------------------------------
|
| Count published posts and Isago videos assigned to this category
| OR any child/grandchild category.
|
*/

function isago_404_category_article_count(
    $category_id
) {

    $category_id = absint(
        $category_id
    );

    if (!$category_id) {
        return 0;
    }


    /*
     * Include the current category.
     */
    $category_ids = [
        $category_id
    ];


    /*
     * Include ALL descendants.
     *
     * get_term_children() recursively returns descendants,
     * including grandchildren and deeper levels.
     */
    $children = get_term_children(
        $category_id,
        'category'
    );


    if (
        !is_wp_error($children)
        &&
        !empty($children)
    ) {

        $category_ids = array_merge(
            $category_ids,
            $children
        );

    }


    $category_ids = array_unique(
        array_map(
            'absint',
            $category_ids
        )
    );


    /*
     * Count published editorial content.
     */
    $query = new WP_Query([

        'post_type' => [

            'post',
            'isago_video'

        ],

        'post_status' => 'publish',

        'posts_per_page' => 1,

        'fields' => 'ids',

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $category_ids,

                'include_children' => false

            ]

        ],

        'no_found_rows' => false,

        'ignore_sticky_posts' => true,

        'update_post_meta_cache' => false,

        'update_post_term_cache' => false

    ]);


    return (int) $query->found_posts;

}


/*
|--------------------------------------------------------------------------
| LATEST POSTS
|--------------------------------------------------------------------------
*/

$latest_query = new WP_Query([

    'post_type' => [

        'post',
        'isago_video'

    ],

    'post_status' => 'publish',

    'posts_per_page' => 4,

    'orderby' => 'date',

    'order' => 'DESC',

    'ignore_sticky_posts' => true

]);

?>


<section class="isago-404-content">


    <!-- =========================================================
    HERO
    ========================================================= -->

    <div class="isago-404-hero">

        <div class="isago-404-container">

            <div class="isago-404-copy">


                <!-- Eyebrow -->

                <span class="isago-404-eyebrow">

                    ISAGO • PAGE NOT FOUND

                </span>


                <!-- 404 -->

                <div
                    class="isago-404-number"
                    aria-hidden="true"
                >

                    404

                </div>


                <!-- Heading -->

                <h1>

                    This page went
                    <span>off the map.</span>

                </h1>


                <!-- Description -->

                <p class="isago-404-description">

                    The page you're looking for may have moved,
                    been renamed, or no longer exists.

                    Don't worry — there's plenty more to explore
                    on Isago.

                </p>


                <!-- Actions -->

                <div class="isago-404-actions">


                    <!-- HOME -->

                    <a
                        href="<?php echo esc_url(
                            $home_url
                        ); ?>"
                        class="isago-404-button isago-404-button-primary"
                    >

                        <i
                            class="fa-solid fa-house"
                            aria-hidden="true"
                        ></i>

                        Back to Home

                    </a>


                    <!-- AI NEWS -->

                    <?php

                    $ai_news_category =
                        get_category_by_slug(
                            'ai-news'
                        );

                    $ai_news_url = $ai_news_category
                        ? get_category_link(
                            $ai_news_category->term_id
                        )
                        : home_url('/');

                    ?>

                    <a
                        href="<?php echo esc_url(
                            $ai_news_url
                        ); ?>"
                        class="isago-404-button isago-404-button-secondary"
                    >

                        Explore Articles

                        <i
                            class="fa-solid fa-arrow-right"
                            aria-hidden="true"
                        ></i>

                    </a>


                </div>


            </div>


            <!-- =================================================
            VISUAL
            ================================================= -->

            <div class="isago-404-visual">

                <div class="isago-404-grid">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>


                <div class="isago-404-orbit">

                    <div class="isago-404-orbit-dot"></div>

                </div>


                <div class="isago-404-floating-card">

                    <span class="isago-404-card-label">

                        ERROR

                    </span>

                    <strong>

                        Nothing here.

                    </strong>

                    <small>

                        Let's find something better.

                    </small>

                </div>


                <div class="isago-404-corner">

                    ISAGO.

                </div>

            </div>

        </div>

    </div>



    <!-- =========================================================
    SEARCH
    ========================================================= -->

    <section class="isago-404-search-section">

        <div class="isago-404-container">

            <div class="isago-404-search-box">

                <div class="isago-404-search-heading">

                    <span>

                        SEARCH ISAGO

                    </span>

                    <h2>

                        Looking for something?

                    </h2>

                </div>


                <div class="isago-404-search-form">

                    <?php

                    get_search_form([

                        'aria_label' =>
                            'Search Isago'

                    ]);

                    ?>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
    BROWSE ISAGO
    ========================================================= -->

    <?php if (!empty($browse_categories)) : ?>

        <section class="isago-404-categories">

            <div class="isago-404-container">


                <div class="isago-404-section-heading">

                    <div>

                        <span>

                            EXPLORE

                        </span>

                        <h2>

                            Browse Isago

                        </h2>

                    </div>

                </div>


                <div class="isago-404-category-grid">


                    <?php foreach (
                        $browse_categories as $category
                    ) : ?>


                        <?php

                        /*
                         * Calculate the true editorial total,
                         * including children and grandchildren.
                         */
                        $article_count =
                            isago_404_category_article_count(
                                $category->term_id
                            );


                        $category_url =
                            get_category_link(
                                $category->term_id
                            );

                        ?>


                        <a
                            href="<?php echo esc_url(
                                $category_url
                            ); ?>"
                            class="isago-404-category-card"
                        >


                            <span
                                class="isago-404-category-icon"
                            >

                                <i
                                    class="fa-solid fa-arrow-up-right-from-square"
                                    aria-hidden="true"
                                ></i>

                            </span>


                            <span
                                class="isago-404-category-name"
                            >

                                <?php echo esc_html(
                                    $category->name
                                ); ?>

                            </span>


                            <span
                                class="isago-404-category-count"
                            >

                                <?php echo esc_html(
                                    $article_count
                                ); ?>

                                <?php

                                echo esc_html(

                                    _n(

                                        'article',

                                        'articles',

                                        $article_count,

                                        'isago'

                                    )

                                );

                                ?>

                            </span>


                        </a>


                    <?php endforeach; ?>


                </div>

            </div>

        </section>

    <?php endif; ?>



    <!-- =========================================================
    LATEST ARTICLES
    ========================================================= -->

    <?php if (
        $latest_query->have_posts()
    ) : ?>

        <section class="isago-404-latest">

            <div class="isago-404-container">


                <div class="isago-404-section-heading">

                    <div>

                        <span>

                            FRESH FROM ISAGO

                        </span>

                        <h2>

                            Latest Articles

                        </h2>

                    </div>


                    <a
                        href="<?php echo esc_url(
                            home_url('/')
                        ); ?>"
                    >

                        View all

                        <i
                            class="fa-solid fa-arrow-right"
                            aria-hidden="true"
                        ></i>

                    </a>

                </div>


                <div class="isago-404-post-grid">


                    <?php

                    while (
                        $latest_query->have_posts()
                    ) :

                        $latest_query->the_post();

                    ?>


                        <article
                            class="isago-404-post-card"
                        >


                            <a
                                href="<?php the_permalink(); ?>"
                                class="isago-404-post-image"
                            >

                                <?php

                                echo isago_get_post_thumbnail(

                                    get_the_ID(),

                                    'medium_large',

                                    [

                                        'loading' =>
                                            'lazy',

                                        'alt' =>
                                            the_title_attribute([

                                                'echo' => false

                                            ])

                                    ]

                                );

                                ?>

                            </a>


                            <div
                                class="isago-404-post-content"
                            >


                                <span
                                    class="isago-404-post-date"
                                >

                                    <?php echo esc_html(
                                        get_the_date()
                                    ); ?>

                                </span>


                                <h3>

                                    <a
                                        href="<?php the_permalink(); ?>"
                                    >

                                        <?php the_title(); ?>

                                    </a>

                                </h3>


                                <a
                                    href="<?php the_permalink(); ?>"
                                    class="isago-404-read-more"
                                >

                                    Read article

                                    <i
                                        class="fa-solid fa-arrow-right"
                                        aria-hidden="true"
                                    ></i>

                                </a>


                            </div>


                        </article>


                    <?php endwhile; ?>


                </div>

            </div>

        </section>

    <?php endif; ?>


</section>


<?php

wp_reset_postdata();

?>
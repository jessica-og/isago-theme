<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO CATEGORY HEADER
=========================================================*/

$category = get_queried_object();

$settings =
    isago_get_category_archive_settings();


/*=========================================================
VALIDATION
=========================================================*/

if (
    !$category
    ||
    !isset($category->term_id)
    ||
    !is_category()
) {

    return;

}


/*=========================================================
CATEGORY INFORMATION
=========================================================*/

$category_id =
    absint(
        $category->term_id
    );

$category_title =
    $category->name;

$category_description =
    term_description(
        $category_id,
        'category'
    );


/*=========================================================
GLOBAL CATEGORY HERO IMAGE
=========================================================*/

$hero_image_url =
    isago_get_category_hero_image_url(
        'large'
    );

/*=========================================================
ADVERTISEMENT
=========================================================*/

$category_ad =
    isago_get_ad(
        'category'
    );

?>


<header

    class="category-header"

    data-category="<?php
    echo esc_attr(
        $category_id
    );
    ?>"

>


    <?php

    /*=====================================================
    BREADCRUMB
    =====================================================*/

    if (!empty($settings['breadcrumb'])) {

        get_template_part(
            'template-parts/common/breadcrumb'
        );

    }

    ?>


    <!--=====================================================
    HEADER GRID
    =====================================================-->

    <div class="category-header-grid">


        <!--=================================================
        LEFT
        =================================================-->

        <div class="category-header-content">


            <div class="category-header-heading-row">


                <div class="category-header-heading">


                    <h1 class="category-header-title">

                        <?php echo esc_html(
                            $category_title
                        ); ?>

                    </h1>


                    <?php if (
                        !empty(
                            $category_description
                        )
                    ) : ?>

                        <div
                            class="category-header-description"
                        >

                            <?php

                            echo wp_kses_post(
                                wpautop(
                                    $category_description
                                )
                            );

                            ?>

                        </div>

                    <?php endif; ?>


                </div>


            </div>


        </div>


        <!--=================================================
        RIGHT: HERO IMAGE
        =================================================-->

        <div class="category-header-media">


            <?php if ($hero_image_url) : ?>


                <img

                    src="<?php echo esc_url(
                        $hero_image_url
                    ); ?>"

                    alt="<?php echo esc_attr(
                        $category_title
                    ); ?>"

                    loading="eager"

                >


            <?php else : ?>


                <div
                    class="category-header-placeholder"
                >

                    <i
                        class="fa-solid fa-image"
                        aria-hidden="true"
                    ></i>

                </div>


            <?php endif; ?>


        </div>


    </div>


    <!--=====================================================
    CATEGORY ADVERTISEMENT
    =====================================================-->

    <?php if (
        isago_ad_enabled('category')
    ) : ?>


        <div class="ad-box padding-top-bottom">


            <?php

            echo isago_render_ad(
                'category',
                'ad-image',
                'category-banner'
            );

            ?>


        </div>


    <?php endif; ?>


</header>
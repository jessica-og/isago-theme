<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO TAG HEADER
=========================================================*/

$tag = get_queried_object();


/*
|--------------------------------------------------------------------------
| Make sure this is a tag archive
|--------------------------------------------------------------------------
*/

if (
    !$tag ||
    !isset($tag->term_id) ||
    !is_tag()
) {
    return;
}


/*
|--------------------------------------------------------------------------
| Tag Information
|--------------------------------------------------------------------------
*/

$tag_id = absint(
    $tag->term_id
);

$tag_title = $tag->name;

$tag_description = term_description(
    $tag_id,
    'post_tag'
);


/*
|--------------------------------------------------------------------------
| GLOBAL TAG HERO IMAGE
|--------------------------------------------------------------------------
|
| ONE image is used for ALL tags.
|
*/

$hero_image_url =
    isago_get_tag_hero_image_url(
        'large'
    );

/*
|--------------------------------------------------------------------------
| Advertisement
|--------------------------------------------------------------------------
|
| Uses the TAG advertisement slot.
|
*/

$tag_ad = isago_get_ad(
    'tag'
);

?>


<header
    class="category-header tag-header"
    data-tag="<?php echo esc_attr($tag_id); ?>"
>


    <?php

    /*
    |--------------------------------------------------------------------------
    | Breadcrumb
    |--------------------------------------------------------------------------
    */

    get_template_part(
        'template-parts/common/breadcrumb'
    );

    ?>


    <!--=====================================================
    TAG HEADER GRID
    =====================================================-->

    <div class="category-header-grid">


        <!--=================================================
        LEFT: TAG TITLE + DESCRIPTION
        =================================================-->

        <div class="category-header-content">


            <h1 class="category-header-title">

                <?php echo esc_html(
                    $tag_title
                ); ?>

            </h1>


            <?php if (!empty($tag_description)) : ?>

                <div class="category-header-description">

                    <?php

                    echo wp_kses_post(
                        wpautop(
                            $tag_description
                        )
                    );

                    ?>

                </div>

            <?php endif; ?>


        </div>


        <!--=================================================
        RIGHT: GLOBAL TAG HERO IMAGE
        =================================================-->

        <div class="category-header-media">


            <?php if ($hero_image_url) : ?>


                <img
                    src="<?php echo esc_url(
                        $hero_image_url
                    ); ?>"
                    alt="<?php echo esc_attr(
                        $tag_title
                    ); ?>"
                    loading="eager"
                >


            <?php else : ?>


                <div class="category-header-placeholder">

                    <i class="fa-solid fa-image"></i>

                </div>


            <?php endif; ?>


        </div>


    </div>


    <!--=====================================================
    TAG ADVERTISEMENT
    =====================================================-->

    <?php if (isago_ad_enabled('tag')) : ?>


        <div class="ad-box padding-top-bottom">


            <?php

            echo isago_render_ad(
                'tag',
                'ad-image',
                'category-banner'
            );

            ?>


        </div>


    <?php endif; ?>


</header>
<?php

if (!defined('ABSPATH')) {
    exit;
}

$category = get_queried_object();

$settings = isago_get_category_archive_settings();

/*
|--------------------------------------------------------------------------
| Category Advertisement
|--------------------------------------------------------------------------
|
| Uses the Header Banner slot from Advertisement Manager.
|
*/

$category_ad = isago_get_ad('category');

?>

<header
    class="category-header"
    data-category="<?php echo esc_attr(get_queried_object_id()); ?>"
>

    <?php

    /*
    |--------------------------------------------------------------------------
    | Breadcrumb
    |--------------------------------------------------------------------------
    */

    if ($settings['breadcrumb']) {

        get_template_part(
            'template-parts/common/breadcrumb'
        );

    }

    ?>

    <div class="category-header-grid">

        <div class="category-header-content">

            <h1>

                <?php
                echo esc_html(
                    single_cat_title('', false)
                );
                ?>

            </h1>

            <?php if (!empty($category->description)) : ?>

                <p>

                    <?php
                    echo esc_html(
                        $category->description
                    );
                    ?>

                </p>

            <?php endif; ?>

        </div>


        <?php if (
            !empty($category_ad['enable']) &&
            !empty($category_ad['image'])
        ) : ?>

            <div class="category-banner">

                <?php if (!empty($category_ad['url'])) : ?>

                    <a
                        href="<?php echo esc_url($category_ad['url']); ?>"
                        <?php
                        if (!empty($category_ad['blank'])) {
                            echo 'target="_blank" rel="noopener noreferrer"';
                        }
                        ?>
                    >

                        <img
                            src="<?php echo esc_url($category_ad['image']); ?>"
                            alt="<?php echo esc_attr($category_ad['alt']); ?>"
                        >

                    </a>

                <?php else : ?>

                    <img
                        src="<?php echo esc_url($category_ad['image']); ?>"
                        alt="<?php echo esc_attr($category_ad['alt']); ?>"
                    >

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</header>
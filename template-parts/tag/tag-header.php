<?php

if (!defined('ABSPATH')) {
    exit;
}

$tag = get_queried_object();

$tag_ad = isago_get_ad('tag');

?>

<header
    class="category-header"
    data-tag="<?php echo esc_attr($tag->term_id); ?>">

    <?php

    get_template_part(
        'template-parts/common/breadcrumb'
    );

    ?>

    <div class="category-header-grid">

        <div class="category-header-content">

            <h1>

                <?php echo esc_html($tag->name); ?>

            </h1>

            <?php if (!empty($tag->description)) : ?>

                <p>

                    <?php echo esc_html($tag->description); ?>

                </p>

            <?php endif; ?>

        </div>

        <?php if (
            !empty($tag_ad['enable'])
            && !empty($tag_ad['image'])
        ) : ?>

            <div class="category-banner">

                <?php if (!empty($tag_ad['url'])) : ?>

                    <a
                        href="<?php echo esc_url($tag_ad['url']); ?>">

                        <img
                            src="<?php echo esc_url($tag_ad['image']); ?>"
                            alt="<?php echo esc_attr($tag_ad['alt']); ?>">

                    </a>

                <?php else : ?>

                    <img
                        src="<?php echo esc_url($tag_ad['image']); ?>"
                        alt="<?php echo esc_attr($tag_ad['alt']); ?>">

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</header>
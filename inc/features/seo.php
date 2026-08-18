<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
SEO Plugin Detection
=========================================================*/

function isago_has_seo_plugin()
{
    return (
        defined('RANK_MATH_VERSION') ||
        defined('WPSEO_VERSION') ||
        defined('AIOSEO_VERSION')
    );
}

/*=========================================================
Default SEO
Only runs when no SEO plugin is installed
=========================================================*/

add_action(
    'wp_head',
    'isago_default_meta',
    1
);

function isago_default_meta()
{

    if (isago_has_seo_plugin()) {
        return;
    }

    $seo = isago_get_option(
        'isago_seo_settings'
    );

    $title = $seo['title'] ?? get_bloginfo('name');

    $description = $seo['description'] ?? get_bloginfo('description');

    $image = $seo['og_image'] ?? '';

    ?>

    <meta name="description"
          content="<?php echo esc_attr($description); ?>">

    <meta name="robots"
          content="index,follow">

    <meta property="og:type"
          content="website">

    <meta property="og:title"
          content="<?php echo esc_attr($title); ?>">

    <meta property="og:description"
          content="<?php echo esc_attr($description); ?>">

    <?php if (!empty($image)) : ?>

        <meta property="og:image"
              content="<?php echo esc_url($image); ?>">

    <?php endif; ?>

    <meta name="twitter:card"
          content="summary_large_image">

    <link rel="canonical"
          href="<?php echo esc_url(get_permalink()); ?>">

    <?php

}
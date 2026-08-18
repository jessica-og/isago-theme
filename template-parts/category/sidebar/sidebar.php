<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<aside class="category-sidebar">

<?php

if (is_category()) {

    get_template_part(
        'template-parts/category/sidebar/about-category'
    );

} elseif (is_tag()) {

    get_template_part(
        'template-parts/tag/about-tag'
    );
}

get_template_part(
    'template-parts/category/sidebar/browse-category'
);

get_template_part(
    'template-parts/category/sidebar/popular-posts'
);

get_template_part(
    'template-parts/sidebar/widgets/advertisement-300'
);

get_template_part(
    'template-parts/sidebar/widgets/newsletter'
);

get_template_part(
    'template-parts/sidebar/widgets/popular-tags'
);

?>

</aside>
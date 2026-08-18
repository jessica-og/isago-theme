<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<aside class="post-sidebar">

<?php

get_template_part(
    'template-parts/category/sidebar/about-category'
);

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
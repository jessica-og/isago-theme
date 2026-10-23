<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<aside class="homepage-sidebar">
    <div class="sidebar-grid">
<?php

get_template_part(
'template-parts/sidebar/widgets/latest-posts'
);
?>

    
        <?php
get_template_part(
'template-parts/sidebar/widgets/featured-story'
);

get_template_part(
'template-parts/sidebar/widgets/editorial-list'
);
?>

<?php
get_template_part(
'template-parts/sidebar/widgets/video-widget'
);

get_template_part(
'template-parts/sidebar/widgets/newsletter'
);

get_template_part(
'template-parts/sidebar/widgets/advertisement-300'
);

get_template_part(
'template-parts/sidebar/widgets/popular-tags'
);

get_template_part(
'template-parts/sidebar/widgets/advertisement-300'
);

?>
</div>

</aside>
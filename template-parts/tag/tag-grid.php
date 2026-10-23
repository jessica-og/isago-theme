<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = get_query_var(
    'tag_query'
);

if (!$query || !$query->have_posts()) {
    return;
}

?>

<div
class="category-post-grid tag-post-grid"
style="--isago-category-columns:<?php echo absint($settings['columns']); ?>;">


    <?php

    while ($query->have_posts()) :

        $query->the_post();

  
if (get_post_type() === 'ai_prompt') {

    get_template_part(
        'template-parts/ai-prompts/components/card',
        null,
        array(
            'prompt' => get_post()
        )
    );

} else {

    get_template_part(
        'template-parts/category/cards/post-card'
    );

}

    endwhile;

    wp_reset_postdata();

    ?>

</div>

<section class="sidebar-widget">

    <h3 class="isago-section-title">
        POPULAR TAGS
    </h3>

    <div class="popular-tags cat-margin">

        <?php
        $settings = isago_get_sidebar_settings();

        $limit = absint(
            $settings['popular_tags_count'] ?? 8
        );

        $tags = function_exists('isago_get_contextual_popular_tags')
            ? isago_get_contextual_popular_tags($limit)
            : [];

        if (!empty($tags)) :

            foreach ($tags as $tag) :
        ?>

                <a href="<?php echo esc_url(get_tag_link($tag)); ?>">
                    <?php echo esc_html($tag->name); ?>
                </a>

        <?php
            endforeach;

        endif;
        ?>

    </div>

</section>

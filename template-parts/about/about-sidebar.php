<?php if (!defined('ABSPATH')) exit; ?>

<?php $settings = isago_get_theme_settings(); ?>

<div class="about-card">

    <h3><?php echo esc_html($settings['about_sidebar_title']); ?></h3>

    <p>
        <?php echo esc_html($settings['about_sidebar_desc']); ?>
    </p>

    <ul class="about-meta">
        <li><span>Founded</span><strong><?php echo esc_html($settings['about_founded']); ?></strong></li>
        <li><span>Headquarters</span><strong><?php echo esc_html($settings['about_hq']); ?></strong></li>
        <li><span>Focus</span><strong><?php echo esc_html($settings['about_focus']); ?></strong></li>
    </ul>

</div>

    <!-- NEWSLETTER -->
    <?php get_template_part('template-parts/sidebar/widgets/newsletter'); ?>

    <!-- POPULAR CATEGORIES -->
    <div class="about-card">

        <h3>Popular Categories</h3>

        <ul class="about-categories">

            <?php
            $cats = get_categories([
                'orderby' => 'count',
                'order'   => 'DESC',
                'number'  => 6
            ]);

            foreach ($cats as $cat) : ?>

                <li>
                    <a href="<?php echo get_category_link($cat->term_id); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </a>
                    <span><?php echo $cat->count; ?></span>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

    <!-- AD -->
    <?php get_template_part('template-parts/sidebar/widgets/advertisement-300'); ?>

</div>
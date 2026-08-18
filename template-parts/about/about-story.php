<section class="about-story">
<?php $settings = isago_get_theme_settings(); ?>

<div class="about-story-grid">

    <div class="about-story-image">
        <img src="<?php echo esc_url($settings['about_story_image'] ?: ISAGO_URI . '/assets/images/placeholder.png'); ?>" alt="">
    </div>

    <div class="about-story-content">

        <h2><?php echo esc_html($settings['about_story_title'] ?: 'Our Story'); ?></h2>

        <p>
            <?php echo nl2br(esc_html($settings['about_story_text'])); ?>
        </p>

    </div>

</div>

</section>
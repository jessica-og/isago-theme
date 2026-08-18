<?php
$settings = isago_get_theme_settings();
?>

<section class="about-hero">

    <div class="about-hero-left">

      <h1><?php echo esc_html($settings['about_title'] ?? 'About Isago'); ?></h1>

<p class="about-lead">
<?php echo esc_html($settings['about_desc'] ?? ''); ?>
</p>

       <div class="about-features">

    <div class="about-feature">
        <div class="icon"><i class="fas fa-lightbulb"></i></div>
        <h4>Practical Knowledge</h4>
        <p>Actionable guides and prompts.</p>
    </div>

    <div class="about-feature">
        <div class="icon"><i class="fas fa-layer-group"></i></div>
        <h4>Curated Resources</h4>
        <p>We review the best AI tools.</p>
    </div>

    <div class="about-feature">
        <div class="icon"><i class="fas fa-users"></i></div>
        <h4>Creator Focused</h4>
        <p>Built for creators.</p>
    </div>

</div>

    </div>

    <div class="about-hero-right">

       <img src="<?php echo esc_url($settings['about_hero_image'] ?: ISAGO_URI . '/assets/images/placeholder.png'); ?>" alt="">
    </div>

</section>
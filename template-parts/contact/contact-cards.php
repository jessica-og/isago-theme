<?php 
$settings = isago_get_theme_settings();
$social   = isago_get_option('isago_social_settings');
?>

<section class="contact-cards">

<div class="contact-card">
    <div class="card-icon"><i class="fas fa-envelope"></i></div>
    <div class="card-content">
        <h4>Email Us</h4>
        <p><?php echo esc_html($settings['contact_email']); ?></p>
        <span>We’ll reply as soon as possible</span>
    </div>
</div>

<div class="contact-card">
    <div class="card-icon"><i class="fas fa-phone"></i></div>
    <div class="card-content">
        <h4>Call Us</h4>
        <p><?php echo esc_html($settings['contact_phone']); ?></p>
        <span>Mon – Fri, 9AM – 6PM</span>
    </div>
</div>

<div class="contact-card">
    <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
    <div class="card-content">
        <h4>Our Office</h4>
        <p><?php echo esc_html($settings['contact_address']); ?></p>
    </div>
</div>

<div class="contact-card">
    <div class="card-icon"><i class="fas fa-globe"></i></div>
    <div class="card-content">
        <h4>Follow Us</h4>

        <div class="contact-social">
            <?php if(!empty($social['facebook'])): ?>
                <a href="<?php echo esc_url($social['facebook']); ?>"><i class="fab fa-facebook-f"></i></a>
            <?php endif; ?>

            <?php if(!empty($social['x'])): ?>
                <a href="<?php echo esc_url($social['x']); ?>"><i class="fab fa-x-twitter"></i></a>
            <?php endif; ?>

            <?php if(!empty($social['instagram'])): ?>
                <a href="<?php echo esc_url($social['instagram']); ?>"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>

            <?php if(!empty($social['linkedin'])): ?>
                <a href="<?php echo esc_url($social['linkedin']); ?>"><i class="fab fa-linkedin-in"></i></a>
            <?php endif; ?>

            <?php if(!empty($social['youtube'])): ?>
                <a href="<?php echo esc_url($social['youtube']); ?>"><i class="fab fa-youtube"></i></a>
            <?php endif; ?>
        </div>

    </div>
</div>

</section>
<section class="contact-section">

<div class="contact-grid">

<!-- FORM -->
<div class="contact-box" id="contact-form">

<h3>Send Us a Message</h3>

<?php if(isset($_GET['success'])): ?>
<div class="form-success">
Message sent successfully 🎉
</div>
<?php endif; ?>

<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

<input type="hidden" name="action" value="isago_contact">
<?php wp_nonce_field('isago_contact','isago_nonce'); ?>

<div class="form-row">
<input type="text" name="name" placeholder="Your Name *" required>
<input type="email" name="email" placeholder="Your Email *" required>
</div>

<input type="text" name="subject" placeholder="Subject *" required>

<textarea name="message" placeholder="Your Message *" required></textarea>

<label class="checkbox">
<input type="checkbox" required>
<span>I agree to the privacy policy</span>
</label>

<button type="submit">
Send Message ✈
</button>

</form>

</div>

<!-- MAP -->
<div class="contact-box" >

<h3>Our Location</h3>

<div class="map-embed">
<iframe 
src="https://www.google.com/maps?q=lagos&output=embed"
width="100%" 
height="260" 
style="border:0;" 
loading="lazy">
</iframe>
</div>

<div class="location-meta">

<div>
<strong>Isago HQ</strong>
<p>Worldwide</p>
</div>

<div>
<strong>Hours</strong>
<p>Mon – Fri: 9AM – 6PM</p>
</div>

</div>

</div>

</div>

</section>
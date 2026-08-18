<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="footer-brand">

    <a
        href="<?php echo esc_url(home_url('/')); ?>"
        class="footer-logo">

        <?php

        if (has_custom_logo()) {

            the_custom_logo();

        } else {

            ?>

            <span>ISAGO.</span>

            <?php

        }

        ?>

    </a>

   <?php

$theme = isago_get_theme_settings();

$social = isago_get_social_settings();

?>

<p class="footer-description">

<?php

echo esc_html(

$theme['footer_description']

);

?>

</p>
   <?php

if (!empty($social['footer'])) :

?>

<div class="footer-socials">
<?php

$icons = [

'facebook'=>'fa-facebook-f',

'x'=>'fa-x-twitter',

'instagram'=>'fa-instagram',

'linkedin'=>'fa-linkedin-in',

'youtube'=>'fa-youtube',

'tiktok'=>'fa-tiktok',

'pinterest'=>'fa-pinterest',

'threads'=>'fa-threads'

];

foreach($icons as $network=>$icon):

if(empty($social[$network])){

continue;

}

?>

<a

href="<?php echo esc_url($social[$network]); ?>"

<?php

if(!empty($social['blank'])){

echo 'target="_blank" rel="noopener"';

}

?>

>

<i class="fa-brands <?php echo esc_attr($icon); ?>"></i>

</a>

<?php endforeach; ?>

<?php

if(!empty($social['rss'])):

?>

<a

href="<?php echo esc_url(home_url('/feed')); ?>">

<i class="fa-solid fa-rss"></i>

</a>

<?php endif; ?>

</div>

<?php endif; ?>

    </div>

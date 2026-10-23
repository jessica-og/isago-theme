<?php

if (!defined('ABSPATH')) {
    exit;
}
$theme = isago_get_theme_settings();

?>

<div class="footer-bottom">

    <p>

       <?php

echo esc_html(

str_replace(

'{{year}}',

date('Y'),

$theme['copyright']

)

);

?>

    </p>

    <nav>
<?php

wp_nav_menu([

'theme_location'=>'footer_bottom',

'container'=>false,

'menu_class'=>'footer-bottom-menu',

'fallback_cb'=>false

]);

?>




               

    </nav>
<button
    type="button"
    class="isago-cookie-settings-link"
    onclick="if(window.isagoCookie){window.isagoCookie.openPreferences();}">

    Cookie Settings

</button>
</div>
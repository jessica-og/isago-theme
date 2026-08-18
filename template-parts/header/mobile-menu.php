<?php

if (!defined('ABSPATH')) {
    exit;
}

$menu = isago_get_menu();

?>

<div class="isago-mobile-overlay"></div>

<aside class="isago-mobile-menu">

<button
class="isago-mobile-close">

<i class="fa-solid fa-xmark"></i>

</button>

<ul>

<?php

foreach($menu as $item){

include locate_template(

'template-parts/header/mobile-menu-item.php'

);

}

?>

</ul>

<?php

get_template_part(

'template-parts/header/social-icons'

);

?>

</aside>
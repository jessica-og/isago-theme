<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'admin_menu',
    'isago_performance_menu'
);

add_action(
    'admin_init',
    'isago_register_performance'
);

function isago_register_performance()
{

    register_setting(

        'isago_performance_options',

        'isago_performance_settings'

    );

}

function isago_performance_menu()
{

    add_submenu_page(

        'isago-home',

        'Performance',

        'Performance',

        'manage_options',

        'isago-performance',

        'isago_performance_page'

    );

}

function isago_performance_page()
{

$options=isago_get_option(
'isago_performance_settings'
);

?>

<div class="wrap isago-admin">

<h1>

Performance

</h1>

<form

method="post"

action="options.php">

<?php settings_fields(
'isago_performance_options'
); ?>

<table class="form-table">

<tbody>

<tr>

<th colspan="2">

<hr>

<h2>

Optimization

</h2>

</th>

</tr>

<?php

$items=[

'lazyload'=>'Lazy Load Images',

'lazy_iframe'=>'Lazy Load Iframes',

'disable_emojis'=>'Disable Emojis',

'disable_embeds'=>'Disable Embeds',

'defer_scripts'=>'Defer Theme JavaScript',

'preload_fonts'=>'Preload Fonts',

'webp'=>'Prefer WebP Images'

];

foreach($items as $key=>$label):

?>

<tr>

<th>

<?php echo esc_html($label);?>

</th>

<td>

<label>

<input

type="checkbox"

value="1"

name="isago_performance_settings[<?php echo $key;?>]"

<?php checked(

$options[$key]??1,

1

);?>

>

Enable

</label>

</td>

</tr>

<?php endforeach;?>


</tbody>

</table>

<?php submit_button();?>

</form>

</div>

<?php

}





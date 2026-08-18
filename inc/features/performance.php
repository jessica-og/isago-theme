<?php

if (!defined('ABSPATH')) {
    exit;
}

/*--------------------------------------------------------------
Performance Helper
--------------------------------------------------------------*/

function isago_get_performance_settings()
{

    return isago_get_option(
        'isago_performance_settings'
    );

}

/*--------------------------------------------------------------
Disable Emojis
--------------------------------------------------------------*/

add_action(
    'init',
    'isago_disable_emojis'
);

function isago_disable_emojis()
{

$settings=isago_get_performance_settings();

if(empty($settings['disable_emojis'])){

return;

}

remove_action(
'wp_head',
'print_emoji_detection_script',
7
);

remove_action(
'admin_print_scripts',
'print_emoji_detection_script'
);

remove_action(
'wp_print_styles',
'print_emoji_styles'
);

remove_action(
'admin_print_styles',
'print_emoji_styles'
);

}

/*--------------------------------------------------------------
Disable Embeds
--------------------------------------------------------------*/

add_action(
'init',
'isago_disable_embeds'
);

function isago_disable_embeds()
{

$settings=isago_get_performance_settings();

if(empty($settings['disable_embeds'])){

return;

}

wp_deregister_script(
'wp-embed'
);

}

/*--------------------------------------------------------------
Lazy Loading
--------------------------------------------------------------*/

add_filter(
'wp_get_attachment_image_attributes',
'isago_lazyload_images'
);

function isago_lazyload_images($attr)
{

$settings=isago_get_performance_settings();

if(empty($settings['lazyload'])){

return $attr;

}

$attr['loading']='lazy';

$attr['decoding']='async';

return $attr;

}

/*--------------------------------------------------------------
Defer Theme Scripts
--------------------------------------------------------------*/

add_filter(
'script_loader_tag',
'isago_defer_scripts',
10,
3
);

function isago_defer_scripts(
$tag,
$handle,
$src
){

$settings=isago_get_performance_settings();

if(empty($settings['defer_scripts'])){

return $tag;

}

$excluded=[

'jquery',

'jquery-core',

'jquery-migrate'

];

if(in_array($handle,$excluded,true)){

return $tag;

}

return str_replace(
'<script ',
'<script defer ',
$tag
);

}
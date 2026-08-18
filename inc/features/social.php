<?php

if (!defined('ABSPATH')) {
    exit;
}

/*--------------------------------------------------------------
Social Helper
--------------------------------------------------------------*/

function isago_get_social_settings()
{

    return isago_get_option(
        'isago_social_settings'
    );

}






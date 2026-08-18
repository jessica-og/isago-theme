<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Admin Styles
|--------------------------------------------------------------------------
*/

add_action(

    'admin_head',

    function () {

?>

<style>

.post-type-isago_video
.column-thumbnail{

width:95px;

}

.post-type-isago_video
.column-thumbnail img{

width:80px;

height:45px;

object-fit:cover;

border-radius:4px;

}

</style>

<?php

    }

);
<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Register Meta Box
|--------------------------------------------------------------------------
*/

add_action(

    'add_meta_boxes',

    function () {

        add_meta_box(

            'isago-video-settings',

            'Video Settings',

            'isago_video_meta_callback',

            'isago_video',

            'normal',

            'high'

        );

    }

);

/*
|--------------------------------------------------------------------------
| Meta Box
|--------------------------------------------------------------------------
*/

function isago_video_meta_callback($post)
{

    wp_nonce_field(

        'isago_video_meta',

        'isago_video_meta_nonce'

    );

    $url = get_post_meta(

        $post->ID,

        '_isago_video_url',

        true

    );

?>

<table class="form-table">

<tr>

<th>

Video URL

</th>

<td>

<input

type="url"

class="widefat"

name="isago_video_url"

value="<?php echo esc_attr($url); ?>"

placeholder="https://youtube.com/watch?v=..."

>

<p>

Supports YouTube, Vimeo and MP4.

</p>

</td>

</tr>

</table>

<?php

}

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

add_action(

    'save_post_isago_video',

    function ($post_id) {

        if (

            !isset($_POST['isago_video_meta_nonce'])

        ) {

            return;

        }

        if (

            !wp_verify_nonce(

                $_POST['isago_video_meta_nonce'],

                'isago_video_meta'

            )

        ) {

            return;

        }

        if (

            defined('DOING_AUTOSAVE')

            && DOING_AUTOSAVE

        ) {

            return;

        }

        update_post_meta(

            $post_id,

            '_isago_video_url',

            esc_url_raw(

                $_POST['isago_video_url']

            )

        );

        

    }

);





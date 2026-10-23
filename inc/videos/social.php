  <?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Register Social Video oEmbed Providers
|--------------------------------------------------------------------------
|
| WordPress already supports:
|
| - YouTube
| - Vimeo
| - TikTok
| - Pinterest
|
| Instagram is registered here using Meta's current
| Instagram oEmbed endpoint.
|
|--------------------------------------------------------------------------
*/

add_filter(
    'oembed_providers',
    'isago_register_social_video_oembed_providers'
);

function isago_register_social_video_oembed_providers(
    $providers
) {

    /*
    |--------------------------------------------------------------------------
    | Instagram Posts
    |--------------------------------------------------------------------------
    */

    $providers[
        '#https?://(www\.)?instagram\.com/(p|reel|tv)/[^/?#]+/?#i'
    ] = [
        'https://graph.facebook.com/v25.0/instagram_oembed',
        true
    ];


    /*
    |--------------------------------------------------------------------------
    | Instagram Short Domain
    |--------------------------------------------------------------------------
    */

    $providers[
        '#https?://instagr\.am/(p|reel|tv)/[^/?#]+/?#i'
    ] = [
        'https://graph.facebook.com/v25.0/instagram_oembed',
        true
    ];


    /*
    |--------------------------------------------------------------------------
    | Facebook Posts
    |--------------------------------------------------------------------------
    */

    $providers[
        '#https?://(www\.)?facebook\.com/[^/?#]+/posts/[^/?#]+/?#i'
    ] = [
        'https://graph.facebook.com/v25.0/oembed_post',
        true
    ];


    /*
    |--------------------------------------------------------------------------
    | Facebook Reels
    |--------------------------------------------------------------------------
    */

    $providers[
        '#https?://(www\.)?facebook\.com/reel/[^/?#]+/?#i'
    ] = [
        'https://graph.facebook.com/v25.0/oembed_video',
        true
    ];


    return $providers;
}


/*
|--------------------------------------------------------------------------
| Detect Social Video Provider
|--------------------------------------------------------------------------
*/
function isago_get_social_video_provider(
    $url
) {

    $url =
        trim(
            $url
        );


    if (
        empty($url)
    ) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | HOST
    |--------------------------------------------------------------------------
    */

    $host =
        wp_parse_url(
            $url,
            PHP_URL_HOST
        );


    /*
    |--------------------------------------------------------------------------
    | PATH
    |--------------------------------------------------------------------------
    */

    $path =
        wp_parse_url(
            $url,
            PHP_URL_PATH
        );


    if (
        !$host
    ) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE HOST
    |--------------------------------------------------------------------------
    */

    $host =
        strtolower(
            preg_replace(
                '/^www\./',
                '',
                $host
            )
        );


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE PATH
    |--------------------------------------------------------------------------
    */

    $path =
        strtolower(
            (string) $path
        );


    /*
    |--------------------------------------------------------------------------
    | YOUTUBE
    |--------------------------------------------------------------------------
    */

    if (
        $host === 'youtube.com'
        ||
        $host === 'm.youtube.com'
        ||
        $host === 'youtu.be'
    ) {

        return 'youtube';

    }


    /*
    |--------------------------------------------------------------------------
    | INSTAGRAM
    |--------------------------------------------------------------------------
    */

    if (
        (
            $host === 'instagram.com'
            ||
            $host === 'instagr.am'
        )
        &&
        preg_match(
            '#/(p|reel|tv)/#i',
            $path
        )
    ) {

        return 'instagram';

    }


    /*
    |--------------------------------------------------------------------------
    | PINTEREST
    |--------------------------------------------------------------------------
    */

    if (
        strpos(
            $host,
            'pinterest.'
        ) === 0
        &&
        preg_match(
            '#/pin/[^/]+#i',
            $path
        )
    ) {

        return 'pinterest';

    }


    /*
    |--------------------------------------------------------------------------
    | TIKTOK
    |--------------------------------------------------------------------------
    */

    if (
        $host === 'tiktok.com'
        &&
        (
            preg_match(
                '#/video/[0-9]+#i',
                $path
            )
            ||
            preg_match(
                '#/player/v1/[0-9]+#i',
                $path
            )
            ||
            preg_match(
                '#/embed/v2/[0-9]+#i',
                $path
            )
        )
    ) {

        return 'tiktok';

    }


    /*
    |--------------------------------------------------------------------------
    | VIMEO
    |--------------------------------------------------------------------------
    */

    if (
        $host === 'vimeo.com'
        ||
        preg_match(
            '#\.vimeo\.com$#i',
            $host
        )
    ) {

        return 'vimeo';

    }


    /*
    |--------------------------------------------------------------------------
    | FACEBOOK
    |--------------------------------------------------------------------------
    */

    if (
        $host === 'facebook.com'
        &&
        (
            strpos(
                $path,
                '/reel/'
            ) !== false
            ||
            strpos(
                $path,
                '/posts/'
            ) !== false
        )
    ) {

        return 'facebook';

    }


    return '';
}
/*
|--------------------------------------------------------------------------
| Get Social Video Embed
|--------------------------------------------------------------------------
*/

function isago_get_social_video_embed(
    $url,
    $width = 900
) {

    $url = esc_url_raw(
        trim($url)
    );


    if (empty($url)) {
        return '';
    }


    $provider =
        isago_get_social_video_provider(
            $url
        );


    if (empty($provider)) {
        return '';
    }


    /*
    |--------------------------------------------------------------------------
    | Load provider scripts
    |--------------------------------------------------------------------------
    */

    if (
        $provider === 'instagram'
        ||
        $provider === 'facebook'
    ) {

        wp_enqueue_script(
            'isago-meta-embed',
            'https://www.instagram.com/embed.js',
            [],
            null,
            true
        );

    }


    if ($provider === 'pinterest') {

        wp_enqueue_script(
            'isago-pinterest-embed',
            'https://assets.pinterest.com/js/pinit.js',
            [],
            null,
            true
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Get oEmbed HTML
    |--------------------------------------------------------------------------
    */

    $embed =
        wp_oembed_get(
            $url,
            [
                'width' =>
                    absint($width)
            ]
        );


    if (!$embed) {
        return '';
    }


    return $embed;
}


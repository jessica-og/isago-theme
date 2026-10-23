<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Video URL
|--------------------------------------------------------------------------
|
| Supports:
|
| - YouTube
| - Instagram
| - Pinterest
| - TikTok
| - Vimeo
| - Other supported oEmbed video providers
|
|--------------------------------------------------------------------------
*/

function isago_get_video_url(
    $post_id = null
) {

    $post_id =
        $post_id ?: get_the_ID();

    $post_id =
        absint($post_id);

    if (!$post_id) {
        return '';
    }


    /*
    |--------------------------------------------------------------------------
    | Image + Social Video
    |--------------------------------------------------------------------------
    */

    $image_video_source_url =
        get_post_meta(
            $post_id,
            '_isago_image_video_source_url',
            true
        );


    /*
     * Legacy fallback.
     */

    if (
        empty(
            $image_video_source_url
        )
    ) {

        $image_video_source_url =
            get_post_meta(
                $post_id,
                '_isago_image_video_url',
                true
            );
    }


    if (
        !empty(
            $image_video_source_url
        )
    ) {

        return esc_url(
            $image_video_source_url
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normal Video / Social URL
    |--------------------------------------------------------------------------
    */

    $video_source_url =
        get_post_meta(
            $post_id,
            '_isago_video_source_url',
            true
        );


    /*
     * Legacy YouTube fallback.
     */

    if (
        empty(
            $video_source_url
        )
    ) {

        $video_source_url =
            get_post_meta(
                $post_id,
                '_isago_youtube_url',
                true
            );
    }


    if (
        !empty(
            $video_source_url
        )
    ) {

        return esc_url(
            $video_source_url
        );
    }


    return '';
}




/*
|--------------------------------------------------------------------------
| Get Video Thumbnail
|--------------------------------------------------------------------------
*/

function isago_get_video_thumbnail(
    $post_id = null
) {

    $post_id =
        $post_id ?: get_the_ID();


    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | 1. IMAGEKIT IMAGE
    |--------------------------------------------------------------------------
    */

    $image_id =
        isago_media_get_field(
            'post',
            $post_id,
            'image'
        );


    if ($image_id) {

        $image_url =
            isago_media_get_field_image_url(
                'post',
                $post_id,
                'image',
                'large'
            );


        if ($image_url) {

            return sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                >',

                esc_url(
                    $image_url
                ),

                esc_attr(
                    get_the_title(
                        $post_id
                    )
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 2. WORDPRESS FEATURED IMAGE
    |--------------------------------------------------------------------------
    */

    if (
        has_post_thumbnail(
            $post_id
        )
    ) {

        return get_the_post_thumbnail(
            $post_id,
            'large'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | 3. VIDEO / SOCIAL URL
    |--------------------------------------------------------------------------
    */

    $video_url =
        isago_get_video_url(
            $post_id
        );


    if (!empty($video_url)) {

        $social_thumbnail_url =
            isago_get_social_video_thumbnail_url(
                $video_url
            );


        if (!empty($social_thumbnail_url)) {

            return sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    onerror="this.onerror=null;this.src=\'%s\';"
                >',

                esc_url(
                    $social_thumbnail_url
                ),

                esc_attr(
                    get_the_title(
                        $post_id
                    )
                ),

                esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. PLACEHOLDER
    |--------------------------------------------------------------------------
    */

    return sprintf(

        '<img
            src="%s"
            alt="%s"
            loading="lazy"
        >',

        esc_url(
            get_template_directory_uri()
            . '/assets/images/placeholder.png'
        ),

        esc_attr(
            get_the_title(
                $post_id
            )
        )

    );
}




/*
|--------------------------------------------------------------------------
| Get YouTube Video ID
|--------------------------------------------------------------------------
*/

function isago_get_youtube_id($url)
{
    if (empty($url)) {

        return '';

    }


    preg_match(

        '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([^?&]+)/',

        $url,

        $matches

    );


    return $matches[1] ?? '';
}



/*
|--------------------------------------------------------------------------
| Get Social Video Thumbnail URL
|--------------------------------------------------------------------------
|
| Attempts to retrieve the real thumbnail for a social/video URL.
|
| Priority:
|
| 1. YouTube
| 2. WordPress oEmbed
| 3. Provider-specific oEmbed endpoints
| 4. Open Graph image
| 5. Twitter image
|
|--------------------------------------------------------------------------
*/

function isago_get_social_video_thumbnail_url(
    $url
) {

    if (empty($url)) {
        return '';
    }


    $url =
        esc_url_raw(
            trim($url)
        );


    if (empty($url)) {
        return '';
    }


    /*
    |--------------------------------------------------------------------------
    | CACHE
    |--------------------------------------------------------------------------
    |
    | Use a new cache key so old failed results from the previous
    | implementation do not remain stuck for 12 hours.
    |
    */

    $cache_key =
        'isago_social_thumb_v2_' .
        md5($url);


    $cached =
        get_transient(
            $cache_key
        );


    if ($cached !== false) {

        return is_string($cached)
            ? $cached
            : '';

    }


    $thumbnail_url = '';


    /*
    |--------------------------------------------------------------------------
    | 1. YOUTUBE
    |--------------------------------------------------------------------------
    */

    $youtube_id =
        isago_get_youtube_id(
            $url
        );


    if (!empty($youtube_id)) {

        $thumbnail_url =
            sprintf(
                'https://img.youtube.com/vi/%s/hqdefault.jpg',
                rawurlencode(
                    $youtube_id
                )
            );

    }


    /*
    |--------------------------------------------------------------------------
    | 2. WORDPRESS oEMBED
    |--------------------------------------------------------------------------
    */

    if (
        empty($thumbnail_url)
        &&
        function_exists(
            'wp_oembed_get'
        )
    ) {

        $oembed_object =
            function_exists(
                '_wp_oembed_get_object'
            )
                ? _wp_oembed_get_object()
                : null;


        if ($oembed_object) {

            $data =
                $oembed_object->get_data(
                    $url,
                    array(
                        'discover' => true,
                    )
                );


            if (
                $data
                &&
                !empty(
                    $data->thumbnail_url
                )
            ) {

                $thumbnail_url =
                    esc_url_raw(
                        $data->thumbnail_url
                    );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. PROVIDER-SPECIFIC oEMBED
    |--------------------------------------------------------------------------
    |
    | Some providers expose their own oEmbed endpoint even when
    | WordPress does not return the thumbnail through its normal
    | provider discovery.
    |
    */

    if (empty($thumbnail_url)) {

        $provider_oembed_url = '';


        /*
        |--------------------------------------------------------------------------
        | TikTok
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '#(?:^|://)(?:www\.)?tiktok\.com/#i',
                $url
            )
        ) {

            $provider_oembed_url =
                add_query_arg(
                    'url',
                    rawurlencode($url),
                    'https://www.tiktok.com/oembed'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Vimeo
        |--------------------------------------------------------------------------
        */

        elseif (
            preg_match(
                '#(?:^|://)(?:www\.)?vimeo\.com/#i',
                $url
            )
        ) {

            $provider_oembed_url =
                add_query_arg(
                    'url',
                    rawurlencode($url),
                    'https://vimeo.com/api/oembed.json'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Pinterest
        |--------------------------------------------------------------------------
        */

        elseif (
            preg_match(
                '#(?:^|://)(?:www\.)?pinterest\.[^/]+/#i',
                $url
            )
        ) {

            $provider_oembed_url =
                add_query_arg(
                    'url',
                    rawurlencode($url),
                    'https://www.pinterest.com/oembed.json'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Fetch Provider oEmbed
        |--------------------------------------------------------------------------
        */

        if (!empty($provider_oembed_url)) {

            $response =
                wp_safe_remote_get(
                    $provider_oembed_url,
                    array(
                        'timeout' =>
                            8,

                        'redirection' =>
                            3,

                        'headers' =>
                            array(
                                'Accept' =>
                                    'application/json',
                            ),
                    )
                );


            if (
                !is_wp_error(
                    $response
                )
            ) {

                $status_code =
                    wp_remote_retrieve_response_code(
                        $response
                    );


                if (
                    $status_code >= 200
                    &&
                    $status_code < 400
                ) {

                    $body =
                        wp_remote_retrieve_body(
                            $response
                        );


                    $data =
                        json_decode(
                            $body
                        );


                    if (
                        $data
                        &&
                        !empty(
                            $data->thumbnail_url
                        )
                    ) {

                        $thumbnail_url =
                            esc_url_raw(
                                $data->thumbnail_url
                            );

                    }

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. OPEN GRAPH / TWITTER IMAGE
    |--------------------------------------------------------------------------
    */

    if (empty($thumbnail_url)) {

        $response =
            wp_safe_remote_get(
                $url,
                array(
                    'timeout' =>
                        8,

                    'redirection' =>
                        3,

                    'headers' =>
                        array(
                            'User-Agent' =>
                                'Mozilla/5.0 ' .
                                '(compatible; IsagoBot/1.0; +' .
                                home_url('/') .
                                ')',
                        ),
                )
            );


        if (
            !is_wp_error(
                $response
            )
        ) {

            $status_code =
                wp_remote_retrieve_response_code(
                    $response
                );


            if (
                $status_code >= 200
                &&
                $status_code < 400
            ) {

                $body =
                    wp_remote_retrieve_body(
                        $response
                    );


                if (!empty($body)) {

                    /*
                    |--------------------------------------------------------------------------
                    | OG IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        preg_match(
                            '/<meta[^>]+property=[\'"]og:image(?::secure_url)?[\'"][^>]+content=[\'"]([^\'"]+)[\'"][^>]*>/i',
                            $body,
                            $matches
                        )
                    ) {

                        $thumbnail_url =
                            html_entity_decode(
                                $matches[1],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TWITTER IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        empty(
                            $thumbnail_url
                        )
                        &&
                        preg_match(
                            '/<meta[^>]+name=[\'"]twitter:image(?:src)?[\'"][^>]+content=[\'"]([^\'"]+)[\'"][^>]*>/i',
                            $body,
                            $matches
                        )
                    ) {

                        $thumbnail_url =
                            html_entity_decode(
                                $matches[1],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                    }


                    if (!empty($thumbnail_url)) {

                        $thumbnail_url =
                            esc_url_raw(
                                $thumbnail_url
                            );

                    }

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CACHE ONLY A SUCCESSFUL RESULT
    |--------------------------------------------------------------------------
    |
    | Do not cache failures for 12 hours. This is important while
    | provider thumbnails are being discovered.
    |
    */

    if (!empty($thumbnail_url)) {

        set_transient(
            $cache_key,
            $thumbnail_url,
            12 * HOUR_IN_SECONDS
        );

    }


    return $thumbnail_url;
}



/*
|--------------------------------------------------------------------------
| Get Image + YouTube Image
|--------------------------------------------------------------------------
|
| Returns the uploaded image used by the Image + YouTube mode.
|
| Stored as:
|
| _isago_image_id
|
|--------------------------------------------------------------------------
*/

function isago_get_image_video_image(
    $post_id = null
) {

    $post_id =
        $post_id ?: get_the_ID();


    $post_id = absint($post_id);


    if (!$post_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | Attachment ID
    |--------------------------------------------------------------------------
    */

    $image_id = absint(
        get_post_meta(
            $post_id,
            '_isago_image_id',
            true
        )
    );


    if (!$image_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | Attachment URL
    |--------------------------------------------------------------------------
    */

    $image_url =
        wp_get_attachment_image_url(
            $image_id,
            'full'
        );


    if ($image_url) {

        return esc_url($image_url);

    }


    return '';
}


/*
|--------------------------------------------------------------------------
| Get Image + YouTube URL
|--------------------------------------------------------------------------
*/

function isago_get_image_video_url(
    $post_id = null
) {

    $post_id =
        $post_id ?: get_the_ID();

    $post_id =
        absint($post_id);

    if (!$post_id) {
        return '';
    }


    $url =
        get_post_meta(
            $post_id,
            '_isago_image_video_source_url',
            true
        );


    /*
     * Legacy fallback.
     */

    if (empty($url)) {

        $url =
            get_post_meta(
                $post_id,
                '_isago_image_video_url',
                true
            );
    }


    return esc_url($url);
}

/*
|--------------------------------------------------------------------------
| Convert ISO8601 Duration
|--------------------------------------------------------------------------
*/

function isago_format_youtube_duration(
    $duration
) {

    if (empty($duration)) {

        return '';

    }


    try {

        $interval =
            new DateInterval($duration);

    } catch (Exception $e) {

        return '';

    }


    $hours =
        $interval->h;


    $minutes =
        $interval->i;


    $seconds =
        $interval->s;


    /*
    |--------------------------------------------------------------------------
    | Hours
    |--------------------------------------------------------------------------
    */

    if ($hours > 0) {

        return sprintf(
            '%d:%02d:%02d',
            $hours,
            $minutes,
            $seconds
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Minutes / Seconds
    |--------------------------------------------------------------------------
    */

    return sprintf(
        '%d:%02d',
        $minutes,
        $seconds
    );
}


/*
|--------------------------------------------------------------------------
| Video View Counter
|--------------------------------------------------------------------------
*/

function isago_track_video_view()
{

    if (
        !is_singular(
            'isago_video'
        )
    ) {

        return;

    }


    $video_id =
        get_queried_object_id();


    if (!$video_id) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Prevent repeated counting
    |--------------------------------------------------------------------------
    */

    $cookie_name =
        'isago_video_viewed_' . $video_id;


    if (
        isset(
            $_COOKIE[$cookie_name]
        )
    ) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Current views
    |--------------------------------------------------------------------------
    */

    $views =
        absint(
            get_post_meta(
                $video_id,
                '_isago_video_views',
                true
            )
        );


    $views++;


    update_post_meta(
        $video_id,
        '_isago_video_views',
        $views
    );


    /*
    |--------------------------------------------------------------------------
    | Remember browser
    |--------------------------------------------------------------------------
    */

    setcookie(
        $cookie_name,
        '1',
        time() + DAY_IN_SECONDS,
        COOKIEPATH,
        COOKIE_DOMAIN
    );

}


add_action(
    'wp',
    'isago_track_video_view'
);


/*
|--------------------------------------------------------------------------
| Uploaded Video Preview
|--------------------------------------------------------------------------
|
| Returns the <video> element used for video previews.
|
|--------------------------------------------------------------------------
*/

function isago_get_uploaded_video_preview(
    $post_id = null,
    $attributes = []
) {

    $post_id =
        $post_id ?: get_the_ID();


    $post_id = absint($post_id);


    if (!$post_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | Video Attachment
    |--------------------------------------------------------------------------
    */

    $video_file_id =
        absint(
            get_post_meta(
                $post_id,
                '_isago_video_file_id',
                true
            )
        );


    if (!$video_file_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | Video URL
    |--------------------------------------------------------------------------
    */

    $video_url =
        wp_get_attachment_url(
            $video_file_id
        );


    if (!$video_url) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | Poster
    |--------------------------------------------------------------------------
    */

    $poster = '';


    /*
    |--------------------------------------------------------------------------
    | 1. Custom Poster
    |--------------------------------------------------------------------------
    */

    $thumbnail_id =
        absint(
            get_post_meta(
                $post_id,
                '_isago_video_thumbnail_id',
                true
            )
        );


    if ($thumbnail_id) {

        $poster =
            wp_get_attachment_image_url(
                $thumbnail_id,
                'medium_large'
            );

    }


    /*
    |--------------------------------------------------------------------------
    | 2. Automatically Generated Video Thumbnail
    |--------------------------------------------------------------------------
    */

    if (empty($poster)) {

        $generated_thumbnail_id =
            get_post_thumbnail_id(
                $video_file_id
            );


        if ($generated_thumbnail_id) {

            $poster =
                wp_get_attachment_image_url(
                    $generated_thumbnail_id,
                    'medium_large'
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. Featured Image
    |--------------------------------------------------------------------------
    */

    if (
        empty($poster)
        &&
        has_post_thumbnail($post_id)
    ) {

        $poster =
            get_the_post_thumbnail_url(
                $post_id,
                'medium_large'
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Additional Attributes
    |--------------------------------------------------------------------------
    */

    $extra_attributes = '';


    if (
        !empty($attributes)
        &&
        is_array($attributes)
    ) {

        foreach (
            $attributes as $key => $value
        ) {

            if (
                !is_string($key)
                ||
                $value === ''
            ) {

                continue;

            }


            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate poster attribute
            |--------------------------------------------------------------------------
            */

            if ($key === 'poster') {

                continue;

            }


            $extra_attributes .= sprintf(
                ' %s="%s"',
                esc_attr($key),
                esc_attr($value)
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Poster Attribute
    |--------------------------------------------------------------------------
    */

    $poster_attribute = '';


    if (!empty($poster)) {

        $poster_attribute =
            sprintf(
                ' poster="%s"',
                esc_url($poster)
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Video Element
    |--------------------------------------------------------------------------
    */

    return sprintf(

        '<video
            class="isago-video-preview-thumbnail"
            src="%s"
            %s
            %s
        ></video>',

        esc_url($video_url),

        $poster_attribute,

        $extra_attributes

    );
}



/*
|--------------------------------------------------------------------------
| Universal Post Thumbnail
|--------------------------------------------------------------------------

|--------------------------------------------------------------------------
*/

function isago_get_post_thumbnail(
    $post_id = null,
    $size = 'medium_large',
    $attributes = []
) {

    $post_id =
        $post_id ?: get_the_ID();


    $post_id = absint($post_id);


    if (!$post_id) {

        return '';

    }





/*
|--------------------------------------------------------------------------
| AI PROMPT POST
|--------------------------------------------------------------------------
|
| AI Prompts use the central Isago Media system.
|
| Media fields:
|
|     post / ai_prompt_video
|     post / ai_prompt_image
|
| Video URL:
|
|     _isago_ai_prompt_video_source_url
|
| Legacy fallback:
|
|     _isago_ai_prompt_youtube_url
|
|--------------------------------------------------------------------------
*/

if (
    get_post_type($post_id)
    === 'ai_prompt'
) {

    /*
    |--------------------------------------------------------------------------
    | 1. IMAGEKIT AI PROMPT VIDEO
    |--------------------------------------------------------------------------
    */

    $prompt_video_id =
        isago_media_get_field(
            'post',
            $post_id,
            'ai_prompt_video'
        );


    if ($prompt_video_id) {

        $prompt_video_url =
            isago_media_get_field_url(
                'post',
                $post_id,
                'ai_prompt_video'
            );


        if ($prompt_video_url) {

            /*
            |--------------------------------------------------------------------------
            | VIDEO POSTER
            |--------------------------------------------------------------------------
            */

            $poster = '';


            /*
            |--------------------------------------------------------------------------
            | AI PROMPT IMAGE
            |--------------------------------------------------------------------------
            */

            $prompt_image_id =
                isago_media_get_field(
                    'post',
                    $post_id,
                    'ai_prompt_image'
                );


            if ($prompt_image_id) {

                $poster =
                    isago_media_get_field_image_url(
                        'post',
                        $post_id,
                        'ai_prompt_image',
                        $size
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | FEATURED IMAGE FALLBACK
            |--------------------------------------------------------------------------
            */

            if (
                empty($poster)
                &&
                has_post_thumbnail($post_id)
            ) {

                $poster =
                    get_the_post_thumbnail_url(
                        $post_id,
                        $size
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | POSTER ATTRIBUTE
            |--------------------------------------------------------------------------
            */

            $poster_attribute = '';


            if (!empty($poster)) {

                $poster_attribute =
                    sprintf(
                        ' poster="%s"',
                        esc_url($poster)
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | ADDITIONAL VIDEO ATTRIBUTES
            |--------------------------------------------------------------------------
            */

            $extra_attributes = '';


            if (
                is_array($attributes)
                &&
                !empty($attributes)
            ) {

                foreach (
                    $attributes as $key => $value
                ) {

                    if (
                        !is_string($key)
                        ||
                        $value === ''
                    ) {

                        continue;

                    }


                    if (
                        $key === 'poster'
                    ) {

                        continue;

                    }


                    $extra_attributes .= sprintf(
                        ' %s="%s"',
                        esc_attr($key),
                        esc_attr($value)
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | RETURN IMAGEKIT VIDEO
            |--------------------------------------------------------------------------
            */

            return sprintf(

                '<video
                    class="isago-video-preview-thumbnail"
                    muted
                    playsinline
                    preload="none"
                    data-video-src="%s"
                    %s
                    %s
                ></video>',

                esc_url(
                    $prompt_video_url
                ),

                $poster_attribute,

                $extra_attributes

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 2. AI PROMPT VIDEO / SOCIAL URL
    |--------------------------------------------------------------------------
    */

    $prompt_video_source_url =
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_video_source_url',
            true
        );


    /*
     * Legacy YouTube fallback.
     */

    if (
        empty(
            $prompt_video_source_url
        )
    ) {

        $prompt_video_source_url =
            get_post_meta(
                $post_id,
                '_isago_ai_prompt_youtube_url',
                true
            );

    }


    if (
        !empty(
            $prompt_video_source_url
        )
    ) {

        /*
        |--------------------------------------------------------------------------
        | YOUTUBE THUMBNAIL
        |--------------------------------------------------------------------------
        */

        $youtube_id =
            isago_get_youtube_id(
                $prompt_video_source_url
            );


        if (!empty($youtube_id)) {

            $image_attributes = '';


            if (
                is_array($attributes)
                &&
                !empty($attributes)
            ) {

                $allowed_attributes = [

                    'class',
                    'alt',
                    'loading',
                    'width',
                    'height',
                    'decoding',
                    'fetchpriority'

                ];


                foreach (
                    $attributes as $key => $value
                ) {

                    if (
                        !is_string($key)
                        ||
                        $value === ''
                    ) {

                        continue;

                    }


                    if (
                        !in_array(
                            $key,
                            $allowed_attributes,
                            true
                        )
                    ) {

                        continue;

                    }


                    $image_attributes .= sprintf(
                        ' %s="%s"',
                        esc_attr($key),
                        esc_attr($value)
                    );

                }

            }


            return sprintf(

                '<img
                    src="https://img.youtube.com/vi/%s/hqdefault.jpg"
                    %s
                >',

                esc_attr(
                    $youtube_id
                ),

                $image_attributes

            );

        }


   /*
|--------------------------------------------------------------------------
| OTHER SOCIAL PLATFORMS
|--------------------------------------------------------------------------
|
| Attempt to retrieve the actual remote thumbnail.
|
| If one is available, use it directly.
| If not, fall back to the Prompt Image.
|
|--------------------------------------------------------------------------
*/

$social_thumbnail_url =
    isago_get_social_video_thumbnail_url(
        $prompt_video_source_url
    );


if (!empty($social_thumbnail_url)) {

    $image_attributes = '';


    if (
        is_array($attributes)
        &&
        !empty($attributes)
    ) {

        $allowed_attributes = array(

            'class',
            'alt',
            'loading',
            'width',
            'height',
            'decoding',
            'fetchpriority'

        );


        foreach (
            $attributes as $key => $value
        ) {

            if (
                !is_string($key)
                ||
                $value === ''
            ) {

                continue;

            }


            if (
                !in_array(
                    $key,
                    $allowed_attributes,
                    true
                )
            ) {

                continue;

            }


            $image_attributes .= sprintf(

                ' %s="%s"',

                esc_attr($key),

                esc_attr($value)

            );

        }

    }


    return sprintf(

        '<img
            src="%s"
            %s
        >',

        esc_url(
            $social_thumbnail_url
        ),

        $image_attributes

    );

}


/*
|--------------------------------------------------------------------------
| Prompt Image Fallback
|--------------------------------------------------------------------------
*/

$prompt_image_id =
    isago_media_get_field(
        'post',
        $post_id,
        'ai_prompt_image'
    );


if ($prompt_image_id) {

    $prompt_image_url =
        isago_media_get_field_image_url(
            'post',
            $post_id,
            'ai_prompt_image',
            $size
        );


    if ($prompt_image_url) {

        $image_attributes = '';


        if (
            is_array($attributes)
            &&
            !empty($attributes)
        ) {

            $allowed_attributes = array(

                'class',
                'alt',
                'loading',
                'width',
                'height',
                'decoding',
                'fetchpriority'

            );


            foreach (
                $attributes as $key => $value
            ) {

                if (
                    !is_string($key)
                    ||
                    $value === ''
                ) {

                    continue;

                }


                if (
                    !in_array(
                        $key,
                        $allowed_attributes,
                        true
                    )
                ) {

                    continue;

                }


                $image_attributes .= sprintf(

                    ' %s="%s"',

                    esc_attr($key),

                    esc_attr($value)

                );

            }

        }


        return sprintf(

            '<img
                src="%s"
                %s
            >',

            esc_url(
                $prompt_image_url
            ),

            $image_attributes

        );

    }

}


/*
|--------------------------------------------------------------------------
| Social URL Without Thumbnail
|--------------------------------------------------------------------------
|
| Last-resort placeholder.
|
|--------------------------------------------------------------------------
*/

$fallback_attributes = '';


if (
    is_array($attributes)
    &&
    !empty($attributes)
) {

    foreach (
        $attributes as $key => $value
    ) {

        if (
            !is_string($key)
            ||
            $value === ''
        ) {

            continue;

        }


        if (
            !in_array(
                $key,
                array(

                    'class',
                    'alt',
                    'loading',
                    'width',
                    'height',
                    'decoding',
                    'fetchpriority'

                ),
                true
            )
        ) {

            continue;

        }


        $fallback_attributes .= sprintf(

            ' %s="%s"',

            esc_attr($key),

            esc_attr($value)

        );

    }

}


return sprintf(

    '<img
        src="%s"
        %s
    >',

    esc_url(
        get_template_directory_uri()
        . '/assets/images/placeholder.png'
    ),

    $fallback_attributes

);

        /*
        |--------------------------------------------------------------------------
        | SOCIAL VIDEO WITHOUT PROMPT IMAGE
        |--------------------------------------------------------------------------
        |
        | Use the site placeholder so the card still has
        | a visual area and the play button can appear.
        |
        */

        $fallback_attributes = '';


        if (
            is_array($attributes)
            &&
            !empty($attributes)
        ) {

            foreach (
                $attributes as $key => $value
            ) {

                if (
                    !is_string($key)
                    ||
                    $value === ''
                ) {

                    continue;

                }


                if (
                    !in_array(
                        $key,
                        [
                            'class',
                            'alt',
                            'loading',
                            'width',
                            'height',
                            'decoding',
                            'fetchpriority'
                        ],
                        true
                    )
                ) {

                    continue;

                }


                $fallback_attributes .= sprintf(
                    ' %s="%s"',
                    esc_attr($key),
                    esc_attr($value)
                );

            }

        }


        return sprintf(

            '<img
                src="%s"
                %s
            >',

            esc_url(
                get_template_directory_uri()
                . '/assets/images/placeholder.png'
            ),

            $fallback_attributes

        );

    }


    /*
    |--------------------------------------------------------------------------
    | 3. AI PROMPT IMAGE
    |--------------------------------------------------------------------------
    */

    $prompt_image_id =
        isago_media_get_field(
            'post',
            $post_id,
            'ai_prompt_image'
        );


    if ($prompt_image_id) {

        $prompt_image_url =
            isago_media_get_field_image_url(
                'post',
                $post_id,
                'ai_prompt_image',
                $size
            );


        if ($prompt_image_url) {

            $image_attributes = '';


            if (
                is_array($attributes)
                &&
                !empty($attributes)
            ) {

                $allowed_attributes = [

                    'class',
                    'alt',
                    'loading',
                    'width',
                    'height',
                    'decoding',
                    'fetchpriority'

                ];


                foreach (
                    $attributes as $key => $value
                ) {

                    if (
                        !is_string($key)
                        ||
                        $value === ''
                    ) {

                        continue;

                    }


                    if (
                        !in_array(
                            $key,
                            $allowed_attributes,
                            true
                        )
                    ) {

                        continue;

                    }


                    $image_attributes .= sprintf(
                        ' %s="%s"',
                        esc_attr($key),
                        esc_attr($value)
                    );

                }

            }


            return sprintf(

                '<img
                    src="%s"
                    %s
                >',

                esc_url(
                    $prompt_image_url
                ),

                $image_attributes

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. WORDPRESS FEATURED IMAGE
    |--------------------------------------------------------------------------
    */

    if (
        has_post_thumbnail(
            $post_id
        )
    ) {

        return get_the_post_thumbnail(
            $post_id,
            $size,
            $attributes
        );   

    }


    /*
    |--------------------------------------------------------------------------
    | NO AI PROMPT MEDIA
    |--------------------------------------------------------------------------
    */

    return '';

}




  
/*
|--------------------------------------------------------------------------
| ISAGO VIDEO POST
|--------------------------------------------------------------------------
*/

if (
    get_post_type($post_id)
    === 'isago_video'
) {

    /*
    |--------------------------------------------------------------------------
    | 1. IMAGEKIT IMAGE
    |--------------------------------------------------------------------------
    */

    $image_id =
        isago_media_get_field(
            'post',
            $post_id,
            'image'
        );


    if ($image_id) {

        $image_url =
            isago_media_get_field_image_url(
                'post',
                $post_id,
                'image',
                $size
            );


        if ($image_url) {

            $image_attributes = '';


            if (
                is_array($attributes)
                &&
                !empty($attributes)
            ) {

                $allowed_attributes = [

                    'class',
                    'alt',
                    'loading',
                    'width',
                    'height',
                    'decoding',
                    'fetchpriority'

                ];


                foreach (
                    $attributes as $key => $value
                ) {

                    if (
                        !is_string($key)
                        ||
                        $value === ''
                    ) {

                        continue;

                    }


                    if (  
                        !in_array(
                            $key,
                            $allowed_attributes,
                            true
                        )
                    ) {

                        continue;

                    }


                    $image_attributes .= sprintf(
                        ' %s="%s"',
                        esc_attr($key),
                        esc_attr($value)
                    );

                }

            }


            return sprintf(

                '<img
                    src="%s"
                    %s
                >',

                esc_url($image_url),

                $image_attributes

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 2. VIDEO / SOCIAL URL
    |--------------------------------------------------------------------------
    */

    $video_url =
        isago_get_video_url(
            $post_id
        );


    if (!empty($video_url)) {

        /*
        |--------------------------------------------------------------------------
        | YouTube Thumbnail
        |--------------------------------------------------------------------------
        */

        $youtube_id =
            isago_get_youtube_id(
                $video_url
            );


        if ($youtube_id) {

            $image_attributes = '';


            if (
                is_array($attributes)
                &&
                !empty($attributes)
            ) {

                $allowed_attributes = [

                    'class',
                    'alt',
                    'loading',
                    'width',
                    'height',
                    'decoding',
                    'fetchpriority'

                ];


                foreach (
                    $attributes as $key => $value
                ) {

                    if (
                        !is_string($key)
                        ||
                        $value === ''
                    ) {

                        continue;

                    }


                    if (
                        !in_array(
                            $key,
                            $allowed_attributes,
                            true
                        )
                    ) {

                        continue;

                    }


                    $image_attributes .= sprintf(
                        ' %s="%s"',
                        esc_attr($key),
                        esc_attr($value)
                    );

                }

            }


            return sprintf(

                '<img
                    src="https://img.youtube.com/vi/%s/hqdefault.jpg"
                    %s
                >',

                esc_attr($youtube_id),

                $image_attributes

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. FALL BACK TO FEATURED IMAGE
    |--------------------------------------------------------------------------
    */

    if (
        has_post_thumbnail(
            $post_id
        )
    ) {

        return get_the_post_thumbnail(
            $post_id,
            $size,
            $attributes
        );

    }

}




/*
|--------------------------------------------------------------------------
| ISAGO BLOG POST FEATURED IMAGE
|--------------------------------------------------------------------------
*/

$featured_image_id =
    isago_media_get_field(
        'post',
        $post_id,
        'featured_image'
    );

if ($featured_image_id) {

    $featured_image_url =
        isago_media_get_field_image_url(
            'post',
            $post_id,
            'featured_image',
            $size
        );

    if ($featured_image_url) {

        $image_attributes = '';

        if (
            is_array($attributes)
            &&
            !empty($attributes)
        ) {

            $allowed_attributes = [

                'class',
                'alt',
                'loading',
                'width',
                'height',
                'decoding',
                'fetchpriority'

            ];

            foreach (
                $attributes as $key => $value
            ) {

                if (
                    !is_string($key)
                    ||
                    $value === ''
                ) {

                    continue;
                }

                if (
                    !in_array(
                        $key,
                        $allowed_attributes,
                        true
                    )
                ) {

                    continue;
                }

                $image_attributes .= sprintf(
                    ' %s="%s"',
                    esc_attr($key),
                    esc_attr($value)
                );
            }
        }

        return sprintf(

            '<img
                src="%s"
                %s
            >',

            esc_url(
                $featured_image_url
            ),

            $image_attributes

        );
    }
}


/*
|--------------------------------------------------------------------------
| NORMAL WORDPRESS FEATURED IMAGE
|--------------------------------------------------------------------------
*/

if (
    has_post_thumbnail(
        $post_id
    )
) {

    return get_the_post_thumbnail(
        $post_id,
        $size,
        $attributes
    );

}
}
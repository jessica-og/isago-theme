<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
REGISTER ADMIN PAGE
=========================================================*/

add_action(
    'admin_menu',
    'isago_register_broken_social_videos_page'
);

function isago_register_broken_social_videos_page()
{
    add_submenu_page(
        'isago-home',
        'Broken Social Videos',
        'Broken Social Videos',
        'manage_options',
        'isago-broken-social-videos',
        'isago_render_broken_social_videos_page'
    );
}


/*=========================================================
SUPPORTED POST TYPES
=========================================================*/

function isago_broken_social_video_post_types()
{
    return [
        'post',
        'isago_video',
        'ai_prompt',
    ];
}


/*=========================================================
STATUS META
=========================================================*/

function isago_broken_social_video_status_meta_key()
{
    return '_isago_broken_social_video_status';
}


function isago_broken_social_video_message_meta_key()
{
    return '_isago_broken_social_video_message';
}


function isago_broken_social_video_http_code_meta_key()
{
    return '_isago_broken_social_video_http_code';
}


function isago_broken_social_video_checked_at_meta_key()
{
    return '_isago_broken_social_video_checked_at';
}


function isago_broken_social_video_scan_token_meta_key()
{
    return '_isago_broken_social_video_scan_token';
}


function isago_broken_social_video_scan_state_option_key()
{
    return 'isago_broken_social_video_scan_state';
}


/*=========================================================
ALLOWED STATUS VALUES
=========================================================*/

function isago_broken_social_video_allowed_statuses()
{
    return [
        'broken',
        'unknown',
        'working',
        'not_checked',
        'invalid',
    ];
}


/*=========================================================
GET SAVED STATUS
=========================================================*/

function isago_get_broken_social_video_saved_status(
    $post_id
) {

    $status =
        sanitize_key(
            (string) get_post_meta(
                $post_id,
                isago_broken_social_video_status_meta_key(),
                true
            )
        );


    if (
        !in_array(
            $status,
            isago_broken_social_video_allowed_statuses(),
            true
        )
    ) {

        return 'not_checked';
    }


    return $status;
}


/*=========================================================
GET SOCIAL URL
=========================================================*/

function isago_get_broken_social_video_source(
    $post_id
) {

    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {
        return false;
    }


    $post_type =
        get_post_type(
            $post_id
        );


    if (
        !in_array(
            $post_type,
            isago_broken_social_video_post_types(),
            true
        )
    ) {

        return false;
    }


    if (
        $post_type ===
        'ai_prompt'
    ) {

        $sources = [

            [
                'key' =>
                    '_isago_ai_prompt_video_source_url',

                'label' =>
                    'AI Prompt Video URL',
            ],

            [
                'key' =>
                    '_isago_ai_prompt_youtube_url',

                'label' =>
                    'Legacy AI Prompt YouTube URL',
            ],

        ];

    } else {

        $sources = [

            [
                'key' =>
                    '_isago_image_video_source_url',

                'label' =>
                    'Image + Video URL',
            ],

            [
                'key' =>
                    '_isago_image_video_url',

                'label' =>
                    'Legacy Image + Video URL',
            ],

            [
                'key' =>
                    '_isago_video_source_url',

                'label' =>
                    'Video / Social URL',
            ],

            [
                'key' =>
                    '_isago_youtube_url',

                'label' =>
                    'Legacy YouTube URL',
            ],

        ];
    }


    foreach (
        $sources as $source
    ) {

        $url =
            get_post_meta(
                $post_id,
                $source['key'],
                true
            );


        $url =
            is_string($url)
                ? trim($url)
                : '';


        if ($url !== '') {

            return [

                'key' =>
                    $source['key'],

                'label' =>
                    $source['label'],

                'url' =>
                    esc_url_raw($url),

            ];
        }
    }


    return false;
}


/*=========================================================
PROVIDER DETECTION
=========================================================*/

function isago_broken_social_video_provider(
    $url
) {

    $url =
        trim(
            (string) $url
        );


    if ($url === '') {
        return '';
    }


    $host =
        wp_parse_url(
            $url,
            PHP_URL_HOST
        );


    $path =
        wp_parse_url(
            $url,
            PHP_URL_PATH
        );


    if (!$host) {
        return '';
    }


    $host =
        strtolower(
            preg_replace(
                '/^www\./i',
                '',
                $host
            )
        );


    $path =
        strtolower(
            (string) $path
        );


    /*-----------------------------------------------------
    YOUTUBE
    -----------------------------------------------------*/

    if (
        $host === 'youtube.com'
        ||
        $host === 'm.youtube.com'
        ||
        $host === 'youtu.be'
    ) {

        return 'youtube';
    }


    /*-----------------------------------------------------
    INSTAGRAM
    -----------------------------------------------------*/

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


    /*-----------------------------------------------------
    PINTEREST
    -----------------------------------------------------*/

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


    /*-----------------------------------------------------
    TIKTOK
    -----------------------------------------------------*/

    if (
        $host === 'tiktok.com'
        ||
        $host === 'vm.tiktok.com'
        ||
        $host === 'vt.tiktok.com'
    ) {

        return 'tiktok';
    }


    /*-----------------------------------------------------
    VIMEO
    -----------------------------------------------------*/

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


    /*-----------------------------------------------------
    FACEBOOK
    -----------------------------------------------------*/

    if (
        $host === 'facebook.com'
        ||
        $host === 'fb.watch'
    ) {

        return 'facebook';
    }


    return '';
}


/*=========================================================
PROVIDER LABEL
=========================================================*/

function isago_broken_social_video_provider_label(
    $provider
) {

    $labels = [

        'youtube' =>
            'YouTube',

        'instagram' =>
            'Instagram',

        'pinterest' =>
            'Pinterest',

        'tiktok' =>
            'TikTok',

        'vimeo' =>
            'Vimeo',

        'facebook' =>
            'Facebook',

    ];


    return $labels[$provider]
        ?? 'Unknown';
}


/*=========================================================
GET ALL SOCIAL VIDEO ITEMS
=========================================================*/

function isago_get_broken_social_video_items()
{

    global $wpdb;


    $meta_keys = [

        '_isago_ai_prompt_video_source_url',

        '_isago_ai_prompt_youtube_url',

        '_isago_image_video_source_url',

        '_isago_image_video_url',

        '_isago_video_source_url',

        '_isago_youtube_url',

    ];


    $post_types = [

        'post',

        'isago_video',

        'ai_prompt',

    ];


    $post_statuses = [

        'publish',

        'draft',

        'pending',

        'future',

        'private',

    ];


    $type_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_types),
                '%s'
            )
        );


    $status_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_statuses),
                '%s'
            )
        );


    $meta_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($meta_keys),
                '%s'
            )
        );


    $sql = "
        SELECT DISTINCT
            p.ID

        FROM {$wpdb->posts} AS p

        INNER JOIN {$wpdb->postmeta} AS pm
            ON pm.post_id = p.ID

        WHERE
            p.post_type IN ($type_placeholders)

            AND
            p.post_status IN ($status_placeholders)

            AND
            pm.meta_key IN ($meta_placeholders)

            AND
            pm.meta_value <> ''

        ORDER BY
            p.post_date DESC
    ";


    $ids =
        $wpdb->get_col(
            $wpdb->prepare(
                $sql,
                ...array_merge(
                    $post_types,
                    $post_statuses,
                    $meta_keys
                )
            )
        );


    if (
        empty($ids)
        ||
        !is_array($ids)
    ) {

        return [];
    }


    $items = [];


    foreach (
        $ids as $post_id
    ) {

        $post_id =
            absint(
                $post_id
            );


        if (!$post_id) {
            continue;
        }


        $source =
            isago_get_broken_social_video_source(
                $post_id
            );


        if (!$source) {
            continue;
        }


        $provider =
            isago_broken_social_video_provider(
                $source['url']
            );


        $post_type =
            get_post_type(
                $post_id
            );


        $post_type_obj =
            get_post_type_object(
                $post_type
            );


        $message =
            get_post_meta(
                $post_id,
                isago_broken_social_video_message_meta_key(),
                true
            );


        $items[] = [

            'post_id' =>
                $post_id,

            'title' =>
                get_the_title(
                    $post_id
                )
                ?: '(Untitled)',

            'edit_url' =>
                get_edit_post_link(
                    $post_id,
                    ''
                ),

            'post_type' =>
                $post_type,

            'post_type_label' =>
                $post_type_obj
                    ? $post_type_obj
                        ->labels
                        ->singular_name
                    : $post_type,

            'source_key' =>
                $source['key'],

            'source_label' =>
                $source['label'],

            'url' =>
                $source['url'],

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),

            'status' =>
                isago_get_broken_social_video_saved_status(
                    $post_id
                ),

            'message' =>
                is_string($message)
                    ? $message
                    : '',
        ];
    }


    return $items;
}


/*=========================================================
UNAVAILABLE TEXT DETECTION
=========================================================*/

function isago_broken_social_video_body_is_unavailable(
    $body
) {

    $body =
        strtolower(
            (string) $body
        );


    if ($body === '') {
        return false;
    }


    $phrases = [

        'this video is unavailable',

        'video unavailable',

        "this video isn't available",

        "this content isn't available",

        'this content is not available',

        'this content is no longer available',

        "this page isn't available",

        'this page is not available',

        "sorry, this page isn't available",

        "page isn't available",

        'page is not available',

        'content not available',

        'content unavailable',

        'video has been removed',

        'this video has been removed',

        'account not found',

        'user not found',

        "couldn't find this page",

        'could not find this page',

        'the page you are looking for',

    ];


    foreach (
        $phrases as $phrase
    ) {

        if (
            strpos(
                $body,
                $phrase
            ) !== false
        ) {

            return true;
        }
    }


    return false;
}


/*=========================================================
OEMBED CHECK
=========================================================*/

/*=========================================================
OEMBED ENDPOINT
=========================================================*/

function isago_broken_social_video_oembed_endpoint(
    $provider,
    $url
) {

    $endpoint = '';


    switch ($provider) {

        case 'youtube':

            $endpoint =
                'https://www.youtube.com/oembed';

            break;


        case 'vimeo':

            $endpoint =
                'https://vimeo.com/api/oembed.json';

            break;


        case 'tiktok':

            $endpoint =
                'https://www.tiktok.com/oembed';

            break;


        case 'pinterest':

            $endpoint =
                'https://www.pinterest.com/oembed.json';

            break;


        default:

            return null;
    }


    $check_url =
        add_query_arg(
            [
                'url' =>
                    $url,

                'format' =>
                    'json',
            ],
            $endpoint
        );


    $response =
        wp_safe_remote_get(
            $check_url,
            [
                'timeout' =>
                    8,

                'redirection' =>
                    3,

                'headers' =>
                    [
                        'Accept' =>
                            'application/json',
                    ],
            ]
        );


    if (
        is_wp_error(
            $response
        )
    ) {

        return null;
    }


    $status_code =
        (int)
        wp_remote_retrieve_response_code(
            $response
        );


    /*
     * These responses mean the provider could not
     * resolve the supplied social-video URL.
     *
     * YouTube commonly uses HTTP 400 for an invalid
     * or unresolved oEmbed request.
     */
    if (
        $status_code === 400
        ||
        $status_code === 404
        ||
        $status_code === 410
    ) {

        return 'broken';
    }


    /*
     * Successful oEmbed response.
     */
    if (
        $status_code >= 200
        &&
        $status_code < 400
    ) {

        $body =
            wp_remote_retrieve_body(
                $response
            );


        if ($body !== '') {

            $data =
                json_decode(
                    $body,
                    true
                );


            if (
                is_array(
                    $data
                )
            ) {

                return 'working';
            }
        }


        return 'working';
    }


    return null;
}


/*=========================================================
CHECK ONE SOCIAL VIDEO URL
=========================================================*/

function isago_check_broken_social_video_url(
    $url
) {

    $url =
        esc_url_raw(
            trim(
                (string) $url
            )
        );


    if (
        $url === ''
    ) {

        return [

            'status' =>
                'invalid',

            'label' =>
                'Invalid URL',

            'message' =>
                'The saved URL is empty.',

            'http_code' =>
                0,

            'provider' =>
                '',

            'provider_label' =>
                'Unknown',
        ];
    }


    $provider =
        isago_broken_social_video_provider(
            $url
        );


    if (
        $provider === ''
    ) {

        return [

            'status' =>
                'invalid',

            'label' =>
                'Unsupported URL',

            'message' =>
                'The saved URL is not a supported social-video provider.',

            'http_code' =>
                0,

            'provider' =>
                '',

            'provider_label' =>
                'Unknown',
        ];
    }


    $oembed =
        isago_broken_social_video_oembed_endpoint(
            $provider,
            $url
        );


    if (
        $oembed === 'broken'
    ) {

        return [

            'status' =>
                'broken',

            'label' =>
                'Broken',

            'message' =>
                'The provider could not resolve this video URL.',

            'http_code' =>
                404,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    if (
        $oembed === 'working'
    ) {

        return [

            'status' =>
                'working',

            'label' =>
                'Working',

            'message' =>
                'The provider resolved this video URL.',

            'http_code' =>
                200,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    $response =
        wp_safe_remote_get(
            $url,
            [

                'timeout' =>
                    10,

                'redirection' =>
                    5,

                'headers' =>
                    [

                        'User-Agent' =>
                            'Mozilla/5.0 (compatible; Isago Social Video Checker/1.0; +'
                            . home_url('/')
                            . ')',

                        'Accept' =>
                            'text/html,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8',

                    ],

            ]
        );


    if (
        is_wp_error(
            $response
        )
    ) {

        return [

            'status' =>
                'unknown',

            'label' =>
                'Could Not Verify',

            'message' =>
                $response->get_error_message(),

            'http_code' =>
                0,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    $status_code =
        (int)
        wp_remote_retrieve_response_code(
            $response
        );


    $body =
        wp_remote_retrieve_body(
            $response
        );


    if (
        $status_code === 404
        ||
        $status_code === 410
    ) {

        return [

            'status' =>
                'broken',

            'label' =>
                'Broken',

            'message' =>
                'The social platform returned HTTP '
                . $status_code
                . '.',

            'http_code' =>
                $status_code,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    if (
        isago_broken_social_video_body_is_unavailable(
            $body
        )
    ) {

        return [

            'status' =>
                'broken',

            'label' =>
                'Broken',

            'message' =>
                'The provider page indicates that the content is unavailable.',

            'http_code' =>
                $status_code,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    if (
        $status_code >= 200
        &&
        $status_code < 400
    ) {

        return [

            'status' =>
                'working',

            'label' =>
                'Working',

            'message' =>
                'The social platform returned a successful response.',

            'http_code' =>
                $status_code,

            'provider' =>
                $provider,

            'provider_label' =>
                isago_broken_social_video_provider_label(
                    $provider
                ),
        ];
    }


    return [

        'status' =>
            'unknown',

        'label' =>
            'Could Not Verify',

        'message' =>
            'The provider returned HTTP '
            . $status_code
            . ' and the result could not be safely classified.',

        'http_code' =>
            $status_code,

        'provider' =>
            $provider,

        'provider_label' =>
            isago_broken_social_video_provider_label(
                $provider
            ),
    ];
}




/*=========================================================
SAVE CHECK RESULT
=========================================================*/

function isago_save_broken_social_video_check_result(
    $post_id,
    $result
) {

    $post_id =
        absint(
            $post_id
        );


    if (
        !$post_id
        ||
        !is_array($result)
    ) {

        return false;
    }


    update_post_meta(
        $post_id,
        isago_broken_social_video_status_meta_key(),
        sanitize_key(
            $result['status'] ?? 'unknown'
        )
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_message_meta_key(),
        isset($result['message'])
            ? sanitize_text_field(
                $result['message']
            )
            : ''
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_http_code_meta_key(),
        absint(
            $result['http_code'] ?? 0
        )
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_checked_at_meta_key(),
        current_time('mysql')
    );


    return true;
}




/*=========================================================
AJAX: CHECK ONE URL
=========================================================*/

add_action(
    'wp_ajax_isago_check_broken_social_video',
    'isago_ajax_check_broken_social_video'
);

function isago_ajax_check_broken_social_video()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to perform this action.',
            ],
            403
        );
    }


    $post_id =
        isset($_POST['post_id'])
            ? absint($_POST['post_id'])
            : 0;


    if (!$post_id) {

        wp_send_json_error(
            [
                'message' =>
                    'Invalid post ID.',
            ],
            400
        );
    }


    $source =
        isago_get_broken_social_video_source(
            $post_id
        );


    if (!$source) {

        wp_send_json_error(
            [
                'message' =>
                    'This post no longer has a social-video URL.',
            ],
            404
        );
    }


    $result =
        isago_check_broken_social_video_url(
            $source['url']
        );


    /*-----------------------------------------------------
    SAVE RESULT
    -----------------------------------------------------*/

    isago_save_broken_social_video_check_result(
        $post_id,
        $result
    );


    /*-----------------------------------------------------
    MARK MANUAL SCAN ITEM COMPLETE
    -----------------------------------------------------*/

    $scan_token =
        isset($_POST['scan_token'])
            ? sanitize_text_field(
                wp_unslash($_POST['scan_token'])
            )
            : '';


    if ($scan_token !== '') {

        $state =
            get_option(
                isago_broken_social_video_scan_state_option_key(),
                []
            );


        if (
            is_array($state)
            &&
            !empty($state['token'])
            &&
            hash_equals(
                (string) $state['token'],
                $scan_token
            )
            &&
            isset($state['status'])
            &&
            $state['status'] === 'running'
        ) {

            update_post_meta(
                $post_id,
                isago_broken_social_video_scan_token_meta_key(),
                $scan_token
            );

        }
    }


    $result['post_id'] =
        $post_id;


    $result['source_key'] =
        $source['key'];


    $result['url'] =
        $source['url'];


    wp_send_json_success(
        $result
    );
}


/*=========================================================
MANUAL SCAN QUEUE
=========================================================*/

function isago_broken_social_video_get_scan_state()
{

    $state =
        get_option(
            isago_broken_social_video_scan_state_option_key(),
            []
        );


    if (!is_array($state)) {
        $state = [];
    }


    return $state;
}


function isago_broken_social_video_get_candidate_ids(
    $limit = 0,
    $scan_token = ''
) {

    global $wpdb;


    $post_types = [

        'post',

        'isago_video',

        'ai_prompt',

    ];


    $post_statuses = [

        'publish',

        'draft',

        'pending',

        'future',

        'private',

    ];


    $meta_keys = [

        '_isago_ai_prompt_video_source_url',

        '_isago_ai_prompt_youtube_url',

        '_isago_image_video_source_url',

        '_isago_image_video_url',

        '_isago_video_source_url',

        '_isago_youtube_url',

    ];


    $type_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_types),
                '%s'
            )
        );


    $status_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_statuses),
                '%s'
            )
        );


    $meta_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($meta_keys),
                '%s'
            )
        );


    $values =
        array_merge(
            $post_types,
            $post_statuses,
            $meta_keys
        );


    $scan_join = '';
    $scan_where = '';


    if ($scan_token !== '') {

        $scan_join = "
            LEFT JOIN {$wpdb->postmeta} AS sm
                ON sm.post_id = p.ID
                AND sm.meta_key = '" .
                esc_sql(
                    isago_broken_social_video_scan_token_meta_key()
                ) .
                "'
                AND sm.meta_value = %s
        ";

        $scan_where = "
            AND sm.post_id IS NULL
        ";

        array_unshift(
            $values,
            $scan_token
        );
    }


    $limit_sql = '';


    if (
        $limit > 0
    ) {

        $limit_sql = ' LIMIT %d';

        $values[] = absint($limit);
    }


    $sql = "
        SELECT DISTINCT
            p.ID

        FROM {$wpdb->posts} AS p

        INNER JOIN {$wpdb->postmeta} AS pm
            ON pm.post_id = p.ID

        $scan_join

        WHERE
            p.post_type IN (
                $type_placeholders
            )

            AND

            p.post_status IN (
                $status_placeholders
            )

            AND

            pm.meta_key IN (
                $meta_placeholders
            )

            AND

            TRIM(pm.meta_value) <> ''

            $scan_where

        ORDER BY
            p.post_date DESC

        $limit_sql
    ";


    $prepared =
        $wpdb->prepare(
            $sql,
            ...$values
        );


    $ids =
        $wpdb->get_col(
            $prepared
        );


    if (!is_array($ids)) {
        return [];
    }


    return array_values(
        array_unique(
            array_filter(
                array_map(
                    'absint',
                    $ids
                )
            )
        )
    );
}


function isago_broken_social_video_count_candidates()
{

    return count(
        isago_broken_social_video_get_candidate_ids()
    );
}


function isago_broken_social_video_count_completed(
    $scan_token
) {

    global $wpdb;


    if ($scan_token === '') {
        return 0;
    }


    $count =
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(DISTINCT post_id)
                FROM {$wpdb->postmeta}
                WHERE meta_key = %s
                AND meta_value = %s
                ",
                isago_broken_social_video_scan_token_meta_key(),
                $scan_token
            )
        );


    return absint($count);
}


/*=========================================================
AJAX: START / RESUME MANUAL SCAN
=========================================================*/

add_action(
    'wp_ajax_isago_start_broken_social_video_scan',
    'isago_ajax_start_broken_social_video_scan'
);


function isago_ajax_start_broken_social_video_scan()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to scan social-video URLs.',
            ],
            403
        );
    }


    $state =
        isago_broken_social_video_get_scan_state();


    if (
        !empty($state['status'])
        &&
        $state['status'] === 'running'
        &&
        !empty($state['token'])
    ) {

        wp_send_json_success(
            [
                'state' =>
                    isago_broken_social_video_get_public_scan_state(
                        $state
                    ),

                'scan_token' =>
                    (string) ($state['token'] ?? ''),
            ]
        );
    }


    /*
     * Remove completion markers from a previous finished scan
     * before creating a new scan session.
     */
    if (
        !empty($state['token'])
    ) {

        isago_broken_social_video_cleanup_scan_token(
            $state['token']
        );
    }


    $post_ids =
        isago_broken_social_video_get_candidate_ids();


    $total =
        count($post_ids);


    if ($total === 0) {

        $state = [

            'status' =>
                'complete',

            'token' =>
                '',

            'total' =>
                0,

            'completed' =>
                0,

            'started_at' =>
                current_time('mysql'),

            'finished_at' =>
                current_time('mysql'),
        ];


        update_option(
            isago_broken_social_video_scan_state_option_key(),
            $state,
            false
        );


        wp_send_json_success(
            [
                'state' =>
                    isago_broken_social_video_get_public_scan_state(
                        $state
                    ),

                'scan_token' =>
                    '',
            ]
        );
    }


    $token =
        wp_generate_uuid4();


    $state = [

        'status' =>
            'running',

        'token' =>
            $token,

        'total' =>
            $total,

        'completed' =>
            0,

        'started_at' =>
            current_time('mysql'),

        'finished_at' =>
            '',
    ];


    update_option(
        isago_broken_social_video_scan_state_option_key(),
        $state,
        false
    );


    wp_schedule_single_event(
        time() + 5,
        'isago_broken_social_videos_process_scan_queue'
    );


    wp_send_json_success(
        [
            'state' =>
                isago_broken_social_video_get_public_scan_state(
                    $state
                ),

            'scan_token' =>
                $token,
        ]
    );
}


/*=========================================================
CLEAN UP SCAN TOKEN META
=========================================================*/

function isago_broken_social_video_cleanup_scan_token(
    $scan_token
) {

    global $wpdb;


    $scan_token =
        sanitize_text_field(
            (string) $scan_token
        );


    if ($scan_token === '') {
        return;
    }


    $wpdb->delete(
        $wpdb->postmeta,
        [
            'meta_key' =>
                isago_broken_social_video_scan_token_meta_key(),

            'meta_value' =>
                $scan_token,
        ],
        [
            '%s',
            '%s',
        ]
    );
}


/*=========================================================
PUBLIC SCAN STATE
=========================================================*/

function isago_broken_social_video_get_public_scan_state(
    $state
) {

    if (!is_array($state)) {
        $state = [];
    }


    $status =
        sanitize_key(
            $state['status'] ?? 'idle'
        );


    $total =
        isset($state['total'])
            ? absint($state['total'])
            : 0;


    if ($status === 'complete') {

        $completed =
            isset($state['completed'])
                ? min(
                    $total,
                    absint($state['completed'])
                )
                : $total;

    } else {

        $token =
            isset($state['token'])
                ? (string) $state['token']
                : '';


        $completed =
            $token !== ''
                ? isago_broken_social_video_count_completed(
                    $token
                )
                : 0;
    }


    $remaining =
        max(
            0,
            $total - $completed
        );


    $percentage =
        $total > 0
            ? round(
                ($completed / $total) * 100,
                1
            )
            : 100;


    return [

        'status' =>
            $status,

        'total' =>
            $total,

        'completed' =>
            $completed,

        'remaining' =>
            $remaining,

        'percentage' =>
            $percentage,

        'started_at' =>
            $state['started_at'] ?? '',

        'finished_at' =>
            $state['finished_at'] ?? '',
    ];
}


/*=========================================================
AJAX: GET SCAN STATE
=========================================================*/

add_action(
    'wp_ajax_isago_get_broken_social_video_scan_state',
    'isago_ajax_get_broken_social_video_scan_state'
);


function isago_ajax_get_broken_social_video_scan_state()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to view the scan status.',
            ],
            403
        );
    }


    $state =
        isago_broken_social_video_get_scan_state();


    wp_send_json_success(
        [
            'state' =>
                isago_broken_social_video_get_public_scan_state(
                    $state
                ),
        ]
    );
}


/*=========================================================
AJAX: GET ACTIVE SCAN SESSION
=========================================================*/

add_action(
    'wp_ajax_isago_get_broken_social_video_scan_session',
    'isago_ajax_get_broken_social_video_scan_session'
);


function isago_ajax_get_broken_social_video_scan_session()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to view the scan session.',
            ],
            403
        );
    }


    $state =
        isago_broken_social_video_get_scan_state();


    $token =
        '';


    if (
        ($state['status'] ?? '') === 'running'
        &&
        !empty($state['token'])
    ) {

        $token =
            (string) $state['token'];
    }


    wp_send_json_success(
        [
            'state' =>
                isago_broken_social_video_get_public_scan_state(
                    $state
                ),

            'scan_token' =>
                $token,
        ]
    );
}


/*=========================================================
AJAX: GET NEXT SCAN BATCH
=========================================================*/

add_action(
    'wp_ajax_isago_get_broken_social_video_scan_batch',
    'isago_ajax_get_broken_social_video_scan_batch'
);


function isago_ajax_get_broken_social_video_scan_batch()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to scan social-video URLs.',
            ],
            403
        );
    }


    $token =
        isset($_POST['scan_token'])
            ? sanitize_text_field(
                wp_unslash($_POST['scan_token'])
            )
            : '';


    $state =
        isago_broken_social_video_get_scan_state();


    if (
        $token === ''
        ||
        empty($state['token'])
        ||
        !hash_equals(
            (string) $state['token'],
            $token
        )
        ||
        ($state['status'] ?? '') !== 'running'
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'The scan session is no longer active.',
            ],
            409
        );
    }


    $ids =
        isago_broken_social_video_get_candidate_ids(
            6,
            $token
        );


    if (empty($ids)) {

        $completed =
            isago_broken_social_video_count_completed(
                $token
            );


        $state['status'] =
            'complete';

        $state['completed'] =
            min(
                absint($state['total'] ?? 0),
                $completed
            );

        $state['finished_at'] =
            current_time('mysql');

        $state['token'] =
            '';


        update_option(
            isago_broken_social_video_scan_state_option_key(),
            $state,
            false
        );


        isago_broken_social_video_cleanup_scan_token(
            $token
        );


        wp_send_json_success(
            [
                'complete' =>
                    true,

                'post_ids' =>
                    [],

                'state' =>
                    isago_broken_social_video_get_public_scan_state(
                        $state
                    ),
            ]
        );
    }


    wp_send_json_success(
        [
            'complete' =>
                false,

            'post_ids' =>
                $ids,

            'state' =>
                isago_broken_social_video_get_public_scan_state(
                    $state
                ),
        ]
    );
}


/*=========================================================
AJAX: MARK FAILED SCAN ITEM
=========================================================*/

add_action(
    'wp_ajax_isago_mark_broken_social_video_scan_item',
    'isago_ajax_mark_broken_social_video_scan_item'
);


function isago_ajax_mark_broken_social_video_scan_item()
{

    check_ajax_referer(
        'isago_broken_social_videos',
        'nonce'
    );


    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'You do not have permission to update the scan queue.',
            ],
            403
        );
    }


    $post_id =
        isset($_POST['post_id'])
            ? absint($_POST['post_id'])
            : 0;


    $scan_token =
        isset($_POST['scan_token'])
            ? sanitize_text_field(
                wp_unslash($_POST['scan_token'])
            )
            : '';


    $state =
        isago_broken_social_video_get_scan_state();


    if (
        !$post_id
        ||
        $scan_token === ''
        ||
        empty($state['token'])
        ||
        !hash_equals(
            (string) $state['token'],
            $scan_token
        )
        ||
        ($state['status'] ?? '') !== 'running'
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'The scan session is no longer active.',
            ],
            409
        );
    }


    update_post_meta(
        $post_id,
        isago_broken_social_video_status_meta_key(),
        'unknown'
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_message_meta_key(),
        'Could Not Verify during this scan. It will be checked again automatically.'
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_http_code_meta_key(),
        0
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_checked_at_meta_key(),
        current_time('mysql')
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_scan_token_meta_key(),
        $scan_token
    );


    wp_send_json_success(
        [
            'post_id' =>
                $post_id,
        ]
    );
}


/*=========================================================
AUTOMATIC QUEUE FALLBACK
=========================================================*/

function isago_broken_social_video_cron_schedules(
    $schedules
) {

    if (!isset($schedules['isago_every_minute'])) {

        $schedules['isago_every_minute'] = [

            'interval' =>
                60,

            'display' =>
                'Every Minute',
        ];
    }


    return $schedules;
}


add_filter(
    'cron_schedules',
    'isago_broken_social_video_cron_schedules'
);


function isago_schedule_broken_social_video_crons()
{

    if (
        !wp_next_scheduled(
            'isago_broken_social_videos_process_scan_queue'
        )
    ) {

        wp_schedule_event(
            time() + 60,
            'isago_every_minute',
            'isago_broken_social_videos_process_scan_queue'
        );
    }


    if (
        !wp_next_scheduled(
            'isago_broken_social_videos_automatic_check'
        )
    ) {

        wp_schedule_event(
            time() + 90,
            'isago_every_minute',
            'isago_broken_social_videos_automatic_check'
        );
    }
}


add_action(
    'init',
    'isago_schedule_broken_social_video_crons'
);


/*=========================================================
PROCESS ONE MANUAL QUEUE ITEM
=========================================================*/

add_action(
    'isago_broken_social_videos_process_scan_queue',
    'isago_process_broken_social_video_scan_queue'
);


function isago_process_broken_social_video_scan_queue()
{

    $state =
        isago_broken_social_video_get_scan_state();


    if (
        empty($state['token'])
        ||
        ($state['status'] ?? '') !== 'running'
    ) {

        return;
    }


    $token =
        (string) $state['token'];


    $ids =
        isago_broken_social_video_get_candidate_ids(
            1,
            $token
        );


    if (empty($ids)) {

        $completed =
            isago_broken_social_video_count_completed(
                $token
            );


        $state['status'] =
            'complete';

        $state['completed'] =
            min(
                absint($state['total'] ?? 0),
                $completed
            );

        $state['finished_at'] =
            current_time('mysql');

        $state['token'] =
            '';


        update_option(
            isago_broken_social_video_scan_state_option_key(),
            $state,
            false
        );


        isago_broken_social_video_cleanup_scan_token(
            $token
        );


        return;
    }


    $post_id =
        absint(
            $ids[0]
        );


    $source =
        isago_get_broken_social_video_source(
            $post_id
        );


    if (!$source) {

        update_post_meta(
            $post_id,
            isago_broken_social_video_scan_token_meta_key(),
            $token
        );

        return;
    }


    $result =
        isago_check_broken_social_video_url(
            $source['url']
        );


    isago_save_broken_social_video_check_result(
        $post_id,
        $result
    );


    update_post_meta(
        $post_id,
        isago_broken_social_video_scan_token_meta_key(),
        $token
    );
}


/*=========================================================
AUTOMATIC STALE LINK CHECKER
=========================================================*/

add_action(
    'isago_broken_social_videos_automatic_check',
    'isago_run_broken_social_videos_automatic_check'
);


function isago_run_broken_social_videos_automatic_check()
{

    $state =
        isago_broken_social_video_get_scan_state();


    /*
     * Do not compete with a full manual scan.
     */
    if (
        ($state['status'] ?? '') === 'running'
    ) {

        return;
    }


    global $wpdb;


    $post_types = [
        'post',
        'isago_video',
        'ai_prompt',
    ];


    $post_statuses = [
        'publish',
        'draft',
        'pending',
        'future',
        'private',
    ];


    $meta_keys = [
        '_isago_ai_prompt_video_source_url',
        '_isago_ai_prompt_youtube_url',
        '_isago_image_video_source_url',
        '_isago_image_video_url',
        '_isago_video_source_url',
        '_isago_youtube_url',
    ];


    $type_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_types),
                '%s'
            )
        );


    $status_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($post_statuses),
                '%s'
            )
        );


    $meta_placeholders =
        implode(
            ',',
            array_fill(
                0,
                count($meta_keys),
                '%s'
            )
        );


    $values =
        array_merge(
            $post_types,
            $post_statuses,
            $meta_keys
        );


    $sql = "
        SELECT DISTINCT
            p.ID,
            cm.meta_value AS checked_at

        FROM {$wpdb->posts} AS p

        INNER JOIN {$wpdb->postmeta} AS pm
            ON pm.post_id = p.ID

        LEFT JOIN {$wpdb->postmeta} AS cm
            ON cm.post_id = p.ID
            AND cm.meta_key = '" .
            esc_sql(
                isago_broken_social_video_checked_at_meta_key()
            ) .
            "'

        WHERE
            p.post_type IN ($type_placeholders)

            AND

            p.post_status IN ($status_placeholders)

            AND

            pm.meta_key IN ($meta_placeholders)

            AND

            TRIM(pm.meta_value) <> ''

            AND

            (
                cm.meta_value IS NULL
                OR
                cm.meta_value = ''
                OR
                cm.meta_value <= %s
            )

        ORDER BY
            CASE
                WHEN cm.meta_value IS NULL
                    OR cm.meta_value = ''
                THEN 0
                ELSE 1
            END ASC,
            cm.meta_value ASC,
            p.post_date ASC

        LIMIT 1
    ";


    $values[] =
        date(
            'Y-m-d H:i:s',
            time() - DAY_IN_SECONDS
        );


    $post_id =
        $wpdb->get_var(
            $wpdb->prepare(
                $sql,
                ...$values
            )
        );


    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {
        return;
    }


    $source =
        isago_get_broken_social_video_source(
            $post_id
        );


    if (!$source) {
        return;
    }


    $result =
        isago_check_broken_social_video_url(
            $source['url']
        );


    isago_save_broken_social_video_check_result(
        $post_id,
        $result
    );
}


/*=========================================================
FILTER VALIDATION
=========================================================*/

function isago_broken_social_video_normalize_filter(
    $value,
    $allowed,
    $default = 'all'
) {

    $value =
        sanitize_key(
            (string) $value
        );


    if (
        !in_array(
            $value,
            $allowed,
            true
        )
    ) {

        return $default;
    }


    return $value;
}


/*=========================================================
RENDER ADMIN PAGE
=========================================================*/

function isago_render_broken_social_videos_page()
{

    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        wp_die(
            esc_html__(
                'You do not have permission to access this page.',
                'isago'
            )
        );
    }


    $items =
        isago_get_broken_social_video_items();


    $status_filter =
        isago_broken_social_video_normalize_filter(
            isset($_GET['status'])
                ? wp_unslash(
                    $_GET['status']
                )
                : 'all',

            array_merge(
                [
                    'all',
                ],
                isago_broken_social_video_allowed_statuses()
            )
        );


    $provider_filter =
        isago_broken_social_video_normalize_filter(
            isset($_GET['provider'])
                ? wp_unslash(
                    $_GET['provider']
                )
                : 'all',

            [
                'all',
                'youtube',
                'instagram',
                'pinterest',
                'tiktok',
                'vimeo',
                'facebook',
            ]
        );


    $counts = [

        'all' =>
            count($items),

        'broken' =>
            0,

        'unknown' =>
            0,

        'working' =>
            0,

        'not_checked' =>
            0,

        'invalid' =>
            0,
    ];


    $filtered_items = [];


    foreach (
        $items as $item
    ) {

        if (
            isset(
                $counts[
                    $item['status']
                ]
            )
        ) {

            $counts[
                $item['status']
            ]++;
        }


        $status_matches =
            (
                $status_filter ===
                'all'
                ||
                $item['status'] ===
                $status_filter
            );


        $provider_matches =
            (
                $provider_filter ===
                'all'
                ||
                $item['provider'] ===
                $provider_filter
            );


        if (
            $status_matches
            &&
            $provider_matches
        ) {

            $filtered_items[] =
                $item;
        }
    }

    ?>

    <div
        class="wrap isago-admin isago-broken-social-videos-page"
    >

        <h1>Broken Social Videos</h1>


        <p>
            Scan all saved social-video URLs. After scanning,
            use the Status and Platform filters to show only
            the results you need.
        </p>


        <form
            method="get"
            class="isago-broken-video-toolbar"
            id="isago-broken-video-filter-form"
        >

            <input
                type="hidden"
                name="page"
                value="isago-broken-social-videos"
            >


            <button
                type="button"
                class="button button-primary"
                id="isago-broken-video-scan-all"
            >
                Scan All Links
            </button>


            <label
                for="isago-broken-video-status-filter"
            >
                Status
            </label>


            <select
                id="isago-broken-video-status-filter"
                name="status"
            >

                <option
                    value="all"
                    <?php
                    selected(
                        $status_filter,
                        'all'
                    );
                    ?>
                >
                    All Statuses
                </option>


                <option
                    value="broken"
                    <?php
                    selected(
                        $status_filter,
                        'broken'
                    );
                    ?>
                >
                    Broken
                </option>


                <option
                    value="unknown"
                    <?php
                    selected(
                        $status_filter,
                        'unknown'
                    );
                    ?>
                >
                    Could Not Verify
                </option>


                <option
                    value="working"
                    <?php
                    selected(
                        $status_filter,
                        'working'
                    );
                    ?>
                >
                    Working
                </option>


                <option
                    value="not_checked"
                    <?php
                    selected(
                        $status_filter,
                        'not_checked'
                    );
                    ?>
                >
                    Not Checked
                </option>


                <option
                    value="invalid"
                    <?php
                    selected(
                        $status_filter,
                        'invalid'
                    );
                    ?>
                >
                    Invalid / Unsupported
                </option>

            </select>


            <label
                for="isago-broken-video-provider-filter"
            >
                Platform
            </label>


            <select
                id="isago-broken-video-provider-filter"
                name="provider"
            >

                <option
                    value="all"
                    <?php
                    selected(
                        $provider_filter,
                        'all'
                    );
                    ?>
                >
                    All Platforms
                </option>


                <option
                    value="youtube"
                    <?php
                    selected(
                        $provider_filter,
                        'youtube'
                    );
                    ?>
                >
                    YouTube
                </option>


                <option
                    value="instagram"
                    <?php
                    selected(
                        $provider_filter,
                        'instagram'
                    );
                    ?>
                >
                    Instagram
                </option>


                <option
                    value="pinterest"
                    <?php
                    selected(
                        $provider_filter,
                        'pinterest'
                    );
                    ?>
                >
                    Pinterest
                </option>


                <option
                    value="tiktok"
                    <?php
                    selected(
                        $provider_filter,
                        'tiktok'
                    );
                    ?>
                >
                    TikTok
                </option>


                <option
                    value="vimeo"
                    <?php
                    selected(
                        $provider_filter,
                        'vimeo'
                    );
                    ?>
                >
                    Vimeo
                </option>


                <option
                    value="facebook"
                    <?php
                    selected(
                        $provider_filter,
                        'facebook'
                    );
                    ?>
                >
                    Facebook
                </option>

            </select>


            <button
                type="submit"
                class="button"
            >
                Apply Filters
            </button>


            <?php
            if (
                $status_filter !== 'all'
                ||
                $provider_filter !== 'all'
            ) :
            ?>

                <a
                    class="button"
                    href="<?php
                    echo esc_url(
                        admin_url(
                            'admin.php?page=isago-broken-social-videos'
                        )
                    );
                    ?>"
                >
                    Clear Filters
                </a>

            <?php endif; ?>


            <span
                class="isago-broken-video-progress"
                id="isago-broken-video-progress"
            ></span>

        </form>


        <div
            class="notice notice-info inline isago-broken-video-notice"
            id="isago-broken-video-notice"
            style="display:none;"
        >

            <p></p>

        </div>


        <div
            class="isago-broken-video-summary"
        >

            <span
                class="isago-broken-count"
                data-count="all"
            >
                All:
                <strong>
                    <?php
                    echo esc_html(
                        $counts['all']
                    );
                    ?>
                </strong>
            </span>


            <span
                class="isago-broken-count"
                data-count="broken"
            >
                Broken:
                <strong>
                    <?php
                    echo esc_html(
                        $counts['broken']
                    );
                    ?>
                </strong>
            </span>


            <span
                class="isago-broken-count"
                data-count="unknown"
            >
                Could Not Verify:
                <strong>
                    <?php
                    echo esc_html(
                        $counts['unknown']
                    );
                    ?>
                </strong>
            </span>


            <span
                class="isago-broken-count"
                data-count="working"
            >
                Working:
                <strong>
                    <?php
                    echo esc_html(
                        $counts['working']
                    );
                    ?>
                </strong>
            </span>


            <span
                class="isago-broken-count"
                data-count="not_checked"
            >
                Not Checked:
                <strong>
                    <?php
                    echo esc_html(
                        $counts['not_checked']
                    );
                    ?>
                </strong>
            </span>

        </div>


        <table
            class="widefat fixed striped isago-broken-video-table"
        >

            <thead>

                <tr>

                    <th class="column-title">
                        Post
                    </th>

                    <th class="column-type">
                        Type
                    </th>

                    <th class="column-platform">
                        Platform
                    </th>

                    <th class="column-url">
                        Current URL
                    </th>

                    <th class="column-status">
                        Status
                    </th>

                    <th class="column-actions">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody
                id="isago-broken-video-table-body"
            >

            <?php
            if (
                !empty(
                    $filtered_items
                )
            ) :
            ?>

                <?php
                foreach (
                    $filtered_items as $item
                ) :
                ?>

                    <tr
                        class="isago-broken-video-row"
                        data-post-id="<?php
                        echo esc_attr(
                            $item['post_id']
                        );
                        ?>"
                        data-provider="<?php
                        echo esc_attr(
                            $item['provider']
                        );
                        ?>"
                        data-status="<?php
                        echo esc_attr(
                            $item['status']
                        );
                        ?>"
                    >

                        <td
                            class="column-title"
                        >

                            <strong>

                                <a
                                    href="<?php
                                    echo esc_url(
                                        $item['edit_url']
                                    );
                                    ?>"
                                >
                                    <?php
                                    echo esc_html(
                                        $item['title']
                                    );
                                    ?>
                                </a>

                            </strong>


                            <div
                                class="isago-broken-video-source-label"
                            >
                                <?php
                                echo esc_html(
                                    $item['source_label']
                                );
                                ?>
                            </div>

                        </td>


                        <td
                            class="column-type"
                        >
                            <?php
                            echo esc_html(
                                $item['post_type_label']
                            );
                            ?>
                        </td>


                        <td
                            class="column-platform"
                        >

                            <span
                                class="isago-broken-provider-badge"
                            >
                                <?php
                                echo esc_html(
                                    $item['provider_label']
                                );
                                ?>
                            </span>

                        </td>


                        <td
                            class="column-url"
                        >

                            <a
                                class="isago-broken-video-current-url"
                                href="<?php
                                echo esc_url(
                                    $item['url']
                                );
                                ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <?php
                                echo esc_html(
                                    $item['url']
                                );
                                ?>
                            </a>

                        </td>


                        <td
                            class="column-status"
                        >

                            <span
                                class="isago-broken-video-status-badge status-<?php
                                echo esc_attr(
                                    $item['status']
                                );
                                ?>"
                            >

                                <?php

                                switch (
                                    $item['status']
                                ) {

                                    case 'broken':

                                        echo 'Broken';

                                        break;


                                    case 'working':

                                        echo 'Working';

                                        break;


                                    case 'unknown':

                                        echo 'Could Not Verify';

                                        break;


                                    case 'invalid':

                                        echo 'Invalid / Unsupported';

                                        break;


                                    default:

                                        echo 'Not Checked';

                                        break;
                                }

                                ?>

                            </span>


                            <?php
                            if (
                                !empty(
                                    $item['message']
                                )
                            ) :
                            ?>

                                <div
                                    class="isago-broken-video-status-message"
                                >
                                    <?php
                                    echo esc_html(
                                        $item['message']
                                    );
                                    ?>
                                </div>

                            <?php endif; ?>

                        </td>


                        <td
                            class="column-actions"
                        >

                            <a
                                class="button"
                                href="<?php
                                echo esc_url(
                                    $item['edit_url']
                                );
                                ?>"
                            >
                                Edit Post
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr>

                    <td
                        colspan="6"
                    >
                        No social-video URLs match
                        the selected filters.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>


        <p
            class="description isago-broken-video-footnote"
        >

            A URL is not classified as Broken merely because
            it has been saved. Click
            <strong>
                Scan All Links
            </strong>
            first so Isago can verify the URLs and save
            their results.

        </p>

    </div>

    <?php
}
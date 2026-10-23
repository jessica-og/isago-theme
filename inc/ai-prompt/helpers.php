<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI PROMPT HELPERS
=========================================================*/


/*=========================================================
GET AI PROMPT TERMS
=========================================================*/

function isago_get_ai_prompt_terms(
    $taxonomy,
    $args = array()
) {

    if (!taxonomy_exists($taxonomy)) {
        return array();
    }

    $defaults = array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    );

    $args = wp_parse_args(
        $args,
        $defaults
    );

    $terms = get_terms($args);

    if (is_wp_error($terms)) {
        return array();
    }

    return $terms;
}


/*=========================================================
GET AI PROMPT CATEGORIES
=========================================================*/

function isago_get_ai_prompt_categories(
    $args = array()
) {

    return isago_get_ai_prompt_terms(
        'ai_prompt_category',
        $args
    );
}



/*=========================================================
PROMPT TAGS
=========================================================*/

function isago_get_ai_prompt_tags(
    $post_id
) {

    return isago_get_ai_prompt_post_terms(
        $post_id,
        'post_tag'
    );
}


/*=========================================================
PROMPT TAG NAMES
=========================================================*/

function isago_get_ai_prompt_tag_names(
    $post_id
) {

    return isago_get_ai_prompt_term_names(
        $post_id,
        'post_tag'
    );
}



/*=========================================================
GET AI PLATFORMS FOR PROMPT ADMIN
=========================================================*/

function isago_get_ai_prompt_platforms(
    $args = array()
) {

    return isago_get_ai_prompt_terms(
        'ai_platform',
        $args
    );
}


/*=========================================================
GET SINGLE PROMPT CATEGORY
=========================================================*/

function isago_get_ai_prompt_category(
    $term_id
) {

    $term_id = absint($term_id);

    if (!$term_id) {
        return false;
    }

    $term = get_term(
        $term_id,
        'ai_prompt_category'
    );

    if (
        !$term ||
        is_wp_error($term)
    ) {
        return false;
    }

    return $term;
}


/*=========================================================
GET PROMPT CATEGORY URL
=========================================================*/

function isago_get_ai_prompt_category_url(
    $term_id
) {

    $term =
        isago_get_ai_prompt_category(
            $term_id
        );

    if (!$term) {
        return '';
    }

    $url = get_term_link(
        $term,
        'ai_prompt_category'
    );

    if (is_wp_error($url)) {
        return '';
    }

    return esc_url($url);
}


/*=========================================================
GET TERMS ASSIGNED TO PROMPT
=========================================================*/

function isago_get_ai_prompt_post_terms(
    $post_id,
    $taxonomy
) {

    $post_id = absint($post_id);

    if (
        !$post_id ||
        !taxonomy_exists($taxonomy)
    ) {
        return array();
    }

    $terms = get_the_terms(
        $post_id,
        $taxonomy
    );

    if (
        !$terms ||
        is_wp_error($terms)
    ) {
        return array();
    }

    return $terms;
}


/*=========================================================
PROMPT CATEGORIES
=========================================================*/

function isago_get_ai_prompt_post_categories(
    $post_id
) {

    return isago_get_ai_prompt_post_terms(
        $post_id,
        'ai_prompt_category'
    );
}


/*=========================================================
PROMPT AI PLATFORMS
=========================================================*/

function isago_get_ai_prompt_platforms_for_post(
    $post_id
) {

    return isago_get_ai_prompt_post_terms(
        $post_id,
        'ai_platform'
    );
}


/*=========================================================
PROMPT TERM NAMES
=========================================================*/

function isago_get_ai_prompt_term_names(
    $post_id,
    $taxonomy
) {

    $terms =
        isago_get_ai_prompt_post_terms(
            $post_id,
            $taxonomy
        );

    if (empty($terms)) {
        return array();
    }

    return wp_list_pluck(
        $terms,
        'name'
    );
}


/*=========================================================
CATEGORY NAMES
=========================================================*/

function isago_get_ai_prompt_category_names(
    $post_id
) {

    return isago_get_ai_prompt_term_names(
        $post_id,
        'ai_prompt_category'
    );
}


/*=========================================================
PLATFORM NAMES
=========================================================*/

function isago_get_ai_prompt_platform_names(
    $post_id
) {

    return isago_get_ai_prompt_term_names(
        $post_id,
        'ai_platform'
    );
}


/*=========================================================
SHORT DESCRIPTION
=========================================================*/

function isago_get_ai_prompt_short_description(
    $post_id
) {

    return get_post_meta(
        absint($post_id),
        '_isago_ai_prompt_short_description',
        true
    );
}


/*=========================================================
PROMPT VARIABLES
=========================================================*/

function isago_get_ai_prompt_variables(
    $post_id
) {

    $variables = get_post_meta(
        absint($post_id),
        '_isago_ai_prompt_variables',
        true
    );

    if (!is_array($variables)) {
        return array();
    }

    return array_values(
        array_filter(
            array_map(
                'sanitize_text_field',
                $variables
            )
        )
    );
}


/*=========================================================
FEATURED STATUS
=========================================================*/

function isago_is_ai_prompt_featured(
    $post_id
) {

    return (
        get_post_meta(
            absint($post_id),
            '_isago_ai_prompt_featured',
            true
        ) === '1'
    );
}


/*=========================================================
PROMPT ACCESS TYPE
=========================================================*/

/**
 * Get the access type for an AI Prompt.
 *
 * Supported values:
 *
 * free
 * paid
 *
 * Existing prompts with no saved value are treated
 * as free.
 *
 * @param int $post_id
 *
 * @return string
 */
function isago_get_ai_prompt_access(
    $post_id
) {

    $post_id = absint($post_id);

    if (!$post_id) {
        return 'free';
    }


    $access =
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_access',
            true
        );


    if ($access === 'paid') {

        return 'paid';
    }


    return 'free';
}


/*=========================================================
CHECK IF PROMPT IS PAID
=========================================================*/

/**
 * @param int $post_id
 *
 * @return bool
 */
function isago_is_ai_prompt_paid(
    $post_id
) {

    return (
        isago_get_ai_prompt_access(
            $post_id
        ) === 'paid'
    );
}


/*=========================================================
PROMPT PRICE
=========================================================*/

/**
 * Get the saved AI Prompt price.
 *
 * Price is returned as a normalized decimal string.
 *
 * Example:
 *
 * 4
 * becomes
 * 4.00
 *
 * @param int $post_id
 *
 * @return string
 */
function isago_get_ai_prompt_price(
    $post_id
) {

    $post_id = absint($post_id);

    if (!$post_id) {
        return '0.00';
    }


    $price =
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_price',
            true
        );


    if (
        $price === ''
        ||
        !is_numeric($price)
    ) {

        return '0.00';
    }


    $price =
        (float) $price;


    if ($price <= 0) {

        return '0.00';
    }


    return number_format(
        $price,
        2,
        '.',
        ''
    );
}


/*=========================================================
PROMPT IMAGE ID
=========================================================*/

function isago_get_ai_prompt_image_id(
    $post_id
) {

    $post_id = absint($post_id);

    if (!$post_id) {
        return 0;
    }

    $image_id = absint(
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_image_id',
            true
        )
    );

    if (
        !$image_id ||
        !wp_attachment_is_image($image_id)
    ) {
        return 0;
    }

    return $image_id;
}


/*=========================================================
PROMPT IMAGE URL
=========================================================*/

function isago_get_ai_prompt_image_url(
    $post_id,
    $size = 'medium'
) {

    $image_id =
        isago_get_ai_prompt_image_id(
            $post_id
        );

    if (!$image_id) {
        return '';
    }

    $url =
        wp_get_attachment_image_url(
            $image_id,
            $size
        );

    return $url
        ? esc_url($url)
        : '';
}


/*=========================================================
PROMPT VIDEO ID
=========================================================*/

function isago_get_ai_prompt_video_id(
    $post_id
) {

    $post_id = absint($post_id);

    if (!$post_id) {
        return 0;
    }

    $video_id = absint(
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_video_id',
            true
        )
    );

    if (
        !$video_id ||
        !wp_attachment_is('video', $video_id)
    ) {
        return 0;
    }

    return $video_id;
}


/*=========================================================
PROMPT VIDEO URL
=========================================================*/

function isago_get_ai_prompt_video_url(
    $post_id
) {

    $video_id =
        isago_get_ai_prompt_video_id(
            $post_id
        );

    if (!$video_id) {
        return '';
    }

    $url =
        wp_get_attachment_url(
            $video_id
        );

    return $url
        ? esc_url($url)
        : '';
}


/*=========================================================
PROMPT YOUTUBE URL
=========================================================*/

function isago_get_ai_prompt_youtube_url(
    $post_id
) {

    $url = get_post_meta(
        absint($post_id),
        '_isago_ai_prompt_youtube_url',
        true
    );

    if (
        !$url ||
        !preg_match(
            '#^https?://#i',
            $url
        )
    ) {
        return '';
    }

    return esc_url($url);
}





/*=========================================================
RESOLVE AI PROMPT SOCIAL IMAGE URL
=========================================================*/

function isago_ai_prompt_resolve_social_image_url($url) {

    $url = esc_url_raw(trim((string) $url));

    if (!$url || !wp_http_validate_url($url)) {
        return '';
    }

    $scheme = strtolower(
        (string) wp_parse_url($url, PHP_URL_SCHEME)
    );

    if (!in_array($scheme, array('http', 'https'), true)) {
        return '';
    }

    // A direct image URL can be used without fetching a webpage.
    $path = (string) wp_parse_url($url, PHP_URL_PATH);

    if (preg_match('/\.(jpg|jpeg|png|webp|gif|avif|bmp|svg)$/i', $path)) {
        return $url;
    }

    // Try to retrieve the page regardless of the social platform.
    $response = wp_safe_remote_get(
        $url,
        array(
            'timeout'             => 12,
            'redirection'         => 5,
            'limit_response_size' => 2097152,
            'headers'             => array(
                'User-Agent' => 'Mozilla/5.0 (compatible; IsagoBot/1.0)',
                'Accept'     => 'text/html,application/xhtml+xml',
            ),
        )
    );

    if (
        is_wp_error($response)
        || (int) wp_remote_retrieve_response_code($response) !== 200
    ) {
        return '';
    }

    $html = wp_remote_retrieve_body($response);

    if (!is_string($html) || $html === '') {
        return '';
    }

    $image_url = '';

    // Read Open Graph and Twitter image metadata.
    if (class_exists('DOMDocument')) {

        $previous_errors = libxml_use_internal_errors(true);

        $document = new DOMDocument();

        if ($document->loadHTML('<?xml encoding="utf-8" ?>' . $html)) {

            $xpath = new DOMXPath($document);

            $nodes = $xpath->query(
                '//meta[@property="og:image"]'
                . ' | //meta[@property="og:image:url"]'
                . ' | //meta[@name="twitter:image"]'
                . ' | //meta[@name="twitter:image:src"]'
            );

            if ($nodes) {
                foreach ($nodes as $node) {
                    $candidate = trim(
                        (string) $node->getAttribute('content')
                    );

                    if ($candidate !== '') {
                        $image_url = $candidate;
                        break;
                    }
                }
            }

            // Some pages expose their preview image through image_src.
            if ($image_url === '') {
                $nodes = $xpath->query(
                    '//link[@rel="image_src"]'
                );

                if ($nodes && $nodes->length) {
                    $image_url = trim(
                        (string) $nodes->item(0)->getAttribute('href')
                    );
                }
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous_errors);
    }

    if ($image_url === '') {
        return '';
    }

    $image_url = html_entity_decode(
        $image_url,
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );

    // Support protocol-relative image URLs such as //cdn.example.com/image.jpg.
    if (strpos($image_url, '//') === 0) {
        $image_url = $scheme . ':' . $image_url;
    }

    $image_url = esc_url_raw($image_url);

    if (
        !$image_url
        || !wp_http_validate_url($image_url)
        || !in_array(
            strtolower((string) wp_parse_url($image_url, PHP_URL_SCHEME)),
            array('http', 'https'),
            true
        )
    ) {
        return '';
    }

    return $image_url;
}





/*=========================================================
AI PROMPT CATEGORY ICON
=========================================================*/

function isago_get_ai_prompt_category_icon(
    $term_id
) {

    $term_id = absint($term_id);

    if (!$term_id) {
        return '';
    }

    $icon = get_term_meta(
        $term_id,
        '_isago_ai_prompt_category_icon',
        true
    );

    if (!$icon) {
        return '';
    }

    return sanitize_text_field($icon);
}




/*=========================================================
AI PROMPT DIRECTORY
=========================================================*/

/**
 * Get AI Prompt directory data.
 *
 * Supports:  
 *
 * - Prompt category filtering
 * - AI tool/platform filtering
 * - Search across prompt metadata
 * - Prompt title
 * - Prompt content
 * - Short description
 * - Categories
 * - Tags
 * - AI platforms
 * - AI platform type
 * - Prompt variables
 * - Pagination
 */
function isago_get_ai_prompt_directory(
    $selected_category = '',
    $selected_platform = '',
    $search = '',
    $current_page = 1
) {

    global $wpdb;


    /*=====================================================
    SANITIZE VALUES
    =====================================================*/

    $selected_category =
        sanitize_key(
            $selected_category
        );


    $selected_platform =
        absint(
            $selected_platform
        );


    $search =
        sanitize_text_field(
            $search
        );


    $current_page =
        max(
            1,
            absint(
                $current_page
            )
        );  


    /*=====================================================
    PROMPTS PER PAGE
    =====================================================*/

    $prompts_per_page = 12;


    /*=====================================================
    PROMPT CATEGORIES
    =====================================================*/

    $prompt_categories =
        isago_get_ai_prompt_categories(
            array(

                'hide_empty' =>
                    false,

                'orderby' =>
                    'name',

                'order' =>
                    'ASC',

            )
        );


    /*=====================================================
    AI TOOLS / PLATFORMS
    =====================================================*/

    $prompt_platforms =
        get_terms(
            array(

                'taxonomy' =>
                    'ai_platform',

                'hide_empty' =>
                    false,

                'orderby' =>
                    'name',

                'order' =>
                    'ASC',

            )
        );


    if (
        is_wp_error(
            $prompt_platforms
        )
    ) {

        $prompt_platforms =
            array();

    }


    /*=====================================================
    AI PLATFORM TYPES
    =====================================================*/

    if (
        function_exists(
            'isago_get_ai_platform_categories'
        )
    ) {

        $platform_types =
            isago_get_ai_platform_categories();

    } else {

        $platform_types =
            array();

    }


    if (
        !is_array(
            $platform_types
        )
    ) {

        $platform_types =
            array();

    }


    /*=====================================================
    BASE QUERY
    =====================================================*/

    $query_args = array(

        'post_type' =>
            'ai_prompt',

        'post_status' =>
            'publish',

        'posts_per_page' =>
            $prompts_per_page,

        'paged' =>
            $current_page,

        'orderby' =>
            'date',

        'order' =>
            'DESC',

        'ignore_sticky_posts' =>
            true,

    );


    /*=====================================================
    TAXONOMY FILTERS
    =====================================================*/

    $tax_query =
        array(
            'relation' =>
                'AND'
        );


    /*=====================================================
    PROMPT CATEGORY
    =====================================================*/

    if (
        !empty(
            $selected_category
        )
    ) {

        $tax_query[] =
            array(

                'taxonomy' =>
                    'ai_prompt_category',

                'field' =>
                    'slug',

                'terms' =>
                    $selected_category,

                'include_children' =>
                    true,

                'operator' =>
                    'IN',

            );

    }


    /*=====================================================
    AI TOOL / PLATFORM
    =====================================================*/

    if (
        $selected_platform
    ) {

        $tax_query[] =
            array(

                'taxonomy' =>
                    'ai_platform',

                'field' =>
                    'term_id',

                'terms' =>
                    $selected_platform,

                'operator' =>
                    'IN',

            );

    }


    if (
        count(
            $tax_query
        ) > 1
    ) {

        $query_args['tax_query'] =
            $tax_query;

    }


    /*=====================================================
    ADVANCED SEARCH
    =====================================================*/

    if (
        !empty(
            $search
        )
    ) {

        $like =
            '%' .
            $wpdb->esc_like(
                $search
            ) .
            '%';


        /*
         * Search:
         *
         * - title
         * - content
         * - excerpt
         * - short description
         * - prompt variables
         * - category names
         * - tag names
         * - AI tool names
         * - AI platform type
         */

        $search_sql = "

            SELECT DISTINCT 
                p.ID

            FROM
                {$wpdb->posts} AS p

            LEFT JOIN
                {$wpdb->term_relationships} AS tr
                ON p.ID = tr.object_id

            LEFT JOIN
                {$wpdb->term_taxonomy} AS tt
                ON tr.term_taxonomy_id = tt.term_taxonomy_id

            LEFT JOIN
                {$wpdb->terms} AS t
                ON tt.term_id = t.term_id

            LEFT JOIN
                {$wpdb->termmeta} AS tm
                ON tt.term_id = tm.term_id

            LEFT JOIN
                {$wpdb->postmeta} AS short_desc
                ON p.ID = short_desc.post_id
                AND short_desc.meta_key =
                    '_isago_ai_prompt_short_description'

            LEFT JOIN
                {$wpdb->postmeta} AS variables
                ON p.ID = variables.post_id
                AND variables.meta_key =
                    '_isago_ai_prompt_variables'

            WHERE

                p.post_type = 'ai_prompt'

                AND p.post_status = 'publish'

                AND (

                    p.post_title LIKE %s

                    OR p.post_content LIKE %s

                    OR p.post_excerpt LIKE %s

                    OR short_desc.meta_value LIKE %s

                    OR variables.meta_value LIKE %s

                    OR (

                        tt.taxonomy IN (
                            'ai_prompt_category',
                            'post_tag',
                            'ai_platform'
                        )

                        AND t.name LIKE %s

                    )

                    OR (

                        tt.taxonomy = 'ai_platform'

                        AND tm.meta_key =
                            '_isago_ai_platform_category'

                        AND tm.meta_value LIKE %s

                    )

                )

        ";


        $search_ids =
            $wpdb->get_col(
                $wpdb->prepare(
                    $search_sql,
                    $like,
                    $like,
                    $like,
                    $like,
                    $like,
                    $like,
                    $like
                )
            );


        if (
            !empty(
                $search_ids
            )
        ) {

            $query_args['post__in'] =
                array_map(
                    'absint',
                    $search_ids
                );

        } else {

            /*
             * Force no results when
             * the search finds nothing.
             */

            $query_args['post__in'] =
                array(0);

        }

    }


    /*=====================================================
    QUERY
    =====================================================*/

    $prompt_query =
        new WP_Query(
            $query_args
        );


    /*=====================================================
    TOTAL
    =====================================================*/

    $total_prompts =
        (int)
        $prompt_query->found_posts;


    /*=====================================================
    RETURN
    =====================================================*/

    return array(

        'selected_category' =>
            $selected_category,

        'selected_platform' =>
            $selected_platform,

        'search' =>
            $search,

        'prompt_categories' =>
            $prompt_categories,

        'prompt_platforms' =>
            $prompt_platforms,

        'platform_types' =>
            $platform_types,

        'prompts' =>
            $prompt_query->posts,

        'prompt_query' =>
            $prompt_query,

        'total_prompts' =>
            $total_prompts,

        'prompts_per_page' =>
            $prompts_per_page,

        'current_page' =>
            $current_page,

    );

}




/*=========================================================
AI PROMPT ACCESS CHECK
=========================================================*/

/**
 * Determine whether the current user can access
 * the full content of an AI Prompt.
 *
 * Access rules:
 *
 * - Free prompt:
 *   Everyone can access.
 *
 * - Paid prompt:
 *   User must have an active entitlement.
 *
 * Payment verification and entitlement creation
 * are handled by the payment system.
 *
 * @param int $post_id 
 *
 * @return bool
 */
function isago_user_can_access_prompt(
    $post_id
) {

    $post_id =
        absint(
            $post_id
        );


    /*
     * Invalid prompt.
     */

    if (!$post_id) {

        return false;

    }


    /*
     * Make sure this is actually
     * an AI Prompt.
     */

    if (
        get_post_type(
            $post_id
        ) !== 'ai_prompt'
    ) {

        return false;

    }


    /*
     * Free prompts are public.
     */

    if (
        !isago_is_ai_prompt_paid(
            $post_id
        )
    ) {

        return true;

    }


    /*
     * Paid prompt.
     *
     * Access comes from the entitlement
     * system only.
     */

    if (
        function_exists(
            'isago_user_can_access_paid_prompt'
        )
    ) {

        return isago_user_can_access_paid_prompt(
            $post_id
        );

    }


    /*
     * Payment system unavailable.
     *
     * Fail closed.
     */

    return false;
}

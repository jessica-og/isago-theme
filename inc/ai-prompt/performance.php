<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ISAGO AI PROMPT PERFORMANCE
=========================================================*/

function isago_get_prompt_views_table_name()
{
    global $wpdb;
    return $wpdb->prefix . 'isago_prompt_views';
}

function isago_create_prompt_views_table()
{
    global $wpdb;

    $table_name = isago_get_prompt_views_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "
        CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            prompt_id bigint(20) unsigned NOT NULL,
            viewed_on date NOT NULL,
            visitor_key varchar(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            views bigint(20) unsigned NOT NULL DEFAULT 1,
            last_viewed_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY prompt_day_visitor (prompt_id, viewed_on, visitor_key),
            KEY prompt_id (prompt_id),
            KEY viewed_on (viewed_on),
            KEY prompt_viewed_on (prompt_id, viewed_on)
        ) {$charset_collate};
    ";

    dbDelta($sql);
}

function isago_maybe_install_prompt_views_database()
{
    $version = get_option('isago_prompt_views_db_version', '');

    if ($version !== '1.0.0') {
        isago_create_prompt_views_table();
        update_option('isago_prompt_views_db_version', '1.0.0');
    }
}

add_action('init', 'isago_maybe_install_prompt_views_database', 5);

function isago_prompt_performance_is_bot()
{
    $user_agent = isset($_SERVER['HTTP_USER_AGENT'])
        ? strtolower((string) $_SERVER['HTTP_USER_AGENT'])
        : '';

    if (!$user_agent) {
        return false;
    }

    $bots = array(
        'bot',
        'crawler',
        'spider',
        'slurp',
        'bingpreview',
        'facebookexternalhit',
        'whatsapp',
        'headless',
    );

    foreach ($bots as $bot) {
        if (strpos($user_agent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

function isago_prompt_performance_get_visitor_key()
{
    $cookie_name = 'isago_prompt_viewer';

    if (!empty($_COOKIE[$cookie_name])) {
        $value = sanitize_text_field(wp_unslash($_COOKIE[$cookie_name]));

        if (preg_match('/^[A-Za-z0-9]{20,64}$/', $value)) {
            return hash('sha256', $value . wp_salt('auth'));
        }
    }

    $raw = wp_generate_password(32, false, false);

    if (!headers_sent()) {
        setcookie(
            $cookie_name,
            $raw,
            array(
                'expires'  => time() + YEAR_IN_SECONDS,
                'path'     => COOKIEPATH ? COOKIEPATH : '/',
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            )
        );
    }

    return hash('sha256', $raw . wp_salt('auth'));
}

function isago_record_ai_prompt_view($prompt_id)
{
    global $wpdb;

    $prompt_id = absint($prompt_id);

    if (!$prompt_id || get_post_type($prompt_id) !== 'ai_prompt') {
        return;
    }

    if (is_admin() || is_preview() || isago_prompt_performance_is_bot()) {
        return;
    }

    if (is_user_logged_in() && current_user_can('manage_options')) {
        return;
    }

    $table_name = isago_get_prompt_views_table_name();
    $visitor_key = isago_prompt_performance_get_visitor_key();
    $viewed_on = wp_date('Y-m-d', null, wp_timezone());
    $now_gmt = current_time('mysql', true);
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;

    $sql = $wpdb->prepare(
        "INSERT INTO {$table_name}
            (prompt_id, viewed_on, visitor_key, user_id, views, last_viewed_at)
         VALUES (%d, %s, %s, %d, 1, %s)
         ON DUPLICATE KEY UPDATE
            views = views + 1,
            user_id = VALUES(user_id),
            last_viewed_at = VALUES(last_viewed_at)",
        $prompt_id,
        $viewed_on,
        $visitor_key,
        $user_id,
        $now_gmt
    );

    $wpdb->query($sql);
}

add_action('template_redirect', function () {
    if (is_singular('ai_prompt')) {
        isago_record_ai_prompt_view(get_queried_object_id());
    }
}, 20);

function isago_get_prompt_performance_rows($args = array())
{
    global $wpdb;

    $defaults = array(
        'orderby' => 'views',
        'order'   => 'ASC',
        'limit'   => 100,
        'offset'  => 0,
        'search'  => '',
        'status'  => 'all',
    );

    $args = wp_parse_args(
        $args,
        $defaults
    );

    $table  = isago_get_prompt_views_table_name();
    $orders = isago_get_orders_table_name();

    /*=====================================================
    ALLOWED SORT COLUMNS
    =====================================================*/

    $allowed_orderby = array(
        'views'          => 'views',
        'purchases'      => 'purchases',
        'conversion'     => 'conversion',
        'revenue'        => 'revenue',
        'last_viewed'    => 'last_viewed',
        'last_purchased' => 'last_purchased',
    );

    $orderby = isset(
        $allowed_orderby[$args['orderby']]
    )
        ? $allowed_orderby[$args['orderby']]
        : 'views';

    $order =
        strtoupper($args['order']) === 'DESC'
            ? 'DESC'
            : 'ASC';

    $limit = max(
        1,
        min(
            500,
            absint($args['limit'])
        )
    );

    $offset = max(
        0,
        absint($args['offset'])
    );

    /*=====================================================
    NORMALIZE SEARCH / STATUS
    =====================================================*/

    $search = sanitize_text_field(
        (string) $args['search']
    );

    $status = sanitize_key(
        (string) $args['status']
    );

    if (
        !in_array(
            $status,
            array(
                'all',
                'available',
                'scheduled',
                'retired',
            ),
            true
        )
    ) {
        $status = 'all';
    }

    /*=====================================================
    BASE WHERE
    =====================================================*/

    /*
     * Only published AI Prompts belong in the performance
     * watcher.
     *
     * This deliberately excludes:
     *
     * - auto-draft
     * - draft
     * - pending
     * - trash
     * - inherit
     *
     * A prompt must actually be published to be considered
     * part of the public performance dataset.
     */

    $where = "
        WHERE p.post_type = 'ai_prompt'
          AND p.post_status = 'publish'
    ";

    $params = array();

    /*=====================================================
    SEARCH
    =====================================================*/

    if ($search !== '') {

        $where .=
            ' AND p.post_title LIKE %s';

        $params[] =
            '%' .
            $wpdb->esc_like($search) .
            '%';
    }

    /*=====================================================
    RETIREMENT STATUS FILTER
    =====================================================*/

    if ($status === 'scheduled') {

        $where .= "
            AND pm_status.meta_value = 'scheduled'
        ";

    } elseif ($status === 'retired') {

        $where .= "
            AND pm_status.meta_value = 'retired'
        ";

    } elseif ($status === 'available') {

        $where .= "
            AND (
                pm_status.meta_value IS NULL
                OR pm_status.meta_value = 'available'
                OR pm_status.meta_value = ''
            )
        ";
    }

    /*=====================================================
    PERFORMANCE QUERY
    =====================================================*/

    $sql = "
        SELECT

            p.ID AS prompt_id,

            p.post_title AS title,

            COALESCE(
                v.views,
                0
            ) AS views,

            COALESCE(
                o.purchases,
                0
            ) AS purchases,

            COALESCE(
                o.revenue,
                0
            ) AS revenue,

            v.last_viewed,

            o.last_purchased,

            CASE

                WHEN COALESCE(v.views, 0) > 0

                THEN
                    (
                        COALESCE(
                            o.purchases,
                            0
                        )
                        /
                        v.views
                    ) * 100

                ELSE 0

            END AS conversion,

            COALESCE(
                pm_status.meta_value,
                'available'
            ) AS retirement_status,

            pm_removal.meta_value AS removal_at

        FROM {$wpdb->posts} p

        /*=================================================
        VIEW DATA
        =================================================*/

        LEFT JOIN (

            SELECT

                prompt_id,

                SUM(views) AS views,

                MAX(last_viewed_at) AS last_viewed

            FROM {$table}

            GROUP BY prompt_id

        ) v

            ON v.prompt_id = p.ID

        /*=================================================
        PURCHASE DATA
        =================================================*/

        LEFT JOIN (

            SELECT

                prompt_id,

                COUNT(*) AS purchases,

                SUM(amount) AS revenue,

                MAX(paid_at) AS last_purchased

            FROM {$orders}

            WHERE status = 'paid'

              AND refund_status = 'none'

            GROUP BY prompt_id

        ) o

            ON o.prompt_id = p.ID

        /*=================================================
        RETIREMENT STATUS
        =================================================*/

        LEFT JOIN {$wpdb->postmeta} pm_status

            ON pm_status.post_id = p.ID

           AND pm_status.meta_key =
               '_isago_ai_prompt_retirement_status'

        /*=================================================
        REMOVAL DATE
        =================================================*/

        LEFT JOIN {$wpdb->postmeta} pm_removal

            ON pm_removal.post_id = p.ID

           AND pm_removal.meta_key =
               '_isago_ai_prompt_removal_at'

        {$where}

        ORDER BY
            {$orderby} {$order},
            p.ID ASC

        LIMIT %d
        OFFSET %d
    ";

    $params[] = $limit;
    $params[] = $offset;

    $prepared =
        $wpdb->prepare(
            $sql,
            $params
        );

    $rows =
        $wpdb->get_results(
            $prepared
        );

    return is_array($rows)
        ? $rows
        : array();
}

function isago_get_prompt_performance_total($args = array())
{
    global $wpdb;

    $defaults = array(
        'search' => '',
        'status' => 'all',
    );

    $args = wp_parse_args(
        $args,
        $defaults
    );

    /*=====================================================
    NORMALIZE SEARCH / STATUS
    =====================================================*/

    $search = sanitize_text_field(
        (string) $args['search']
    );

    $status = sanitize_key(
        (string) $args['status']
    );

    if (
        !in_array(
            $status,
            array(
                'all',
                'available',
                'scheduled',
                'retired',
            ),
            true
        )
    ) {
        $status = 'all';
    }

    /*=====================================================
    ONLY PUBLISHED PROMPTS
    =====================================================*/

    /*
     * Keep this query consistent with
     * isago_get_prompt_performance_rows().
     *
     * Auto drafts and unpublished prompts must never
     * appear in the performance watcher.
     */

    $where = "
        WHERE post_type = 'ai_prompt'
          AND post_status = 'publish'
    ";

    $params = array();

    /*=====================================================
    SEARCH
    =====================================================*/

    if ($search !== '') {

        $where .=
            ' AND post_title LIKE %s';

        $params[] =
            '%' .
            $wpdb->esc_like($search) .
            '%';
    }

    /*=====================================================
    STATUS
    =====================================================*/

    if ($status === 'scheduled') {

        $where .= "
            AND ID IN (

                SELECT post_id

                FROM {$wpdb->postmeta}

                WHERE meta_key =
                    '_isago_ai_prompt_retirement_status'

                AND meta_value = 'scheduled'
            )
        ";

    } elseif ($status === 'retired') {

        $where .= "
            AND ID IN (

                SELECT post_id

                FROM {$wpdb->postmeta}

                WHERE meta_key =
                    '_isago_ai_prompt_retirement_status'

                AND meta_value = 'retired'
            )
        ";

    } elseif ($status === 'available') {

        $where .= "
            AND ID NOT IN (

                SELECT post_id

                FROM {$wpdb->postmeta}

                WHERE meta_key =
                    '_isago_ai_prompt_retirement_status'

                AND meta_value IN (
                    'scheduled',
                    'retired'
                )
            )
        ";
    }

    /*=====================================================
    COUNT
    =====================================================*/

    $sql = "
        SELECT COUNT(*)

        FROM {$wpdb->posts}

        {$where}
    ";

    if (!empty($params)) {

        $sql =
            $wpdb->prepare(
                $sql,
                $params
            );
    }

    return (int) $wpdb->get_var($sql);
}

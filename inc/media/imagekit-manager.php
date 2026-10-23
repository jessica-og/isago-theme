<?php
/**
 * Isago ImageKit Media Manager.
 *
 * File: inc/media/imagekit-manager.php
 * This file is already loaded by your media loader.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Keep the nonce action consistent with the existing Isago ImageKit AJAX code.
 */
if (!function_exists('isago_imagekit_manager_nonce_action')) {
    function isago_imagekit_manager_nonce_action() {
        return 'isago_imagekit_upload';
    }
}

/**
 * Register the page under Media.
 */
add_action('admin_menu', 'isago_register_imagekit_manager_page', 99);

function isago_register_imagekit_manager_page()
{
    if (!current_user_can('upload_files')) {
        return;
    }

  add_media_page(
    'Isago ImageKit',
    'Isago ImageKit',
    'upload_files',
    'isago-imagekit-manager',
    'isago_imagekit_manager_render_page'
);
}

/**
 * Render the manager interface.
 */
function isago_imagekit_manager_render_page() {
    if (!current_user_can('upload_files')) {
        wp_die(esc_html__('You do not have permission to manage media.', 'isago'));
    }
    ?>
    <div class="wrap isago-ik-manager">
        <h1>Isago ImageKit Media</h1>
        <p>Upload images directly to ImageKit and manage images registered in Isago's central media database.</p>

        <div class="isago-ik-upload-panel">
            <div>
                <label for="isago-ik-account"><strong>ImageKit account</strong></label>
            </div>
            <div>
                <select id="isago-ik-account" class="regular-text">
                    <option value="">Loading ImageKit accounts…</option>
                </select>
            </div>

<div>
    <label for="isago-ik-folder"><strong>Destination folder</strong></label>
</div>
<div>
   <select id="isago-ik-folder" class="regular-text">
    <option value="/Isago-Media/Site-Assets/">Site Assets</option>
    <option value="/Isago-Media/AI-News-Articles/">AI News Articles</option>
    <option value="/Isago-Media/Blog-Posts-Articles/">Blog Posts Articles</option>
    <option value="/Isago-Media/AI-Prompts/">AI Prompts</option>
    <option value="/Isago-Media/AI-Tools/">AI Tools</option>
</select>
    <p class="description">Choose where ImageKit should store the uploaded images.</p>
</div>

            <div>
                <label for="isago-ik-files"><strong>Select image(s)</strong></label>
            </div>
            <div>
                <input id="isago-ik-files" type="file" accept="image/*" multiple>
            </div>

            <div></div>
            <div>
                <button type="button" class="button button-primary" id="isago-ik-upload">Upload selected images</button>
                <p class="description">Images are uploaded to your selected ImageKit folder and registered in Isago's media database. They are not added to the WordPress uploads folder.</p>
            </div>
        </div>

        <div id="isago-ik-notice" class="notice" style="display:none"><p></p></div>
        <p id="isago-ik-progress" aria-live="polite"></p>

        <hr>
        <h2>Saved ImageKit images</h2>
       <p>Manage images stored in your selected ImageKit account. Deleting an image permanently removes it from ImageKit and removes its matching Isago media records and references. Content that uses the image may display a broken image afterward.</p>
  <div id="isago-ik-grid"><p>Loading images…</p></div>
<div id="isago-ik-pagination" class="isago-ik-pagination"></div>
    </div>

    <style>
        .isago-ik-upload-panel{display:grid;grid-template-columns:180px minmax(240px,560px);gap:12px 16px;align-items:start;max-width:900px;padding:20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;margin:18px 0}
        .isago-ik-upload-panel label{display:inline-block;padding-top:5px}
        .isago-ik-upload-panel .description{margin:10px 0 0}
        #isago-ik-progress{margin:12px 0;white-space:pre-line}
        /* Two-column ImageKit media grid */

/* Compact ImageKit media grid */
#isago-ik-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-top: 16px;
    align-items: start;
    max-width: 1200px;
}

#isago-ik-grid > p {
    grid-column: 1 / -1;
}

.isago-ik-card {
    min-width: 0;
    padding: 10px;
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 7px;
    box-sizing: border-box;
}

.isago-ik-card img {
    display: block;
    width: 100%;
    height: 115px;
    object-fit: contain;
    background: #f6f7f7;
    border-radius: 4px;
}

.isago-ik-card h3 {
    margin: 8px 0 5px;
    font-size: 12px;
    line-height: 1.45;
    overflow-wrap: anywhere;

    /* Limit long filenames to two lines */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    overflow: hidden;
}

.isago-ik-meta {
    margin-bottom: 8px;
    color: #646970;
    font-size: 11px;
    line-height: 1.5;
    overflow-wrap: anywhere;

    /* Keep file details compact */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    overflow: hidden;
}

.isago-ik-actions {
    display: flex;
    gap: 5px;
    align-items: center;
    flex-wrap: wrap;
}

.isago-ik-actions .button {
    min-height: 28px;
    height: auto;
    padding: 2px 8px;
    font-size: 11px;
    line-height: 1.5;
}

.isago-ik-delete {
    color: #b32d2e !important;
    border-color: #b32d2e !important;
}

.isago-ik-delete:disabled {
    opacity: .5;
}

.isago-ik-empty {
    grid-column: 1 / -1;
    padding: 16px;
    background: #fff;
    border: 1px dashed #c3c4c7;
}

/* Pagination */
.isago-ik-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin: 18px 0;
}

.isago-ik-pagination:empty {
    display: none;
}

.isago-ik-pagination .button.current-page {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
    pointer-events: none;
}

.isago-ik-page-info {
    margin: 0 8px;
    color: #646970;
}

/* Tablet */
@media (max-width: 1000px) {
    #isago-ik-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* Mobile */
@media (max-width: 600px) {
    #isago-ik-grid {
        grid-template-columns: 1fr;
    }

    .isago-ik-card img {
        height: 140px;
    }
}

       
      
    </style>
    <?php
}

/**
 * Enqueue only on this manager page.
 *
 * IMPORTANT: Do not enqueue this same script handle in admin.php as well.
 */

add_action('admin_enqueue_scripts', 'isago_imagekit_manager_enqueue', 20);
add_action('admin_notices', function () {
    if (
        isset($_GET['page']) &&
        sanitize_key(wp_unslash($_GET['page'])) === 'isago-imagekit-manager'
    ) {
        echo '<div class="notice notice-info"><p>';
        echo 'Isago ImageKit manager PHP file is loaded.';
        echo '</p></div>';
    }
});

function isago_imagekit_manager_enqueue($hook_suffix) {

    // Only load on the Isago ImageKit manager page.
    if (
        !isset($_GET['page']) ||
        sanitize_key(wp_unslash($_GET['page'])) !== 'isago-imagekit-manager'
    ) {
        return;
    }

    $relative_path = '/assets/js/admin/imagekit-manager.js';

    // Resolve the actual file and URL using the active theme.
    $script_path = get_theme_file_path($relative_path);
    $script_url  = get_theme_file_uri($relative_path);

    // Log a useful error if the file cannot be found.
    if (!file_exists($script_path)) {
        error_log(
            'Isago ImageKit Manager: JavaScript file not found at: ' .
            $script_path
        );

        add_action('admin_notices', function () use ($script_path) {
            echo '<div class="notice notice-error"><p>';
            echo esc_html(
                'Isago ImageKit Manager: JavaScript file not found at: ' .
                $script_path
            );
            echo '</p></div>';
        });

        return;
    }

    wp_enqueue_script(
        'isago-imagekit-manager',
        $script_url,
        [],
        (string) filemtime($script_path),
        true
    );

    $accounts_for_js = [];

    if (function_exists('isago_get_imagekit_accounts')) {
        $accounts = (array) isago_get_imagekit_accounts();

        foreach ($accounts as $account_id => $account) {
            if (!is_array($account)) {
                continue;
            }

            $account_id = sanitize_key((string) $account_id);

            if (!$account_id) {
                continue;
            }

            $account_name = '';

            foreach (['name', 'label', 'account_name', 'title'] as $key) {
                if (!empty($account[$key]) && is_scalar($account[$key])) {
                    $account_name = (string) $account[$key];
                    break;
                }
            }

            $accounts_for_js[$account_id] = [
                'name' => sanitize_text_field(
                    $account_name ?: $account_id
                ),
            ];
        }
    }

    wp_localize_script(
        'isago-imagekit-manager',
        'isagoImageKitManager',
        [
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce(
                isago_imagekit_manager_nonce_action()
            ),
            'accounts' => $accounts_for_js,
            'strings'  => [
                'confirmDelete' =>
                    'Permanently delete this image from ImageKit and Isago’s database? This cannot be undone.',
                'empty' => 'No saved ImageKit images found.',
            ],
        ]
    );
}


/**
 * List ImageKit image records.
 */
add_action(
    'wp_ajax_isago_imagekit_manager_list',
    'isago_imagekit_manager_ajax_list'
);

function isago_imagekit_manager_ajax_list() {
    if (!current_user_can('upload_files')) {
        wp_send_json_error(
            ['message' => 'You do not have permission to view media.'],
            403
        );
    }

    check_ajax_referer(isago_imagekit_manager_nonce_action(), 'nonce');

    if (
        !function_exists('isago_media_table') ||
        !function_exists('isago_media_reference_count')
    ) {
        wp_send_json_error(
            ['message' => 'Isago central media functions are unavailable.'],
            500
        );
    }

    global $wpdb;

    $table = isago_media_table();

    // Pagination settings.
    $page = isset($_POST['page'])
        ? max(1, absint($_POST['page']))
        : 1;

    $per_page = 12;
    $offset = ($page - 1) * $per_page;

    // Count matching images so we can calculate the total pages.
    $total_items = $wpdb->get_var(
        "SELECT COUNT(*)
         FROM {$table}
         WHERE source = 'imagekit'
         AND media_type = 'image'"
    );

    if ($total_items === null && $wpdb->last_error) {
        error_log(
            'Isago ImageKit manager count error: ' . $wpdb->last_error
        );

        wp_send_json_error(
            ['message' => 'Could not count saved ImageKit images. Check the PHP error log.'],
            500
        );
    }

    $total_items = absint($total_items);
    $total_pages = (int) ceil($total_items / $per_page);

    // Fetch only the images needed for this page.
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                id,
                account_id,
                file_id,
                file_path,
                url,
                thumbnail_url,
                width,
                height,
                file_size,
                original_name
             FROM {$table}
             WHERE source = 'imagekit'
             AND media_type = 'image'
             ORDER BY id DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    if ($rows === null && $wpdb->last_error) {
        error_log(
            'Isago ImageKit manager list error: ' . $wpdb->last_error
        );

        wp_send_json_error(
            ['message' => 'Could not read the Isago media table. Check the PHP error log.'],
            500
        );
    }

    $items = [];

    foreach ((array) $rows as $row) {
        $media_id = absint($row->id);

        $items[] = [
            'id'              => $media_id,
            'account_id'      => sanitize_key((string) $row->account_id),
            'file_id'         => (string) $row->file_id,
            'file_path'       => (string) $row->file_path,
            'url'             => esc_url_raw($row->url),
            'thumbnail_url'   => esc_url_raw(
                $row->thumbnail_url ?: $row->url
            ),
            'width'           => absint($row->width),
            'height'          => absint($row->height),
            'file_size'       => absint($row->file_size),
            'original_name'   => sanitize_text_field($row->original_name),
            'reference_count' => absint(
                isago_media_reference_count($media_id)
            ),
        ];
    }

    wp_send_json_success([
        'items'       => $items,
        'pagination'  => [
            'current_page' => $page,
            'per_page'     => $per_page,
            'total_items'  => $total_items,
            'total_pages'  => $total_pages,
        ],
    ]);
}




add_action(
    'wp_ajax_isago_imagekit_manager_delete_imagekit_file',
    'isago_imagekit_manager_ajax_delete_imagekit_file'
);

function isago_imagekit_manager_ajax_delete_imagekit_file() {
    // Match the nonce used by the rest of the ImageKit manager.
    if (!current_user_can('upload_files')) {
        wp_send_json_error(
            array('message' => 'You do not have permission to delete media.'),
            403
        );
    }

    check_ajax_referer(isago_imagekit_manager_nonce_action(), 'nonce');

    $account_id = isset($_POST['account_id'])
        ? sanitize_key(wp_unslash($_POST['account_id']))
        : '';

    $file_id = isset($_POST['file_id'])
        ? sanitize_text_field(wp_unslash($_POST['file_id']))
        : '';

    if ($account_id === '' || $file_id === '') {
        wp_send_json_error(
            array('message' => 'The ImageKit account ID or file ID is missing.'),
            400
        );
    }

    if (
        !function_exists('isago_get_imagekit_account') ||
        !function_exists('isago_media_table')
    ) {
        wp_send_json_error(
            array('message' => 'Required Isago ImageKit or database functions are unavailable.'),
            500
        );
    }

    $account = isago_get_imagekit_account($account_id);

    if (!is_array($account) || empty($account['private_key'])) {
        wp_send_json_error(
            array('message' => 'The selected ImageKit account has no valid private API key.'),
            400
        );
    }

    /*
     * STEP 1: Delete the file from ImageKit.
     * The private key remains on the server and is never sent to JavaScript.
     */
    $api_url = 'https://api.imagekit.io/v1/files/' . rawurlencode($file_id);

    $response = wp_remote_request(
        $api_url,
        array(
            'method'  => 'DELETE',
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode(
                    $account['private_key'] . ':'
                ),
                'Accept' => 'application/json',
            ),
        )
    );

    if (is_wp_error($response)) {
        error_log(
            'Isago ImageKit delete connection error: ' .
            $response->get_error_message()
        );

        wp_send_json_error(
            array(
                'message' => 'Could not contact ImageKit. ' .
                    $response->get_error_message(),
            ),
            502
        );
    }

    $status = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $api_error = json_decode($response_body, true);

    if ($status < 200 || $status >= 300) {
        $message = is_array($api_error) && !empty($api_error['message'])
            ? sanitize_text_field($api_error['message'])
            : 'ImageKit rejected the deletion request (HTTP ' . $status . ').';

        error_log(
            'Isago ImageKit delete failed. HTTP ' . $status .
            '. Response: ' . substr($response_body, 0, 1000)
        );

        wp_send_json_error(
            array('message' => $message),
            502
        );
    }

    /*
     * STEP 2: Remove matching Isago media records and their references.
     * This also works when an ImageKit file has no Isago record.
     */
    global $wpdb;

    $media_table = isago_media_table();
    $refs_table = $wpdb->prefix . 'isago_media_refs';

    // Find every matching record for this account and ImageKit file.
    $media_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT id FROM {$media_table}
             WHERE account_id = %s AND file_id = %s",
            $account_id,
            $file_id
        )
    );

    if ($wpdb->last_error) {
        error_log(
            'Isago media lookup failed after ImageKit deletion: ' .
            $wpdb->last_error
        );

        wp_send_json_error(
            array(
                'message' => 'ImageKit deleted the file, but Isago could not look up its database records. Check the PHP error log.',
            ),
            500
        );
    }

    foreach ((array) $media_ids as $media_id) {
        $media_id = absint($media_id);

        if (!$media_id) {
            continue;
        }

        // Remove all reference rows belonging to this media record.
        $refs_deleted = $wpdb->delete(
            $refs_table,
            array('media_id' => $media_id),
            array('%d')
        );

        if ($refs_deleted === false) {
            error_log(
                'Isago media reference cleanup failed for media ID ' .
                $media_id . ': ' . $wpdb->last_error
            );

            wp_send_json_error(
                array(
                    'message' => 'ImageKit deleted the file, but Isago could not remove its database references. Check the PHP error log.',
                ),
                500
            );
        }

        // Now remove the central media record.
        $media_deleted = $wpdb->delete(
            $media_table,
            array('id' => $media_id),
            array('%d')
        );

        if ($media_deleted === false || $media_deleted !== 1) {
            error_log(
                'Isago media record cleanup failed for media ID ' .
                $media_id . ': ' . $wpdb->last_error
            );

            wp_send_json_error(
                array(
                    'message' => 'ImageKit deleted the file, but Isago could not remove its media record. Check the PHP error log.',
                ),
                500
            );
        }
    }

    wp_send_json_success(
        array(
            'message' => 'Image deleted from ImageKit and all matching Isago media records and references were removed.',
            'file_id' => $file_id,
            'records_removed' => count((array) $media_ids),
        )
    );
}




/**
 * Browse images directly from ImageKit by folder.
 *
 * This is separate from the Isago database listing endpoint.
 */
add_action(
    'wp_ajax_isago_imagekit_manager_browse_folder',
    'isago_imagekit_manager_ajax_browse_folder'
);


function isago_imagekit_manager_ajax_browse_folder() {
    if (!current_user_can('upload_files')) {
        wp_send_json_error(
            ['message' => 'You do not have permission to view media.'],
            403
        );
    }

    check_ajax_referer(
        isago_imagekit_manager_nonce_action(),
        'nonce'
    );

    if (!function_exists('isago_get_imagekit_account')) {
        wp_send_json_error(
            ['message' => 'ImageKit account configuration is unavailable.'],
            500
        );
    }

    $account_id = isset($_POST['account_id'])
        ? sanitize_key(wp_unslash($_POST['account_id']))
        : '';

    $page = isset($_POST['page'])
        ? max(1, absint($_POST['page']))
        : 1;

    $per_page = 12;
    $api_limit = $per_page + 1;
    $skip = ($page - 1) * $per_page;

    $allowed_folders = [
        '/Isago-Media/Site-Assets/',
        '/Isago-Media/AI-News-Articles/',
        '/Isago-Media/Blog-Posts-Articles/',
        '/Isago-Media/AI-Prompts/',
        '/Isago-Media/AI-Tools/',
    ];

    $folder = isset($_POST['folder'])
        ? sanitize_text_field(wp_unslash($_POST['folder']))
        : '';

    if (!in_array($folder, $allowed_folders, true)) {
        wp_send_json_error(
            ['message' => 'Invalid ImageKit folder selected.'],
            400
        );
    }

    $account = isago_get_imagekit_account($account_id);

    if (
        !is_array($account) ||
        empty($account['private_key'])
    ) {
        wp_send_json_error(
            ['message' => 'The selected ImageKit account is missing its private API key.'],
            400
        );
    }

    $api_url = 'https://api.imagekit.io/v1/files';

    $headers = [
        'Authorization' => 'Basic ' . base64_encode(
            $account['private_key'] . ':'
        ),
        'Accept' => 'application/json',
    ];

    /*
     * First request: ask ImageKit to filter by folder.
     */
    $response = wp_remote_get(
        add_query_arg(
            [
                'path'     => $folder,
                'type'     => 'file',
                'fileType' => 'image',
                'sort'     => 'DESC_CREATED',
                'limit'    => $api_limit,
                'skip'     => $skip,
            ],
            $api_url
        ),
        [
            'timeout' => 25,
            'headers' => $headers,
        ]
    );

    if (is_wp_error($response)) {
        error_log(
            'Isago ImageKit folder request failed: ' .
            $response->get_error_message()
        );

        wp_send_json_error(
            ['message' => 'Could not connect to ImageKit. Check your connection and PHP error log.'],
            502
        );
    }

    $status = wp_remote_retrieve_response_code($response);
    $raw_body = wp_remote_retrieve_body($response);
    $body = json_decode($raw_body, true);

    if ($status < 200 || $status >= 300 || !is_array($body)) {
        $api_message = is_array($body)
            ? ($body['message'] ?? '')
            : '';

        error_log(
            'Isago ImageKit API error. HTTP ' .
            $status . '. Response: ' .
            substr($raw_body, 0, 1500)
        );

        wp_send_json_error(
            [
                'message' => $status === 401
                    ? 'ImageKit rejected the private API key. Check your account configuration.'
                    : ($status === 403
                        ? 'ImageKit denied access. Check the API key permissions.'
                        : 'ImageKit could not list files. ' .
                            ($api_message ?: 'Check the PHP error log.')),
            ],
            502
        );
    }

    /*
     * The list-files API should return a JSON array.
     * Reject an unexpected object instead of silently showing
     * an empty folder.
     */
    if (
        !empty($body) &&
        array_keys($body) !== range(0, count($body) - 1)
    ) {
        error_log(
            'Isago ImageKit returned an unexpected list response: ' .
            substr($raw_body, 0, 1500)
        );

        wp_send_json_error(
            ['message' => 'ImageKit returned an unexpected response. Check the PHP error log.'],
            502
        );
    }

    /*
     * If folder filtering returns no images, check the actual
     * file paths from ImageKit. This helps diagnose path-filter
     * differences without exposing private credentials.
     */
    if (empty($body) && $page === 1) {
        $fallback_response = wp_remote_get(
            add_query_arg(
                [
                    'type'     => 'file',
                    'fileType' => 'image',
                    'sort'     => 'DESC_CREATED',
                    'limit'    => 1000,
                    'skip'     => 0,
                ],
                $api_url
            ),
            [
                'timeout' => 25,
                'headers' => $headers,
            ]
        );

        if (
            !is_wp_error($fallback_response) &&
            wp_remote_retrieve_response_code($fallback_response) >= 200 &&
            wp_remote_retrieve_response_code($fallback_response) < 300
        ) {
            $fallback_body = json_decode(
                wp_remote_retrieve_body($fallback_response),
                true
            );

            if (is_array($fallback_body)) {
                $normalized_folder = untrailingslashit($folder);

                $matching_files = [];

                foreach ($fallback_body as $file) {
                    if (!is_array($file)) {
                        continue;
                    }

                    $file_path = isset($file['filePath'])
                        ? (string) $file['filePath']
                        : '';

                    /*
                     * Match the selected folder and its descendants,
                     * but do not match similarly named sibling folders.
                     */
                    if (
                        strpos(
                            $file_path,
                            $normalized_folder . '/'
                        ) === 0
                    ) {
                        $matching_files[] = $file;
                    }
                }

                $body = array_slice(
                    $matching_files,
                    $skip,
                    $api_limit
                );
            }
        }
    }

    $has_next = count($body) > $per_page;
    $body = array_slice($body, 0, $per_page);

    $items = [];

    foreach ($body as $file) {
        if (!is_array($file)) {
            continue;
        }

        $url = isset($file['url'])
            ? esc_url_raw($file['url'])
            : '';

        if (!$url) {
            continue;
        }

        $items[] = [
            'file_id' => sanitize_text_field(
                $file['fileId'] ?? ''
            ),
            'file_path' => sanitize_text_field(
                $file['filePath'] ?? ''
            ),
            'url' => $url,
            'thumbnail_url' => esc_url_raw(
                $file['thumbnail'] ??
                $file['thumbnailUrl'] ??
                $url
            ),
            'width' => absint($file['width'] ?? 0),
            'height' => absint($file['height'] ?? 0),
            'file_size' => absint($file['size'] ?? 0),
            'original_name' => sanitize_text_field(
                $file['name'] ?? ''
            ),
        ];
    }

    wp_send_json_success([
        'items' => $items,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $per_page,
            'has_next' => $has_next,
        ],
        'folder' => $folder,
        'empty_message' => empty($items)
            ? 'No images were found in this ImageKit folder. Check that the images are stored in this account and that their file paths begin with ' . $folder
            : '',
    ]);
}





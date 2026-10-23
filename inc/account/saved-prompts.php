<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO SAVED PROMPTS / COLLECTIONS
=========================================================*/


/**
 * ---------------------------------------------------------
 * TABLE NAMES
 * ---------------------------------------------------------
 */

function isago_get_saved_prompts_table_name()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_saved_prompts';
}


function isago_get_prompt_collections_table_name()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_prompt_collections';
}


function isago_get_prompt_collection_items_table_name()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_prompt_collection_items';
}


/**
 * ---------------------------------------------------------
 * CREATE TABLES
 * ---------------------------------------------------------
 *
 * Creates the saved prompt / collection tables once.
 */

function isago_create_saved_prompt_tables()
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';


    $charset_collate =
        $wpdb->get_charset_collate();


    $saved_prompts_table =
        isago_get_saved_prompts_table_name();


    $collections_table =
        isago_get_prompt_collections_table_name();


    $collection_items_table =
        isago_get_prompt_collection_items_table_name();


    dbDelta(

        "CREATE TABLE {$saved_prompts_table} (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            user_id BIGINT UNSIGNED NOT NULL,

            prompt_id BIGINT UNSIGNED NOT NULL,

            created_at DATETIME NOT NULL,

            PRIMARY KEY (id),

            UNIQUE KEY user_prompt (
                user_id,
                prompt_id
            ),

            KEY user_id (
                user_id
            ),

            KEY prompt_id (
                prompt_id
            )

        ) {$charset_collate};"


        .

        "CREATE TABLE {$collections_table} (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            user_id BIGINT UNSIGNED NOT NULL,

            name VARCHAR(191) NOT NULL,

            created_at DATETIME NOT NULL,

            updated_at DATETIME NOT NULL,

            PRIMARY KEY (id),

            KEY user_id (
                user_id
            )

        ) {$charset_collate};"


        .

        "CREATE TABLE {$collection_items_table} (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            collection_id BIGINT UNSIGNED NOT NULL,

            prompt_id BIGINT UNSIGNED NOT NULL,

            created_at DATETIME NOT NULL,

            PRIMARY KEY (id),

            UNIQUE KEY collection_prompt (
                collection_id,
                prompt_id
            ),

            KEY collection_id (
                collection_id
            ),

            KEY prompt_id (
                prompt_id
            )

        ) {$charset_collate};"

    );


    update_option(
        'isago_saved_prompt_tables_version',
        '1.0.0'
    );
}


add_action(
    'after_switch_theme',
    'isago_create_saved_prompt_tables'
);


/**
 * ---------------------------------------------------------
 * SAFETY NET
 * ---------------------------------------------------------
 *
 * Makes sure the tables also exist if the theme was already
 * active before this feature was installed.
 */

function isago_maybe_create_saved_prompt_tables()
{
    if (
        get_option(
            'isago_saved_prompt_tables_version'
        ) !== '1.0.0'
    ) {

        isago_create_saved_prompt_tables();

    }
}


add_action(
    'init',
    'isago_maybe_create_saved_prompt_tables',
    5
);


/**
 * ---------------------------------------------------------
 * CHECK SAVED PROMPT
 * ---------------------------------------------------------
 */

function isago_is_prompt_saved(
    $user_id,
    $prompt_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id
        ||
        !$prompt_id
    ) {

        return false;

    }


    static $saved_cache = array();


    if (
        !isset(
            $saved_cache[$user_id]
        )
    ) {

        $table =
            isago_get_saved_prompts_table_name();


        $saved_prompt_ids =
            $wpdb->get_col(

                $wpdb->prepare(

                    "
                    SELECT prompt_id
                    FROM {$table}
                    WHERE user_id = %d
                    ",

                    $user_id

                )

            );


        $saved_cache[$user_id] =
            array_map(
                'absint',
                $saved_prompt_ids
            );

    }


    return in_array(
        $prompt_id,
        $saved_cache[$user_id],
        true
    );
}


/**
 * ---------------------------------------------------------
 * GET SAVED PROMPTS
 * ---------------------------------------------------------
 *
 * Returns saved prompts that are NOT currently inside
 * a collection owned by this user.
 *
 * Prompts placed into a collection are displayed inside
 * that collection instead of the main Saved Prompts list.
 */

function isago_get_saved_prompts(
    $user_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    if (!$user_id) {

        return array();

    }


    $saved_table =
        isago_get_saved_prompts_table_name();


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $collections_table =
        isago_get_prompt_collections_table_name();


    $results =
        $wpdb->get_results(

            $wpdb->prepare(

                "
                SELECT

                    s.id,
                    s.user_id,
                    s.prompt_id,
                    s.created_at,

                    p.post_title,
                    p.post_status

                FROM {$saved_table} s

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = s.prompt_id

                WHERE s.user_id = %d

                    AND p.post_type = 'ai_prompt'

                    AND p.post_status = 'publish'

                    AND NOT EXISTS (

                        SELECT 1

                        FROM {$items_table} ci

                        INNER JOIN {$collections_table} c
                            ON c.id = ci.collection_id

                        WHERE ci.prompt_id = s.prompt_id

                            AND c.user_id = s.user_id

                    )

                ORDER BY
                    s.created_at DESC,
                    s.id DESC
                ",

                $user_id

            )

        );


    if (
        empty($results)
        ||
        !is_array($results)
    ) {

        return array();

    }


    return $results;
}


/**
 * ---------------------------------------------------------
 * SAVE PROMPT
 * ---------------------------------------------------------
 */

function isago_save_prompt(
    $user_id,
    $prompt_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id
        ||
        !$prompt_id
    ) {

        return false;

    }


    /*
     * Only AI Prompts can be saved.
     */

    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return false;

    }


    /*
     * Only published prompts can be saved.
     */

    if (
        get_post_status(
            $prompt_id
        ) !== 'publish'
    ) {

        return false;

    }


    $table =
        isago_get_saved_prompts_table_name();


    $inserted =
        $wpdb->query(

            $wpdb->prepare(

                "
                INSERT IGNORE INTO {$table}
                (
                    user_id,
                    prompt_id,
                    created_at
                )
                VALUES
                (
                    %d,
                    %d,
                    %s
                )
                ",

                $user_id,

                $prompt_id,

                current_time(
                    'mysql',
                    true
                )

            )

        );


    return (
        $inserted !== false
    );
}


/**
 * ---------------------------------------------------------
 * REMOVE SAVED PROMPT
 * ---------------------------------------------------------
 *
 * Removes the prompt from saved prompts and from all
 * collections belonging to this user.
 */

function isago_unsave_prompt(
    $user_id,
    $prompt_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id
        ||
        !$prompt_id
    ) {

        return false;

    }


    $saved_table =
        isago_get_saved_prompts_table_name();


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $collections_table =
        isago_get_prompt_collections_table_name();


    /*
     * Remove from Saved Prompts.
     */

    $deleted_saved =
        $wpdb->delete(

            $saved_table,

            array(
                'user_id' =>
                    $user_id,

                'prompt_id' =>
                    $prompt_id,
            ),

            array(
                '%d',
                '%d',
            )

        );


    /*
     * Remove from every collection owned
     * by this user.
     */

    $collection_ids =
        $wpdb->get_col(

            $wpdb->prepare(

                "
                SELECT ci.collection_id

                FROM {$items_table} ci

                INNER JOIN {$collections_table} c
                    ON c.id = ci.collection_id

                WHERE ci.prompt_id = %d

                    AND c.user_id = %d
                ",

                $prompt_id,
                $user_id

            )

        );


    if (
        !empty($collection_ids)
    ) {

        foreach (
            $collection_ids
            as $collection_id
        ) {

            $wpdb->delete(

                $items_table,

                array(
                    'collection_id' =>
                        absint(
                            $collection_id
                        ),

                    'prompt_id' =>
                        $prompt_id,
                ),

                array(
                    '%d',
                    '%d',
                )

            );

        }

    }


    /*
     * If nothing existed in Saved Prompts, still return
     * true when the database operation itself succeeded.
     */

    return (
        $deleted_saved !== false
    );
}

/**
 * ---------------------------------------------------------
 * CREATE COLLECTION
 * ---------------------------------------------------------
 */

function isago_create_prompt_collection(
    $user_id,
    $name
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $name =
        is_string(
            $name
        )
            ? trim(
                sanitize_text_field(
                    $name
                )
            )
            : '';


    if (
        !$user_id
        ||
        $name === ''
    ) {

        return 0;

    }


    /*
     * Keep collection names reasonable.
     */

    $name =
        mb_substr(
            $name,
            0,
            191
        );


    $table =
        isago_get_prompt_collections_table_name();


    $now =
        current_time(
            'mysql',
            true
        );


    $inserted =
        $wpdb->insert(

            $table,

            array(

                'user_id' =>
                    $user_id,

                'name' =>
                    $name,

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now,

            ),

            array(
                '%d',
                '%s',
                '%s',
                '%s',
            )

        );


    if (!$inserted) {

        return 0;

    }


    return (int)
        $wpdb->insert_id;
}


/**
 * ---------------------------------------------------------
 * GET USER COLLECTIONS
 * ---------------------------------------------------------
 */

function isago_get_prompt_collections(
    $user_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    if (!$user_id) {

        return array();

    }


    $collections_table =
        isago_get_prompt_collections_table_name();


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $results =
        $wpdb->get_results(

            $wpdb->prepare(

                "
                SELECT

                    c.id,
                    c.user_id,
                    c.name,
                    c.created_at,
                    c.updated_at,

                    COUNT(i.id) AS prompt_count

                FROM {$collections_table} c

                LEFT JOIN {$items_table} i
                    ON i.collection_id = c.id

                WHERE c.user_id = %d

                GROUP BY
                    c.id

                ORDER BY
                    c.updated_at DESC,
                    c.id DESC
                ",

                $user_id

            )

        );


    if (
        empty($results)
        ||
        !is_array($results)
    ) {

        return array();

    }


    return $results;
}




/**
 * ---------------------------------------------------------
 * GET PROMPTS INSIDE COLLECTION
 * ---------------------------------------------------------
 *
 * Returns the published AI Prompts belonging to a specific
 * collection owned by the current user.
 */

function isago_get_prompt_collection_prompts(
    $user_id,
    $collection_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $collection_id =
        absint(
            $collection_id
        );


    if (
        !$user_id
        ||
        !$collection_id
    ) {

        return array();

    }


    $collections_table =
        isago_get_prompt_collections_table_name();


    $items_table =
        isago_get_prompt_collection_items_table_name();


    /*
     * Verify that the collection belongs to
     * the current user.
     */

    $collection_exists =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id

                FROM {$collections_table}

                WHERE id = %d

                    AND user_id = %d

                LIMIT 1
                ",

                $collection_id,
                $user_id

            )

        );


    if (!$collection_exists) {

        return array();

    }


    /*
     * Get prompts inside the collection.
     */

    $results =
        $wpdb->get_results(

            $wpdb->prepare(

                "
                SELECT

                    ci.id,
                    ci.collection_id,
                    ci.prompt_id,
                    ci.created_at,

                    p.post_title,
                    p.post_status

                FROM {$items_table} ci

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = ci.prompt_id

                WHERE ci.collection_id = %d

                    AND p.post_type = 'ai_prompt'

                    AND p.post_status = 'publish'

                ORDER BY
                    ci.created_at DESC,
                    ci.id DESC
                ",

                $collection_id

            )

        );


    if (
        empty($results)
        ||
        !is_array($results)
    ) {

        return array();

    }


    return $results;
}






/**
 * ---------------------------------------------------------
 * ADD PROMPT TO COLLECTION
 * ---------------------------------------------------------
 */

function isago_add_prompt_to_collection(
    $user_id,
    $collection_id,
    $prompt_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $collection_id =
        absint(
            $collection_id
        );


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id
        ||
        !$collection_id
        ||
        !$prompt_id
    ) {

        return false;

    }


    /*
     * The prompt must exist and be an AI Prompt.
     */

    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return false;

    }


    if (
        get_post_status(
            $prompt_id
        ) !== 'publish'
    ) {

        return false;

    }


    /*
     * Verify collection ownership.
     */

    $collections_table =
        isago_get_prompt_collections_table_name();


    $owner =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id
                FROM {$collections_table}
                WHERE id = %d
                    AND user_id = %d
                LIMIT 1
                ",

                $collection_id,
                $user_id

            )

        );


    if (!$owner) {

        return false;

    }


    /*
     * A prompt placed in a collection is automatically
     * a saved prompt.
     */

    isago_save_prompt(
        $user_id,
        $prompt_id
    );


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $inserted =
        $wpdb->query(

            $wpdb->prepare(

                "
                INSERT IGNORE INTO {$items_table}
                (
                    collection_id,
                    prompt_id,
                    created_at
                )
                VALUES
                (
                    %d,
                    %d,
                    %s
                )
                ",

                $collection_id,
                $prompt_id,
                current_time(
                    'mysql',
                    true
                )

            )

        );


    /*
     * Update collection timestamp.
     */

    $wpdb->update(

        $collections_table,

        array(
            'updated_at' =>
                current_time(
                    'mysql',
                    true
                ),
        ),

        array(
            'id' =>
                $collection_id,

            'user_id' =>
                $user_id,
        ),

        array(
            '%s',
        ),

        array(
            '%d',
            '%d',
        )

    );


    return (
          $inserted > 0
    );
}


/**
 * ---------------------------------------------------------
 * REMOVE PROMPT FROM COLLECTION
 * ---------------------------------------------------------
 */

function isago_remove_prompt_from_collection(
    $user_id,
    $collection_id,
    $prompt_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $collection_id =
        absint(
            $collection_id
        );


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$user_id
        ||
        !$collection_id
        ||
        !$prompt_id
    ) {

        return false;

    }


    $collections_table =
        isago_get_prompt_collections_table_name();


    $owner =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id
                FROM {$collections_table}
                WHERE id = %d
                    AND user_id = %d
                LIMIT 1
                ",

                $collection_id,
                $user_id

            )

        );


    if (!$owner) {

        return false;

    }


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $deleted =
        $wpdb->delete(

            $items_table,

            array(
                'collection_id' =>
                    $collection_id,

                'prompt_id' =>
                    $prompt_id,
            ),

            array(
                '%d',
                '%d',
            )

        );


    return (
        $deleted !== false
    );
}


/**
 * ---------------------------------------------------------
 * DELETE COLLECTION
 * ---------------------------------------------------------
 */

function isago_delete_prompt_collection(
    $user_id,
    $collection_id
) {

    global $wpdb;


    $user_id =
        absint(
            $user_id
        );


    $collection_id =
        absint(
            $collection_id
        );


    if (
        !$user_id
        ||
        !$collection_id
    ) {

        return false;

    }


    $collections_table =
        isago_get_prompt_collections_table_name();


    $owner =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id
                FROM {$collections_table}
                WHERE id = %d
                    AND user_id = %d
                LIMIT 1
                ",

                $collection_id,
                $user_id

            )

        );


    if (!$owner) {

        return false;

    }


    $items_table =
        isago_get_prompt_collection_items_table_name();


    $wpdb->delete(

        $items_table,

        array(
            'collection_id' =>
                $collection_id,
        ),

        array(
            '%d',
        )

    );


    $deleted =
        $wpdb->delete(

            $collections_table,

            array(
                'id' =>
                    $collection_id,

                'user_id' =>
                    $user_id,
            ),

            array(
                '%d',
                '%d',
            )

        );


    return (
        $deleted !== false
    );
}


/**
 * ---------------------------------------------------------
 * CLEAR PROMPT FROM ALL SAVED DATA
 * ---------------------------------------------------------
 *
 * This is called when an AI Prompt is permanently deleted.
 */

function isago_clear_deleted_prompt_saved_data(
    $prompt_id
) {

    global $wpdb;


    $prompt_id =
        absint(
            $prompt_id
        );


    if (!$prompt_id) {

        return;

    }


    $saved_table =
        isago_get_saved_prompts_table_name();


    $items_table =
        isago_get_prompt_collection_items_table_name();


    /*
     * Remove the prompt from favourites/saved prompts.
     */

    $wpdb->delete(

        $saved_table,

        array(
            'prompt_id' =>
                $prompt_id,
        ),

        array(
            '%d',
        )

    );


    /*
     * Remove the prompt from every collection.
     *
     * Collections themselves are intentionally preserved.
     */

    $wpdb->delete(

        $items_table,

        array(
            'prompt_id' =>
                $prompt_id,
        ),

        array(
            '%d',
        )

    );
}


/**
 * ---------------------------------------------------------
 * PERMANENT PROMPT DELETE CLEANUP
 * ---------------------------------------------------------
 */

function isago_saved_prompts_after_delete(
    $post_id,
    $post
) {

    if (
        !$post instanceof WP_Post
    ) {

        return;

    }


    if (
        $post->post_type !== 'ai_prompt'
    ) {

        return;

    }


    isago_clear_deleted_prompt_saved_data(
        $post_id
    );
}


add_action(
    'after_delete_post',
    'isago_saved_prompts_after_delete',
    20,
    2
);


/**
 * ---------------------------------------------------------
 * AJAX — SAVE PROMPT
 * ---------------------------------------------------------
 */

function isago_ajax_save_prompt()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in to save prompts.',
            ),
            401
        );

    }


    $user_id =
        get_current_user_id();


    $prompt_id =
        isset(
            $_POST['prompt_id']
        )
            ? absint(
                $_POST['prompt_id']
            )
            : 0;


    $collection_id =
        isset(
            $_POST['collection_id']
        )
            ? absint(
                $_POST['collection_id']
            )
            : 0;


    if (!$prompt_id) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid prompt.',
            ),
            400
        );

    }


    /*
     * Make sure this is a published AI Prompt.
     */

    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
        ||
        get_post_status(
            $prompt_id
        ) !== 'publish'
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'This prompt cannot be saved.',
            ),
            400
        );

    }


    /*
     * Save the prompt.
     */

    $saved =
        isago_save_prompt(
            $user_id,
            $prompt_id
        );


    if (!$saved) {

        /*
         * The prompt may already be saved.
         *
         * Check whether it actually exists before
         * treating this as an error.
         */

        if (
            !isago_is_prompt_saved(
                $user_id,
                $prompt_id
            )
        ) {

            wp_send_json_error(
                array(
                    'message' =>
                        'Could not save the prompt.',
                ),
                500
            );

        }

    }


    /*
     * If a collection was selected, add the prompt
     * to that collection.
     */

    if ($collection_id) {

        $added =
            isago_add_prompt_to_collection(
                $user_id,
                $collection_id,
                $prompt_id
            );


        if (!$added) {

            global $wpdb;


            $items_table =
                isago_get_prompt_collection_items_table_name();


            $existing =
                $wpdb->get_var(

                    $wpdb->prepare(

                        "
                        SELECT id
                        FROM {$items_table}
                        WHERE collection_id = %d
                            AND prompt_id = %d
                        LIMIT 1
                        ",

                        $collection_id,
                        $prompt_id

                    )

                );


            if (!$existing) {

                wp_send_json_error(
                    array(
                        'message' =>
                            'The prompt was saved, but could not be added to the selected collection.',
                    ),
                    500
                );

            }

        }

    }


    wp_send_json_success(
        array(
            'saved' =>
                true,

            'collection_id' =>
                $collection_id,

            'message' =>
                $collection_id
                    ? 'Prompt saved and added to the collection.'
                    : 'Prompt saved.',
        )
    );
}


add_action(
    'wp_ajax_isago_save_prompt',
    'isago_ajax_save_prompt'
);


/**
 * ---------------------------------------------------------
 * AJAX — UNSAVE PROMPT
 * ---------------------------------------------------------
 */

function isago_ajax_unsave_prompt()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $prompt_id =
        isset(
            $_POST['prompt_id']
        )
            ? absint(
                $_POST['prompt_id']
            )
            : 0;


    if (!$prompt_id) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid prompt.',
            ),
            400
        );

    }


    $removed =
        isago_unsave_prompt(
            get_current_user_id(),
            $prompt_id
        );


    if (!$removed) {

        wp_send_json_error(
            array(
                'message' =>
                    'Could not remove the prompt.',
            ),
            500
        );

    }


    wp_send_json_success(
        array(
            'saved' =>
                false,

            'message' =>
                'Prompt removed from saved prompts.',
        )
    );
}
add_action(
    'wp_ajax_isago_unsave_prompt',
    'isago_ajax_unsave_prompt'
);

/**
 * ---------------------------------------------------------
 * AJAX — GET USER COLLECTIONS
 * ---------------------------------------------------------
 */

function isago_ajax_get_prompt_collections()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $collections =
        isago_get_prompt_collections(
            get_current_user_id()
        );


    $data =
        array();


    foreach (
        $collections
        as $collection
    ) {

        $data[] =
            array(
                'id' =>
                    (int)
                    $collection->id,

                'name' =>
                    $collection->name,

                'prompt_count' =>
                    (int)
                    $collection->prompt_count,
            );

    }


    wp_send_json_success(
        array(
            'collections' =>
                $data,
        )
    );
}


add_action(
    'wp_ajax_isago_get_prompt_collections',
    'isago_ajax_get_prompt_collections'
);





/**
 * ---------------------------------------------------------
 * AJAX — GET COLLECTION PROMPTS
 * ---------------------------------------------------------
 */

function isago_ajax_get_prompt_collection_prompts()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $collection_id =
        isset(
            $_POST['collection_id']
        )
            ? absint(
                $_POST['collection_id']
            )
            : 0;


    if (!$collection_id) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid collection.',
            ),
            400
        );

    }


    $user_id =
        get_current_user_id();


    /*
     * Get the user's collections first so we can
     * safely verify ownership.
     */

    $collections =
        isago_get_prompt_collections(
            $user_id
        );


    $selected_collection =
        null;


    foreach (
        $collections
        as $collection
    ) {

        if (
            (int)
            $collection->id
            ===
            $collection_id
        ) {

            $selected_collection =
                $collection;

            break;

        }

    }


    if (!$selected_collection) {

        wp_send_json_error(
            array(
                'message' =>
                    'Collection not found.',
            ),
            404
        );

    }


    $prompts =
        isago_get_prompt_collection_prompts(
            $user_id,
            $collection_id
        );


    $data =
        array();


    foreach (
        $prompts
        as $prompt
    ) {

        $prompt_id =
            absint(
                $prompt->prompt_id
            );


        $data[] =
            array(
                'id' =>
                    $prompt_id,

                'title' =>
                    get_the_title(
                        $prompt_id
                    ),

                'url' =>
                    get_permalink(
                        $prompt_id
                    ),

                'created_at' =>
                    mysql2date(
                        get_option(
                            'date_format'
                        ),
                        $prompt->created_at
                    ),
            );

    }


    wp_send_json_success(
        array(
            'collection' =>
                array(
                    'id' =>
                        (int)
                        $selected_collection->id,

                    'name' =>
                        $selected_collection->name,

                    'prompt_count' =>
                        count(
                            $data
                        ),
                ),

            'prompts' =>
                $data,
        )
    );
}


add_action(
    'wp_ajax_isago_get_prompt_collection_prompts',
    'isago_ajax_get_prompt_collection_prompts'
);





/**
 * ---------------------------------------------------------
 * AJAX — CREATE COLLECTION
 * ---------------------------------------------------------
 */

function isago_ajax_create_prompt_collection()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $name =
        isset(
            $_POST['name']
        )
            ? wp_unslash(
                $_POST['name']
            )
            : '';


    $collection_id =
        isago_create_prompt_collection(
            get_current_user_id(),
            $name
        );


    if (!$collection_id) {

        wp_send_json_error(
            array(
                'message' =>
                    'Could not create the collection.',
            ),
            400
        );

    }


    wp_send_json_success(
        array(
            'collection_id' =>
                $collection_id,

            'message' =>
                'Collection created.',
        )
    );
}


add_action(
    'wp_ajax_isago_create_prompt_collection',
    'isago_ajax_create_prompt_collection'
);


/**
 * ---------------------------------------------------------
 * AJAX — ADD PROMPT TO COLLECTION
 * ---------------------------------------------------------
 */

function isago_ajax_add_prompt_to_collection()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $prompt_id =
        isset(
            $_POST['prompt_id']
        )
            ? absint(
                $_POST['prompt_id']
            )
            : 0;


    $collection_id =
        isset(
            $_POST['collection_id']
        )
            ? absint(
                $_POST['collection_id']
            )
            : 0;


    if (
        !$prompt_id
        ||
        !$collection_id
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Invalid collection or prompt.',
            ),
            400
        );

    }


    $success =
        isago_add_prompt_to_collection(
            get_current_user_id(),
            $collection_id,
            $prompt_id
        );


    /*
     * A return value of false means the prompt was already
     * inside the collection or the operation failed.
     *
     * Check whether it is already there so we can return
     * a useful response to JavaScript.
     */

    if (!$success) {

        global $wpdb;


        $items_table =
            isago_get_prompt_collection_items_table_name();


        $existing =
            $wpdb->get_var(

                $wpdb->prepare(

                    "
                    SELECT id
                    FROM {$items_table}
                    WHERE collection_id = %d
                        AND prompt_id = %d
                    LIMIT 1
                    ",

                    $collection_id,
                    $prompt_id

                )

            );


        if ($existing) {

            wp_send_json_success(
                array(
                    'added' =>
                        false,

                    'already_added' =>
                        true,

                    'message' =>
                        'Prompt is already in this collection.',
                )
            );

        }


        wp_send_json_error(
            array(
                'message' =>
                    'Could not add the prompt to the collection.',
            ),
            400
        );

    }


    wp_send_json_success(
        array(
            'added' =>
                true,

            'already_added' =>
                false,

            'message' =>
                'Prompt added to collection.',
        )
    );
}


add_action(
    'wp_ajax_isago_add_prompt_to_collection',
    'isago_ajax_add_prompt_to_collection'
);


/**
 * ---------------------------------------------------------
 * AJAX — REMOVE PROMPT FROM COLLECTION
 * ---------------------------------------------------------
 */

function isago_ajax_remove_prompt_from_collection()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $prompt_id =
        isset(
            $_POST['prompt_id']
        )
            ? absint(
                $_POST['prompt_id']
            )
            : 0;


    $collection_id =
        isset(
            $_POST['collection_id']
        )
            ? absint(
                $_POST['collection_id']
            )
            : 0;


    $success =
        isago_remove_prompt_from_collection(
            get_current_user_id(),
            $collection_id,
            $prompt_id
        );


    if (!$success) {

        wp_send_json_error(
            array(
                'message' =>
                    'Could not remove the prompt from the collection.',
            ),
            400
        );

    }


    wp_send_json_success(
        array(
            'message' =>
                'Prompt removed from collection.',
        )
    );
}


add_action(
    'wp_ajax_isago_remove_prompt_from_collection',
    'isago_ajax_remove_prompt_from_collection'
);


/**
 * ---------------------------------------------------------
 * AJAX — DELETE COLLECTION
 * ---------------------------------------------------------
 */

function isago_ajax_delete_prompt_collection()
{
    check_ajax_referer(
        'isago_saved_prompts',
        'nonce'
    );


    if (
        !is_user_logged_in()
    ) {

        wp_send_json_error(
            array(
                'message' =>
                    'Please sign in.',
            ),
            401
        );

    }


    $collection_id =
        isset(
            $_POST['collection_id']
        )
            ? absint(
                $_POST['collection_id']
            )
            : 0;


    $success =
        isago_delete_prompt_collection(
            get_current_user_id(),
            $collection_id
        );


    if (!$success) {

        wp_send_json_error(
            array(
                'message' =>
                    'Could not delete the collection.',
            ),
            400
        );

    }


    wp_send_json_success(
        array(
            'message' =>
                'Collection deleted.',
        )
    );
}


add_action(
    'wp_ajax_isago_delete_prompt_collection',
    'isago_ajax_delete_prompt_collection'
);
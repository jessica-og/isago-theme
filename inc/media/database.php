<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO MEDIA DATABASE
 * ---------------------------------------------------------
 *
 * Creates the database tables used by the Isago Media System.
 *
 * Table 1:
 *     {$wpdb->prefix}isago_media
 *
 * Stores the actual media asset.
 *
 * Table 2:
 *     {$wpdb->prefix}isago_media_refs
 *
 * Stores the places where that asset is being used.
 *
 * This separation allows the same ImageKit file to be
 * safely referenced by multiple Isago objects.
 */


/**
 * ---------------------------------------------------------
 * DATABASE VERSION
 * ---------------------------------------------------------
 */

function isago_media_database_version()
{
    return '1.0.0';
}


/**
 * ---------------------------------------------------------
 * TABLE NAMES
 * ---------------------------------------------------------
 */

function isago_media_table()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_media';
}


function isago_media_refs_table()
{
    global $wpdb;

    return $wpdb->prefix . 'isago_media_refs';
}


/**
 * ---------------------------------------------------------
 * CREATE / UPDATE TABLES
 * ---------------------------------------------------------
 */

function isago_install_media_database()
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate =
        $wpdb->get_charset_collate();


    /*
     * -----------------------------------------------------
     * MEDIA TABLE
     * -----------------------------------------------------
     *
     * One row represents one physical media asset.
     *
     * source:
     *     wordpress
     *     imagekit
     *
     * For ImageKit:
     *     account_id
     *     file_id
     *     file_path
     *
     * are retained so the asset can always be resolved
     * back to the correct ImageKit account.
     */

    $media_table =
        isago_media_table();


    $sql_media = "
        CREATE TABLE {$media_table} (

            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

            source varchar(20) NOT NULL DEFAULT 'wordpress',

            media_type varchar(20) NOT NULL DEFAULT 'image',

            mime_type varchar(100) NOT NULL DEFAULT '',

            attachment_id bigint(20) unsigned NOT NULL DEFAULT 0,

            account_id varchar(100) NOT NULL DEFAULT '',

            file_id varchar(255) NOT NULL DEFAULT '',

            file_path text NOT NULL,

            url text NOT NULL,

            thumbnail_url text NOT NULL,

            width int(11) unsigned NOT NULL DEFAULT 0,

            height int(11) unsigned NOT NULL DEFAULT 0,

            file_size bigint(20) unsigned NOT NULL DEFAULT 0,

            original_name text NOT NULL,

            created_by bigint(20) unsigned NOT NULL DEFAULT 0,

            created_at datetime NOT NULL,

            updated_at datetime NOT NULL,

            PRIMARY KEY (id),

            KEY source (source),

            KEY media_type (media_type),

            KEY attachment_id (attachment_id),

            KEY account_id (account_id),

            KEY file_id (file_id),

            KEY created_by (created_by)

        ) {$charset_collate};
    ";


    dbDelta($sql_media);


    /*
     * -----------------------------------------------------
     * MEDIA REFERENCES TABLE
     * -----------------------------------------------------
     *
     * This table answers:
     *
     * "Where is this media being used?"
     *
     * Examples:
     *
     * post / 123 / featured_image
     *
     * term / 45 / ai_platform_image
     *
     * theme / 0 / about_hero_image
     *
     * This allows safe deletion.
     */

    $refs_table =
        isago_media_refs_table();


    $sql_refs = "
        CREATE TABLE {$refs_table} (

            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

            media_id bigint(20) unsigned NOT NULL,

            object_type varchar(50) NOT NULL,

            object_id bigint(20) unsigned NOT NULL DEFAULT 0,

            field_key varchar(100) NOT NULL,

            created_at datetime NOT NULL,

            updated_at datetime NOT NULL,

            PRIMARY KEY (id),

            UNIQUE KEY media_reference (
                media_id,
                object_type,
                object_id,
                field_key
            ),

            KEY media_id (media_id),

            KEY object_lookup (
                object_type,
                object_id
            ),

            KEY field_key (field_key)

        ) {$charset_collate};
    ";


    dbDelta($sql_refs);


    /*
     * -----------------------------------------------------
     * STORE DATABASE VERSION
     * -----------------------------------------------------
     */

    update_option(
        'isago_media_database_version',
        isago_media_database_version()
    );
}


/**
 * ---------------------------------------------------------
 * DATABASE INSTALL CHECK
 * ---------------------------------------------------------
 *
 * Runs only when the stored database version does not
 * match the current version.
 *
 * This means we don't run dbDelta() unnecessarily on
 * every request.
 */

function isago_maybe_install_media_database()
{
    $installed_version =
        get_option(
            'isago_media_database_version',
            ''
        );


    if (
        $installed_version !==
        isago_media_database_version()
    ) {

        isago_install_media_database();

    }
}


add_action(
    'admin_init',
    'isago_maybe_install_media_database'
);

<?php

if (!defined('ABSPATH')) {
    exit;
}  
//add_action('init', 'isago_create_contact_table');
add_action('after_switch_theme', 'isago_create_contact_table');

function isago_create_contact_table(){

global $wpdb;

$table = $wpdb->prefix . 'isago_contacts';

$charset = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS $table (
id BIGINT(20) NOT NULL AUTO_INCREMENT,
name VARCHAR(200),
email VARCHAR(200),
subject VARCHAR(255),
message LONGTEXT,
is_read TINYINT(1) DEFAULT 0,
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) $charset;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

}
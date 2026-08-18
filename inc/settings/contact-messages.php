<?php

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'isago_register_contact_menu');




function isago_register_contact_menu(){

global $wpdb;
$table = $wpdb->prefix . 'isago_contacts';

/* COUNT UNREAD */
$count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE is_read = 0");

$title = 'Contact Messages';

if($count > 0){
    $title .= " <span class='awaiting-mod'>{$count}</span>";
}

add_menu_page(
    'Contact Messages',
    $title,
    'manage_options',
    'isago-contacts',
    'isago_contacts_page',
    'dashicons-email',
    25
);

}
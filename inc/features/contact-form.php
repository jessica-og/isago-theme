<?php

if (!defined('ABSPATH')) {
    exit;
}

/* CONNECT FORM */
add_action('admin_post_nopriv_isago_contact', 'isago_handle_contact');
add_action('admin_post_isago_contact', 'isago_handle_contact');

function isago_handle_contact(){

/* SECURITY */
if (!isset($_POST['isago_nonce']) || !wp_verify_nonce($_POST['isago_nonce'], 'isago_contact')) {
    wp_die('Security check failed');
}

global $wpdb;
$table = $wpdb->prefix . 'isago_contacts';

/* SANITIZE */
$name    = sanitize_text_field($_POST['name']);
$email   = sanitize_email($_POST['email']);
$subject = sanitize_text_field($_POST['subject']);
$message = sanitize_textarea_field($_POST['message']);

/* SINGLE INSERT ONLY */
$insert = $wpdb->insert($table, [
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'created_at' => current_time('mysql')
]);

/* ERROR CHECK */
if(!$insert){
    wp_die('DB INSERT FAILED: ' . $wpdb->last_error);
}

/* EMAIL */
$to = get_option('admin_email');

$headers = ['Content-Type: text/html; charset=UTF-8'];

$body = "
<h3>New Contact Message</h3>
<p><strong>Name:</strong> $name</p>
<p><strong>Email:</strong> $email</p>
<p><strong>Subject:</strong> $subject</p>
<p><strong>Message:</strong><br>$message</p>
";

wp_mail($to, $subject, $body, $headers);

/* REDIRECT */
$redirect = add_query_arg('success','1', wp_get_referer());
wp_redirect($redirect . '#contact-form');
exit;
}
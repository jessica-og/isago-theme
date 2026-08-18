<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_contacts_page(){

global $wpdb;
$table = $wpdb->prefix . 'isago_contacts';

$messages = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

?>

<div class="wrap">
<h1>Contact Messages</h1>

<table class="widefat fixed striped">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Subject</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($messages as $msg): ?>

<tr>
<td><?php echo esc_html($msg->name); ?></td>
<td><?php echo esc_html($msg->email); ?></td>
<td>
<?php if(!$msg->is_read): ?>
<strong style="color:#d63638;">●</strong>
<?php endif; ?>

<?php echo esc_html($msg->subject); ?>
</td>
<td><?php echo esc_html($msg->created_at); ?></td>
<td>
<a href="?page=isago-contacts&view=<?php echo $msg->id; ?>">View</a> |
<a href="?page=isago-contacts&delete=<?php echo $msg->id; ?>" onclick="return confirm('Delete?')">Delete</a>
</td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php
/* VIEW SINGLE MESSAGE */
if(isset($_GET['view'])){

$id = intval($_GET['view']);

/* ✅ MARK AS READ */
$wpdb->update(
    $table,
    ['is_read' => 1],
    ['id' => $id]
);

$msg = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM $table WHERE id=%d",$id)
);


echo '
<div class="isago-message-card">

<div class="isago-message-header">
    <h2>' . esc_html($msg->subject) . '</h2>
    <span class="isago-message-date">' . esc_html($msg->created_at) . '</span>
</div>

<div class="isago-message-meta">
    <div>
        <strong>From:</strong> ' . esc_html($msg->name) . '
    </div>
    <div>
        <strong>Email:</strong> 
        <a href="mailto:' . esc_attr($msg->email) . '">' . esc_html($msg->email) . '</a>
    </div>
</div>

<div class="isago-message-body">
    ' . nl2br(esc_html($msg->message)) . '
</div>

<div class="isago-message-actions">
    <a href="mailto:' . esc_attr($msg->email) . '" class="button button-primary">
        Reply
    </a>
    <a href="?page=isago-contacts" class="button">
        Back
    </a>
</div>

</div>
';
}

/* DELETE */
if(isset($_GET['delete'])){

$id = intval($_GET['delete']);
$wpdb->delete($table, ['id'=>$id]);

echo "<div class='updated'><p>Deleted</p></div>";
}

?>

</div>

<?php
}
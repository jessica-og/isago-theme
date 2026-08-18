<?php

if (!defined('ABSPATH')) {
    exit;
}

/*--------------------------------------------------------------
Import / Export
--------------------------------------------------------------*/

add_action(
    'admin_menu',
    'isago_import_export_menu'
);

function isago_import_export_menu()
{

    add_submenu_page(

        'isago-home',

        'Import / Export',

        'Import / Export',

        'manage_options',

        'isago-import-export',

        'isago_import_export_page'

    );

}

function isago_import_export_page()
{
?>

<div class="wrap isago-admin">

<h1>

Backup & Restore

</h1>

<p>

Export or restore your complete Isago theme configuration.

</p>

<div class="isago-admin-grid">

<div class="isago-card">

<h2>

Export Settings

</h2>

<p>

Download all Isago settings.

</p>

<form method="post">

<?php wp_nonce_field(
'isago_export'
); ?>

<input
type="hidden"
name="isago_export"
value="1">

<?php submit_button(
'Export JSON',
'primary',
'',
false
); ?>

</form>

</div>

<div class="isago-card">

<h2>

Import Settings

</h2>

<p>

Upload a previously exported JSON file.

</p>

<form

method="post"

enctype="multipart/form-data">

<?php wp_nonce_field(
'isago_import'
); ?>

<input

type="file"

name="settings"

accept=".json"

required>

<br><br>

<?php submit_button(
'Import',
'secondary',
'',
false
); ?>

</form>

</div>
<div class="isago-card">

<h2>

Reset Theme

</h2>

<p>

Restore Isago to its defaults.

</p>

<form method="post">

<?php wp_nonce_field(
'isago_reset'
); ?>

<select

name="reset_target">

<option value="homepage">

Homepage Builder

</option>

<option value="theme">

Theme Settings

</option>

<option value="ads">

Advertisements

</option>

<option value="seo">

SEO

</option>

<option value="performance">

Performance

</option>

<option value="all">

Everything

</option>

</select>

<br><br>

<?php submit_button(
'Reset',
'delete',
'',
false
); ?>

</form>

</div>


</div>

</div>

<?php

}

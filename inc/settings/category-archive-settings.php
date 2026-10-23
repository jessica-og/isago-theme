<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_get_category_archive_settings()
{

$options = isago_get_option(
'isago_category_archive_settings'
);

return [

'featured' => !empty($options['featured']),

'toolbar' => !empty($options['toolbar']),

'breadcrumb' => !empty($options['breadcrumb']),

'sidebar' => !empty($options['sidebar']),

'posts' => absint(
$options['posts'] ?? 9
),

'columns' => absint(  
$options['columns'] ?? 3
),

'filter' => sanitize_key(
$options['filter'] ?? 'latest'
),

'sort' => sanitize_key(
$options['sort'] ?? 'latest'
)

];

}
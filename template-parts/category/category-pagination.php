<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = get_query_var('isago_pagination_query');

if (!$query) {
    return;
}

isago_pagination($query);
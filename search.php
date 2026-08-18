<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();

get_template_part(
    'template-parts/search/search-page'
);

get_footer();
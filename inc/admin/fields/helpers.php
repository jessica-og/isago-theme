<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_text_field($args = [])
{
    $name  = $args['name'] ?? '';
    $value = $args['value'] ?? '';
    $class = $args['class'] ?? 'regular-text';
    $type  = $args['type'] ?? 'text';

    printf(
        '<input type="%s" class="%s" name="%s" value="%s">',
        esc_attr($type),
        esc_attr($class),
        esc_attr($name),
        esc_attr($value)
    );
}
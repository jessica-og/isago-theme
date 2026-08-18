<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'wp_head',
    'isago_theme_css'
);

function isago_theme_css()
{

    $theme = isago_get_theme_settings();

?>

<style>

:root{

--isago-primary:<?php echo esc_html($theme['site_accent']); ?>;

--isago-secondary:<?php echo esc_html($theme['site_accent_alt']); ?>;

--isago-container:<?php echo absint($theme['container_width']); ?>px;

--isago-font: "<?php echo esc_html( $theme['font_family']); ?>", sans-serif;

--isago-bg:#ffffff;

--isago-card:#ffffff;

--isago-nav:#00d4f5;

--isago-text:#111827;

--isago-card-text :#6b7280;

--isago-muted:#6b7280;

--isago-date:#6b7280;

--isago-border:#ececec;

--isago-hover-bg:#f7fbfc;

--isago-scrolled-bg:rgba(255,255,255,.96);

--isago-footer-bg: #0f172a;

;
}

body.dark-mode{

--isago-bg:#0f172a;

--isago-card:#172033;

--isago-nav:#172033;

--isago-text:#f8fafc;

--isago-muted:#cbd5e1;

--isago-date:#858585;

--isago-card-text :#6b7280;

--isago-border:#263244;

--isago-hover-bg:#263244;

--isago-scrolled-bg:#0f172a;

--isago-footer-bg: #172033;
}

body{

background:var(--isago-bg);

color:var(--isago-text);

transition:

background .35s,

color .35s;

}

</style>

<?php

}
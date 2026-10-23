<?php

if ( ! defined( 'ABSPATH' ) ) {
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

/*=========================================================
ISAGO THEME VARIABLES
=========================================================*/

:root {

    --isago-primary:
        <?php echo esc_html( $theme['site_accent'] ); ?>;

    --isago-secondary:
        <?php echo esc_html( $theme['site_accent_alt'] ); ?>;

    --isago-container:
        <?php echo absint( $theme['container_width'] ); ?>px;

    --isago-font:
        "<?php echo esc_html( $theme['font_family'] ); ?>",
        sans-serif;
--isago-rgb-border: rgba(0, 212, 245, 0.4);
--isago-box-shadow: 2px 2px 12px #0002;

    /*=====================================================
    LIGHT THEME COLORS
    =====================================================*/

    --isago-bg:
        #fafafa;

    --isago-card:
        #fbfbfb;

    --isago-nav:
        #00d4f5;

    --isago-text:
        #111827;

    --isago-heading:
        #102027;

    --isago-post-text:
        #111827;

    --isago-card-text:
        #6b7280;

    --isago-muted:
        #6b7280;

    --isago-date:
        #858585;

    --isago-light-text:
        #26343B;

    --isago-border:
        #ececec;

    --isago-hover-bg:
        #e8ecec;

    --isago-scrolled-bg:
        rgba(255,255,255,.96);


    /*=====================================================
    BUTTONS & LINKS
    =====================================================*/

    --isago-button-bg:
        #00D4F5;

    --isago-button-text:
        #062027;

    --isago-link:
        #009FBE;

    --isago-link-hover:
        #007E98;


    /*=====================================================
    FOOTER
    =====================================================*/

    --isago-footer-bg:
        #0f172a;

    --isago-workspace : #f4fcfe;
    --isago-workspace-body: #e2eef1;
       --isago-workspace-header: #e4f4f7;

    
    /*=====================================================
    LIGHT THEME BACKGROUND
    =====================================================*/

    --isago-body-bg:
        radial-gradient(
            circle at 78% 42%,
            rgba(0,212,245,.13),
            transparent 28%
        ),
        radial-gradient(
            circle at 10% 70%,
            rgba(54,254,251,.09),
            transparent 30%
        ),
        linear-gradient(
            180deg,
            #ffffff 0%,
            #f7fbfc 100%
        );


    /*=====================================================
    LIGHT CARD GRADIENT
    =====================================================*/

    --isago-card-gradient:
        linear-gradient(
            145deg,
            rgba(255,255,255,.94),
            rgba(241,249,251,.84)
        );

  
    /*=====================================================
    LIGHT THEME GLOWS
    =====================================================*/
    --isago-footer-glow-cyan-rgb: 54, 254, 251;
    --isago-footer-glow-blue-rgb: 0, 212, 245;

    --isago-footer-glow-opacity: 0.07;
    --isago-footer-glow-medium-opacity: 0.10;
    --isago-footer-glow-soft-opacity: 0.055;
    --isago-footer-glow-faint-opacity: 0.025;
}


/*=========================================================
DARK THEME
=========================================================*/

html.isago-dark-preload,
body.dark-mode {
  --isago-workspace : #181817;
  --isago-workspace-body: #252521;
   --isago-workspace-header: #292927;

    --isago-bg:
        #0f172a;

    --isago-card:
        #172033;

    --isago-nav:
        #172033;

    --isago-text:
        #ebfbfe;

    --isago-heading:
        #F1F8FA;

    --isago-post-text:
        #c1d4da;

    --isago-muted:
        #c1d4da;

    --isago-date:
        #89a1a7;

    --isago-light-text:
        #d8dcde;

    --isago-card-text:
        #a7b8be;

    --isago-border: 
        #263244;

    --isago-hover-bg:
        #263244;

    --isago-scrolled-bg:
        #0f172a;


    /*=====================================================
    LINKS
    =====================================================*/

    --isago-link:
        #36E8FF;

    --isago-link-hover:
        #7AF2FF;


    /*=====================================================
    BUTTONS
    =====================================================*/

    --isago-button-bg:
        #00D4F5;

    --isago-button-text:
        #03171C;


    /*=====================================================
    FOOTER
    =====================================================*/

    --isago-footer-bg:
        #172033;


    /*=====================================================
    DARK THEME BACKGROUND
    =====================================================*/

    --isago-body-bg:
        radial-gradient(
            circle at 78% 42%,
            rgba(0,212,245,.10),
            transparent 28%
        ),
        radial-gradient(
            circle at 10% 70%,
            rgba(54,254,251,.07),
            transparent 30%
        ),
        linear-gradient(
            180deg,
            #0f172a 0%,
            #101b2f 100%
        );


    /*=====================================================
    DARK CARD GRADIENT
    =====================================================*/

    --isago-card-gradient:
        linear-gradient(
            145deg,
            rgba(54,254,251,.07),
            rgba(0,212,245,.10)
        );


    /*=====================================================
    DARK THEME GLOWS
    =====================================================*/
   --isago-footer-glow-cyan-rgb: 54, 254, 251;
    --isago-footer-glow-blue-rgb: 0, 212, 245;

    --isago-footer-glow-opacity: 0.2;
    --isago-footer-glow-medium-opacity: 0.16;
    --isago-footer-glow-soft-opacity: 0.08;
    --isago-footer-glow-faint-opacity: 0.05;
  
}


/*=========================================================
HTML
=========================================================*/

html {

    background:
        var(--isago-body-bg);

}


/*=========================================================
BODY
=========================================================*/

body {

    background:
        var(--isago-body-bg);

    color:
        var(--isago-text);

    transition:
        background .35s ease,
        color .35s ease;

}


/*=========================================================
DARK MODE HTML PRELOAD
=========================================================*/

html.isago-dark-preload {

    background:
        var(--isago-body-bg);

}


/*=========================================================
DARK MODE BODY
=========================================================*/

body.dark-mode {

    background:
        var(--isago-body-bg);

}

</style>

<?php
}
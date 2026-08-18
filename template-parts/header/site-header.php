<?php
/**
 * Premium Editorial Header
 * Theme: Isago
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Theme Settings
|--------------------------------------------------------------------------
*/

$theme = isago_get_theme_settings();
$header_ad = isago_get_ad('header');
?>
<div class="isago-header-wrapper">

<header class="isago-header">

      <?php if (
    !empty($header_ad['enable']) &&
    !empty($header_ad['image'])
) : ?>

    <div class="header-ad-label">
        Advertisement
    </div>

    <div class="header-ad-box">

        <div class="header-banner">

            <?php if (!empty($header_ad['url'])) : ?>

                <a
                    href="<?php echo esc_url($header_ad['url']); ?>"
                    <?php
                    if (!empty($header_ad['blank'])) {
                        echo 'target="_blank" rel="noopener noreferrer"';
                    }
                    ?>
                >

                    <img
                        src="<?php echo esc_url($header_ad['image']); ?>"
                        alt="<?php echo esc_attr($header_ad['alt']); ?>"
                    >

                </a>

            <?php else : ?>

                <img
                    src="<?php echo esc_url($header_ad['image']); ?>"
                    alt="<?php echo esc_attr($header_ad['alt']); ?>"
                >

            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>

  


    <div class="isago-topbar container">

        <!-- =========================
             Left
        ========================== -->

        <div class="isago-header-left">

            <!-- Mobile Menu -->

            <button
                class="isago-menu-button"
                aria-label="Open Menu">

                <i class="fa-solid fa-bars"></i>

            </button>

            <!-- Search -->

            <?php if (!empty($theme['header_search'])) : ?>

                <button
                    class="isago-search-button"
                    aria-label="Search">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </button>

            <?php endif; ?>  

            <!-- Dark Mode -->

            <?php if (!empty($theme['header_dark_toggle'])) : ?>
    <div class="isago-dark-desktop">

        <button
            class="isago-dark-toggle"
            aria-label="Toggle Dark Mode">

            <i class="fa-regular fa-moon"></i>

        </button>

    </div>

            <?php endif; ?>

        </div>

        <!-- =========================
             Logo
        ========================== -->

        <div class="isago-logo">

            <?php if (has_custom_logo()) : ?>

                <?php the_custom_logo(); ?>

            <?php else : ?>

                <a href="<?php echo esc_url(home_url('/')); ?>">

                    <h1>ISAGO</h1>

                </a>

            <?php endif; ?>

        </div>

        <!-- =========================
             Right
        ========================== -->

        <div class="isago-header-right">

    <?php if (!empty($theme['header_dark_toggle'])) : ?>

        <button
            class="isago-dark-toggle isago-dark-mobile"
            aria-label="Toggle Dark Mode">

            <i class="fa-regular fa-moon"></i>

        </button>

    <?php endif; ?>
    
     <?php

get_template_part(

'template-parts/header/social-icons'

);

?>

        </div> 

    </div>

</header>
<?php

get_template_part(
    'template-parts/header/mobile-menu'
);

?>
<?php

get_template_part(

'template-parts/header/search-overlay'

);

?>
<?php
/*
|--------------------------------------------------------------------------
| Bottom Navigation
|--------------------------------------------------------------------------
*/

get_template_part(
    'template-parts/header/navigation'
);
?>
</div>
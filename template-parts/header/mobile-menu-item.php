<?php

if (!defined('ABSPATH')) {
    exit;
}
 
?>

<li class="mobile-item <?php echo !empty($item->children) ? 'mobile-has-children' : ''; ?>">

    <div class="mobile-link-row">

        <a href="<?php echo esc_url($item->url); ?>">

            <?php echo esc_html($item->title); ?>

        </a>

        <?php if (!empty($item->children)) : ?>

            <button
                class="mobile-toggle"
                type="button">

                <i class="fa-solid fa-plus"></i>

            </button>

        <?php endif; ?>

    </div>

    <?php if (!empty($item->children)) : ?>

        <ul class="mobile-submenu">

            <?php foreach ($item->children as $child) : ?>

                <li>

                    <a href="<?php echo esc_url($child->url); ?>">

                        <?php echo esc_html($child->title); ?>

                    </a>

                    <?php if (!empty($child->children)) : ?>

                        <ul class="mobile-submenu-level2">

                            <?php foreach ($child->children as $grandchild) : ?>

                                <li>

                                    <a href="<?php echo esc_url($grandchild->url); ?>">

                                        <?php echo esc_html($grandchild->title); ?>

                                    </a>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

</li>
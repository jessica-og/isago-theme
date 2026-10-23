<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ADMIN STYLES
=========================================================*/

add_action(
    'admin_head',
    function () {

        ?>

        <style>

        .post-type-isago_video
        .column-thumbnail {
            width: 95px;
        }

        .post-type-isago_video
        .column-thumbnail img {
            width: 80px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
        }

        </style>

        <?php

    }
);


/*=========================================================
REMOVE VIDEO TAXONOMY MENUS
=========================================================*/

/**
 * Keep only:
 *
 * Videos
 * Add New Video
 *
 * Remove:
 *
 * Categories
 * Tags
 * AI Tools
 * AI Tool Categories
 *
 * This only changes the admin navigation.
 * The taxonomies remain registered and functional.
 */
function isago_clean_video_admin_menu()
{

    $video_menu = 'edit.php?post_type=isago_video';


    /*------------------------------------------------------
    Remove standard Category menu
    ------------------------------------------------------*/

    remove_submenu_page(
        $video_menu,
        'edit-tags.php?taxonomy=category&post_type=isago_video'
    );


    /*------------------------------------------------------
    Remove standard Tags menu
    ------------------------------------------------------*/

    remove_submenu_page(
        $video_menu,
        'edit-tags.php?taxonomy=post_tag&post_type=isago_video'
    );


    /*------------------------------------------------------
    Remove AI Tools menu
    ------------------------------------------------------*/

    remove_submenu_page(
        $video_menu,
        'edit-tags.php?taxonomy=ai_platform&post_type=isago_video'
    );


    /*------------------------------------------------------
    Remove AI Tool Categories menu
    ------------------------------------------------------*/

    remove_submenu_page(
        $video_menu,
        'edit-tags.php?taxonomy=ai_tool_category&post_type=isago_video'
    );

}

add_action(
    'admin_menu',
    'isago_clean_video_admin_menu',
    999
);


/*=========================================================
FINAL VIDEO SUBMENU CLEANUP
=========================================================*/

/**
 * WordPress/plugin registrations can sometimes add
 * taxonomy submenu items using slightly different
 * submenu URLs.
 *
 * This final cleanup guarantees that the Videos menu
 * contains only the main Videos screen and Add New Video.
 */
function isago_filter_video_admin_submenu(
    $submenu
) {

    $video_menu = 'edit.php?post_type=isago_video';


    if (
        !isset(
            $submenu[$video_menu]
        )
    ) {
        return $submenu;
    }


    $allowed = [
        'edit.php?post_type=isago_video',
        'post-new.php?post_type=isago_video',
    ];


    $clean_submenu = [];


    foreach (
        $submenu[$video_menu]
        as $item
    ) {

        if (
            !isset($item[2])
        ) {
            continue;
        }


        if (
            in_array(
                $item[2],
                $allowed,
                true
            )
        ) {

            $clean_submenu[] = $item;

        }

    }


    $submenu[$video_menu] = $clean_submenu;


    return $submenu;

}

add_filter(
    'submenu_file',
    function ($submenu_file) {

        /*
         * Keep the normal WordPress active submenu
         * behaviour for the Videos post type.
         */

        return $submenu_file;

    }
);


/*=========================================================
VIDEO SUBMENU HARD CLEANUP
=========================================================*/

/**
 * Runs after all admin menus have been registered.
 *
 * This directly removes every submenu item except
 * the Videos list and Add New Video.
 */
function isago_hard_clean_video_submenu()
{

    global $submenu;


    $video_menu = 'edit.php?post_type=isago_video';


    if (
        empty(
            $submenu[$video_menu]
        )
    ) {
        return;
    }


    foreach (
        $submenu[$video_menu]
        as $index => $item
    ) {

        if (
            empty($item[2])
        ) {
            unset(
                $submenu[$video_menu][$index]
            );

            continue;
        }


        /*
         * Keep Videos list.
         */
        if (
            $item[2] ===
            'edit.php?post_type=isago_video'
        ) {
            continue;
        }


        /*
         * Keep Add New Video.
         */
        if (
            $item[2] ===
            'post-new.php?post_type=isago_video'
        ) {
            continue;
        }


        /*
         * Remove everything else.
         */
        unset(
            $submenu[$video_menu][$index]
        );

    }


    /*
     * Re-index the submenu array.
     */
    $submenu[$video_menu] = array_values(
        $submenu[$video_menu]
    );

}

add_action(
    'admin_menu',
    'isago_hard_clean_video_submenu',
    9999
);
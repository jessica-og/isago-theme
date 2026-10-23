<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ORBIT TYPE
=========================================================*/

/*
 * This component is shared by:
 *
 * AI Tools
 * AI Prompts
 *
 * Usage:
 *
 * 'tools'
 *
 * or
 *
 * 'prompts'
 */

$orbit_type =
    isset($args['orbit_type'])
        ? sanitize_key($args['orbit_type'])
        : 'tools';


/*=========================================================
ORBIT DATA
=========================================================*/

$orbit_data = [


    /*=====================================================
    AI TOOLS
    =====================================================*/

    'tools' => [

        'badge' =>
            'AI TOOLS',

        'symbol' =>
            'fa-solid fa-microchip',

        'title' =>
            'AI Tools',

        'description' =>
            'Explore the tools powering modern AI.',

        'status' =>
            'Explore the AI ecosystem',

        'nodes' => [

            [
                'icon'  => 'fa-solid fa-robot',
                'label' => 'ChatGPT',
            ],

            [
                'icon'  => 'fa-solid fa-brain',
                'label' => 'Claude',
            ],

            [
                'icon'  => 'fa-solid fa-wand-magic-sparkles',
                'label' => 'Gemini',
            ],

            [
                'icon'  => 'fa-solid fa-bolt',
                'label' => 'Grok',
            ],

            [
                'icon'  => 'fa-solid fa-palette',
                'label' => 'Midjourney',
            ],

            [
                'icon'  => 'fa-solid fa-film',
                'label' => 'Google Flow',
            ],

        ],

    ],


    /*=====================================================
    AI PROMPTS
    =====================================================*/

    'prompts' => [

        'badge' =>
            'AI PROMPTS',

        'symbol' =>
            'fa-solid fa-wand-magic-sparkles',

        'title' =>
            'AI Prompts',

        'description' =>
            'Discover prompts for creating, writing, researching and more.',

        'status' =>
            'Explore the prompt library',

        'nodes' => [

            [
                'icon'  => 'fa-solid fa-pen-nib',
                'label' => 'Writing',
            ],

            [
                'icon'  => 'fa-solid fa-image',
                'label' => 'Image',
            ],

            [
                'icon'  => 'fa-solid fa-clapperboard',
                'label' => 'Video',
            ],

            [
                'icon'  => 'fa-solid fa-code',
                'label' => 'Coding',
            ],

            [
                'icon'  => 'fa-solid fa-bullhorn',
                'label' => 'Marketing',
            ],

            [
                'icon'  => 'fa-solid fa-microscope',
                'label' => 'Research',
            ],

        ],

    ],

];


/*=========================================================
VALIDATE TYPE
=========================================================*/

if (
    !isset(
        $orbit_data[$orbit_type]
    )
) {

    $orbit_type =
        'tools';

}


/*=========================================================
SELECT DATA
=========================================================*/

$data =
    $orbit_data[$orbit_type];


/*=========================================================
NODES
=========================================================*/

$nodes =
    isset($data['nodes'])
        ? $data['nodes']
        : [];

?>

<!-- =========================================================
     ISAGO INTERACTIVE ORBIT
     ========================================================= -->

<div
    class="isago-orbit-hero"
    data-isago-orbit
    data-orbit-type="<?php echo esc_attr($orbit_type); ?>"
>


    <!-- =====================================================
         BACKGROUND GLOW
         ===================================================== -->

    <div
        class="isago-orbit-hero-glow"
        aria-hidden="true"
    ></div>


    <!-- =====================================================
         INTERACTIVE SCENE
         ===================================================== -->

    <div
        class="isago-orbit-hero-scene"
        data-isago-orbit-scene
    >


        <!-- =================================================
             ORBIT SYSTEM
             ================================================= -->

        <div
            class="isago-orbit-hero-system"
            data-isago-orbit-system
        >


            <!-- =============================================
                 ORBIT RINGS
                 ============================================= -->

            <div
                class="
                    isago-orbit-hero-ring
                    isago-orbit-hero-ring-one
                "
                aria-hidden="true"
            ></div>


            <div
                class="
                    isago-orbit-hero-ring
                    isago-orbit-hero-ring-two
                "
                aria-hidden="true"
            ></div>


            <div
                class="
                    isago-orbit-hero-ring
                    isago-orbit-hero-ring-three
                "
                aria-hidden="true"
            ></div>


            <!-- =============================================
                 ORBIT NODES
                 ============================================= -->

            <?php

            foreach (
                $nodes
                as $index => $node
            ) :

                $node_number =
                    $index + 1;

            ?>

                <div
                    class="
                        isago-orbit-hero-node
                        isago-orbit-hero-node-<?php
                        echo esc_attr(
                            $node_number
                        );
                        ?>
                    "
                    aria-hidden="true"
                >


                    <!-- NODE ICON -->

                    <span
                        class="isago-orbit-hero-node-icon"
                    >

                        <i
                            class="<?php
                            echo esc_attr(
                                $node['icon']
                            );
                            ?>"
                            aria-hidden="true"
                        ></i>

                    </span>


                    <!-- NODE LABEL -->

                    <span
                        class="isago-orbit-hero-node-label"
                    >

                        <?php
                        echo esc_html(
                            $node['label']
                        );
                        ?>

                    </span>


                </div>

            <?php

            endforeach;

            ?>


            <!-- =============================================
                 CENTER CARD
                 ============================================= -->

            <div
                class="isago-orbit-hero-card"
            >


                <!-- CARD SHINE -->

                <div
                    class="isago-orbit-hero-card-shine"
                    aria-hidden="true"
                ></div>


                <!-- =========================================
                     BADGE
                     ========================================= -->

                <div
                    class="isago-orbit-hero-card-badge"
                >

                    <?php
                    echo esc_html(
                        $data['badge']
                    );
                    ?>

                </div>


                <!-- =========================================
                     CENTER ICON
                     ========================================= -->

                <div
                    class="isago-orbit-hero-card-symbol"
                >

                    <i
                        class="<?php
                        echo esc_attr(
                            $data['symbol']
                        );
                        ?>"
                        aria-hidden="true"
                    ></i>

                </div>


                <!-- =========================================
                     TITLE
                     ========================================= -->

                <h3>

                    <?php
                    echo esc_html(
                        $data['title']
                    );
                    ?>

                </h3>


                <!-- =========================================
                     DESCRIPTION
                     ========================================= -->

                <p>

                    <?php
                    echo esc_html(
                        $data['description']
                    );
                    ?>

                </p>


                <!-- =========================================
                     DIVIDER
                     ========================================= -->

                <div
                    class="isago-orbit-hero-card-divider"
                    aria-hidden="true"
                ></div>


                <!-- =========================================
                     STATUS
                     ========================================= -->

                <span
                    class="isago-orbit-hero-card-status"
                >

                    <span></span>

                    <?php
                    echo esc_html(
                        $data['status']
                    );
                    ?>

                </span>


            </div>


        </div>


    </div>


</div>
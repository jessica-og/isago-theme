<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
DATA
=========================================================*/

$platform_name =
    $args['platform_name']
    ?? '';

$information_cards =
    $args['information_cards']
    ?? [];

$has_information =
    !empty(
        $args['has_information']
    );


if (!$has_information) {
    return;
}

?>


<section class="ai-platform-page-overview">

    <div class="container">


        <div class="ai-platform-page-overview-header">

            <h2>

                <?php echo esc_html(
                    $platform_name
                ); ?>
             OVERVIEW
            </h2>

        </div>


        <div class="ai-platform-page-overview-grid">


            <?php foreach (
                $information_cards
                as $information_card
            ) : ?>


                <?php if (
                    empty(
                        $information_card['content']
                    )
                ) : ?>

                    <?php continue; ?>

                <?php endif; ?>


                <article
                    class="ai-platform-page-overview-card"
                >


                    <h3>

                        <?php echo esc_html(
                            $information_card['title']
                        ); ?>

                    </h3>


                    <div class="content-overview">

                        <?php
                        echo wp_kses_post(
                            $information_card['content']
                        );
                        ?>

                    </div>


                </article>


            <?php endforeach; ?>


        </div>


    </div>

</section>
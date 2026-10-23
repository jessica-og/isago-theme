<?php

if (!defined('ABSPATH')) {
    exit;
}

?>


<section class="isago-post-page">


    <div class="container">


        <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>


        <div class="post-layout">


            <main class="post-main">


                <?php if (is_singular('ai_prompt')) : ?>


                    <!-- =================================================
                         AI PROMPT
                         ================================================= -->


                    <?php

                    get_template_part(
                        'template-parts/post/ai-prompt-header'
                    );

                    ?>


                    <?php

                    get_template_part(
                        'template-parts/post/ai-prompt-single'
                    );

                    ?>

<?php
get_template_part(
    'template-parts/post/buy-me-a-coffee'
);
?>
                <?php else : ?>


                    <!-- =================================================
                         NORMAL POST
                         ================================================= -->


                    <?php

                    get_template_part(
                        'template-parts/post/post-header'
                    );

                    ?>


                    <?php

                    get_template_part(
                        'template-parts/post/post-content'
                    );

                    ?>

<?php if (is_singular('isago_video')) : ?>

    <?php
    get_template_part(
        'template-parts/post/video-ai-prompt'
    );

    get_template_part(
        'template-parts/post/buy-me-a-coffee'
    );
    ?>

<?php endif; ?>
                <?php endif; ?>


                <!-- =====================================================
                     AI PLATFORM CTA
                     =====================================================

                     Normal posts only.
                     AI Prompt posts do not use this.
                -->


                <?php if (!is_singular('ai_prompt')) : ?>


                    <?php

                    get_template_part(
                        'template-parts/post/ai-platform-cta'
                    );

                    ?>


                <?php endif; ?>


                <!-- =====================================================
                     AUTHOR
                     ===================================================== -->


                <?php

                get_template_part(
                    'template-parts/post/post-author'
                );

                ?>


                <!-- =====================================================
                     RELATED POSTS
                     ===================================================== -->


                <?php

                get_template_part(
                    'template-parts/post/related-posts'
                );

                ?>


         <?php

get_template_part(
    'template-parts/post/ai-news'
);

?>


            </main>


            <!-- =========================================================
                 SIDEBAR
                 ========================================================= -->


            <?php

            get_template_part(
                'template-parts/sidebar/sidebar'
            );

            ?>


        </div>


    </div>


</section>


<!-- =============================================================
     HOMEPAGE VIDEO SECTION
     ============================================================= -->


<section class="homepage-video-wrapper">


    <div class="container">


        <?php

        get_template_part(
            'template-parts/sections/video-section'
        );

        ?>


    </div>


</section>
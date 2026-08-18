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

                <?php

                get_template_part(
                    'template-parts/post/post-header'
                );

                get_template_part(
                    'template-parts/post/post-content'
                );

                get_template_part(
                    'template-parts/post/post-author'
                );

                get_template_part(
                    'template-parts/post/related-posts'
                );

                get_template_part(
                    'template-parts/post/comments'
                );

                get_template_part(
                    'template-parts/post/video-section'
                );

                ?>

            </main>

            <?php

            get_template_part(
                'template-parts/post/sidebar'
            );

            ?>

        </div>

    </div>

</section>
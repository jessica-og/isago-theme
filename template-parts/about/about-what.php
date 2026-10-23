<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="about-what">

    <h2>What We Do</h2>


    <div class="about-what-grid">

        <?php

        $items = [

            /*
            |---------------------------------------------------------
            | AI PROMPTS
            |---------------------------------------------------------
            */
            [
                'title' => 'AI Prompts',
                'desc'  => 'Discover powerful prompts for every use case.',
                'icon'  => 'fas fa-robot',
                'type'  => 'page',
                'slug'  => 'ai-prompts-library'
            ],


            /*
            |---------------------------------------------------------
            | AI TOOLS
            |---------------------------------------------------------
            */
            [
                'title' => 'AI Tools',
                'desc'  => 'Find and compare the best tools.',
                'icon'  => 'fas fa-tools',
                'type'  => 'page',
                'slug'  => 'ai-tools'
            ],


            /*
            |---------------------------------------------------------
            | GUIDES & TUTORIALS
            |---------------------------------------------------------
            */
            [
                'title' => 'Guides & Tutorials',
                'desc'  => 'Step-by-step learning resources.',
                'icon'  => 'fas fa-book',
                'type'  => 'category',
                'slug'  => 'ai-tutorials'
            ],


            /*
            |---------------------------------------------------------
            | CREATOR RESOURCES / AI NEWS
            |---------------------------------------------------------
            */
            [
                'title' => 'Creator Resources',
                'desc'  => 'Templates and tools for creators.',
                'icon'  => 'fas fa-cogs',
                'type'  => 'category',
                'slug'  => 'ai-news'
            ]

        ];


        foreach ($items as $item) :

            $link = '#';


            /*
            |---------------------------------------------------------
            | PAGE LINK
            |---------------------------------------------------------
            */
            if ($item['type'] === 'page') {

                $page = get_page_by_path(
                    $item['slug']
                );

                if ($page) {

                    $link = get_permalink(
                        $page->ID
                    );

                }

            }


            /*
            |---------------------------------------------------------
            | CATEGORY LINK
            |---------------------------------------------------------
            */
            elseif ($item['type'] === 'category') {

                $category =
                    get_category_by_slug(
                        $item['slug']
                    );

                if ($category) {

                    $link =
                        get_category_link(
                            $category->term_id
                        );

                }

            }

        ?>

            <a
                href="<?php echo esc_url($link); ?>"
                class="about-box"
            >

                <div class="about-box-icon">

                    <i class="<?php echo esc_attr(
                        $item['icon']
                    ); ?>"></i>

                </div>


                <h4>

                    <?php echo esc_html(
                        $item['title']
                    ); ?>

                </h4>


                <p>

                    <?php echo esc_html(
                        $item['desc']
                    ); ?>

                </p>


            </a>


        <?php endforeach; ?>


    </div>

</section>
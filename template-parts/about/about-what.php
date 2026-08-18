<section class="about-what">

    <h2>What We Do</h2>

    <div class="about-what-grid">

        <?php
        $items = [
            [
                'title' => 'AI Prompts',
                'desc'  => 'Discover powerful prompts for every use case.',
                'icon'  => 'fas fa-robot',
                'slug'  => 'ai-prompts'
            ],
            [
                'title' => 'AI Tools',
                'desc'  => 'Find and compare the best tools.',
                'icon'  => 'fas fa-tools',
                'slug'  => 'ai-tools'
            ],
            [
                'title' => 'Guides & Tutorials',
                'desc'  => 'Step-by-step learning resources.',
                'icon'  => 'fas fa-book',
                'slug'  => 'tutorials'
            ],
            [
                'title' => 'Creator Resources',
                'desc'  => 'Templates and tools for creators.',
                'icon'  => 'fas fa-cogs',
                'slug'  => 'resources'
            ],
        ];

        foreach ($items as $item):

            $cat = get_category_by_slug($item['slug']);
            $link = $cat ? get_category_link($cat->term_id) : '#';
        ?>

            <a href="<?php echo esc_url($link); ?>" class="about-box">

                <div class="about-box-icon">
                    <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                </div>

                <h4><?php echo esc_html($item['title']); ?></h4>

                <p><?php echo esc_html($item['desc']); ?></p>

            </a>

        <?php endforeach; ?>

    </div>

</section>
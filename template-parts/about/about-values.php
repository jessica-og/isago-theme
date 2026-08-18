<section class="about-values">

    <h2>Our Values</h2>

    <div class="about-values-grid">

        <?php
        $values = [
            ['title'=>'Clarity','desc'=>'We make AI simple and accessible.','icon'=>'fas fa-eye'],
            ['title'=>'Integrity','desc'=>'We recommend only what we trust.','icon'=>'fas fa-shield-alt'],
            ['title'=>'Innovation','desc'=>'We stay ahead so you can too.','icon'=>'fas fa-lightbulb'],
            ['title'=>'Community','desc'=>'We grow through sharing and learning.','icon'=>'fas fa-users'],
        ];

        foreach ($values as $v): ?>

            <div class="value-box">

                <div class="value-icon">
                    <i class="<?php echo esc_attr($v['icon']); ?>"></i>
                </div>

                <h4><?php echo esc_html($v['title']); ?></h4>
                <p><?php echo esc_html($v['desc']); ?></p>

            </div>

        <?php endforeach; ?>

    </div>

</section>
<?php
/*
Template Name: Privacy Policy
*/
get_header();
?>

<div class="container">

  
    <div class="policy-grid">

        <!-- LEFT CONTENT -->
        <main class="policy-content">

            <h1 class="policy-title">Privacy Policy</h1>

            <p class="policy-date">Last updated: May 15, 2024</p>

            <p class="policy-intro">
                At Isago, we value your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.
            </p>

            <?php
            $sections = [
                ['icon'=>'fa-user','title'=>'Information We Collect','text'=>'We collect information you provide directly to us, such as when you subscribe to our newsletter, comment on articles, or contact us.'],
                ['icon'=>'fa-chart-bar','title'=>'How We Use Your Information','text'=>'We use the information we collect to provide, maintain, and improve our website and services.'],
                ['icon'=>'fa-share-alt','title'=>'How We Share Your Information','text'=>'We do not sell or rent your personal information.'],
                ['icon'=>'fa-cookie-bite','title'=>'Cookies and Tracking Technologies','text'=>'We use cookies to enhance your experience and analyze site traffic.'],
                ['icon'=>'fa-lock','title'=>'Data Security','text'=>'We implement security measures to protect your information.'],
                ['icon'=>'fa-user-shield','title'=>'Your Rights','text'=>'You may request access, update, or deletion of your data.'],
                ['icon'=>'fa-globe','title'=>'Third-Party Links','text'=>'We are not responsible for external website privacy practices.'],
                ['icon'=>'fa-pen','title'=>'Changes to This Policy','text'=>'We may update this policy from time to time.'],
                ['icon'=>'fa-envelope','title'=>'Contact Us','text'=>'Contact us at privacy@isago.com.'],
            ];

            foreach($sections as $i => $sec): ?>
            
            <div class="policy-item">
                <div class="policy-icon">
                    <i class="fas <?php echo $sec['icon']; ?>"></i>
                </div>
                <div>
                    <h3><?php echo ($i+1).'. '.$sec['title']; ?></h3>
                    <p><?php echo $sec['text']; ?></p>
                </div>
            </div>

            <?php endforeach; ?>

        </main>

        <!-- RIGHT SIDEBAR -->
        <aside class="policy-sidebar">

            <div class="policy-card">
                <div class="policy-card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <h4>Your privacy is important</h4>

                <p>
                    We are committed to protecting your personal information and being transparent about how we use it.
                </p>
            </div>

        </aside>

    </div>

</div>



<?php get_footer(); ?>
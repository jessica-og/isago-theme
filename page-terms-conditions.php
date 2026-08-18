<?php
/*
Template Name: Terms and Conditions
*/
get_header();
?>

<div class="container">


    <div class="policy-grid">

        <!-- LEFT CONTENT -->
        <main class="policy-content">

            <h1 class="policy-title">Terms and Conditions</h1>

            <p class="policy-date">Last updated: May 15, 2024</p>

            <p class="policy-intro">
                Welcome to Isago. By accessing or using our website, you agree to comply with and be bound by the following terms and conditions.
            </p>

            <?php
            $sections = [
                ['icon'=>'fa-user','title'=>'Use of Our Website','text'=>'You must be at least 13 years old to use Isago. You agree to use our website for lawful purposes only.'],
                ['icon'=>'fa-file-alt','title'=>'Content and Intellectual Property','text'=>'All content on Isago is owned or licensed to us. You may not copy or distribute content without permission.'],
                ['icon'=>'fa-link','title'=>'User Contributions','text'=>'By submitting content, you grant Isago a license to use, display, and distribute it.'],
                ['icon'=>'fa-shield-alt','title'=>'Prohibited Conduct','text'=>'You agree not to engage in harmful activities such as hacking, spamming, or unauthorized access.'],
                ['icon'=>'fa-external-link-alt','title'=>'Third-Party Links','text'=>'We are not responsible for third-party content or websites.'],
                ['icon'=>'fa-credit-card','title'=>'Purchases and Payments','text'=>'You agree to provide accurate information for transactions and authorize secure processing.'],
                ['icon'=>'fa-exclamation-circle','title'=>'Disclaimer of Warranties','text'=>'Isago is provided “as is” without warranties of any kind.'],
                ['icon'=>'fa-balance-scale','title'=>'Limitation of Liability','text'=>'We are not liable for damages resulting from use of our services.'],
                ['icon'=>'fa-pen','title'=>'Changes to These Terms','text'=>'We may update these terms at any time.'],
                ['icon'=>'fa-envelope','title'=>'Contact Us','text'=>'Contact us at legal@isago.com.'],
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
                    <i class="fas fa-file-contract"></i>
                </div>

                <h4>Agreement to our terms</h4>

                <p>
                    By using Isago, you agree to these terms and conditions and our policies. If you do not agree, please do not use our website.
                </p>
            </div>

        </aside>

    </div>

</div>



<?php get_footer(); ?>
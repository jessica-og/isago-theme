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

            <h1 class="policy-title">
                Terms and Conditions
            </h1>

            <p class="policy-date">
                Last updated: October 1, 2026
            </p>

            <p class="policy-intro">
                Welcome to Isago. These Terms and Conditions explain how you can use our website, how purchases work, and what you can expect from us when using Isago.
                By accessing or using Isago, you agree to these terms. If you do not agree with them, please do not use the website or purchase our products.
            </p>


            <?php

            $sections = [

                [
                    'icon'  => 'fa-user',
                    'title' => 'Using Isago',
                    'text'  =>
                        'Isago is an online platform that provides AI-related content, resources, tools, prompts, guides, and other digital products. You agree to use the website responsibly and only for lawful purposes. You must also provide accurate information when creating an account or making a purchase.'
                ],

                [
                    'icon'  => 'fa-user-check',
                    'title' => 'Your Account',
                    'text'  =>
                        'Some Isago features require an account. You are responsible for keeping your login information secure and for the activity that takes place through your account. If you believe someone has accessed your account without your permission, please contact us as soon as possible.'
                ],

                [
                    'icon'  => 'fa-file-alt',
                    'title' => 'Isago Content and Intellectual Property',
                    'text'  =>
                        'Unless otherwise stated, the content published on Isago, including articles, guides, graphics, original prompt collections, designs, text, branding, and other materials, is owned by or licensed to Isago. You may use purchased digital content for your own permitted purposes, but you may not copy, resell, redistribute, publish, or commercially exploit Isago content in a way that infringes our rights or the rights of our licensors.'
                ],

                [
                    'icon'  => 'fa-robot',
                    'title' => 'AI Prompts and Digital Products',
                    'text'  =>
                        'Some products sold through Isago are digital AI prompts or other digital resources. Purchasing a prompt gives you access to the purchased digital resource under these Terms; it does not transfer ownership of Isago’s website, branding, intellectual property, or other unrelated content. AI-generated results can vary depending on the AI tool, model, settings, inputs, and other factors, so we cannot guarantee a particular output or result from using a prompt.'
                ],

                [
                    'icon'  => 'fa-credit-card',
                    'title' => 'Purchases and Payments',
                    'text'  =>
                        'When you purchase a paid Isago product, you agree to provide accurate account and payment information. Payments are processed through our payment provider, Paystack, or another payment service we may use in the future. Isago does not need to store your full card number or card security details to provide your purchased access. After a payment is successfully verified, Isago creates the relevant order record and activates access to the purchased product.'
                ],

                [
                    'icon'  => 'fa-receipt',
                    'title' => 'Orders and Purchase Records',
                    'text'  =>
                        'We may keep records of purchases, including the product purchased, order reference, payment status, amount paid, currency, payment method information provided by the payment processor, transaction reference, refund status, and related dates. These records help us provide access, maintain accurate financial records, handle refunds or payment issues, and protect the website against fraud or misuse.'
                ],

                [
                    'icon'  => 'fa-rotate-left',
                    'title' => 'Refunds',
                    'text'  =>
                        'Isago may provide refunds in special cases, taking into account the circumstances of the request and the nature of the digital product. Refunds are not automatically guaranteed simply because a purchase has been made. Any refund request is subject to these Terms and may be reviewed on a case-by-case basis. Where a refund is approved, the refund will normally be processed through the payment method or payment provider used for the original transaction, where possible. Please contact us if you believe there is a genuine issue with a purchase and we will review the situation.'
                ],

                [
                    'icon'  => 'fa-calendar-times',
                    'title' => 'Prompt Removal and Purchased Access',
                    'text'  =>
                        'Isago may occasionally decide to permanently remove a prompt or digital resource from the platform. When a purchased prompt is scheduled for removal, we may notify users who have purchased it so they have an opportunity to save or store the prompt for their own permitted use before the scheduled removal date. Once a prompt has been permanently removed, access through Isago may no longer be available. Orders and payment records may still be retained for financial, security, accounting, and administrative purposes.'
                ],

                [
                    'icon'  => 'fa-ban',
                    'title' => 'Prohibited Use',
                    'text'  =>
                        'You may not use Isago to break the law, interfere with the operation or security of the website, attempt unauthorized access, distribute malware, abuse our services, commit fraud, scrape or copy substantial amounts of protected content without permission, or otherwise misuse the platform. We may restrict or terminate access where there is a serious or repeated violation of these Terms.'
                ],

                [
                    'icon'  => 'fa-link',
                    'title' => 'Third-Party Services and Links',
                    'text'  =>
                        'Isago may link to or integrate with third-party services, including AI platforms, payment providers, analytics services, social platforms, and other external websites. These services have their own terms and privacy policies. Isago is not responsible for the policies, availability, security, or content of third-party websites and services.'
                ],

                [
                    'icon'  => 'fa-shield-alt',
                    'title' => 'Website Availability and Security',
                    'text'  =>
                        'We work to keep Isago available, secure, and useful, but we cannot promise that the website will always be available or free from errors, interruptions, vulnerabilities, or maintenance periods. We may temporarily change, suspend, or discontinue parts of the website when necessary.'
                ],

                [
                    'icon'  => 'fa-exclamation-circle',
                    'title' => 'Disclaimer',
                    'text'  =>
                        'Isago provides information, resources, prompts, and digital products for general informational and practical use. We do not guarantee that any particular AI prompt, article, recommendation, tool, or resource will produce a specific result. Results may depend on external AI systems, software updates, user input, and other factors outside our control.'
                ],

                [
                    'icon'  => 'fa-balance-scale',
                    'title' => 'Limitation of Liability',
                    'text'  =>
                        'To the extent permitted by applicable law, Isago will not be responsible for indirect, incidental, special, or consequential losses arising from your use of the website, digital products, third-party services, or AI tools. Nothing in these Terms is intended to exclude or limit a responsibility that cannot legally be excluded or limited.'
                ],

                [
                    'icon'  => 'fa-pen',
                    'title' => 'Changes to These Terms',
                    'text'  =>
                        'We may update these Terms from time to time as Isago grows, adds new features, changes its services, or responds to legal or operational requirements. When we make changes, we will update the date shown at the top of this page. Your continued use of Isago after an update means you agree to the revised Terms.'
                ],

                [
                    'icon'  => 'fa-envelope',
                    'title' => 'Contact Us',
                    'text'  =>
                        'If you have questions about these Terms, a purchase, a refund request, or your access to a digital product, please contact us at isago.nae@gmail.com.'
                ],

            ];


            foreach (
                $sections as $i => $sec
            ) :

            ?>

                <div class="policy-item">

                    <div class="policy-icon">

                        <i
                            class="fas <?php echo esc_attr(
                                $sec['icon']
                            ); ?>"
                        ></i>

                    </div>


                    <div>

                        <h3>

                            <?php
                            echo esc_html(
                                ($i + 1) .
                                '. ' .
                                $sec['title']
                            );
                            ?>

                        </h3>


                        <p>

                            <?php
                            echo esc_html(
                                $sec['text']
                            );
                            ?>

                        </p>

                    </div>

                </div>

            <?php

            endforeach;

            ?>

        </main>


        <!-- RIGHT SIDEBAR -->
        <aside class="policy-sidebar">

            <div class="policy-card">

                <div class="policy-card-icon">

                    <i
                        class="fas fa-file-contract"
                    ></i>

                </div>


                <h4>
                    Using Isago
                </h4>


                <p>
                    We want Isago to be useful, straightforward, and fair.
                    These terms explain how the website, digital products,
                    purchases, refunds, and accounts work.
                </p>

            </div>

        </aside>

    </div>

</div>


<?php
get_footer();
?>
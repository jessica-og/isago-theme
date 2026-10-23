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

            <h1 class="policy-title">
                Privacy Policy
            </h1>

            <p class="policy-date">
                Last updated: October 1, 2026
            </p>

            <p class="policy-intro">
                Your privacy matters to us. This Privacy Policy explains what information Isago may collect, why we use it, how we protect it, and when it may be shared with service providers that help us operate the website.
            </p>


            <?php

            $sections = [

                [
                    'icon'  => 'fa-user',
                    'title' => 'Information You Give Us',
                    'text'  =>
                        'When you create an Isago account, contact us, subscribe to communications, or use certain features, we may collect information such as your name, email address, account details, and information you choose to provide. We only ask for information that is reasonably needed to provide the relevant service.'
                ],

                [
                    'icon'  => 'fa-user-circle',
                    'title' => 'Account Information',
                    'text'  =>
                        'If you create an account, we keep information needed to manage that account, such as your name, email address, account status, login credentials handled through WordPress, and related account records. Your password is handled using WordPress security mechanisms rather than being stored by Isago as plain text.'
                ],

                [
                    'icon'  => 'fa-shopping-bag',
                    'title' => 'Purchase and Order Information',
                    'text'  =>
                        'When you purchase a paid Isago product, we may store information needed to manage the purchase. This can include the product purchased, order ID, purchase amount, currency, payment status, payment method or channel, transaction reference, entitlement or access status, refund status, and relevant dates. We use this information to provide purchased access, maintain accurate records, resolve payment problems, and provide customer support.'
                ],

                [
                    'icon'  => 'fa-credit-card',
                    'title' => 'Payment Processing',
                    'text'  =>
                        'Payments for Isago products are processed through Paystack or another payment provider we may use in the future. Payment providers may collect and process payment information according to their own privacy policies and terms. Isago does not need to store your full card number or card security code in order to process a purchase. We receive and retain transaction information needed to confirm the payment and connect it to your Isago order.'
                ],

                [
                    'icon'  => 'fa-chart-bar',
                    'title' => 'How We Use Information',
                    'text'  =>
                        'We use information to operate Isago, create and manage accounts, process purchases, verify payments, provide access to purchased products, maintain order history, communicate with users, respond to support requests, prevent fraud and abuse, improve the website, understand how content is being used, and maintain the security and reliability of our services.'
                ],

                [
                    'icon'  => 'fa-envelope',
                    'title' => 'Emails and Important Notifications',
                    'text'  =>
                        'We may use your email address to send account-related messages, purchase or transaction information, password-reset instructions, important service notices, and other communications necessary to operate your account or provide a service you requested. If you have purchased a prompt that is scheduled for permanent removal, we may also email you about the scheduled removal and provide a copy of the prompt where appropriate so you have an opportunity to save it before removal.'
                ],

                [
                    'icon'  => 'fa-share-alt',
                    'title' => 'When We Share Information',
                    'text'  =>
                        'We do not sell or rent your personal information. We may share limited information with service providers that help us operate Isago, such as payment processors, hosting providers, email delivery services, security services, analytics providers, and other technical providers. These providers receive information only as reasonably necessary for the services they provide or as required by law.'
                ],

                [
                    'icon'  => 'fa-cookie-bite',
                    'title' => 'Cookies and Similar Technologies',
                    'text'  =>
                        'Isago may use cookies and similar technologies to keep you signed in, remember settings, support website functionality, improve the user experience, understand website usage, and help protect the website. Some third-party services used by Isago may also use cookies or similar technologies under their own policies.'
                ],

                [
                    'icon'  => 'fa-eye',
                    'title' => 'Website Usage and Analytics',
                    'text'  =>
                        'We may collect technical and usage information about how visitors interact with Isago. Depending on the features enabled on the website, this may include pages viewed, approximate usage information, browser or device information, referral information, and other technical data. We may use this information to understand which content is useful, improve the website, measure performance, and identify abuse or unusual activity.'
                ],

                [
                    'icon'  => 'fa-bookmark',
                    'title' => 'Saved Prompts and Collections',
                    'text'  =>
                        'If you use features such as Saved Prompts or Prompt Collections, Isago may store information about which prompts you have saved and which prompts belong to collections associated with your account. This information is used to provide the feature and keep your saved content organized. If a prompt is permanently deleted from Isago, the associated saved-prompt and collection associations may also be removed.'
                ],

                [
                    'icon'  => 'fa-trash-alt',
                    'title' => 'Deleted Content and Retained Records',
                    'text'  =>
                        'When an account, prompt, or other content is deleted, some related information may still need to be retained for legitimate operational, security, financial, legal, fraud-prevention, or accounting purposes. For example, deleting a purchased prompt does not necessarily mean that the corresponding order or transaction record is immediately deleted.'
                ],

                [
                    'icon'  => 'fa-lock',
                    'title' => 'How We Protect Information',
                    'text'  =>
                        'We use reasonable technical and organizational measures to protect information handled by Isago. These include WordPress security mechanisms, access controls, server-side payment verification, nonces and other security checks, and measures designed to prevent unauthorized access. No internet service can guarantee absolute security, so we cannot promise that information will always be completely secure.'
                ],

                [
                    'icon'  => 'fa-user-shield',
                    'title' => 'Your Choices and Rights',
                    'text'  =>
                        'Depending on where you live and the laws that apply to you, you may have rights concerning your personal information, including rights to request access, correction, or deletion of certain information. Some information may need to be retained where we have a legitimate reason or legal obligation to keep it. You can contact us to ask about the information associated with your account.'
                ],

                [
                    'icon'  => 'fa-external-link-alt',
                    'title' => 'Third-Party Websites and Services',
                    'text'  =>
                        'Isago may contain links to external websites or use third-party services such as AI platforms, payment providers, social networks, analytics services, hosting providers, and email providers. Once you leave Isago or interact directly with a third-party service, that service may collect information under its own privacy policy. We encourage you to review the privacy practices of services you use.'
                ],

                [
                    'icon'  => 'fa-globe',
                    'title' => 'International Services',
                    'text'  =>
                        'Some of the companies and services that help operate Isago may process information in countries other than the country where you live. Where this happens, information may be subject to the laws and security practices applicable to those services.'
                ],

                [
                    'icon'  => 'fa-pen',
                    'title' => 'Changes to This Privacy Policy',
                    'text'  =>
                        'As Isago grows or changes the way we provide our services, we may update this Privacy Policy. When we make changes, we will update the date shown at the top of this page. We encourage you to check this page occasionally so you know how your information is being handled.'
                ],

                [
                    'icon'  => 'fa-envelope',
                    'title' => 'Contact Us',
                    'text'  =>
                        'If you have a privacy question, want to ask about information associated with your account, or have a concern about how your information is handled, please contact us at isago.nae@gmail.com.'
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
                        class="fas fa-shield-alt"
                    ></i>

                </div>


                <h4>
                    Your privacy matters
                </h4>


                <p>
                    We try to keep our approach simple: collect what we need,
                    use it for clear purposes, protect it responsibly, and be
                    transparent about the services that help us operate Isago.
                </p>

            </div>

        </aside>

    </div>

</div>


<?php
get_footer();
?>
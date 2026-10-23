document.addEventListener(
    'DOMContentLoaded',
    function () {

        const purchaseButtons =
            document.querySelectorAll(
                '.isago-purchase-prompt'
            );


        if (!purchaseButtons.length) {
            return;
        }


        purchaseButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();


                        /*
                         * Prevent duplicate clicks.
                         */

                        if (
                            button.dataset.processing === 'true'
                        ) {
                            return;
                        }


                        /*
                         * Prompt ID.
                         */

                        const promptId =
                            parseInt(
                                button.dataset.promptId ||
                                '0',
                                10
                            );


                        if (!promptId) {

                            window.alert(
                                'Unable to start checkout. The prompt could not be identified.'
                            );

                            return;

                        }


                        /*
                         * Payment configuration.
                         */

                        if (
                            typeof isagoPayment === 'undefined'
                        ) {

                            window.alert(
                                'Payment system is not available right now. Please try again later.'
                            );

                            return;

                        }


                        /*
                         * If the visitor is not logged in,
                         * send them to the Isago login page.
                         */

                        if (
                            !isagoPayment.isLoggedIn
                        ) {

                            if (
                                isagoPayment.loginUrl
                            ) {

                                window.location.href =
                                    isagoPayment.loginUrl;

                            } else {

                                window.alert(
                                    'Please sign in before purchasing this prompt.'
                                );

                            }

                            return;

                        }


                        /*
                         * Lock the button while
                         * checkout is being created.
                         */

                        button.dataset.processing =
                            'true';

                        button.setAttribute(
                            'aria-disabled',
                            'true'
                        );

                        button.classList.add(
                            'is-processing'
                        );


                        const originalHTML =
                            button.innerHTML;


                        button.dataset.originalHtml =
                            originalHTML;


                        button.innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>' +
                            '<span>Preparing Checkout...</span>';


                        /*
                         * Create server-side order
                         * and initialize Paystack.
                         */

                        const formData =
                            new FormData();


                        formData.append(
                            'action',
                            'isago_start_prompt_checkout'
                        );


                        formData.append(
                            'nonce',
                            isagoPayment.nonce
                        );


                        formData.append(
                            'prompt_id',
                            promptId
                        );


                        fetch(
                            isagoPayment.ajaxUrl,
                            {
                                method: 'POST',
                                credentials: 'same-origin',
                                body: formData
                            }
                        )
                        .then(
                            function (response) {

                                return response.json();

                            }
                        )
                        .then(
                            function (result) {

                                /*
                                 * Successful checkout
                                 * initialization.
                                 */

                                if (
                                    result &&
                                    result.success &&
                                    result.data &&
                                    result.data.authorization_url
                                ) {

                                    window.location.href =
                                        result.data.authorization_url;

                                    return;

                                }


                                /*
                                 * Something went wrong.
                                 */

                                let message =
                                    'Unable to start checkout. Please try again.';


                                if (
                                    result &&
                                    result.data
                                ) {

                                    if (
                                        typeof result.data ===
                                        'string'
                                    ) {

                                        message =
                                            result.data;

                                    } else if (
                                        result.data.message
                                    ) {

                                        message =
                                            result.data.message;

                                    }

                                }


                                throw new Error(
                                    message
                                );

                            }
                        )
                        .catch(
                            function (error) {

                                console.error(
                                    'Isago payment error:',
                                    error
                                );


                                window.alert(
                                    error.message ||
                                    'Unable to start checkout. Please try again.'
                                );


                                /*
                                 * Restore button.
                                 */

                                button.dataset.processing =
                                    'false';

                                button.removeAttribute(
                                    'aria-disabled'
                                );

                                button.classList.remove(
                                    'is-processing'
                                );


                                if (
                                    button.dataset.originalHtml
                                ) {

                                    button.innerHTML =
                                        button.dataset.originalHtml;

                                }

                            }
                        );

                    }
                );

            }
        );

    }
);
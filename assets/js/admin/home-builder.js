(function () {

    "use strict";


    /*=========================================================
    ISAGO HOMEPAGE BUILDER ACCORDION
    =========================================================*/

    function isagoInitializeHomeBuilder() {

        const cards = document.querySelectorAll(
            ".isago-builder-card"
        );


        if (!cards.length) {
            return;
        }


        cards.forEach(function (card) {

            const button =
                card.querySelector(
                    ".isago-builder-card-header"
                );


            const content =
                card.querySelector(
                    ".isago-builder-card-content"
                );


            if (!button || !content) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate initialization
            |--------------------------------------------------------------------------
            */

            if (
                card.dataset.isagoAccordionReady === "1"
            ) {
                return;
            }


            card.dataset.isagoAccordionReady = "1";


            /*
            |--------------------------------------------------------------------------
            | Initial state
            |--------------------------------------------------------------------------
            */

            const initiallyOpen =
                card.classList.contains("is-open");


            button.setAttribute(
                "aria-expanded",
                initiallyOpen
                    ? "true"
                    : "false"
            );


            if (initiallyOpen) {

                content.style.display = "block";

            } else {

                content.style.display = "none";

            }


            /*
            |--------------------------------------------------------------------------
            | Click
            |--------------------------------------------------------------------------
            */

            button.addEventListener(
                "click",
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();


                    const isOpen =
                        card.classList.contains(
                            "is-open"
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Close all other cards
                    |--------------------------------------------------------------------------
                    */

                    cards.forEach(
                        function (otherCard) {

                            if (
                                otherCard === card
                            ) {
                                return;
                            }


                            otherCard.classList.remove(
                                "is-open"
                            );


                            const otherButton =
                                otherCard.querySelector(
                                    ".isago-builder-card-header"
                                );


                            const otherContent =
                                otherCard.querySelector(
                                    ".isago-builder-card-content"
                                );


                            if (otherButton) {

                                otherButton.setAttribute(
                                    "aria-expanded",
                                    "false"
                                );

                            }


                            if (otherContent) {

                                otherContent.style.display =
                                    "none";

                            }

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Close current
                    |--------------------------------------------------------------------------
                    */

                    if (isOpen) {

                        card.classList.remove(
                            "is-open"
                        );


                        button.setAttribute(
                            "aria-expanded",
                            "false"
                        );


                        content.style.display =
                            "none";


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Open current
                    |--------------------------------------------------------------------------
                    */

                    card.classList.add(
                        "is-open"
                    );


                    button.setAttribute(
                        "aria-expanded",
                        "true"
                    );


                    content.style.display =
                        "block";

                }
            );

        });

    }


    /*=========================================================
    SAFE INITIALIZATION
    =========================================================*/

    if (
        document.readyState === "loading"
    ) {

        document.addEventListener(
            "DOMContentLoaded",
            isagoInitializeHomeBuilder
        );

    } else {

        isagoInitializeHomeBuilder();

    }

})();
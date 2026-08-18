document.addEventListener("DOMContentLoaded", () => {

    const cards = document.querySelectorAll(
        ".isago-builder-card"
    );

    if (!cards.length) {
        return;
    }

    cards.forEach((card) => {

        const button = card.querySelector(
            ".isago-builder-card-header"
        );

        if (!button) {
            return;
        }

        button.addEventListener("click", () => {

            const isOpen =
                card.classList.contains("is-open");

            /*
            |--------------------------------------------------------------------------
            | Close all cards
            |--------------------------------------------------------------------------
            */

            cards.forEach((otherCard) => {

                otherCard.classList.remove(
                    "is-open"
                );

                const otherButton =
                    otherCard.querySelector(
                        ".isago-builder-card-header"
                    );

                if (otherButton) {

                    otherButton.setAttribute(
                        "aria-expanded",
                        "false"
                    );

                }

            });

            /*
            |--------------------------------------------------------------------------
            | Open clicked card
            |--------------------------------------------------------------------------
            */

            if (!isOpen) {

                card.classList.add(
                    "is-open"
                );

                button.setAttribute(
                    "aria-expanded",
                    "true"
                );

            }

        });

    });

});
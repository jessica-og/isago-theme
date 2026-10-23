document.addEventListener('DOMContentLoaded', () => {

    const header = document.querySelector('.isago-header');

    if (!header) {
        return;
    }

    let ticking = false;

    function updateHeader() {

        if (window.scrollY > 60) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        ticking = false;
    }

    window.addEventListener('scroll', () => {

        if (!ticking) {

            window.requestAnimationFrame(updateHeader);

            ticking = true;

        }

    }, {
        passive: true
    });

    updateHeader();

});




/*=========================================================
ISAGO ACCOUNT DROPDOWN
=========================================================*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const userMenus =
            document.querySelectorAll(
                '.isago-header-user'
            );

        if (!userMenus.length) {
            return;
        }


        userMenus.forEach(
            function (userMenu) {

                const toggle =
                    userMenu.querySelector(
                        '.isago-header-user-toggle'
                    );

                const dropdown =
                    userMenu.querySelector(
                        '.isago-header-user-dropdown'
                    );

                if (!toggle || !dropdown) {
                    return;
                }


                function openMenu() {

                    userMenu.classList.add(
                        'is-open'
                    );

                    toggle.setAttribute(
                        'aria-expanded',
                        'true'
                    );
                }


                function closeMenu() {

                    userMenu.classList.remove(
                        'is-open'
                    );

                    toggle.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }


                toggle.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();

                        event.stopPropagation();

                        const isOpen =
                            userMenu.classList.contains(
                                'is-open'
                            );

                        if (isOpen) {

                            closeMenu();

                        } else {

                            /*
                             * Close any other
                             * open account menu.
                             */
                            document
                                .querySelectorAll(
                                    '.isago-header-user.is-open'
                                )
                                .forEach(
                                    function (otherMenu) {

                                        if (
                                            otherMenu !==
                                            userMenu
                                        ) {

                                            otherMenu
                                                .classList
                                                .remove(
                                                    'is-open'
                                                );

                                            const otherToggle =
                                                otherMenu
                                                    .querySelector(
                                                        '.isago-header-user-toggle'
                                                    );

                                            if (otherToggle) {

                                                otherToggle.setAttribute(
                                                    'aria-expanded',
                                                    'false'
                                                );
                                            }

                                        }

                                    }
                                );

                            openMenu();
                        }

                    }
                );


                dropdown.addEventListener(
                    'click',
                    function (event) {

                        event.stopPropagation();

                    }
                );


                document.addEventListener(
                    'click',
                    function (event) {

                        if (
                            !userMenu.contains(
                                event.target
                            )
                        ) {

                            closeMenu();

                        }

                    }
                );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Escape'
                        ) {

                            closeMenu();

                            toggle.focus();

                        }

                    }
                );

            }
        );

    }
);
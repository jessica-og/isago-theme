document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * -----------------------------------------------------
         * CONFIGURATION
         * -----------------------------------------------------
         */

        if (
            typeof isagoSavedPrompts ===
            'undefined'
        ) {

            console.error(
                'Isago Saved Prompts: configuration not found.'
            );

            return;

        }


        let activePromptId =
            null;


        /*
         * -----------------------------------------------------
         * CREATE MODAL IF NEEDED
         * -----------------------------------------------------
         */

        function getCollectionModal() {

            let modal =
                document.querySelector(
                    '.isago-prompt-collection-modal'
                );


            if (modal) {

                return modal;

            }


            modal =
                document.createElement(
                    'div'
                );


            modal.className =
                'isago-prompt-collection-modal';


            modal.hidden =
                true;


            modal.innerHTML = `
                <div
                    class="isago-prompt-collection-modal-overlay"
                ></div>

                <div
                    class="isago-prompt-collection-modal-content"
                    role="dialog"
                    aria-modal="true"
                >

                    <button
                        type="button"
                        class="isago-prompt-collection-modal-close"
                        aria-label="Close"
                    >

                        <i
                            class="fa-solid fa-xmark"
                            aria-hidden="true"
                        ></i>

                    </button>

                    <h2>
                        Save Prompt
                    </h2>

                    <p>
                        Save this prompt for later or add it to a collection.
                    </p>

                    <div
                        class="isago-prompt-collection-modal-list"
                    >
                        Loading collections...
                    </div>

                    <div
                        class="isago-prompt-collection-modal-create"
                    >

                        <button
                            type="button"
                            class="isago-modal-create-collection-button"
                        >

                            <i
                                class="fa-solid fa-plus"
                                aria-hidden="true"
                            ></i>

                            Create New Collection

                        </button>

                        <div
                            class="isago-modal-create-collection-form"
                            hidden
                        >

                            <input
                                type="text"
                                class="isago-modal-create-collection-input"
                                placeholder="Collection name"
                                maxlength="191"
                            >

                            <button
                                type="button"
                                class="isago-modal-confirm-create-collection"
                            >
                                Create
                            </button>

                            <button
                                type="button"
                                class="isago-modal-cancel-create-collection"
                            >
                                Cancel
                            </button>

                        </div>

                    </div>

                </div>
            `;


            document.body.appendChild(
                modal
            );


            return modal;

        }


        /*
         * -----------------------------------------------------
         * OPEN COLLECTION MODAL
         * -----------------------------------------------------
         */

        function openCollectionModal(
            promptId
        ) {

            const modal =
                getCollectionModal();


            activePromptId =
                promptId;


            modal.hidden =
                false;


            document.body.classList.add(
                'isago-modal-open'
            );


            const list =
                modal.querySelector(
                    '.isago-prompt-collection-modal-list'
                );


            if (!list) {

                return;

            }


            list.innerHTML = `
                <div
                    class="isago-prompt-collection-modal-loading"
                >
                    Loading collections...
                </div>
            `;


            const formData =
                new FormData();


            formData.append(
                'action',
                'isago_get_prompt_collections'
            );


            formData.append(
                'nonce',
                isagoSavedPrompts.nonce
            );


            fetch(
                isagoSavedPrompts.ajaxUrl,
                {
                    method: 'POST',

                    credentials:
                        'same-origin',

                    body:
                        formData
                }
            )

            .then(
                function (response) {

                    return response.json();

                }
            )

            .then(
                function (response) {

                    if (
                        !response
                        ||
                        !response.success
                    ) {

                        throw new Error(
                            response
                            &&
                            response.data
                            &&
                            response.data.message
                                ? response.data.message
                                : 'Could not load collections.'
                        );

                    }


                    renderCollectionOptions(
                        modal,
                        response.data.collections || []
                    );

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Isago Saved Prompts:',
                        error
                    );


                    list.innerHTML = `
                        <div
                            class="isago-modal-error"
                        >
                            Could not load your collections.
                        </div>
                    `;

                }
            );

        }


        /*
         * -----------------------------------------------------
         * RENDER COLLECTION OPTIONS
         * -----------------------------------------------------
         */

        function renderCollectionOptions(
            modal,
            collections
        ) {

            const list =
                modal.querySelector(
                    '.isago-prompt-collection-modal-list'
                );


            if (!list) {

                return;

            }


            list.innerHTML =
                '';


            const saveOption =
                document.createElement(
                    'button'
                );


            saveOption.type =
                'button';


            saveOption.className =
                'isago-modal-save-only';


            saveOption.innerHTML = `
                <i
                    class="fa-solid fa-bookmark"
                    aria-hidden="true"
                ></i>

                <span>
                    Just Save Prompt
                </span>
            `;


            saveOption.addEventListener(
                'click',
                function () {

                    savePrompt(
                        activePromptId,
                        0,
                        modal
                    );

                }
            );


            list.appendChild(
                saveOption
            );


            if (
                collections.length
            ) {

                const heading =
                    document.createElement(
                        'div'
                    );


                heading.className =
                    'isago-modal-collection-heading';


                heading.textContent =
                    'Save to a collection';


                list.appendChild(
                    heading
                );


                collections.forEach(
                    function (collection) {

                        const button =
                            document.createElement(
                                'button'
                            );


                        button.type =
                            'button';


                        button.className =
                            'isago-modal-collection-option';


                        button.dataset.collectionId =
                            collection.id;


                        button.innerHTML = `
                            <i
                                class="fa-regular fa-folder"
                                aria-hidden="true"
                            ></i>

                            <span>
                                ${escapeHtml(
                                    collection.name
                                )}
                            </span>

                            <small>
                                ${collection.prompt_count}
                                prompt${collection.prompt_count !== 1 ? 's' : ''}
                            </small>
                        `;


                        button.addEventListener(
                            'click',
                            function () {

                                savePrompt(
                                    activePromptId,
                                    collection.id,
                                    modal
                                );

                            }
                        );


                        list.appendChild(
                            button
                        );

                    }
                );

            }

        }


        /*
         * -----------------------------------------------------
         * SAVE PROMPT
         * -----------------------------------------------------
         */

        function savePrompt(
            promptId,
            collectionId,
            modal
        ) {

            if (!promptId) {

                return;

            }


            const formData =
                new FormData();


            formData.append(
                'action',
                'isago_save_prompt'
            );


            formData.append(
                'nonce',
                isagoSavedPrompts.nonce
            );


            formData.append(
                'prompt_id',
                promptId
            );


            formData.append(
                'collection_id',
                collectionId || 0
            );


            const buttons =
                modal.querySelectorAll(
                    'button'
                );


            buttons.forEach(
                function (button) {

                    button.disabled =
                        true;

                }
            );


            fetch(
                isagoSavedPrompts.ajaxUrl,
                {
                    method: 'POST',

                    credentials:
                        'same-origin',

                    body:
                        formData
                }
            )

            .then(
                function (response) {

                    return response.json();

                }
            )

            .then(
                function (response) {

                    if (
                        !response
                        ||
                        !response.success
                    ) {

                        throw new Error(
                            response
                            &&
                            response.data
                            &&
                            response.data.message
                                ? response.data.message
                                : 'Could not save prompt.'
                        );

                    }


                    updateSaveButton(
                        promptId,
                        true
                    );


                    closeCollectionModal();


                    /*
                     * If the prompt was moved into a collection
                     * from the Saved Prompts screen, remove it
                     * from the uncollected list immediately.
                     */

                    if (collectionId) {

                        const savedItem =
                            document.querySelector(
                                '.isago-saved-prompts-list [data-prompt-id="' +
                                String(promptId) +
                                '"]'
                            );


                        if (savedItem) {

                            savedItem.remove();

                            updateSavedPromptCount();

                        }

                    }

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Isago Saved Prompts:',
                        error
                    );


                    buttons.forEach(
                        function (button) {

                            button.disabled =
                                false;

                        }
                    );

                }
            );

        }


        /*
         * -----------------------------------------------------
         * UPDATE SAVE BUTTON
         * -----------------------------------------------------
         */

        function updateSaveButton(
            promptId,
            saved
        ) {

            document
                .querySelectorAll(
                    '.isago-save-prompt-button'
                )
                .forEach(
                    function (button) {

                        if (
                            button.getAttribute(
                                'data-prompt-id'
                            ) !==
                            String(
                                promptId
                            )
                        ) {

                            return;

                        }


                        button.classList.toggle(
                            'is-saved',
                            saved
                        );


                        button.setAttribute(
                            'aria-pressed',
                            saved
                                ? 'true'
                                : 'false'
                        );


                        button.setAttribute(
                            'aria-label',
                            saved
                                ? 'Remove from saved prompts'
                                : 'Save prompt'
                        );


                        const icon =
                            button.querySelector(
                                'i'
                            );


                        if (icon) {

                            icon.className =
                                saved
                                    ? 'fa-solid fa-bookmark'
                                    : 'fa-regular fa-bookmark';

                        }

                    }
                );

        }


        /*
         * -----------------------------------------------------
         * CLOSE MODAL
         * -----------------------------------------------------
         */

        function closeCollectionModal() {

            const modal =
                document.querySelector(
                    '.isago-prompt-collection-modal'
                );


            if (!modal) {

                return;

            }


            modal.hidden =
                true;


            document.body.classList.remove(
                'isago-modal-open'
            );


            activePromptId =
                null;

        }


        /*
         * -----------------------------------------------------
         * BOOKMARK BUTTON
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-save-prompt-button'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();
                event.stopPropagation();


                const promptId =
                    button.getAttribute(
                        'data-prompt-id'
                    );


                if (!promptId) {

                    return;

                }


                if (
                    button.classList.contains(
                        'is-saved'
                    )
                ) {

                    unsavePrompt(
                        promptId,
                        button
                    );

                    return;

                }


                openCollectionModal(
                    promptId
                );

            }
        );


        /*
         * -----------------------------------------------------
         * UNSAVE PROMPT
         * -----------------------------------------------------
         */

        function unsavePrompt(
            promptId,
            button
        ) {

            if (
                button.dataset.loading ===
                '1'
            ) {

                return;

            }


            button.dataset.loading =
                '1';


            button.disabled =
                true;


            const formData =
                new FormData();


            formData.append(
                'action',
                'isago_unsave_prompt'
            );


            formData.append(
                'nonce',
                isagoSavedPrompts.nonce
            );


            formData.append(
                'prompt_id',
                promptId
            );


            fetch(
                isagoSavedPrompts.ajaxUrl,
                {
                    method: 'POST',

                    credentials:
                        'same-origin',

                    body:
                        formData
                }
            )

            .then(
                function (response) {

                    return response.json();

                }
            )

            .then(
                function (response) {

                    if (
                        !response
                        ||
                        !response.success
                    ) {

                        throw new Error(
                            response
                            &&
                            response.data
                            &&
                            response.data.message
                                ? response.data.message
                                : 'Could not remove prompt.'
                        );

                    }


                    updateSaveButton(
                        promptId,
                        false
                    );

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Isago Saved Prompts:',
                        error
                    );

                }
            )

            .finally(
                function () {

                    button.disabled =
                        false;

                    delete button.dataset.loading;

                }
            );

        }


        /*
         * -----------------------------------------------------
         * OPEN COLLECTION
         * -----------------------------------------------------
         */

        function openCollection(
            collectionId,
            collectionElement
        ) {

            if (!collectionId) {

                return;

            }


            const savedSection =
                document.querySelector(
                    '.isago-saved-prompts-section'
                );


            const savedList =
                document.querySelector(
                    '[data-saved-prompts-list]'
                );


            const savedHeading =
                document.querySelector(
                    '.isago-saved-prompts-section-title'
                );


            const savedCount =
                document.querySelector(
                    '.isago-saved-prompts-count'
                );


            const collectionView =
                document.querySelector(
                    '[data-collection-view]'
                );


            const collectionName =
                document.querySelector(
                    '[data-active-collection-name]'
                );


            const collectionCount =
                document.querySelector(
                    '[data-active-collection-count]'
                );


            const collectionList =
                document.querySelector(
                    '[data-collection-prompts-list]'
                );


            if (
                !collectionView
                ||
                !collectionList
            ) {

                return;

            }


            collectionList.innerHTML = `
                <div
                    class="isago-account-loading"
                >
                    Loading collection...
                </div>
            `;


            savedList.hidden =
                true;


            if (savedHeading) {

                savedHeading.closest(
                    '.isago-saved-prompts-heading'
                ).hidden =
                    true;

            }


            collectionView.hidden =
                false;


            const formData =
                new FormData();


            formData.append(
                'action',
                'isago_get_prompt_collection_prompts'
            );


            formData.append(
                'nonce',
                isagoSavedPrompts.nonce
            );


            formData.append(
                'collection_id',
                collectionId
            );


            fetch(
                isagoSavedPrompts.ajaxUrl,
                {
                    method: 'POST',

                    credentials:
                        'same-origin',

                    body:
                        formData
                }
            )

            .then(
                function (response) {

                    return response.json();

                }
            )

            .then(
                function (response) {

                    if (
                        !response
                        ||
                        !response.success
                    ) {

                        throw new Error(
                            response
                            &&
                            response.data
                            &&
                            response.data.message
                                ? response.data.message
                                : 'Could not load collection.'
                        );

                    }


                    const collection =
                        response.data.collection;


                    const prompts =
                        response.data.prompts || [];


                    if (collectionName) {

                        collectionName.textContent =
                            collection.name;

                    }


                    if (collectionCount) {

                        collectionCount.textContent =
                            prompts.length;

                    }


                    renderCollectionPrompts(
                        collectionList,
                        collection.id,
                        prompts
                    );

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Isago Collections:',
                        error
                    );


                    collectionList.innerHTML = `
                        <div
                            class="isago-account-empty"
                        >

                            <h3>
                                Could not load collection
                            </h3>

                            <p>
                                ${escapeHtml(
                                    error.message
                                )}
                            </p>

                        </div>
                    `;

                }
            );

        }


        /*
         * -----------------------------------------------------
         * RENDER COLLECTION PROMPTS
         * -----------------------------------------------------
         */

        function renderCollectionPrompts(
            container,
            collectionId,
            prompts
        ) {

            container.innerHTML =
                '';


            if (!prompts.length) {

                container.innerHTML = `
                    <div
                        class="isago-account-empty"
                    >

                        <h3>
                            This collection is empty
                        </h3>

                        <p>
                            Add saved prompts to this collection
                            to see them here.
                        </p>

                    </div>
                `;

                return;

            }


            prompts.forEach(
                function (prompt) {

                    const article =
                        document.createElement(
                            'article'
                        );


                    article.className =
                        'isago-saved-prompt-item';


                    article.dataset.promptId =
                        prompt.id;


                    article.innerHTML = `
                        <div
                            class="isago-saved-prompt-info"
                        >

                            <h3>

                                <a
                                    href="${escapeHtml(
                                        prompt.url
                                    )}"
                                >

                                    ${escapeHtml(
                                        prompt.title
                                    )}

                                </a>

                            </h3>

                            <span>
                                Added to collection
                                ${escapeHtml(
                                    prompt.created_at
                                )}
                            </span>

                        </div>


                        <div
                            class="isago-saved-prompt-actions"
                        >

                            <button
                                type="button"
                                class="isago-remove-from-collection-button"
                                data-prompt-id="${prompt.id}"
                                data-collection-id="${collectionId}"
                            >

                                <i
                                    class="fa-solid fa-folder-minus"
                                    aria-hidden="true"
                                ></i>

                                Remove from Collection

                            </button>


                            <button
                                type="button"
                                class="isago-saved-prompt-remove"
                                data-prompt-id="${prompt.id}"
                            >

                                <i
                                    class="fa-solid fa-bookmark"
                                    aria-hidden="true"
                                ></i>

                                Remove

                            </button>

                        </div>
                    `;


                    container.appendChild(
                        article
                    );

                }
            );

        }


        /*
         * -----------------------------------------------------
         * COLLECTION BOX CLICK
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const collection =
                    event.target.closest(
                        '.isago-prompt-collection-item'
                    );


                if (!collection) {

                    return;

                }


                /*
                 * Do NOT open the collection when the user
                 * clicked the delete button.
                 */

                if (
                    event.target.closest(
                        '.isago-delete-collection-button'
                    )
                ) {

                    return;

                }


                event.preventDefault();


                const collectionId =
                    collection.getAttribute(
                        'data-collection-id'
                    );


                openCollection(
                    collectionId,
                    collection
                );

            }
        );


        /*
         * -----------------------------------------------------
         * KEYBOARD — OPEN COLLECTION
         * -----------------------------------------------------
         */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key !==
                    'Enter'
                    &&
                    event.key !==
                    ' '
                ) {

                    return;

                }


                const collection =
                    event.target.closest(
                        '.isago-prompt-collection-item'
                    );


                if (!collection) {

                    return;

                }


                if (
                    event.target.closest(
                        '.isago-delete-collection-button'
                    )
                ) {

                    return;

                }


                event.preventDefault();


                openCollection(
                    collection.getAttribute(
                        'data-collection-id'
                    ),
                    collection
                );

            }
        );


        /*
         * -----------------------------------------------------
         * BACK TO SAVED PROMPTS
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '[data-back-to-saved-prompts]'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();


                const collectionView =
                    document.querySelector(
                        '[data-collection-view]'
                    );


                const savedList =
                    document.querySelector(
                        '[data-saved-prompts-list]'
                    );


                const heading =
                    document.querySelector(
                        '.isago-saved-prompts-section-title'
                    );


                const headingWrapper =
                    heading
                        ? heading.closest(
                            '.isago-saved-prompts-heading'
                        )
                        : null;


                if (collectionView) {

                    collectionView.hidden =
                        true;

                }


                if (savedList) {

                    savedList.hidden =
                        false;

                }


                if (headingWrapper) {

                    headingWrapper.hidden =
                        false;

                }

            }
        );


        /*
         * -----------------------------------------------------
         * CREATE COLLECTION FROM MODAL
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-modal-create-collection-button'
                    );


                if (!button) {

                    return;

                }


                const modal =
                    button.closest(
                        '.isago-prompt-collection-modal'
                    );


                if (!modal) {

                    return;

                }


                const form =
                    modal.querySelector(
                        '.isago-modal-create-collection-form'
                    );


                const input =
                    modal.querySelector(
                        '.isago-modal-create-collection-input'
                    );


                if (!form) {

                    return;

                }


                form.hidden =
                    false;


                button.hidden =
                    true;


                if (input) {

                    input.focus();

                }

            }
        );


        /*
         * -----------------------------------------------------
         * CANCEL MODAL COLLECTION CREATION
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-modal-cancel-create-collection'
                    );


                if (!button) {

                    return;

                }


                const modal =
                    button.closest(
                        '.isago-prompt-collection-modal'
                    );


                if (!modal) {

                    return;

                }


                const form =
                    modal.querySelector(
                        '.isago-modal-create-collection-form'
                    );


                const createButton =
                    modal.querySelector(
                        '.isago-modal-create-collection-button'
                    );


                const input =
                    modal.querySelector(
                        '.isago-modal-create-collection-input'
                    );


                if (form) {

                    form.hidden =
                        true;

                }


                if (createButton) {

                    createButton.hidden =
                        false;

                }


                if (input) {

                    input.value =
                        '';

                }

            }
        );


        /*
         * -----------------------------------------------------
         * CREATE COLLECTION FROM MODAL
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-modal-confirm-create-collection'
                    );


                if (!button) {

                    return;

                }


                const modal =
                    button.closest(
                        '.isago-prompt-collection-modal'
                    );


                if (!modal) {

                    return;

                }


                const input =
                    modal.querySelector(
                        '.isago-modal-create-collection-input'
                    );


                if (!input) {

                    return;

                }


                const name =
                    input.value.trim();


                if (!name) {

                    input.focus();

                    return;

                }


                button.disabled =
                    true;


                const formData =
                    new FormData();


                formData.append(
                    'action',
                    'isago_create_prompt_collection'
                );


                formData.append(
                    'nonce',
                    isagoSavedPrompts.nonce
                );


                formData.append(
                    'name',
                    name
                );


                fetch(
                    isagoSavedPrompts.ajaxUrl,
                    {
                        method: 'POST',

                        credentials:
                            'same-origin',

                        body:
                            formData
                    }
                )

                .then(
                    function (response) {

                        return response.json();

                    }
                )

                .then(
                    function (response) {

                        if (
                            !response
                            ||
                            !response.success
                        ) {

                            throw new Error(
                                response
                                &&
                                response.data
                                &&
                                response.data.message
                                    ? response.data.message
                                    : 'Could not create collection.'
                            );

                        }


                        const collectionId =
                            response.data.collection_id;


                        /*
                         * If this modal was opened from
                         * a prompt, automatically save the
                         * prompt into the new collection.
                         */

                        if (
                            activePromptId
                        ) {

                            return savePrompt(
                                activePromptId,
                                collectionId,
                                modal
                            );

                        }


                        closeCollectionModal();

                    }
                )

                .catch(
                    function (error) {

                        console.error(
                            'Isago Collections:',
                            error
                        );


                        button.disabled =
                            false;

                    }
                );

            }
        );


        /*
         * -----------------------------------------------------
         * CLOSE MODAL
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.closest(
                        '.isago-prompt-collection-modal-close'
                    )
                    ||
                    event.target.classList.contains(
                        'isago-prompt-collection-modal-overlay'
                    )
                ) {

                    closeCollectionModal();

                }

            }
        );


        /*
         * -----------------------------------------------------
         * REMOVE SAVED PROMPT
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-saved-prompt-remove'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();


                const promptId =
                    button.getAttribute(
                        'data-prompt-id'
                    );


                if (!promptId) {

                    return;

                }


                button.disabled =
                    true;


                const formData =
                    new FormData();


                formData.append(
                    'action',
                    'isago_unsave_prompt'
                );


                formData.append(
                    'nonce',
                    isagoSavedPrompts.nonce
                );


                formData.append(
                    'prompt_id',
                    promptId
                );


                fetch(
                    isagoSavedPrompts.ajaxUrl,
                    {
                        method: 'POST',

                        credentials:
                            'same-origin',

                        body:
                            formData
                    }
                )

                .then(
                    function (response) {

                        return response.json();

                    }
                )

                .then(
                    function (response) {

                        if (
                            !response
                            ||
                            !response.success
                        ) {

                            throw new Error(
                                response
                                &&
                                response.data
                                &&
                                response.data.message
                                    ? response.data.message
                                    : 'Could not remove prompt.'
                            );

                        }


                        const item =
                            button.closest(
                                '.isago-saved-prompt-item'
                            );


                        if (item) {

                            item.remove();

                        }


                        updateSavedPromptCount();

                    }
                )

                .catch(
                    function (error) {

                        console.error(
                            'Isago Saved Prompts:',
                            error
                        );


                        button.disabled =
                            false;

                    }
                );

            }
        );


        /*
         * -----------------------------------------------------
         * MOVE SAVED PROMPT TO COLLECTION
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-move-prompt-button'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();


                const promptId =
                    button.getAttribute(
                        'data-prompt-id'
                    );


                if (!promptId) {

                    return;

                }


                openCollectionModal(
                    promptId
                );

            }
        );


        /*
         * -----------------------------------------------------
         * REMOVE PROMPT FROM COLLECTION
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-remove-from-collection-button'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();


                const promptId =
                    button.getAttribute(
                        'data-prompt-id'
                    );


                const collectionId =
                    button.getAttribute(
                        'data-collection-id'
                    );


                if (
                    !promptId
                    ||
                    !collectionId
                ) {

                    return;

                }


                button.disabled =
                    true;


                const formData =
                    new FormData();


                formData.append(
                    'action',
                    'isago_remove_prompt_from_collection'
                );


                formData.append(
                    'nonce',
                    isagoSavedPrompts.nonce
                );


                formData.append(
                    'prompt_id',
                    promptId
                );


                formData.append(
                    'collection_id',
                    collectionId
                );


                fetch(
                    isagoSavedPrompts.ajaxUrl,
                    {
                        method: 'POST',

                        credentials:
                            'same-origin',

                        body:
                            formData
                    }
                )

                .then(
                    function (response) {

                        return response.json();

                    }
                )

                .then(
                    function (response) {

                        if (
                            !response
                            ||
                            !response.success
                        ) {

                            throw new Error(
                                response
                                &&
                                response.data
                                &&
                                response.data.message
                                    ? response.data.message
                                    : 'Could not remove prompt from collection.'
                            );

                        }


                        const item =
                            button.closest(
                                '.isago-saved-prompt-item'
                            );


                        if (item) {

                            item.remove();

                        }


                        updateActiveCollectionCount();

                    }
                )

                .catch(
                    function (error) {

                        console.error(
                            'Isago Collections:',
                            error
                        );


                        button.disabled =
                            false;

                    }
                );

            }
        );


         /*
 * -----------------------------------------------------
 * OPEN COLLECTION WITHOUT PAGE REFRESH
 * -----------------------------------------------------
 */

document.addEventListener(
    'click',
    function (event) {

        const collection =
            event.target.closest(
                '.isago-prompt-collection-item'
            );


        if (!collection) {

            return;

        }


        /*
         * Do not open the collection when the user
         * clicked the delete button.
         */

        if (
            event.target.closest(
                '.isago-delete-collection-button'
            )
        ) {

            return;

        }


        event.preventDefault();


        const collectionId =
            collection.getAttribute(
                'data-collection-id'
            );


        if (!collectionId) {

            return;

        }


        openAccountCollection(
            collectionId
        );

    }
);


/*
 * -----------------------------------------------------
 * OPEN COLLECTION WITH ENTER / SPACE
 * -----------------------------------------------------
 */

document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key !== 'Enter'
            &&
            event.key !== ' '
        ) {

            return;

        }


        const collection =
            event.target.closest(
                '.isago-prompt-collection-item'
            );


        if (!collection) {

            return;

        }


        if (
            event.target.closest(
                '.isago-delete-collection-button'
            )
        ) {

            return;

        }


        event.preventDefault();


        const collectionId =
            collection.getAttribute(
                'data-collection-id'
            );


        if (!collectionId) {

            return;

        }


        openAccountCollection(
            collectionId
        );

    }
);


/*
 * -----------------------------------------------------
 * LOAD COLLECTION VIA AJAX
 * -----------------------------------------------------
 */

function openAccountCollection(
    collectionId
) {

    const savedList =
        document.querySelector(
            '[data-saved-prompts-list]'
        );


    const collectionView =
        document.querySelector(
            '[data-collection-view]'
        );


    const collectionName =
        document.querySelector(
            '[data-active-collection-name]'
        );


    const collectionCount =
        document.querySelector(
            '[data-active-collection-count]'
        );


    const promptList =
        document.querySelector(
            '[data-collection-prompts-list]'
        );


    if (
        !savedList
        ||
        !collectionView
        ||
        !collectionName
        ||
        !collectionCount
        ||
        !promptList
    ) {

        return;

    }


    /*
     * Show collection screen immediately.
     */

    savedList.hidden =
        true;


    collectionView.hidden =
        false;


    collectionName.textContent =
        'Loading...';


    collectionCount.textContent =
        '';


    promptList.innerHTML = `
        <div class="isago-collection-loading">
            <i
                class="fa-solid fa-spinner fa-spin"
                aria-hidden="true"
            ></i>

            Loading prompts...
        </div>
    `;


    const formData =
        new FormData();


    formData.append(
        'action',
        'isago_get_prompt_collection_prompts'
    );


    formData.append(
        'nonce',
        isagoSavedPrompts.nonce
    );


    formData.append(
        'collection_id',
        collectionId
    );


    fetch(
        isagoSavedPrompts.ajaxUrl,
        {
            method: 'POST',

            credentials:
                'same-origin',

            body:
                formData
        }
    )

    .then(
        function (response) {

            return response.json();

        }
    )

    .then(
        function (response) {

            if (
                !response
                ||
                !response.success
            ) {

                throw new Error(
                    response
                    &&
                    response.data
                    &&
                    response.data.message
                        ? response.data.message
                        : 'Could not load collection.'
                );

            }


            const collection =
                response.data.collection
                ||
                {};


            const prompts =
                response.data.prompts
                ||
                [];


            collectionName.textContent =
                collection.name
                ||
                'Collection';


            collectionCount.textContent =
                collection.prompt_count
                ||
                0;


            renderAccountCollectionPrompts(
                promptList,
                prompts,
                collectionId
            );

        }
    )

    .catch(
        function (error) {

            console.error(
                'Isago Collections:',
                error
            );


            collectionName.textContent =
                'Collection';


            collectionCount.textContent =
                '';


            promptList.innerHTML = `
                <div class="isago-account-empty">
                    <h3>
                        Could not load collection
                    </h3>

                    <p>
                        Please try again.
                    </p>
                </div>
            `;

        }
    );

}


/*
 * -----------------------------------------------------
 * RENDER COLLECTION PROMPTS
 * -----------------------------------------------------
 */

function renderAccountCollectionPrompts(
    container,
    prompts,
    collectionId
) {

    container.innerHTML =
        '';


    if (
        !prompts.length
    ) {

        container.innerHTML = `
            <div class="isago-account-empty">
                <h3>
                    This collection is empty
                </h3>

                <p>
                    Add saved prompts to this collection
                    to see them here.
                </p>
            </div>
        `;

        return;

    }


    prompts.forEach(
        function (prompt) {

            const article =
                document.createElement(
                    'article'
                );


            article.className =
                'isago-saved-prompt-item';


            article.dataset.promptId =
                prompt.id;


            article.innerHTML = `
                <div
                    class="isago-saved-prompt-info"
                >

                    <h3>

                        <a
                            href="${escapeHtml(
                                prompt.url
                            )}"
                        >
                            ${escapeHtml(
                                prompt.title
                            )}
                        </a>

                    </h3>

                    <span>
                        Added to collection
                        ${escapeHtml(
                            prompt.created_at
                        )}
                    </span>

                </div>


                <div
                    class="isago-saved-prompt-actions"
                >

                    <button
                        type="button"
                        class="isago-remove-from-collection-button"
                        data-prompt-id="${prompt.id}"
                        data-collection-id="${collectionId}"
                         aria-label="Remove prompt from collection"
                    >

                        <i
                            class="fa-solid fa-folder-minus"
                            aria-hidden="true"
                        ></i>

                        Remove from Collection

                    </button>


                    <button
                        type="button"
                        class="isago-move-prompt-button"
                        data-prompt-id="${prompt.id}"
                    >

                        <i
                            class="fa-solid fa-folder-plus"
                            aria-hidden="true"
                        ></i>

                        Move

                    </button>

                </div>
            `;


            container.appendChild(
                article
            );

        }
    );

}


/*
 * -----------------------------------------------------
 * BACK TO SAVED PROMPTS
 * -----------------------------------------------------
 */

document.addEventListener(
    'click',
    function (event) {

        const button =
            event.target.closest(
                '[data-back-to-saved-prompts]'
            );


        if (!button) {

            return;

        }


        event.preventDefault();


        const savedList =
            document.querySelector(
                '[data-saved-prompts-list]'
            );


        const collectionView =
            document.querySelector(
                '[data-collection-view]'
            );


        if (
            savedList
            &&
            collectionView
        ) {

            collectionView.hidden =
                true;


            savedList.hidden =
                false;

        }

    }
);


/*
 * -----------------------------------------------------
 * REMOVE PROMPT FROM COLLECTION
 * -----------------------------------------------------
 */

document.addEventListener(
    'click',
    function (event) {

        const button =
            event.target.closest(
                '.isago-remove-from-collection-button'
            );


        if (!button) {

            return;

        }


        event.preventDefault();


        const promptId =
            button.getAttribute(
                'data-prompt-id'
            );


        const collectionId =
            button.getAttribute(
                'data-collection-id'
            );


        if (
            !promptId
            ||
            !collectionId
        ) {

            return;

        }


        button.disabled =
            true;


        const formData =
            new FormData();


        formData.append(
            'action',
            'isago_remove_prompt_from_collection'
        );


        formData.append(
            'nonce',
            isagoSavedPrompts.nonce
        );


        formData.append(
            'prompt_id',
            promptId
        );


        formData.append(
            'collection_id',
            collectionId
        );


        fetch(
            isagoSavedPrompts.ajaxUrl,
            {
                method: 'POST',

                credentials:
                    'same-origin',

                body:
                    formData
            }
        )

        .then(
            function (response) {

                return response.json();

            }
        )

        .then(
            function (response) {

                if (
                    !response
                    ||
                    !response.success
                ) {

                    throw new Error(
                        response
                        &&
                        response.data
                        &&
                        response.data.message
                            ? response.data.message
                            : 'Could not remove prompt.'
                    );

                }


                /*
                 * Remove the prompt from the current
                 * collection screen without refreshing.
                 */

                const item =
                    button.closest(
                        '.isago-saved-prompt-item'
                    );


                if (item) {

                    item.remove();

                }


                /*
                 * Refresh the collection count.
                 */

                const count =
                    document.querySelector(
                        '[data-active-collection-count]'
                    );


                if (count) {

                    const current =
                        parseInt(
                            count.textContent,
                            10
                        )
                        ||
                        0;


                    count.textContent =
                        Math.max(
                            0,
                            current - 1
                        );

                }

            }
        )

        .catch(
            function (error) {

                console.error(
                    'Isago Collections:',
                    error
                );


                button.disabled =
                    false;

            }
        );

    }
);


        /*
         * -----------------------------------------------------
         * DELETE COLLECTION
         * -----------------------------------------------------
         */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.isago-delete-collection-button'
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();
                event.stopPropagation();


                const collectionId =
                    button.getAttribute(
                        'data-collection-id'
                    );


                if (!collectionId) {

                    return;

                }


                if (
                    !window.confirm(
                        'Delete this collection? The saved prompts will not be deleted.'
                    )
                ) {

                    return;

                }


                button.disabled =
                    true;


                const formData =
                    new FormData();


                formData.append(
                    'action',
                    'isago_delete_prompt_collection'
                );


                formData.append(
                    'nonce',
                    isagoSavedPrompts.nonce
                );


                formData.append(
                    'collection_id',
                    collectionId
                );


                fetch(
                    isagoSavedPrompts.ajaxUrl,
                    {
                        method: 'POST',

                        credentials:
                            'same-origin',

                        body:
                            formData
                    }
                )

                .then(
                    function (response) {

                        return response.json();

                    }
                )

                .then(
                    function (response) {

                        if (
                            !response
                            ||
                            !response.success
                        ) {

                            throw new Error(
                                response
                                &&
                                response.data
                                &&
                                response.data.message
                                    ? response.data.message
                                    : 'Could not delete collection.'
                            );

                        }


                        const item =
                            button.closest(
                                '.isago-prompt-collection-item'
                            );


                        if (item) {

                            item.remove();

                        }

                    }
                )

                .catch(
                    function (error) {

                        console.error(
                            'Isago Collections:',
                            error
                        );


                        button.disabled =
                            false;

                    }
                );

            }
        );


        /*
         * -----------------------------------------------------
         * ACCOUNT PAGE — NEW COLLECTION
         * -----------------------------------------------------
         */

        const createButton =
            document.querySelector(
                '.isago-create-collection-button'
            );


        const createForm =
            document.querySelector(
                '.isago-create-collection-form'
            );


        const createInput =
            document.querySelector(
                '.isago-create-collection-input'
            );


        const confirmButton =
            document.querySelector(
                '.isago-confirm-create-collection'
            );


        const cancelButton =
            document.querySelector(
                '.isago-cancel-create-collection'
            );


        if (
            createButton
            &&
            createForm
        ) {

            createButton.addEventListener(
                'click',
                function () {

                    createForm.hidden =
                        false;


                    createButton.hidden =
                        true;


                    if (createInput) {

                        createInput.focus();

                    }

                }
            );

        }


        if (
            cancelButton
        ) {

            cancelButton.addEventListener(
                'click',
                function () {

                    createForm.hidden =
                        true;


                    if (createButton) {

                        createButton.hidden =
                            false;

                    }


                    if (createInput) {

                        createInput.value =
                            '';

                    }

                }
            );

        }


        if (
            confirmButton
            &&
            createInput
        ) {

            confirmButton.addEventListener(
                'click',
                function () {

                    const name =
                        createInput.value.trim();


                    if (!name) {

                        createInput.focus();

                        return;

                    }


                    confirmButton.disabled =
                        true;


                    const formData =
                        new FormData();


                    formData.append(
                        'action',
                        'isago_create_prompt_collection'
                    );


                    formData.append(
                        'nonce',
                        isagoSavedPrompts.nonce
                    );


                    formData.append(
                        'name',
                        name
                    );


                    fetch(
                        isagoSavedPrompts.ajaxUrl,
                        {
                            method: 'POST',

                            credentials:
                                'same-origin',

                            body:
                                formData
                        }
                    )

                    .then(
                        function (response) {

                            return response.json();

                        }
                    )

                    .then(
                        function (response) {

                            if (
                                !response
                                ||
                                !response.success
                            ) {

                                throw new Error(
                                    response
                                    &&
                                    response.data
                                    &&
                                    response.data.message
                                        ? response.data.message
                                        : 'Could not create collection.'
                                );

                            }


                            /*
                             * Add the new collection to the
                             * page without refreshing.
                             */

                            addCollectionToPage(
                                response.data.collection_id,
                                name
                            );


                            createInput.value =
                                '';


                            createForm.hidden =
                                true;


                            createButton.hidden =
                                false;


                            confirmButton.disabled =
                                false;

                        }
                    )

                    .catch(
                        function (error) {

                            console.error(
                                'Isago Collections:',
                                error
                            );


                            confirmButton.disabled =
                                false;

                        }
                    );

                }
            );

        }


        /*
         * -----------------------------------------------------
         * ADD NEW COLLECTION TO PAGE
         * -----------------------------------------------------
         */

        function addCollectionToPage(
            collectionId,
            name
        ) {

            const list =
                document.querySelector(
                    '.isago-prompt-collections-list'
                );


            if (!list) {

                return;

            }


            const empty =
                list.querySelector(
                    '.isago-account-empty'
                );


            if (empty) {

                empty.remove();

            }


            const article =
                document.createElement(
                    'article'
                );


            article.className =
                'isago-prompt-collection-item';


            article.dataset.collectionId =
                collectionId;


            article.setAttribute(
                'tabindex',
                '0'
            );


            article.setAttribute(
                'role',
                'button'
            );


            article.innerHTML = `
                <div
                    class="isago-prompt-collection-info"
                >

                    <h3>
                        ${escapeHtml(name)}
                    </h3>

                    <span>
                        0 prompts
                    </span>

                </div>

                <button
                    type="button"
                    class="isago-delete-collection-button"
                    data-collection-id="${collectionId}"
                    aria-label="Delete ${escapeHtml(name)}"
                >

                    <i
                        class="fa-solid fa-trash"
                        aria-hidden="true"
                    ></i>

                </button>
            `;


            list.prepend(
                article
            );

        }


        /*
         * -----------------------------------------------------
         * UPDATE SAVED PROMPT COUNT
         * -----------------------------------------------------
         */

        function updateSavedPromptCount() {

            const list =
                document.querySelector(
                    '[data-saved-prompts-list]'
                );


            const count =
                document.querySelector(
                    '.isago-saved-prompts-count'
                );


            if (
                !list
                ||
                !count
            ) {

                return;

            }


            const items =
                list.querySelectorAll(
                    '.isago-saved-prompt-item'
                );


            count.textContent =
                items.length;

        }


        /*
         * -----------------------------------------------------
         * UPDATE ACTIVE COLLECTION COUNT
         * -----------------------------------------------------
         */

        function updateActiveCollectionCount() {

            const list =
                document.querySelector(
                    '[data-collection-prompts-list]'
                );


            const count =
                document.querySelector(
                    '[data-active-collection-count]'
                );


            if (
                !list
                ||
                !count
            ) {

                return;

            }


            count.textContent =
                list.querySelectorAll(
                    '.isago-saved-prompt-item'
                ).length;

        }


        /*
         * -----------------------------------------------------
         * ESCAPE HTML
         * -----------------------------------------------------
         */

        function escapeHtml(
            value
        ) {

            const div =
                document.createElement(
                    'div'
                );


            div.textContent =
                value;


            return div.innerHTML;

        }


/*
 * -----------------------------------------------------
 * REFRESH COLLECTION LIST WITHOUT PAGE RELOAD
 * -----------------------------------------------------
 */

function refreshAccountCollections()
{

    const collectionsList =
        document.querySelector(
            '.isago-prompt-collections-list'
        );


    if (!collectionsList) {

        return;

    }


    const formData =
        new FormData();


    formData.append(
        'action',
        'isago_get_prompt_collections'
    );


    formData.append(
        'nonce',
        isagoSavedPrompts.nonce
    );


    fetch(
        isagoSavedPrompts.ajaxUrl,
        {
            method: 'POST',

            credentials:
                'same-origin',

            body:
                formData
        }
    )

    .then(
        function (response) {

            return response.json();

        }
    )

    .then(
        function (response) {

            if (
                !response
                ||
                !response.success
            ) {

                throw new Error(
                    'Could not refresh collections.'
                );

            }


            const collections =
                response.data.collections
                ||
                [];


            collectionsList.innerHTML =
                '';


            if (!collections.length) {

                collectionsList.innerHTML = `
                    <div
                        class="isago-account-empty isago-account-empty-small"
                    >

                        <h3>
                            No collections yet
                        </h3>

                        <p>
                            Create collections to organize your
                            saved prompts.
                        </p>

                    </div>
                `;

                return;

            }


            collections.forEach(
                function (collection) {

                    const article =
                        document.createElement(
                            'article'
                        );


                    article.className =
                        'isago-prompt-collection-item';


                    article.dataset.collectionId =
                        collection.id;


                    article.tabIndex =
                        0;


                    article.setAttribute(
                        'role',
                        'button'
                    );


                    article.setAttribute(
                        'aria-label',
                        'Open collection ' +
                        collection.name
                    );


                    article.innerHTML = `
                        <div
                            class="isago-prompt-collection-info"
                        >

                            <div
                                class="isago-prompt-collection-icon"
                            >

                                <i
                                    class="fa-regular fa-folder"
                                    aria-hidden="true"
                                ></i>

                            </div>


                            <div>

                                <h3>
                                    ${escapeHtml(
                                        collection.name
                                    )}
                                </h3>

                                <span>
                                    ${collection.prompt_count}
                                    prompt${collection.prompt_count !== 1 ? 's' : ''}
                                </span>

                            </div>

                        </div>


                        <div
                            class="isago-prompt-collection-actions"
                        >

                            <i
                                class="fa-solid fa-chevron-right isago-collection-open-icon"
                                aria-hidden="true"
                            ></i>


                            <button
                                type="button"
                                class="isago-delete-collection-button"
                                data-collection-id="${collection.id}"
                                aria-label="Delete ${escapeHtml(
                                    collection.name
                                )}"
                            >

                                <i
                                    class="fa-solid fa-trash"
                                    aria-hidden="true"
                                ></i>

                            </button>

                        </div>
                    `;


                    collectionsList.appendChild(
                        article
                    );

                }
            );

        }
    )

    .catch(
        function (error) {

            console.error(
                'Isago Collections:',
                error
            );

        }
    );

}


    }
);
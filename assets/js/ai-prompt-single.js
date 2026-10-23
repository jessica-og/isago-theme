document.addEventListener(
    'DOMContentLoaded',
    function () {

        'use strict';


        /*=================================================
        ROOT
        =================================================*/

        const root =
            document.querySelector(
                '.isago-ai-prompt-single'
            );


        if (!root) {
            return;
        }


        /*=================================================
        DATA
        =================================================*/

        const dataElement =
            root.querySelector(
                '#isago-ai-prompt-data'
            );


        const preview =
            root.querySelector(
                '#isago-ai-prompt-preview'
            );


        if (
            !dataElement ||
            !preview
        ) {
            return;
        }


        /*=================================================
        PARSE DATA
        =================================================*/

        let data;


        try {

            data =
                JSON.parse(
                    dataElement.textContent
                );

        } catch (error) {

            console.error(
                'Isago AI Prompt data error:',
                error
            );

            return;

        }


        /*=================================================
        VARIABLES
        =================================================*/

        const variables =
            Array.isArray(
                data.variables
            )
                ? data.variables
                : [];


        /*=================================================
        INPUTS
        =================================================*/

        const inputs =
            root.querySelectorAll(
                '.isago-ai-prompt-variable-input'
            );


        const counter =
            root.querySelector(
                '.isago-ai-prompt-variable-count'
            );


        const resetButton =
            root.querySelector(
                '.isago-ai-prompt-reset'
            );


        const copyButton =
            root.querySelector(
                '.isago-ai-prompt-copy'
            );


        /*=================================================
        ORIGINAL HTML
        =================================================*/

        const originalHTML =
            preview.innerHTML;


        /*=================================================
        VARIABLE MAP
        =================================================*/

        const variableMap = {};


        variables.forEach(
            function (variable) {

                const name =
                    String(
                        variable.name ||
                        ''
                    )
                    .trim()
                    .toLowerCase();


                if (!name) {
                    return;
                }


                variableMap[name] =
                    name;

            }
        );


        /*=================================================
        GET VALUES
        =================================================*/

        function getValues()
        {

            const values = {};


            inputs.forEach(
                function (input) {

                    const name =
                        String(
                            input.dataset.variable ||
                            ''
                        )
                        .trim()
                        .toLowerCase();


                    if (!name) {
                        return;
                    }


                    values[name] =
                        input.value.trim();

                }
            );


            return values;

        }


        /*=================================================
        ESCAPE HTML
        =================================================*/

        function escapeHTML(
            value
        )
        {

            const div =
                document.createElement(
                    'div'
                );


            div.textContent =
                value;


            return div.innerHTML;

        }


        /*=================================================
        ESCAPE REGEX
        =================================================*/

        function escapeRegExp(
            value
        )
        {

            return value.replace(
                /[.*+?^${}()|[\]\\]/g,
                '\\$&'
            );

        }


        /*=================================================
        BUILD VARIABLE TOKENS
        =================================================*/

        function buildVariableTokens()
        {

            /*
             * Restore the untouched original HTML
             * before building tokens.
             */

            preview.innerHTML =
                originalHTML;


            /*
             * Process every text node.
             */

            const walker =
                document.createTreeWalker(
                    preview,
                    NodeFilter.SHOW_TEXT,
                    {
                        acceptNode:
                            function (node) {

                                /*
                                 * Ignore script/style.
                                 */

                                if (
                                    node.parentElement &&
                                    (
                                        node.parentElement.closest(
                                            'script'
                                        )
                                        ||
                                        node.parentElement.closest(
                                            'style'
                                        )
                                    )
                                ) {

                                    return NodeFilter.FILTER_REJECT;

                                }


                                /*
                                 * Only process text containing
                                 * at least one variable.
                                 */

                                const text =
                                    node.nodeValue;


                                const hasVariable =
                                    /\{\{\s*[a-zA-Z0-9_-]+\s*\}\}/.test(
                                        text
                                    );


                                return hasVariable
                                    ? NodeFilter.FILTER_ACCEPT
                                    : NodeFilter.FILTER_REJECT;

                            }
                    }
                );


            const textNodes = [];


            let currentNode;


            while (
                (
                    currentNode =
                        walker.nextNode()
                )
            ) {

                textNodes.push(
                    currentNode
                );

            }


            /*
             * Process from the end so replacing
             * nodes doesn't affect the walker.
             */

            textNodes.reverse();


            textNodes.forEach(
                function (textNode) {

                    let text =
                        textNode.nodeValue;


                    /*
                     * Match every {{variable}}
                     * in this text node.
                     */

                    const pattern =
                        /\{\{\s*([a-zA-Z0-9_-]+)\s*\}\}/g;


                    let match;


                    let lastIndex = 0;


                    const fragment =
                        document.createDocumentFragment();


                    while (
                        (
                            match =
                                pattern.exec(text)
                        )
                    ) {

                        const rawName =
                            match[1]
                                .trim()
                                .toLowerCase();


                        /*
                         * Only transform variables
                         * registered for this prompt.
                         */

                        if (
                            !variableMap[rawName]
                        ) {

                            continue;

                        }


                        /*
                         * Text before variable.
                         */

                        if (
                            match.index >
                            lastIndex
                        ) {

                            fragment.appendChild(
                                document.createTextNode(
                                    text.substring(
                                        lastIndex,
                                        match.index
                                    )
                                )
                            );

                        }


                        /*
                         * Variable token.
                         */

                        const token =
                            document.createElement(
                                'span'
                            );


                        token.className =
                            'isago-ai-prompt-variable-token';


                        token.dataset.variable =
                            rawName;


                        token.textContent =
                            '{{' +
                            rawName +
                            '}}';


                        fragment.appendChild(
                            token
                        );


                        lastIndex =
                            pattern.lastIndex;

                    }


                    /*
                     * Remaining text.
                     */

                    if (
                        lastIndex <
                        text.length
                    ) {

                        fragment.appendChild(
                            document.createTextNode(
                                text.substring(
                                    lastIndex
                                )
                            )
                        );

                    }


                    /*
                     * Replace original text node.
                     */

                    textNode.parentNode.replaceChild(
                        fragment,
                        textNode
                    );

                }
            );

        }


        /*=================================================
        UPDATE VARIABLE TOKENS
        =================================================*/

        function updateVariableTokens()
        {

            const values =
                getValues();


            const tokens =
                preview.querySelectorAll(
                    '.isago-ai-prompt-variable-token'
                );


            tokens.forEach(
                function (token) {

                    const name =
                        String(
                            token.dataset.variable ||
                            ''
                        )
                        .trim()
                        .toLowerCase();


                    if (!name) {
                        return;
                    }


                    const value =
                        values[name] ||
                        '';


                    /*
                     * Empty = show original variable.
                     *
                     * Filled = show user value.
                     */

                    if (value) {

                        token.textContent =
                            value;


                        token.classList.add(
                            'is-filled'
                        );

                    } else {

                        token.textContent =
                            '{{' +
                            name +
                            '}}';


                        token.classList.remove(
                            'is-filled'
                        );

                    }

                }
            );


            /*=================================================
            UPDATE COUNTER
            =================================================*/

            let filled =
                0;


            inputs.forEach(
                function (input) {

                    if (
                        input.value.trim() !== ''
                    ) {

                        filled++;

                    }

                }
            );


            if (counter) {

                counter.textContent =
                    filled +
                    '/' +
                    variables.length;

            }

        }


        /*=================================================
        INPUT EVENTS
        =================================================*/

        inputs.forEach(
            function (input) {

                input.addEventListener(
                    'input',
                    function () {

                        updateVariableTokens();

                    }
                );

            }
        );


        /*=================================================
        RESET
        =================================================*/

        if (resetButton) {

            resetButton.addEventListener(
                'click',
                function () {

                    inputs.forEach(
                        function (input) {

                            input.value =
                                '';

                        }
                    );


                    updateVariableTokens();

                }
            );

        }


        /*=================================================
        GET COPY TEXT
        =================================================*/

        function getCopyText()
        {

            const values =
                getValues();


            /*
             * Start from the raw/original prompt HTML.
             */

            const clone =
                document.createElement(
                    'div'
                );


            clone.innerHTML =
                originalHTML;


            /*
             * Replace variables directly in text nodes.
             */

            function replaceForCopy(
                node
            )
            {

                if (
                    node.nodeType ===
                    Node.TEXT_NODE
                ) {

                    let text =
                        node.nodeValue;


                    variables.forEach(
                        function (variable) {

                            const name =
                                String(
                                    variable.name ||
                                    ''
                                )
                                .trim()
                                .toLowerCase();


                            if (!name) {
                                return;
                            }


                            const value =
                                values[name] ||
                                '';


                            /*
                             * Leave {{variable}} in place
                             * if no value was supplied.
                             */

                            const replacement =
                                value ||
                                '{{' +
                                name +
                                '}}';


                            const pattern =
                                new RegExp(
                                    '\\{\\{\\s*' +
                                    escapeRegExp(
                                        name
                                    ) +
                                    '\\s*\\}\\}',
                                    'gi'
                                );


                            text =
                                text.replace(
                                    pattern,
                                    replacement
                                );

                        }
                    );


                    node.nodeValue =
                        text;


                    return;

                }


                if (
                    node.nodeName ===
                    'SCRIPT'
                    ||
                    node.nodeName ===
                    'STYLE'
                ) {

                    return;

                }


                Array.from(
                    node.childNodes
                ).forEach(
                    function (child) {

                        replaceForCopy(
                            child
                        );

                    }
                );

            }


            replaceForCopy(
                clone
            );


            return clone.innerText.trim();

        }


        /*=================================================
        COPY
        =================================================*/

        if (copyButton) {

            copyButton.addEventListener(
                'click',
                async function () {

                    const text =
                        getCopyText();


                    if (!text) {
                        return;
                    }


                    try {

                        await navigator.clipboard.writeText(
                            text
                        );


                        const original =
                            copyButton.innerHTML;


                        copyButton.innerHTML =
                            '<i class="fa-solid fa-check"></i>' +
                            '<span>Copied</span>';


                        setTimeout(
                            function () {

                                copyButton.innerHTML =
                                    original;

                            },
                            1800
                        );


                    } catch (error) {

                        const textarea =
                            document.createElement(
                                'textarea'
                            );


                        textarea.value =
                            text;


                        textarea.style.position =
                            'fixed';


                        textarea.style.opacity =
                            '0';


                        document.body.appendChild(
                            textarea
                        );


                        textarea.select();


                        document.execCommand(
                            'copy'
                        );


                        textarea.remove();

                    }

                }
            );

        }


        /*=================================================
        INITIALIZE
        =================================================*/

        buildVariableTokens();

        updateVariableTokens();

    }
);
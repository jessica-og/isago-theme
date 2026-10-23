<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO SAVED PROMPTS ACCOUNT SCREEN
=========================================================*/

function isago_render_account_saved_prompts()
{
    if (
        !is_user_logged_in()
    ) {

        return '';

    }


    $user_id =
        get_current_user_id();


    $saved_prompts =
        isago_get_saved_prompts(
            $user_id
        );


    $collections =
        isago_get_prompt_collections(
            $user_id
        );


    $logout_url =
        isago_get_logout_url(
            home_url('/')
        );


    ob_start();

    ?>


    <section
        class="isago-account-wrapper isago-saved-prompts-account"
    >

        <div
            class="isago-account-card"
        >


            


            <h1
                class="isago-account-title"
            >
                Saved Prompts
            </h1>


            <p
                class="isago-account-description"
            >
                Save prompts you want to come back to later
                and organize them into collections.
            </p>


            <!-- =============================================
            SAVED PROMPTS
            ============================================== -->

            <section
                class="isago-saved-prompts-section"
            >

                <div
                    class="isago-saved-prompts-heading"
                >

                    <!-- <h2
                        class="isago-saved-prompts-section-title"
                    >
                        Saved Prompts
                    </h2> -->


                    <span
                        class="isago-saved-prompts-count"
                        data-saved-prompts-count
                    >
                        <?php
                        echo esc_html(
                            count(
                                $saved_prompts
                            )
                        );
                        ?>
                    </span>

                </div>


                <div
                    class="isago-saved-prompts-list"
                    data-saved-prompts-list
                >

                    <?php if (!empty($saved_prompts)) : ?>

                        <?php foreach (
                            $saved_prompts
                            as $saved_prompt
                        ) : ?>

                            <?php

                            $prompt_id =
                                absint(
                                    $saved_prompt->prompt_id
                                );


                            $prompt_url =
                                get_permalink(
                                    $prompt_id
                                );


                            $prompt_title =
                                get_the_title(
                                    $prompt_id
                                );

                            ?>


                            <article
                                class="isago-saved-prompt-item"
                                data-prompt-id="<?php echo esc_attr(
                                    $prompt_id
                                ); ?>"
                            >

                                <div
                                    class="isago-saved-prompt-info"
                                >

                                    <h3>

                                        <a
                                            href="<?php echo esc_url(
                                                $prompt_url
                                            ); ?>"
                                        >

                                            <?php
                                            echo esc_html(
                                                $prompt_title
                                            );
                                            ?>

                                        </a>

                                    </h3>


                                    <span>

                                        Saved

                                        <?php
                                        echo esc_html(
                                            mysql2date(
                                                get_option(
                                                    'date_format'
                                                ),
                                                $saved_prompt->created_at
                                            )
                                        );
                                        ?>

                                    </span>

                                </div>


                                <div
                                    class="isago-saved-prompt-actions"
                                >

                                    <button
                                        type="button"
                                        class="isago-move-prompt-button"
                                        data-prompt-id="<?php echo esc_attr(
                                            $prompt_id
                                        ); ?>"
                                    >

                                        <i
                                            class="fa-solid fa-folder-plus"
                                            aria-hidden="true"
                                        ></i>

                                        Move to Collection

                                    </button>


                                    <button
                                        type="button"
                                        class="isago-saved-prompt-remove"
                                        data-prompt-id="<?php echo esc_attr(
                                            $prompt_id
                                        ); ?>"
                                    >

                                        <i
                                            class="fa-solid fa-bookmark"
                                            aria-hidden="true"
                                        ></i>

                                        Remove

                                    </button>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div
                            class="isago-account-empty"
                            data-saved-prompts-empty
                        >

                            <h3>
                                No saved prompts yet
                            </h3>

                            <p>
                                When you save an AI prompt, it will
                                appear here.
                            </p>

                            <a
                                href="<?php echo esc_url(
                                    home_url(
                                        '/ai-prompts-library/'
                                    )
                                ); ?>"
                                class="isago-account-primary-button"
                            >

                                Browse AI Prompts

                                <i
                                    class="fa-solid fa-arrow-right"
                                    aria-hidden="true"
                                ></i>

                            </a>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- =============================================
                AJAX COLLECTION VIEW
                ============================================== -->

                <div
                    class="isago-collection-view"
                    data-collection-view
                    hidden
                >

                    <div
                        class="isago-saved-prompts-heading"
                    >

                        <div>

                            <button
                                type="button"
                                class="isago-back-to-saved-prompts"
                                data-back-to-saved-prompts
                            >

                                <i
                                    class="fa-solid fa-arrow-left"
                                    aria-hidden="true"
                                ></i>

                                Saved Prompts

                            </button>


                            <h2
                                class="isago-active-collection-name"
                                data-active-collection-name
                            ></h2>

                        </div>


                        <span
                            class="isago-active-collection-count"
                            data-active-collection-count
                        >
                            0
                        </span>

                    </div>


                    <div
                        class="isago-saved-prompts-list"
                        data-collection-prompts-list
                    ></div>

                </div>

            </section>


            <!-- =============================================
            COLLECTIONS
            ============================================== -->

            <section
                class="isago-prompt-collections-section"
            >

                <div
                    class="isago-saved-prompts-heading"
                >

                    <!-- <h2>
                        Prompt Collections
                    </h2> -->


                    <button
                        type="button"
                        class="isago-create-collection-button"
                    >

                        <i
                            class="fa-solid fa-plus"
                            aria-hidden="true"
                        ></i>

                        New Collection

                    </button>

                </div>


                <div
                    class="isago-create-collection-form"
                    hidden
                >

                    <input
                        type="text"
                        class="isago-create-collection-input"
                        placeholder="Collection name"
                        maxlength="191"
                    >


                    <button
                        type="button"
                        class="isago-confirm-create-collection"
                    >
                        Create
                    </button>


                    <button
                        type="button"
                        class="isago-cancel-create-collection"
                    >
                        Cancel
                    </button>

                </div>


                <?php if (!empty($collections)) : ?>

                    <div
                        class="isago-prompt-collections-list"
                    >

                        <?php foreach (
                            $collections
                            as $collection
                        ) : ?>

                            <article
                                class="isago-prompt-collection-item"
                                data-collection-id="<?php echo esc_attr(
                                    $collection->id
                                ); ?>"
                                tabindex="0"
                                role="button"
                                aria-label="<?php
                                echo esc_attr(
                                    'Open collection ' .
                                    $collection->name
                                );
                                ?>"
                            >

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

                                            <?php
                                            echo esc_html(
                                                $collection->name
                                            );
                                            ?>

                                        </h3>


                                        <span>

                                            <?php
                                            echo esc_html(
                                                (int)
                                                $collection->prompt_count
                                            );
                                            ?>

                                            prompt<?php
                                            echo (
                                                (int)
                                                $collection->prompt_count !== 1
                                                    ? 's'
                                                    : ''
                                            );
                                            ?>

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
                                        data-collection-id="<?php echo esc_attr(
                                            $collection->id
                                        ); ?>"
                                        aria-label="<?php
                                        echo esc_attr(
                                            'Delete ' .
                                            $collection->name
                                        );
                                        ?>"
                                    >

                                        <i
                                            class="fa-solid fa-trash"
                                            aria-hidden="true"
                                        ></i>

                                    </button>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php else : ?>

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

                <?php endif; ?>

            </section>


            <!-- =============================================
            FOOTER
            ============================================== -->

            <div
                class="isago-account-footer"
            >

                <a
                    href="<?php echo esc_url(
                        home_url('/')
                    ); ?>"
                    class="isago-account-footer-link"
                >
                    Back to Isago
                </a>


                <a
                    href="<?php echo esc_url(
                        $logout_url
                    ); ?>"
                    class="isago-account-footer-link isago-account-logout"
                >

                    <i
                        class="fa-solid fa-right-from-bracket"
                        aria-hidden="true"
                    ></i>

                    Logout

                </a>

            </div>


        </div>

    </section>


    <!-- =====================================================
    SAVE / MOVE PROMPT MODAL
    ====================================================== -->

    <div
        class="isago-prompt-collection-modal"
        hidden
    >

        <div
            class="isago-prompt-collection-modal-overlay"
        ></div>


        <div
            class="isago-prompt-collection-modal-content"
            role="dialog"
            aria-modal="true"
            aria-labelledby="isago-prompt-collection-modal-title"
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


            <h2
                id="isago-prompt-collection-modal-title"
            >
                Save Prompt
            </h2>


            <p>
                Save this prompt or add it to a collection.
            </p>


            <div
                class="isago-prompt-collection-modal-list"
            >

                <div
                    class="isago-prompt-collection-modal-loading"
                >
                    Loading collections...
                </div>

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

    </div>


    <?php

    return ob_get_clean();
}
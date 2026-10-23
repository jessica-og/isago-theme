
(function () {
    'use strict';

    function init() {
        const config = window.isagoImageKitManager;

        const accountSelect = document.getElementById('isago-ik-account');
        const folderSelect = document.getElementById('isago-ik-folder');
        const fileInput = document.getElementById('isago-ik-files');
        const uploadButton = document.getElementById('isago-ik-upload');
        const grid = document.getElementById('isago-ik-grid');
        const pagination = document.getElementById('isago-ik-pagination');
        const notice = document.getElementById('isago-ik-notice');
        const progress = document.getElementById('isago-ik-progress');

        if (
            !accountSelect ||
            !folderSelect ||
            !fileInput ||
            !uploadButton ||
            !grid ||
            !pagination ||
            !notice ||
            !progress
        ) {
            return;
        }

        function showNotice(message, type) {
            notice.className = 'notice ' + (type || 'notice-info');
            notice.style.display = 'block';

            const paragraph = notice.querySelector('p');

            if (paragraph) {
                paragraph.textContent = message;
            }
        }

        function clearNotice() {
            notice.style.display = 'none';

            const paragraph = notice.querySelector('p');

            if (paragraph) {
                paragraph.textContent = '';
            }
        }

        if (!config || !config.ajaxUrl || !config.nonce) {
            showNotice(
                'ImageKit manager configuration was not loaded. Check the PHP enqueue configuration.',
                'notice-error'
            );
            grid.textContent = '';
            return;
        }

        function postAjax(action, data) {
            const body = new FormData();

            body.append('action', action);
            body.append('nonce', config.nonce);

            Object.keys(data || {}).forEach(function (key) {
                body.append(key, data[key] == null ? '' : data[key]);
            });

            return fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            }).then(function (response) {
                return response.text().then(function (text) {
                    let json;

                    try {
                        json = JSON.parse(text);
                    } catch (error) {
                        throw new Error(
                            'WordPress returned a non-JSON response. Check the PHP error log for a fatal error.'
                        );
                    }

                    if (!response.ok || !json || json.success !== true) {
                        const message =
                            json && json.data && json.data.message
                                ? json.data.message
                                : 'The request failed. Check the PHP error log and browser Network tab.';

                        throw new Error(message);
                    }

                    return json.data;
                });
            });
        }

        function fillAccounts() {
            const accounts = config.accounts || {};
            const ids = Object.keys(accounts);

            accountSelect.innerHTML = '';

            if (!ids.length) {
                const option = document.createElement('option');

                option.value = '';
                option.textContent = 'No ImageKit accounts found';

                accountSelect.appendChild(option);
                accountSelect.disabled = true;
                uploadButton.disabled = true;

                showNotice(
                    'No ImageKit accounts were found. Check inc/media/accounts.php and wp-config.php.',
                    'notice-error'
                );

                return;
            }

            ids.forEach(function (id) {
                const option = document.createElement('option');

                option.value = id;
                option.textContent = accounts[id].name || id;

                accountSelect.appendChild(option);
            });

            accountSelect.disabled = false;
            uploadButton.disabled = false;
        }

        function formatBytes(bytes) {
            const size = Number(bytes || 0);

            if (size < 1024) {
                return size + ' B';
            }

            if (size < 1024 * 1024) {
                return (size / 1024).toFixed(1) + ' KB';
            }

            return (size / (1024 * 1024)).toFixed(1) + ' MB';
        }

   
function makeCard(item) {
    const card = document.createElement('article');
    card.className = 'isago-ik-card';

    if (item.file_id) {
        card.dataset.fileId = String(item.file_id);
    }

    const image = document.createElement('img');
    image.src = item.thumbnail_url || item.url;
    image.alt = item.original_name || 'ImageKit image';
    image.loading = 'lazy';

    image.addEventListener('error', function () {
        if (item.url && image.src !== item.url) {
            image.src = item.url;
        }
    });

    const title = document.createElement('h3');
    title.textContent =
        item.original_name ||
        item.file_path ||
        'ImageKit image';

    const meta = document.createElement('div');
    meta.className = 'isago-ik-meta';
    meta.textContent =
        (item.width || 0) + ' × ' + (item.height || 0) +
        ' · ' + formatBytes(item.file_size) +
        ' · ' + (item.file_path || 'ImageKit');

    const actions = document.createElement('div');
    actions.className = 'isago-ik-actions';

    // View image
    const open = document.createElement('a');
    open.className = 'button';
    open.href = item.url;
    open.target = '_blank';
    open.rel = 'noopener noreferrer';
    open.textContent = 'View image';

    // Copy URL
    const copy = document.createElement('button');
    copy.type = 'button';
    copy.className = 'button';
    copy.textContent = 'Copy URL';

    copy.addEventListener('click', function () {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(item.url)
                .then(function () {
                    showNotice('Image URL copied.', 'notice-success');
                })
                .catch(function () {
                    window.prompt('Copy this image URL:', item.url);
                });
        } else {
            window.prompt('Copy this image URL:', item.url);
        }
    });

    // Delete directly from ImageKit using file_id.
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'button button-link-delete';
    remove.textContent = 'Delete';

    if (!item.file_id) {
        remove.disabled = true;
        remove.title = 'ImageKit did not return a file ID for this image.';
    }

    remove.addEventListener('click', function () {
        if (!item.file_id) {
            showNotice(
                'Cannot delete this image because ImageKit did not provide its file ID.',
                'notice-error'
            );
            return;
        }

        const accountId = accountSelect.value;
        const imageName =
            item.original_name ||
            item.file_path ||
            'this image';

        if (!accountId) {
            showNotice('Select an ImageKit account first.', 'notice-error');
            return;
        }

        const confirmed = window.confirm(
            'Permanently delete "' + imageName + '" from ImageKit?\n\n' +
            'This cannot be undone. Any website content using this image may stop displaying it.'
        );

        if (!confirmed) {
            return;
        }

        remove.disabled = true;
        remove.textContent = 'Deleting…';

        postAjax('isago_imagekit_manager_delete_imagekit_file', {
            account_id: accountId,
            file_id: item.file_id
        })
        .then(function (result) {
            showNotice(
                result.message || 'Image deleted successfully.',
                'notice-success'
            );

            // Refresh the selected folder.
            return loadItems(currentPage);
        })
        .catch(function (error) {
            showNotice(error.message, 'notice-error');
        })
        .finally(function () {
            remove.disabled = false;
            remove.textContent = 'Delete';
        });
    });

    actions.appendChild(open);
    actions.appendChild(copy);
    actions.appendChild(remove);

    card.appendChild(image);
    card.appendChild(title);
    card.appendChild(meta);
    card.appendChild(actions);

    return card;
}


        let currentPage = 1;
        let requestNumber = 0;

        function renderItems(items, emptyMessage) {
            grid.innerHTML = '';

            if (!items || !items.length) {
                const empty = document.createElement('p');

                empty.className = 'isago-ik-empty';
                empty.textContent =
                    emptyMessage ||
                    'No images were returned for this folder.';

                grid.appendChild(empty);

                return;
            }

            items.forEach(function (item) {
                grid.appendChild(makeCard(item));
            });
        }

        function renderPagination(info) {
            pagination.innerHTML = '';

            const current = Number(info.current_page || 1);
            const hasNext = Boolean(info.has_next);

            function addButton(label, page, disabled) {
                const button = document.createElement('button');

                button.type = 'button';
                button.className = 'button';
                button.textContent = label;
                button.disabled = disabled;

                button.addEventListener('click', function () {
                    if (!button.disabled) {
                        loadItems(page);
                    }
                });

                pagination.appendChild(button);
            }

            addButton('« Previous', current - 1, current <= 1);

            const pageInfo = document.createElement('span');

            pageInfo.className = 'isago-ik-page-info';
            pageInfo.textContent = 'Page ' + current;

            pagination.appendChild(pageInfo);

            addButton('Next »', current + 1, !hasNext);
        }

        function loadItems(page) {
            page = Math.max(1, Number(page) || 1);

            if (!accountSelect.value || !folderSelect.value) {
                grid.textContent = 'Select an ImageKit account and folder.';
                pagination.innerHTML = '';
                return Promise.resolve();
            }

            const thisRequest = ++requestNumber;

            grid.textContent = 'Loading images from ImageKit…';
            pagination.innerHTML = '';

            return postAjax('isago_imagekit_manager_browse_folder', {
                account_id: accountSelect.value,
                folder: folderSelect.value,
                page: page
            }).then(function (data) {
                // Ignore stale results if the user changed folders quickly.
                if (thisRequest !== requestNumber) {
                    return;
                }

                const info = data.pagination || {};

                currentPage = Number(info.current_page || page);

                renderItems(
                    data.items || [],
                    data.empty_message ||
                        'No images were found in this folder.'
                );

                renderPagination(info);

                if (!data.items || !data.items.length) {
                    showNotice(
                        data.empty_message ||
                            'No images were found in the selected folder. Confirm the folder path in ImageKit.',
                        'notice-warning'
                    );
                } else {
                    clearNotice();
                }
            }).catch(function (error) {
                if (thisRequest !== requestNumber) {
                    return;
                }

                grid.textContent = '';
                pagination.innerHTML = '';

                showNotice(error.message, 'notice-error');
            });
        }

        function getAuth(accountId) {
            return postAjax('isago_imagekit_auth', {
                account_id: accountId
            });
        }

        function uploadOne(file, accountId) {
            progress.textContent =
                'Requesting authorization for ' + file.name + '…';

            return getAuth(accountId).then(function (auth) {
                if (
                    !auth ||
                    !auth.public_key ||
                    !auth.token ||
                    !auth.signature ||
                    !auth.expire
                ) {
                    throw new Error(
                        'The ImageKit authorization endpoint did not return all required upload credentials.'
                    );
                }

                const destinationFolder = folderSelect.value;

                if (!destinationFolder) {
                    throw new Error(
                        'Please select an ImageKit destination folder.'
                    );
                }

                const form = new FormData();

                form.append('file', file);
                form.append('fileName', file.name);
                form.append('folder', destinationFolder);
                form.append('publicKey', auth.public_key);
                form.append('token', auth.token);
                form.append('signature', auth.signature);
                form.append('expire', String(auth.expire));

                progress.textContent =
                    'Uploading ' + file.name + ' to ImageKit…';

                return fetch(
                    'https://upload.imagekit.io/api/v1/files/upload',
                    {
                        method: 'POST',
                        body: form
                    }
                ).then(function (response) {
                    return response.text().then(function (text) {
                        let json;

                        try {
                            json = JSON.parse(text);
                        } catch (error) {
                            throw new Error(
                                'ImageKit returned an unreadable response while uploading ' +
                                file.name + '.'
                            );
                        }

                        if (
                            !response.ok ||
                            !json ||
                            !json.fileId ||
                            !json.url
                        ) {
                            throw new Error(
                                json && (json.message || json.help)
                                    ? (json.message || json.help)
                                    : 'ImageKit upload failed for ' + file.name + '.'
                            );
                        }

                        return json;
                    });
                }).then(function (uploaded) {
                    progress.textContent =
                        'Registering ' + file.name + ' in Isago…';

                    return postAjax('isago_imagekit_save_upload', {
                        account_id: accountId,
                        file_id: uploaded.fileId,
                        file_path: uploaded.filePath || '',
                        url: uploaded.url,
                        thumbnail_url:
                            uploaded.thumbnailUrl || uploaded.url,
                        width: uploaded.width || 0,
                        height: uploaded.height || 0,
                        file_size: uploaded.size || file.size || 0,
                        file_name: uploaded.name || file.name,
                        mime_type: file.type || 'image/*',
                        file_type: 'image'
                    });
                }).then(function (saved) {
                    if (!saved || !saved.media_id || !saved.url) {
                        throw new Error(
                            'ImageKit accepted the upload, but Isago did not confirm a database record. Check the save-upload AJAX handler.'
                        );
                    }

                    return saved;
                });
            });
        }

        function uploadSelected() {
            clearNotice();

            const files = Array.prototype.slice.call(fileInput.files || []);
            const accountId = accountSelect.value;

            if (!accountId) {
                showNotice('Select an ImageKit account first.', 'notice-error');
                return;
            }

            if (!files.length) {
                showNotice(
                    'Choose one or more image files first.',
                    'notice-warning'
                );
                return;
            }

            const invalid = files.find(function (file) {
                return !file.type || file.type.indexOf('image/') !== 0;
            });

            if (invalid) {
                showNotice(
                    'Only image files are allowed. Remove ' +
                    invalid.name + ' and try again.',
                    'notice-error'
                );
                return;
            }

            uploadButton.disabled = true;
            fileInput.disabled = true;
            accountSelect.disabled = true;
            folderSelect.disabled = true;

            let chain = Promise.resolve();
            let completed = 0;

            files.forEach(function (file) {
                chain = chain.then(function () {
                    return uploadOne(file, accountId).then(function () {
                        completed += 1;

                        progress.textContent =
                            'Uploaded and saved ' + completed +
                            ' of ' + files.length + ' image(s).';
                    });
                });
            });

            chain.then(function () {
                showNotice(
                    'All selected images were uploaded and saved to Isago.',
                    'notice-success'
                );

                fileInput.value = '';

                return loadItems(1);
            }).catch(function (error) {
                showNotice(error.message, 'notice-error');
            }).finally(function () {
                uploadButton.disabled = false;
                fileInput.disabled = false;
                accountSelect.disabled = false;
                folderSelect.disabled = false;
                progress.textContent = '';
            });
        }

        fillAccounts();

        uploadButton.addEventListener('click', uploadSelected);

        folderSelect.addEventListener('change', function () {
            clearNotice();
            loadItems(1);
        });

        accountSelect.addEventListener('change', function () {
            clearNotice();
            loadItems(1);
        });

        loadItems(1);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

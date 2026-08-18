document.addEventListener('DOMContentLoaded', () => {

    console.log(" SEARCH SYSTEM RUNNING");

    const open = document.querySelector(".isago-search-button");
    const overlay = document.querySelector(".isago-search-overlay");
    const close = document.querySelector(".isago-search-close");
    const input = document.querySelector(".isago-search-input");

    if (!input) {
        console.error("❌ Input not found");
        return;
    }

    let resultsBox = document.querySelector('.isago-search-results');

    if (!resultsBox) {
        resultsBox = document.createElement('div');
        resultsBox.className = 'isago-search-results';
        input.parentElement.appendChild(resultsBox);
    }

    /* =====================
       OPEN
    ===================== */
    if (open && overlay) {
        open.addEventListener("click", () => {
            overlay.classList.add("active");
            document.body.style.overflow = "hidden";
            setTimeout(() => input.focus(), 150);
        });
    }

    /* =====================
       CLOSE
    ===================== */
    if (close && overlay) {
        close.addEventListener("click", () => {
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        });
    }

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && overlay) {
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        }
    });

    /* =====================
       LIVE SEARCH
    ===================== */
    let timeout;

    input.addEventListener('input', function () {

        const keyword = this.value.trim();

        clearTimeout(timeout);

        if (keyword.length < 2) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {

            fetch(`${isago_search.ajax_url}?action=isago_live_search&keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.json())
                .then(response => {

                    const data = response.data || response;

                    console.log("✅ DATA:", data);

                    if (!data.length) {
                        resultsBox.innerHTML = '<div class="no-results">No results</div>';
                        resultsBox.style.display = 'block';
                        return;
                    }

                    let html = '';

                    data.forEach(post => {
                        html += `
                            <a href="${post.link}" class="search-item">
                                <img src="${post.image}" />
                                <span>${post.title}</span>
                            </a>
                        `;
                    });

                    resultsBox.innerHTML = html;
                    resultsBox.style.display = 'block';

                });

        }, 300);

    });

});
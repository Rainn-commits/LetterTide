// resources/js/surat-wizard.js
//
// Step 1 (pilih-template): filter template cards by search input.
// Step 2 (form): autocomplete for any input with [data-autocomplete="user"],
// backed by UserLookupController::search via the URL in [data-search-url]
// (set from the blade view, e.g. data-search-url="{{ route('users.search') }}").

document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Step 1: filter template grid by search ---------- */
    const searchInput = document.getElementById('templateSearch');
    const cards = document.querySelectorAll('.template-card');

    if (searchInput && cards.length) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const match = card.dataset.name.includes(query);
                card.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            const emptyState = document.getElementById('templateGridEmpty');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        });
    }

    /* ---------- Step 2: "Nama Karyawan" autocomplete ---------- */
    document.querySelectorAll('[data-autocomplete="user"]').forEach((input) => {
        const resultsBox = document.getElementById('results_' + input.id.replace('field_', ''));
        const searchUrl = input.dataset.searchUrl || '/users/search';
        if (!resultsBox) return;

        let debounceTimer = null;

        function closeResults() {
            resultsBox.innerHTML = '';
            resultsBox.classList.remove('is-open');
        }

        function renderResults(users) {
            resultsBox.innerHTML = '';

            if (users.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'autocomplete__empty';
                empty.textContent = 'Nama tidak ditemukan.';
                resultsBox.appendChild(empty);
                resultsBox.classList.add('is-open');
                return;
            }

            users.forEach((user) => {
                const item = document.createElement('div');
                item.className = 'autocomplete__item';
                item.textContent = user.name;
                item.addEventListener('click', () => {
                    input.value = user.name;
                    input.dataset.userId = user.id;
                    closeResults();
                });
                resultsBox.appendChild(item);
            });

            resultsBox.classList.add('is-open');
        }

        input.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                closeResults();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then((res) => res.json())
                    .then(renderResults)
                    .catch(() => closeResults());
            }, 250);
        });

        document.addEventListener('click', (e) => {
            if (!resultsBox.contains(e.target) && e.target !== input) {
                closeResults();
            }
        });
    });

});

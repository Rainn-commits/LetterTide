document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('templateSearch');
    const cards = document.querySelectorAll('.template-card-fixed');

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
                closeResults();
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

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-autocomplete="kontak"]').forEach(function (input) {
        const results = document.getElementById(
            'results_' + input.id.replace('field_', '')
        );

        const form = input.closest('form');
        let timeout = null;

        if (!results || !form) return;

        function closeResults() {
            results.innerHTML = '';
            results.classList.remove('is-open');
        }

        function showResults() {
            results.classList.add('is-open');
        }

        input.addEventListener('input', function () {
            clearTimeout(timeout);
            closeResults();

            const query = input.value.trim();

            if (query.length < 2) return;

            timeout = setTimeout(async function () {
                try {
                    const response = await fetch(
                        input.dataset.searchUrl + '?q=' + encodeURIComponent(query),
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Gagal mencari kontak');
                    }

                    const contacts = await response.json();
                    results.innerHTML = '';

                    if (contacts.length === 0) {
                        closeResults();
                        return;
                    }

                    contacts.forEach(function (contact) {
                        const item = document.createElement('div');
                        item.className = 'autocomplete__item';

                        const details = [
                            contact.nomor_hp,
                            contact.instansi,
                            contact.jabatan
                        ].filter(Boolean).join(' • ');

                        item.textContent = details
                            ? `${contact.nama} (${details})`
                            : contact.nama;

                        item.addEventListener('click', function () {
                            input.value = contact.nama;

                            const phoneField = form.querySelector(
                                `[name="fields[${input.dataset.phoneField}]"]`
                            );

                            if (phoneField) {
                                phoneField.value = contact.nomor_hp || '';
                                phoneField.dispatchEvent(
                                    new Event('input', { bubbles: true })
                                );
                            }

                            closeResults();
                        });

                        results.appendChild(item);
                    });

                    showResults();

                } catch (error) {
                    console.error('Pencarian kontak:', error);
                    closeResults();
                }
            }, 300);
        });

        document.addEventListener('click', function (event) {
            if (!input.contains(event.target) && !results.contains(event.target)) {
                closeResults();
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const tanggalMulai = document.getElementById(
        'field_tanggal-mulai-layanan'
    );

    const tanggalSelesai = document.getElementById(
        'field_tanggal-selesai-layanan'
    );

    if (!tanggalMulai || !tanggalSelesai) return;

    const form = tanggalMulai.closest('form');

    function validasiPeriodeLayanan() {
        tanggalMulai.setCustomValidity('');
        tanggalSelesai.setCustomValidity('');

        tanggalSelesai.min = tanggalMulai.value || '';

        if (!tanggalMulai.value) {
            tanggalMulai.setCustomValidity(
                'Tanggal mulai layanan wajib diisi.'
            );
            return tanggalMulai;
        }

        if (!tanggalSelesai.value) {
            tanggalSelesai.setCustomValidity(
                'Tanggal selesai layanan wajib diisi.'
            );
            return tanggalSelesai;
        }

        if (tanggalSelesai.value < tanggalMulai.value) {
            tanggalSelesai.setCustomValidity(
                'Tanggal selesai tidak boleh lebih awal dari tanggal mulai layanan.'
            );
            return tanggalSelesai;
        }

        return null;
    }

    tanggalMulai.addEventListener('change', function () {
        validasiPeriodeLayanan();
    });

    tanggalSelesai.addEventListener('change', function () {
        validasiPeriodeLayanan();
    });

    form.addEventListener('submit', function (event) {
        const inputTidakValid = validasiPeriodeLayanan();

        if (inputTidakValid) {
            event.preventDefault();
            inputTidakValid.reportValidity();
        }
    });
});

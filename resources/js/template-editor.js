document.addEventListener('DOMContentLoaded', function () {

    const textarea = document.getElementById('isi_template');
    const placeholderList = document.getElementById('placeholderList');

    if (!textarea || !placeholderList) return;

    const PLACEHOLDER_REGEX = /\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/g;
    const EMPTY_MESSAGE = 'Belum ada placeholder terdeteksi. Ketik {{nama_variabel}} di isi template.';

    function extractPlaceholders(text) {
        const found = [];
        const seen = new Set();
        let match;

        PLACEHOLDER_REGEX.lastIndex = 0;
        while ((match = PLACEHOLDER_REGEX.exec(text)) !== null) {
            const name = match[1];
            if (!seen.has(name)) {
                seen.add(name);
                found.push(name);
            }
        }

        return found;
    }

    function renderPlaceholders() {
        const placeholders = extractPlaceholders(textarea.value);

        placeholderList.innerHTML = '';

        if (placeholders.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'placeholder-list__empty';
            empty.textContent = EMPTY_MESSAGE;
            placeholderList.appendChild(empty);
            return;
        }

        placeholders.forEach((name) => {
            const pill = document.createElement('span');
            pill.className = 'placeholder-pill';
            pill.textContent = name;
            placeholderList.appendChild(pill);
        });
    }

    textarea.addEventListener('input', renderPlaceholders);

    renderPlaceholders();
});

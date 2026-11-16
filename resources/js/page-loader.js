(function () {
    const loader = document.getElementById('pageLoader');
    const textEl = document.getElementById('pageLoaderText');

    if (!loader) return;

    const TEKS_HALAMAN = 'Memuat halaman';
    const TEKS_PROSES = 'Sedang memproses';
    const BATAS_WAKTU = 30000;

    let timerBatas = null;

    function show(teks) {
        if (textEl) textEl.textContent = teks || TEKS_HALAMAN;

        loader.hidden = false;
        requestAnimationFrame(function () {
            loader.classList.add('is-active');
        });

        clearTimeout(timerBatas);
        timerBatas = setTimeout(hide, BATAS_WAKTU);
    }

    function hide() {
        clearTimeout(timerBatas);
        loader.classList.remove('is-active');
        loader.hidden = true;
    }

    function formPerluLoader(form) {
        return !form.hasAttribute('data-no-loader') && (!form.target || form.target === '_self');
    }

    function teksForm(form) {
        if (form.dataset.loadingText) return form.dataset.loadingText;

        return (form.getAttribute('method') || 'get').toLowerCase() === 'get' ? TEKS_HALAMAN : TEKS_PROSES;
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !formPerluLoader(form)) return;

        setTimeout(function () {
            if (!event.defaultPrevented) show(teksForm(form));
        }, 0);
    });

    const submitAsli = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        if (formPerluLoader(this)) show(teksForm(this));

        return submitAsli.call(this);
    };

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const link = event.target.closest ? event.target.closest('a[href]') : null;
        if (!link) return;

        if (link.hasAttribute('data-no-loader') || link.hasAttribute('download')) return;
        if (link.target && link.target !== '_self') return;

        const href = link.getAttribute('href') || '';
        if (href === '' || href.charAt(0) === '#') return;

        let url;
        try {
            url = new URL(link.href, window.location.href);
        } catch (e) {
            return;
        }

        if (url.protocol !== 'http:' && url.protocol !== 'https:') return;
        if (url.origin !== window.location.origin) return;

        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;

        show(link.dataset.loadingText || TEKS_HALAMAN);
    });

    window.addEventListener('pageshow', hide);

    window.PageLoader = { show: show, hide: hide };
})();

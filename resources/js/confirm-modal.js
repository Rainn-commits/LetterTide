document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('themedConfirmOverlay');
    if (!overlay) return;

    const modal = overlay.querySelector('.confirm-modal');
    const messageEl = document.getElementById('confirmModalMessage');
    const btnCancel = document.getElementById('confirmModalCancel');
    const btnConfirm = document.getElementById('confirmModalConfirm');

    let activeForm = null;
    let lastFocused = null;

    function openModal(form, message, variant, confirmLabel) {
        activeForm = form;
        lastFocused = document.activeElement;

        messageEl.textContent = message;
        btnConfirm.textContent = confirmLabel || 'Ya, Lanjutkan';

        modal.classList.toggle('confirm-modal--danger', variant === 'danger');

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        btnConfirm.focus();

        document.addEventListener('keydown', onKeydown);
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        activeForm = null;
        document.removeEventListener('keydown', onKeydown);
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    function onKeydown(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    }

    btnCancel.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeModal();
        }
    });

    btnConfirm.addEventListener('click', function () {
        const formToSubmit = activeForm;
        closeModal();
        if (formToSubmit) {
            formToSubmit.submit();
        }
    });

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            openModal(
                form,
                form.dataset.confirm,
                form.dataset.confirmVariant || 'default',
                form.dataset.confirmLabel
            );
        });
    });
});

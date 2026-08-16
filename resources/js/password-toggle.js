document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-toggle-password]');
    if (!btn) return;

    const fieldId = btn.getAttribute('data-toggle-password');
    const input = document.getElementById(fieldId);
    if (!input) return;

    const isCurrentlyHidden = input.type === 'password';
    input.type = isCurrentlyHidden ? 'text' : 'password';

    const showIcon = btn.querySelector('.auth-field__toggle-icon--show');
    const hideIcon = btn.querySelector('.auth-field__toggle-icon--hide');
    if (showIcon && hideIcon) {
        showIcon.style.display = isCurrentlyHidden ? 'none' : 'block';
        hideIcon.style.display = isCurrentlyHidden ? 'block' : 'none';
    }

    btn.setAttribute(
        'aria-label',
        isCurrentlyHidden ? 'Sembunyikan password' : 'Tampilkan password'
    );
});
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-toggle-password]');
    if (!btn) return;

    const fieldId = btn.getAttribute('data-toggle-password');
    const input = document.getElementById(fieldId);
    if (!input) return;

    input.type = input.type === 'password' ? 'text' : 'password';
    btn.setAttribute(
        'aria-label',
        input.type === 'password' ? 'Tampilkan password' : 'Sembunyikan password'
    );
});
document.addEventListener('click', (e) => {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.topbar__toggle');

    if (sidebar && sidebar.classList.contains('is-open') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('is-open');
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const hero = document.querySelector('.hero');
    const scene = document.querySelector('.hero__scene');
    if (!hero || !scene) return;

    const layers = scene.querySelectorAll('[data-depth]');
    if (!layers.length) return;

    let ticking = false;
    let pointerX = 0;
    let pointerY = 0;

    function applyParallax() {
        layers.forEach(function (layer) {
            const depth = parseFloat(layer.dataset.depth) || 0.03;
            const moveX = pointerX * depth * 100;
            const moveY = pointerY * depth * 100;
            layer.style.setProperty('--parallax-x', moveX.toFixed(2) + 'px');
            layer.style.setProperty('--parallax-y', moveY.toFixed(2) + 'px');
        });
        ticking = false;
    }

    hero.addEventListener('mousemove', function (e) {
        const rect = hero.getBoundingClientRect();
        pointerX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;   
        pointerY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;   
        if (!ticking) {
            window.requestAnimationFrame(applyParallax);
            ticking = true;
        }
    });

    hero.addEventListener('mouseleave', function () {
        pointerX = 0;
        pointerY = 0;
        window.requestAnimationFrame(applyParallax);
    });
});
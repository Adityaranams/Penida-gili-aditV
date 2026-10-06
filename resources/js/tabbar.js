/**
 * Mobile bottom tab bar: shrinks (labels fold away) while the page scrolls down,
 * and grows back when scrolling up or near the top. Styles live on the
 * `group-data-[compact]/tabbar` variants in partials/mobile-tabbar.blade.php.
 */
const tabbar = document.querySelector('[data-tabbar]');

if (tabbar) {
    const THRESHOLD = 8; // ignore tiny jitters from momentum scrolling
    let lastY = window.scrollY;
    let ticking = false;

    const update = () => {
        const y = window.scrollY;
        const delta = y - lastY;

        if (y < 80) {
            tabbar.removeAttribute('data-compact');
        } else if (delta > THRESHOLD) {
            tabbar.setAttribute('data-compact', '');
        } else if (delta < -THRESHOLD) {
            tabbar.removeAttribute('data-compact');
        }

        if (Math.abs(delta) > THRESHOLD || y < 80) {
            lastY = y;
        }
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(update);
        }
    }, { passive: true });
}

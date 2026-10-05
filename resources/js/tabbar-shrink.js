/**
 * Mobile tab bar: shrink on the way down, grow on the way up.
 *
 * Scrolling down past a short threshold folds the captions away and slims the bar
 * (styles live in resources/css/app.css under `.tabbar`); scrolling up — or coming
 * back near the top — restores it. The state is a single `data-shrunk` attribute,
 * so the CSS owns the animation and this only decides when to flip it.
 */
const bar = document.querySelector('[data-tabbar]');

if (bar) {
    // Ignore the rubber-banding at the very top and tiny finger wobbles.
    const TOP_ZONE = 24;
    const WOBBLE = 6;

    let last = window.scrollY;

    const update = () => {
        const y = Math.max(0, window.scrollY);
        const moved = y - last;

        if (Math.abs(moved) < WOBBLE) {
            return;
        }

        last = y;
        bar.toggleAttribute('data-shrunk', y > TOP_ZONE && moved > 0);
    };

    window.addEventListener('scroll', update, { passive: true });

    // A tab that is tapped while the bar is small should be readable again.
    bar.addEventListener('focusin', () => bar.removeAttribute('data-shrunk'));
}

/**
 * Hand the desktop nav over to its sticky form as soon as the page moves.
 *
 * The nav drawn on the hero cannot be sticky itself: it lives inside a header
 * with `overflow-hidden`, which disables `position: sticky`, and thirteen
 * pages include it. So a second bar takes over — and it arrives at the exact
 * moment the hero nav scrolls out of sight, so the two read as one nav
 * changing shape rather than two separate bars.
 */
const bar = document.querySelector('[data-sticky-nav]');

if (bar) {
    const heroNav = document.querySelector('[data-hero-nav]');

    // The handover point is the bottom of the hero nav: below it the original
    // is gone from the screen, above it the original is still the one on show.
    const threshold = () => {
        const nav = heroNav?.getBoundingClientRect();

        return Math.max(72, nav ? nav.height + window.scrollY + nav.top - 8 : 0);
    };

    let handover = threshold();

    const update = () => bar.classList.toggle('is-visible', window.scrollY > handover);

    const remeasure = () => {
        if (window.scrollY < 4) {
            handover = threshold();
        }

        update();
    };

    remeasure();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', remeasure, { passive: true });
    window.addEventListener('load', remeasure);
}

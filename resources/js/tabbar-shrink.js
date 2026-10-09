/**
 * Publish the space the mobile tab bar occupies.
 *
 * The bar floats clear of the bottom edge, so anything pinned above it — the
 * WhatsApp button, the order screens' action bar — needs the distance from the
 * bar's top to the bottom of the viewport, not just the bar's own height.
 *
 * (The bar used to shrink and fold its captions away on scroll down; it now
 * keeps the same shape at every scroll position, so there is nothing else to do
 * here.)
 */
const bar = document.querySelector('[data-tabbar]');

if (bar) {
    const publish = () => {
        const rect = bar.getBoundingClientRect();
        const occupied = rect.height > 0 ? Math.round(window.innerHeight - rect.top) : 0;

        document.documentElement.style.setProperty('--tabbar-h', `${occupied}px`);
    };

    publish();
    window.addEventListener('resize', publish, { passive: true });
    window.addEventListener('load', publish);
}

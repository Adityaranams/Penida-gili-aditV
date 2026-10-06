/**
 * Horizontal strips marked `data-center-selected` (date strip, island tabs) can
 * overflow on phones; scroll the item marked `aria-current` into the middle so
 * the chosen one — and its neighbours on both sides — are visible on load.
 */
document.querySelectorAll('[data-center-selected]').forEach((strip) => {
    const selected = strip.querySelector('[aria-current]');

    if (!selected || strip.scrollWidth <= strip.clientWidth) {
        return;
    }

    strip.scrollLeft = selected.offsetLeft - strip.offsetLeft - (strip.clientWidth - selected.offsetWidth) / 2;
});

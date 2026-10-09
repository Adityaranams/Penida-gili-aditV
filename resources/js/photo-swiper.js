/**
 * Keep the counter and dots of a swipeable hero gallery in step with the swipe.
 *
 * The swiping itself is CSS scroll snapping, so this only reads the scroll
 * position — nothing here is required for the photos to move.
 */
document.querySelectorAll('[data-photo-swiper]').forEach((swiper) => {
    const track = swiper.querySelector('[data-photo-swiper-track]');
    const current = swiper.querySelector('[data-photo-swiper-current]');
    const dots = [...swiper.querySelectorAll('[data-photo-swiper-dot]')];

    if (!track) {
        return;
    }

    const paint = () => {
        const width = track.clientWidth || 1;
        const index = Math.round(track.scrollLeft / width);

        if (current) {
            current.textContent = String(index + 1);
        }

        dots.forEach((dot, i) => {
            dot.classList.toggle('w-[18px]', i === index);
            dot.classList.toggle('bg-white', i === index);
            dot.classList.toggle('w-[6px]', i !== index);
            dot.classList.toggle('bg-white/60', i !== index);
        });
    };

    track.addEventListener('scroll', paint, { passive: true });
    window.addEventListener('resize', paint, { passive: true });
});

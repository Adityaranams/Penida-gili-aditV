/**
 * Choosing a room on the phone hotel page.
 *
 * Tapping a room keeps the guest here: the button fills in, the bar at the foot
 * shows that room's total for the default stay, and only then does Book Now
 * become live, carrying the chosen room to the order screen.
 */
const picker = document.querySelector('[data-room-picker]');

if (picker) {
    const nights = Math.max(1, Number(picker.dataset.nights || 1));
    const total = picker.querySelector('[data-room-total]');
    const caption = picker.querySelector('[data-room-total-caption]');
    const book = picker.querySelector('[data-room-book]');
    const buttons = [...picker.querySelectorAll('[data-room-pick]')];

    const idr = (amount) => `IDR ${Math.round(amount).toLocaleString('id-ID')}`;

    const choose = (button) => {
        buttons.forEach((other) => {
            const on = other === button;

            other.classList.toggle('bg-brand', on);
            other.classList.toggle('text-white', on);
            other.classList.toggle('bg-white', !on);
            other.classList.toggle('text-brand', !on);
            other.querySelector('[data-room-pick-label]').textContent = on ? 'Selected' : 'Select Room';

            const card = other.closest('[data-room-card]');
            card?.classList.toggle('border-brand', on);
            card?.classList.toggle('border-[#c0c7d3]', !on);
        });

        if (total && caption) {
            total.textContent = idr(Number(button.dataset.roomNightly || 0) * nights);
            total.hidden = false;
            caption.textContent = `Total for ${nights} night${nights > 1 ? 's' : ''}`;
        }

        if (book) {
            book.href = `${book.dataset.roomBookBase}?room=${encodeURIComponent(button.dataset.roomId)}`;
            book.classList.remove('pointer-events-none', 'opacity-45');
            book.removeAttribute('aria-disabled');
        }
    };

    buttons.forEach((button) => button.addEventListener('click', () => choose(button)));
}

/**
 * Publishing Status / Publishing Settings cards (resources/views/components/admin/radio-cards.blade.php).
 *
 * Blade paints the blue outline on whichever option is saved, which is right on
 * load but stops being right the moment the admin picks another card. Move it
 * with the radio so the highlight and the dot always agree.
 *
 * The card is the radio's ancestor, so a `peer-checked` utility cannot reach
 * it; swapping the classes here keeps the markup plain and works the same
 * whether or not the browser supports styling an ancestor with :has().
 */
document.querySelectorAll('[data-radio-cards]').forEach((group) => {
    const cards = [...group.querySelectorAll('[data-radio-card]')];

    const paint = () => {
        cards.forEach((card) => {
            const checked = card.querySelector('input[type="radio"]')?.checked ?? false;

            card.classList.toggle('border-editorial', checked);
            card.classList.toggle('bg-editorial/5', checked);
            card.classList.toggle('border-[rgba(192,199,211,0.5)]', !checked);
            card.classList.toggle('hover:bg-[#f7fafc]', !checked);
        });
    };

    group.addEventListener('change', paint);
    paint();
});

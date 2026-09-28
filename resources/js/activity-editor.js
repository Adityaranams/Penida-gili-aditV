/**
 * Admin activity editor helpers (Figma 1:8502): select-all days and the pricing
 * block. Inclusion chips reuse the [data-keywords] behaviour.
 */
const form = document.querySelector('[data-activity-form]');

if (form) {
    const days = Array.from(form.querySelectorAll('input[name="days[]"]'));
    form.querySelector('[data-select-all-days]')?.addEventListener('click', () => {
        const all = days.every((day) => day.checked);
        days.forEach((day) => (day.checked = !all));
    });

    /*
     * Discount: the percentage and the strikethrough price describe the same thing,
     * so editing either one updates the other. Only the prices are submitted.
     */
    const base = form.querySelector('input[name="price_adult"]');
    const was = form.querySelector('input[name="price_was"]');
    const percent = form.querySelector('input[name="discount_percent"]');
    const note = form.querySelector('[data-discount-note]');

    const digits = (input) => Number((input?.value || '').replace(/\D/g, ''));
    const rupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

    const showNote = (pct) => {
        note.hidden = pct <= 0;

        if (pct > 0) {
            note.textContent = `Displays ${pct}% discount badge to customers`;
        }
    };

    /** Percentage implied by the two prices. */
    const currentPercent = () => {
        const b = digits(base);
        const w = digits(was);

        return w > b && w > 0 ? Math.round((1 - b / w) * 100) : 0;
    };

    const fromPrices = () => {
        const pct = currentPercent();

        if (percent && document.activeElement !== percent) {
            percent.value = pct > 0 ? pct : '';
        }

        showNote(pct);
    };

    const fromPercent = () => {
        const pct = Number(percent.value);
        const b = digits(base);

        if (!(pct > 0 && pct < 100) || b <= 0) {
            showNote(0);

            return;
        }

        // Round to the nearest thousand rupiah so the old price still looks like a price.
        was.value = rupiah(Math.round(b / (1 - pct / 100) / 1000) * 1000);
        showNote(pct);
    };

    [base, was].forEach((input) => input?.addEventListener('input', fromPrices));
    percent?.addEventListener('input', fromPercent);
    base?.addEventListener('input', () => {
        if (Number(percent?.value) > 0) {
            fromPercent();
        }
    });

    fromPrices();
}

/**
 * Live character counter for console fields that have a server-side limit
 * (<x-admin.field ... :limit="1000" />).
 *
 * The field is deliberately not capped with maxlength: a hard cap silently
 * swallows the tail of a pasted paragraph, and the writer only finds out by
 * re-reading. Going over is allowed, shown in red, and still refused on save —
 * so the text stays editable instead of quietly losing its ending.
 */
document.querySelectorAll('[data-char-count]').forEach((field) => {
    const limit = Number(field.dataset.charCount);
    const counter = document.querySelector(`[data-char-count-for="${CSS.escape(field.name)}"]`);

    if (!counter || !limit) {
        return;
    }

    const update = () => {
        const used = [...field.value].length;

        counter.textContent = `${used} / ${limit}`;
        counter.classList.toggle('text-[#dc2626]', used > limit);
        counter.classList.toggle('font-semibold', used > limit);
        counter.classList.toggle('text-editorial-body', used <= limit);
    };

    field.addEventListener('input', update);
    update();
});

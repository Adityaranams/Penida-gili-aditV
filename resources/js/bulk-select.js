/**
 * Bulk delete on the console listings.
 *
 * Row checkboxes and the Delete button belong to the #bulk-delete form through
 * their `form` attribute, so the markup never nests a form inside the filter
 * form. This only adds the conveniences: select-all, the running count, showing
 * the button when something is ticked, and one confirmation before it posts.
 */
const form = document.querySelector('[data-bulk-form]');

if (form) {
    const all = document.querySelector('[data-bulk-all]');
    const submit = document.querySelector('[data-bulk-submit]');
    const count = submit?.querySelector('[data-bulk-count]');
    const items = () => [...document.querySelectorAll('[data-bulk-item]')];

    const sync = () => {
        const ticked = items().filter((item) => item.checked);

        if (submit) {
            submit.hidden = ticked.length === 0;
        }

        if (count) {
            count.textContent = ticked.length ? ` (${ticked.length})` : '';
        }

        if (all) {
            all.checked = ticked.length > 0 && ticked.length === items().length;
            all.indeterminate = ticked.length > 0 && ticked.length < items().length;
        }

        // Keep the dialog's wording current. It has to be set before the submit
        // fires: resources/js/confirm-delete.js reads it in the capture phase,
        // which runs ahead of this form's own submit handler.
        const noun = form.dataset.bulkNoun ?? 'rows';

        form.dataset.confirmDelete = `${ticked.length} ${ticked.length === 1 ? noun.replace(/s$/, '') : noun}`;
    };

    all?.addEventListener('change', () => {
        items().forEach((item) => (item.checked = all.checked));
        sync();
    });

    items().forEach((item) => item.addEventListener('change', sync));

    form.addEventListener('submit', (event) => {
        const ticked = items().filter((item) => item.checked).length;

        if (ticked === 0) {
            event.preventDefault();
        }
    });

    sync();
}

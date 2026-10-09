/**
 * Console delete confirmation (resources/views/partials/admin/delete-modal.blade.php).
 *
 * A form opts in with data-confirm-delete="<what is being deleted>". The first
 * submit is held back and the dialog opens instead; confirming resubmits the
 * same form, so nothing about the request changes. Without JavaScript, or
 * without <dialog> support, the form posts straight through as it always did.
 */
const modal = document.querySelector('#delete-modal');

if (modal && typeof modal.showModal === 'function') {
    const label = modal.querySelector('[data-delete-label]');
    let pending = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-delete') || form === pending) {
            return;
        }

        event.preventDefault();
        pending = form;
        label.textContent = form.dataset.confirmDelete || 'This item';
        modal.showModal();
    }, true);

    modal.addEventListener('close', () => {
        const form = pending;
        pending = null;

        if (modal.returnValue === 'confirm' && form) {
            // requestSubmit replays the submit event; `pending` is already cleared,
            // so the guard above lets this one through.
            pending = form;
            form.submit();
            pending = null;
        }
    });
}

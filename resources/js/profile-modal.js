/**
 * The account dialog in the console header
 * (resources/views/partials/admin/profile-modal.blade.php).
 *
 * Two tabs over one dialog. Panels are hidden here rather than in the Blade so
 * that without this file both forms simply stack and still work — the dialog
 * is only ever shown by showModal(), so a browser without <dialog> keeps the
 * whole thing out of the way and the /admin/profile page remains the fallback.
 */
const modal = document.querySelector('#profile-modal');

if (modal && typeof modal.showModal === 'function') {
    const tabs = [...modal.querySelectorAll('[data-profile-tab]')];
    const panels = [...modal.querySelectorAll('[data-profile-panel]')];

    const select = (name) => {
        tabs.forEach((tab) => {
            const on = tab.dataset.profileTab === name;
            tab.classList.toggle('bg-surface', on);
            tab.classList.toggle('text-editorial-ink', on);
            tab.classList.toggle('shadow-sm', on);
            tab.classList.toggle('text-editorial-body', !on);
            tab.setAttribute('aria-selected', String(on));
        });

        panels.forEach((panel) => {
            panel.hidden = panel.dataset.profilePanel !== name;
        });
    };

    const open = (name) => {
        select(name || 'account');

        if (!modal.open) {
            modal.showModal();
        }
    };

    tabs.forEach((tab) => tab.addEventListener('click', () => select(tab.dataset.profileTab)));
    modal.querySelector('[data-profile-close]')?.addEventListener('click', () => modal.close());

    // Clicking the dimmed area outside the card closes it, as a modal should.
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.close();
        }
    });

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-profile-open]');

        if (!trigger) {
            return;
        }

        event.preventDefault();
        trigger.closest('details')?.removeAttribute('open');
        open(trigger.dataset.profileOpen);
    });

    // A save that failed validation bounced back; reopen where it failed.
    if (modal.dataset.openTab) {
        open(modal.dataset.openTab);
    } else {
        select('account');
    }
}

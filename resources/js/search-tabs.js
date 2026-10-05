/**
 * Home hero search tabs: `[data-tab]` buttons switch between `[data-panel]` forms,
 * and `[data-chip]` buttons fill the destination input.
 */
document.querySelectorAll('[data-search-tabs]').forEach((root) => {
    const tabs = root.querySelectorAll('[data-tab]');
    const panels = root.querySelectorAll('[data-panel]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((t) => t.setAttribute('aria-selected', String(t === tab)));
            panels.forEach((panel) => {
                const isHidden = panel.dataset.panel !== tab.dataset.tab;
                panel.classList.toggle('hidden', isHidden);
                panel.classList.toggle('flex', !isHidden);
            });
        });
    });

    // The date chevron is handled by calendar.js (it listens for [data-open-picker]).
    root.querySelectorAll('[data-open-picker]').forEach((button) => {
        const input = button.parentElement.querySelector('input');
        button.addEventListener('click', () => {
            if (input.hasAttribute('data-port-picker')) {
                input.click();
            }
        });
    });

    const destination = root.querySelector('[data-destination]');
    root.querySelectorAll('[data-chip]').forEach((chip) => {
        chip.addEventListener('click', () => {
            destination.value = chip.dataset.chip;
            destination.focus();
        });
    });
});

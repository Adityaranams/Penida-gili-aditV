/**
 * Harbor picker: clicking an input marked `data-port-picker="from|to"` opens the
 * shared `[data-port-dialog]` modal, and choosing a harbor fills that input.
 * Harbors can be narrowed by island chip and by the search box.
 */
const dialog = document.querySelector('[data-port-dialog]');

if (dialog) {
    const eyebrow = dialog.querySelector('[data-port-eyebrow]');
    const title = dialog.querySelector('[data-port-title]');
    const search = dialog.querySelector('[data-port-search]');
    const empty = dialog.querySelector('[data-port-empty]');
    const chips = dialog.querySelectorAll('[data-port-area]');
    const options = dialog.querySelectorAll('[data-port-option]');
    let target = null;
    let area = 'All';

    const filter = () => {
        const term = search.value.trim().toLowerCase();
        let visible = 0;

        options.forEach((option) => {
            const matchesArea = area === 'All' || option.dataset.area === area;
            const matchesTerm = !term || `${option.dataset.portOption} ${option.dataset.area}`.toLowerCase().includes(term);
            const isVisible = matchesArea && matchesTerm;
            option.style.display = isVisible ? '' : 'none';
            visible += isVisible ? 1 : 0;
        });

        empty.hidden = visible > 0;
    };

    const open = (input) => {
        const isDestination = input.dataset.portPicker === 'to';
        target = input;
        eyebrow.textContent = isDestination ? 'Destination' : 'Departure';
        title.textContent = isDestination ? 'Where are you heading?' : 'Where are you sailing from?';
        options.forEach((option) => option.toggleAttribute('data-selected', option.dataset.portOption === input.value));
        search.value = '';
        filter();
        dialog.showModal();
    };

    document.querySelectorAll('[data-port-picker]').forEach((input) => {
        input.readOnly = true;
        input.classList.add('cursor-pointer');
        input.addEventListener('click', () => open(input));
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open(input);
            }
        });
    });

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            area = chip.dataset.portArea;
            chips.forEach((c) => c.toggleAttribute('data-active', c === chip));
            filter();
        });
    });

    search.addEventListener('input', filter);

    options.forEach((option) => {
        option.addEventListener('click', () => {
            target.value = option.dataset.portOption;
            target.dispatchEvent(new Event('change', { bubbles: true }));
            dialog.close();
        });
    });

    dialog.querySelector('[data-port-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
}

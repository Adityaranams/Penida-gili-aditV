/**
 * Repeating form rows (guest testimonials, activity experiences).
 *
 * Each block owns a <template> whose markup carries __INDEX__ where the row
 * number belongs; adding a row clones it with the next free index so the fields
 * post as name[i][...]. Saved rows are removed through their own checkbox, which
 * the server honours; unsaved ones are simply dropped from the DOM.
 */
document.querySelectorAll('[data-repeater]').forEach((root) => {
    const list = root.querySelector('[data-repeater-list]');
    const template = root.querySelector('[data-repeater-template]');
    const empty = root.querySelector('[data-repeater-empty]');
    const add = root.querySelector('[data-repeater-add]');

    if (!list || !template || !add) {
        return;
    }

    let next = list.querySelectorAll('[data-repeater-row]').length;

    const syncEmpty = () => {
        if (empty) {
            empty.hidden = list.querySelector('[data-repeater-row]') !== null;
        }
    };

    add.addEventListener('click', () => {
        const holder = document.createElement('div');
        holder.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(next));
        next += 1;

        const row = holder.querySelector('[data-repeater-row]');
        list.appendChild(row);
        syncEmpty();
        row.querySelector('input, textarea')?.focus();
    });

    list.addEventListener('click', (event) => {
        const drop = event.target.closest('[data-repeater-drop]');

        if (drop) {
            drop.closest('[data-repeater-row]').remove();
            syncEmpty();
        }
    });
});

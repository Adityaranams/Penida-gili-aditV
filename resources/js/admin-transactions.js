/**
 * Row menus in the console tables are native <details>, which stay open on their
 * own. Keep one open at a time and close it when focus leaves, so two rows can
 * never offer their actions at once.
 */
const menus = () => document.querySelectorAll('[data-row-menu]');

document.addEventListener('toggle', (event) => {
    const menu = event.target.closest('[data-row-menu]');

    if (!menu?.open) {
        return;
    }

    menus().forEach((other) => {
        if (other !== menu) {
            other.open = false;
        }
    });
}, true);

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-row-menu]')) {
        menus().forEach((menu) => (menu.open = false));
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        menus().forEach((menu) => (menu.open = false));
    }
});

/**
 * Form submission drops the fragment from the action URL, so searching or
 * filtering would bounce back to the top of the page. Navigate by hand instead
 * and keep the reader on the table.
 */
document.querySelectorAll('form[data-keep-anchor]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const query = new URLSearchParams(new FormData(form));
        [...query.keys()].forEach((key) => query.get(key) || query.delete(key));

        const search = query.toString();
        window.location.href = `${form.action}${search ? `?${search}` : ''}${form.dataset.keepAnchor}`;
    });
});

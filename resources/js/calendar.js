/**
 * Calendar popover for inputs marked `data-calendar`.
 *
 * The visible input becomes a read-only label ("Thu, 15 Oct 2026") and a hidden
 * input carries the ISO date under the original name, so forms submit the same
 * `date=YYYY-MM-DD` as before. Clicking the field (or any `[data-calendar-toggle]`
 * pointing at it) opens the popover; clicking again, outside, or Esc closes it.
 */
const DAYS = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
const pad = (n) => String(n).padStart(2, '0');
const iso = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
const parse = (value) => {
    const [y, m, d] = (value || '').split('-').map(Number);
    return y ? new Date(y, m - 1, d) : null;
};
const label = (d) => d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });

const popover = document.createElement('div');
popover.className =
    'fixed z-[60] w-[300px] origin-top rounded-[20px] bg-surface p-[16px] font-jakarta text-ink shadow-[0_20px_60px_rgba(15,34,54,0.25)] ring-1 ring-black/5 ' +
    'pointer-events-none -translate-y-[6px] scale-[0.97] opacity-0 transition-[opacity,transform] duration-200 ease-out';
popover.setAttribute('role', 'dialog');
popover.setAttribute('aria-label', 'Choose date');

let active = null; // { input, hidden, view }

const isOpen = () => active !== null;

const place = () => {
    if (!active) {
        return;
    }
    const rect = active.input.closest('label, [data-calendar-anchor]')?.getBoundingClientRect() ?? active.input.getBoundingClientRect();
    const width = popover.offsetWidth;
    const left = Math.min(Math.max(12, rect.left), window.innerWidth - width - 12);
    const below = rect.bottom + 10;
    const fitsBelow = below + popover.offsetHeight < window.innerHeight - 12;

    popover.style.left = `${left}px`;
    popover.style.top = `${fitsBelow ? below : Math.max(12, rect.top - popover.offsetHeight - 10)}px`;
    popover.style.transformOrigin = fitsBelow ? 'top' : 'bottom';
};

const render = () => {
    const { view, hidden, input } = active;
    const selected = parse(hidden.value);
    const min = parse(input.dataset.min);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const first = new Date(view.getFullYear(), view.getMonth(), 1);
    const offset = (first.getDay() + 6) % 7;
    const daysInMonth = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate();
    const canGoBack = !min || first > new Date(min.getFullYear(), min.getMonth(), 1);

    const cells = [];
    for (let i = 0; i < offset; i++) {
        cells.push('<span></span>');
    }
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(view.getFullYear(), view.getMonth(), day);
        const disabled = min && date < min;
        const isSelected = selected && iso(date) === iso(selected);
        const isToday = iso(date) === iso(today);
        const classes = isSelected
            ? 'bg-brand text-on-brand font-semibold shadow-md shadow-brand/30'
            : disabled
              ? 'text-ink-muted/30 cursor-not-allowed'
              : `hover:bg-brand/10 ${isToday ? 'text-brand font-semibold ring-1 ring-brand/40' : ''}`;
        cells.push(
            `<button type="button" data-date="${iso(date)}" ${disabled ? 'disabled' : ''}
                class="flex size-[36px] items-center justify-center rounded-full text-[13px] transition-colors ${classes}">${day}</button>`,
        );
    }

    popover.innerHTML = `
        <div class="flex items-center justify-between">
            <button type="button" data-nav="-1" ${canGoBack ? '' : 'disabled'} aria-label="Previous month"
                class="flex size-[32px] items-center justify-center rounded-full transition-colors hover:bg-surface-muted disabled:opacity-30">
                <svg class="size-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <p class="text-[14px] font-semibold">${view.toLocaleDateString('en-GB', { month: 'long', year: 'numeric' })}</p>
            <button type="button" data-nav="1" aria-label="Next month"
                class="flex size-[32px] items-center justify-center rounded-full transition-colors hover:bg-surface-muted">
                <svg class="size-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
        <div class="mt-[10px] grid grid-cols-7 justify-items-center text-[11px] font-semibold uppercase text-ink-muted">
            ${DAYS.map((d) => `<span class="py-[4px]">${d}</span>`).join('')}
        </div>
        <div data-grid class="mt-[2px] grid grid-cols-7 justify-items-center gap-y-[2px] transition-opacity duration-150">${cells.join('')}</div>
        <div class="mt-[10px] flex justify-between border-t border-line pt-[10px] text-[12px] font-semibold">
            <button type="button" data-clear class="rounded-full px-[10px] py-[5px] text-ink-muted transition-colors hover:bg-surface-muted hover:text-ink">Clear</button>
            <button type="button" data-today class="rounded-full px-[10px] py-[5px] text-brand transition-colors hover:bg-brand/10">Today</button>
        </div>`;
};

const close = () => {
    if (!active) {
        return;
    }
    active.input.setAttribute('aria-expanded', 'false');
    active = null;
    popover.classList.add('pointer-events-none', 'opacity-0', '-translate-y-[6px]', 'scale-[0.97]');
};

const open = (entry) => {
    const selected = parse(entry.hidden.value) ?? parse(entry.input.dataset.min) ?? new Date();
    active = { ...entry, view: new Date(selected.getFullYear(), selected.getMonth(), 1) };
    entry.input.setAttribute('aria-expanded', 'true');
    render();
    place();
    void popover.offsetHeight; // commit the closed state first so the open transition runs
    popover.classList.remove('pointer-events-none', 'opacity-0', '-translate-y-[6px]', 'scale-[0.97]');
};

const toggle = (entry) => (active?.input === entry.input ? close() : (close(), open(entry)));

const choose = (value) => {
    const date = parse(value);
    active.hidden.value = value;
    active.input.value = date ? label(date) : '';
    active.hidden.dispatchEvent(new Event('change', { bubbles: true }));
    close();
};

popover.addEventListener('click', (event) => {
    event.stopPropagation();
    const target = event.target.closest('button');
    if (!target || target.disabled || !active) {
        return;
    }
    if (target.dataset.nav) {
        const grid = popover.querySelector('[data-grid]');
        grid.style.opacity = '0';
        setTimeout(() => {
            active.view = new Date(active.view.getFullYear(), active.view.getMonth() + Number(target.dataset.nav), 1);
            render();
            place();
        }, 120);
    } else if (target.dataset.date) {
        choose(target.dataset.date);
    } else if ('clear' in target.dataset) {
        choose('');
    } else if ('today' in target.dataset) {
        const today = new Date();
        const min = parse(active.input.dataset.min);
        choose(iso(min && today < min ? min : today));
    }
});

const entries = new Map();

document.querySelectorAll('input[data-calendar]').forEach((input) => {
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = input.name;
    hidden.value = input.value;
    input.after(hidden);

    input.removeAttribute('name');
    input.dataset.min = input.getAttribute('min') ?? '';
    input.removeAttribute('min');
    input.type = 'text';
    input.readOnly = true;
    input.classList.add('cursor-pointer');
    input.setAttribute('aria-haspopup', 'dialog');
    input.setAttribute('aria-expanded', 'false');
    input.value = parse(hidden.value) ? label(parse(hidden.value)) : '';

    const entry = { input, hidden };
    entries.set(input, entry);

    input.addEventListener('click', (event) => {
        event.stopPropagation();
        toggle(entry);
    });
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ' || event.key === 'ArrowDown') {
            event.preventDefault();
            toggle(entry);
        }
    });
});

if (entries.size) {
    document.body.append(popover);

    document.addEventListener('click', (event) => {
        if (event.target.closest('label')?.querySelector('input[data-calendar]')) {
            return;
        }
        const trigger = event.target.closest('[data-calendar-toggle], [data-open-picker]');
        const input = trigger?.parentElement.querySelector('input[data-calendar]');
        if (input && entries.has(input)) {
            event.stopPropagation();
            toggle(entries.get(input));
        } else if (isOpen()) {
            close();
        }
    });
    document.addEventListener('keydown', (event) => event.key === 'Escape' && close());
    window.addEventListener('resize', place);
    window.addEventListener('scroll', place, { passive: true });
}

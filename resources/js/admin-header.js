/**
 * The console header's bell and account menu.
 *
 * The dropdowns themselves are <details>, so they already open without this
 * file. What it adds is the live part: the bell polls for bookings every half
 * minute, shows how many arrived since this admin last looked, and clears the
 * count when the bell is opened. There is no broadcasting in this project, so
 * polling is the whole of "live" here — see App\Support\AdminNotifications.
 */
const bell = document.querySelector('[data-admin-bell]');
const account = document.querySelector('[data-admin-account]');

// Two menus side by side: opening one closes the other, or they overlap.
[bell, account].forEach((menu, index, menus) => {
    menu?.addEventListener('toggle', () => {
        if (menu.open) {
            menus.forEach((other) => other !== menu && other?.removeAttribute('open'));
        }
    });
});

// A click anywhere else closes whichever is open, the way a menu should behave.
document.addEventListener('click', (event) => {
    [bell, account].forEach((menu) => {
        if (menu?.open && !menu.contains(event.target)) {
            menu.removeAttribute('open');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        [bell, account].forEach((menu) => menu?.removeAttribute('open'));
    }
});

if (bell) {
    const dot = bell.querySelector('[data-bell-dot]');
    const list = bell.querySelector('[data-bell-list]');
    const feed = bell.dataset.feed || '/admin/notifications';
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const escape = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const render = (items) => {
        if (!items.length) {
            list.innerHTML = '<p class="px-[18px] py-[22px] font-jakarta text-[13px] leading-[18px] text-editorial-body">No bookings yet.</p>';

            return;
        }

        list.innerHTML = items.map((item) => `
            <a href="${escape(item.url)}" class="flex gap-[10px] border-b border-admin-line px-[18px] py-[13px] transition-colors last:border-b-0 hover:bg-[#f1f4f6] ${item.unread ? 'bg-[#f7fafc]' : ''}">
                <span class="mt-[6px] size-[7px] shrink-0 rounded-full ${item.unread ? 'bg-[#e11d48]' : 'bg-transparent'}"></span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-baseline justify-between gap-[8px]">
                        <span class="truncate font-jakarta text-[13px] font-semibold leading-[18px] text-editorial-ink">${escape(item.title)}</span>
                        <span class="shrink-0 font-jakarta text-[11px] leading-[16px] text-editorial-meta">${escape(item.when)}</span>
                    </span>
                    <span class="block truncate font-jakarta text-[12px] leading-[16px] text-editorial-body">${escape(item.detail)}</span>
                    <span class="block font-jakarta text-[12px] font-semibold leading-[16px] text-editorial">${escape(item.amount)}</span>
                </span>
            </a>
        `).join('');
    };

    const showCount = (count) => {
        dot.textContent = count > 9 ? '9+' : String(count);
        dot.hidden = count < 1;
    };

    const poll = async () => {
        try {
            const response = await fetch(feed, { headers: { Accept: 'application/json' } });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            showCount(data.count ?? 0);
            render(data.items ?? []);
        } catch {
            // Offline or the session expired: leave whatever is on screen.
        }
    };

    // Opening the bell is what marks the bookings as seen.
    bell.addEventListener('toggle', async () => {
        if (!bell.open || dot.hidden) {
            return;
        }

        showCount(0);

        try {
            await fetch(bell.dataset.seen || '/admin/notifications/seen', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token ?? '' },
            });
        } catch {
            // The count comes back on the next poll if this did not land.
        }
    });

    poll();
    setInterval(poll, 30000);

    // A tab left open all afternoon should catch up the moment it is focused.
    document.addEventListener('visibilitychange', () => document.visibilityState === 'visible' && poll());
}

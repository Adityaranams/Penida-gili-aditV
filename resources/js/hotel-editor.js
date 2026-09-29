/**
 * Admin hotel editor (Figma 1:7501): room category cards with inline editing,
 * add / remove, live stat tiles, plus the gallery counter and hero preview.
 */
const form = document.querySelector('[data-hotel-form]');

if (form) {
    const list = form.querySelector('[data-rooms-list]');
    const template = form.querySelector('[data-room-template]');
    const count = form.querySelector('[data-rooms-count]');
    const digits = (value) => Number(String(value || '').replace(/\D/g, ''));
    const idr = (value) => (value ? 'IDR ' + value.toLocaleString('id-ID') : '—');

    const field = (card, key) => card.querySelector(`[name$="[${key}]"]`);

    // Mirror the inputs into the summary tiles so the card reads like the design.
    const syncCard = (card) => {
        const name = field(card, 'name').value.trim();
        const stock = Number(field(card, 'stock').value || 0);
        const low = stock > 0 && stock < 5;

        card.querySelector('[data-room-title]').textContent = name || 'New Room Category';
        card.querySelector('[data-room-stat="capacity"]').textContent =
            `${field(card, 'guests').value || 2} Guests` + (field(card, 'bed').value ? `, ${field(card, 'bed').value}` : '');
        card.querySelector('[data-room-stat="size"]').textContent = field(card, 'size_label').value || '—';
        card.querySelector('[data-room-stat="price"]').textContent = idr(digits(field(card, 'price_per_night').value));
        card.querySelector('[data-room-stat="stock"]').textContent = `${stock} Units Left`;

        const pill = card.querySelector('[data-room-pill]');
        pill.textContent = low ? 'High Demand' : 'Available';
        pill.className = `rounded-full px-[8px] py-[2px] font-jakarta text-[11px] font-semibold leading-[24px] ${low ? 'bg-[#fef3c7] text-[#92400e]' : 'bg-[#d1fae5] text-[#065f46]'}`;

        const stockRow = card.querySelector('[data-room-stock]');
        stockRow.classList.toggle('text-[#b45309]', low);
        stockRow.classList.toggle('text-[#047857]', !low);
        const icon = card.querySelector('[data-room-stock-icon]');
        icon.src = low ? icon.dataset.low : icon.dataset.ok;
    };

    const renumber = () => {
        const cards = Array.from(list.querySelectorAll('[data-room]'));
        cards.forEach((card, i) => (card.querySelector('[data-room-number]').textContent = i + 1));
        count.textContent = cards.filter((card) => field(card, 'name').value.trim()).length;
    };

    const bind = (card) => {
        card.querySelector('[data-room-edit]').addEventListener('click', () => {
            const fields = card.querySelector('[data-room-fields]');
            fields.hidden = !fields.hidden;
            if (!fields.hidden) field(card, 'name').focus();
        });

        // Removed rows post an empty name, which the request drops; the card just disappears.
        card.querySelector('[data-room-remove]').addEventListener('click', () => {
            field(card, 'name').value = '';
            card.remove();
            renumber();
        });

        // Room photo: preview the picked file straight away.
        const photo = card.querySelector('[data-room-photo-input]');
        photo?.addEventListener('change', () => {
            const file = photo.files?.[0];
            if (!file) return;
            const preview = card.querySelector('[data-room-photo-preview]');
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
            card.querySelector('[data-room-photo-name]').textContent = file.name;
        });

        card.querySelectorAll('input').forEach((input) => input.addEventListener('input', () => { syncCard(card); renumber(); }));
        syncCard(card);
    };

    list.querySelectorAll('[data-room]').forEach(bind);
    renumber();

    let next = list.querySelectorAll('[data-room]').length + 100;
    form.querySelector('[data-room-add]')?.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__INDEX__', String(next++));
        list.insertAdjacentHTML('beforeend', html);
        const card = list.lastElementChild;
        bind(card);
        renumber();
        field(card, 'name').focus();
    });

    // Gallery: hero preview + "N images uploaded".
    const galleryCount = form.querySelector('[data-gallery-count]');
    const base = Number(galleryCount?.textContent || 0);
    const cover = form.querySelector('[data-cover-input]');
    const gallery = form.querySelector('[data-gallery-input]');
    const recount = () => {
        const picked = (gallery?.files?.length || 0) + (cover?.files?.length && !form.querySelector('[data-cover-preview]:not([hidden])') ? 1 : 0);
        galleryCount.textContent = base + picked;
    };

    cover?.addEventListener('change', () => {
        const file = cover.files?.[0];
        if (!file) return;
        const img = form.querySelector('[data-cover-preview]');
        recount();
        img.src = URL.createObjectURL(file);
        img.hidden = false;
        form.querySelector('[data-cover-empty]')?.remove();
    });

    gallery?.addEventListener('change', () => {
        const n = gallery.files?.length || 0;
        form.querySelector('[data-gallery-picked]').textContent = n ? `${n} photo${n > 1 ? 's' : ''} selected` : '';
        recount();
    });
}

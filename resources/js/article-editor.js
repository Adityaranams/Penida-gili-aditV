/**
 * Admin article editor helpers (SEO keyword chips, live word count, body preview,
 * cover-image preview). Every control degrades to a plain form field without JS.
 */

// Keyword chips: typing then Enter / comma adds a chip; the hidden input carries
// the comma-separated list the request expects.
document.querySelectorAll('[data-keywords]').forEach((root) => {
    const hidden = root.querySelector('input[type="hidden"]');
    const entry = root.querySelector('input[type="text"]');
    const list = root.querySelector('[data-keywords-list]');
    let keywords = hidden.value.split(',').map((k) => k.trim()).filter(Boolean);

    const render = () => {
        hidden.value = keywords.join(', ');
        hidden.dispatchEvent(new Event('change'));
        list.innerHTML = '';
        keywords.forEach((keyword, index) => {
            const chip = document.createElement('span');
            chip.className = 'flex items-center gap-[6px] rounded-full px-[10px] py-[4px] font-jakarta text-[13px] font-semibold ' + (root.dataset.chipClass || 'bg-editorial/10 text-editorial');
            chip.textContent = keyword;

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.setAttribute('aria-label', `Remove ${keyword}`);
            remove.className = 'text-editorial-body hover:text-[#dc2626]';
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                keywords.splice(index, 1);
                render();
            });

            chip.appendChild(remove);
            list.appendChild(chip);
        });
    };

    const commit = () => {
        const value = entry.value.replace(/,/g, '').trim();
        if (value && !keywords.includes(value)) {
            keywords.push(value);
        }
        entry.value = '';
        render();
    };

    entry.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ',') {
            event.preventDefault();
            commit();
        } else if (event.key === 'Backspace' && entry.value === '' && keywords.length) {
            keywords.pop();
            render();
        }
    });
    entry.addEventListener('blur', commit);

    render();
});

// Live word count for the article body (the rich-text surface mirrors into the textarea).
document.querySelectorAll('[data-body-editor]').forEach((root) => {
    const textarea = root.querySelector('textarea');
    const counter = root.querySelector('[data-word-count]');

    const count = () => {
        const words = textarea.value.replace(/<[^>]*>/g, ' ').trim().split(/\s+/).filter(Boolean).length;
        counter.textContent = words.toLocaleString('en-US');
    };

    textarea.addEventListener('input', count);
    count();
});

// Show the chosen cover image before upload.
document.querySelectorAll('[data-cover-picker]').forEach((root) => {
    const input = root.querySelector('input[type="file"]');
    const img = root.querySelector('[data-cover-preview]');
    const name = root.querySelector('[data-cover-name]');

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) {
            return;
        }
        img.src = URL.createObjectURL(file);
        img.hidden = false;
        root.querySelector('[data-cover-empty]')?.remove();
        if (name) {
            name.textContent = file.name;
        }
    });
});

// Figma 1:8059 sidebar: scheduled date reveal, live author signature, SEO counters,
// SERP preview and a rough SEO score; read time follows the word count.
const articleForm = document.querySelector('[data-article-form]');

if (articleForm) {
    const q = (sel) => articleForm.querySelector(sel);
    const value = (name) => q(`[name="${name}"]`)?.value.trim() ?? '';

    // AUTHOR bar: "+ Add new author…" reveals the name / role fields.
    const authorSelect = q('[data-author-select]');
    const authorNew = q('[data-author-new]');
    const syncAuthor = () => {
        authorNew.hidden = authorSelect.value !== 'new';
        if (!authorNew.hidden) q('[name="author_name"]').focus();
    };
    authorSelect?.addEventListener('change', syncAuthor);

    const slugify = (text) => text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    const syncSeo = () => {
        const title = value('title');
        const metaTitle = value('meta_title');
        const metaDescription = value('meta_description');
        const slug = slugify(title) || 'your-article';

        articleForm.querySelectorAll('[data-count-for]').forEach((el) => {
            el.textContent = value(el.dataset.countFor).length;
        });
        q('[data-serp-slug]').textContent = slug.length > 18 ? slug.slice(0, 18) + '…' : slug;

        // The permalink is derived, not typed: keep the preview in step with the title.
        const permalink = q('[data-permalink]');
        if (permalink) {
            permalink.textContent = slug;
        }
        q('[data-serp-title]').textContent = metaTitle || title || 'Article title';
        q('[data-serp-description]').textContent = metaDescription || value('excerpt') || 'Meta description preview appears here.';

        // Score: each SEO field filled within its ideal length earns points.
        let score = 0;
        if (title) score += 20;
        if (value('excerpt')) score += 10;
        if (metaTitle.length >= 30 && metaTitle.length <= 60) score += 25;
        else if (metaTitle) score += 10;
        if (metaDescription.length >= 100 && metaDescription.length <= 160) score += 25;
        else if (metaDescription) score += 10;
        if (title) score += 5;
        if (q('[name="hero_alt"]').value.trim()) score += 10;
        q('[data-seo-score]').textContent = score;
    };
    ['title', 'excerpt', 'meta_title', 'meta_description', 'hero_alt'].forEach((n) => q(`[name="${n}"]`).addEventListener('input', syncSeo));
    syncSeo();

}

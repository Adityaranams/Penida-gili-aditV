/**
 * Search-as-you-type on the article index.
 *
 * The form still works on its own — pressing Enter reloads the page as usual.
 * With JavaScript, typing fetches the same URL in the background and swaps the
 * results in place, so the reader sees matches without leaving the field.
 */
const inputs = [...document.querySelectorAll('[data-article-search]')];
const regions = () => [...document.querySelectorAll('[data-article-results]')];

if (inputs.length && regions().length) {
    let timer;
    let inFlight;
    let lastUrl = window.location.href;

    /** The page URL for a search term, keeping the category that is being browsed. */
    const urlFor = (term) => {
        const url = new URL(window.location.href);
        url.searchParams.delete('page');
        term ? url.searchParams.set('q', term) : url.searchParams.delete('q');

        return url;
    };

    /**
     * The category pills are rendered with the term the page loaded with. They sit
     * outside the swapped region, so bring their links along — otherwise picking a
     * category would resurrect a search the reader has already cleared.
     */
    const syncFilterLinks = (term) => {
        document.querySelectorAll('[data-article-filter]').forEach((link) => {
            const url = new URL(link.href, window.location.origin);
            url.searchParams.delete('page');
            term ? url.searchParams.set('q', term) : url.searchParams.delete('q');
            link.href = url.href;
        });
    };

    const render = (html) => {
        const fresh = new DOMParser().parseFromString(html, 'text/html').querySelectorAll('[data-article-results]');

        regions().forEach((region, index) => {
            if (!fresh[index]) {
                return;
            }

            region.innerHTML = fresh[index].innerHTML;

            // These nodes never scrolled into view, so the reveal observer will not
            // fire for them; show them straight away.
            region.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-revealed'));
        });
    };

    const search = async (term, source = null) => {
        const url = urlFor(term);

        if (url.href === lastUrl) {
            return;
        }

        inFlight?.abort();
        inFlight = new AbortController();

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'fetch' }, signal: inFlight.signal });

            if (!response.ok) {
                return;
            }

            const html = await response.text();

            // The reader may have typed on while this was in flight; that newer
            // keystroke owns the screen, so drop this answer rather than fight it.
            if (source && source.value.trim() !== term) {
                return;
            }

            render(html);
            syncFilterLinks(term);
            lastUrl = url.href;
            window.history.replaceState({}, '', url);

            // Mirror the term into the other field (mobile / desktop), never into the
            // one being typed in — writing to it would move the caret and eat letters.
            inputs.forEach((other) => {
                if (other !== source && other !== document.activeElement && other.value !== term) {
                    other.value = term;
                }
            });
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url; // Fall back to a normal page load.
            }
        }
    };

    inputs.forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => search(input.value.trim(), input), 250);
        });

        // Enter would submit and reload; the results are already on screen.
        input.closest('form')?.addEventListener('submit', (event) => {
            event.preventDefault();
            clearTimeout(timer);
            search(input.value.trim(), input);
        });
    });
}

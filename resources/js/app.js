import './stepper';
import './order-quote';
import './article-editor';
import './confirm-submit';
import './schedule-summary';
import './activity-editor';
import './hotel-editor';
import './order-email';
import './admin-login';
import './uploader';
import './lightbox';
import './tabbar-shrink';
import './article-search';
import './repeater';
import './bulk-select';
import './radio-cards';
import './rich-text';
import './admin-transactions';

/**
 * Reveal elements as they scroll into view.
 *
 * Elements opt in with `data-reveal`; stagger them by setting `--reveal-delay`
 * inline. IntersectionObserver drives the effect, but it never fires while the
 * document is hidden (background tab, prerender, an embedded webview that is
 * not painting) — so a fallback takes over if the observer has said nothing by
 * the time the page should have settled. Content must never stay invisible.
 */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const targets = Array.from(document.querySelectorAll('[data-reveal]'));

const reveal = (el) => el.classList.add('is-revealed');

if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    targets.forEach(reveal);
} else {
    let observerWorks = false;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                observerWorks = true;
                reveal(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '0px 0px -12% 0px', threshold: 0.08 }
    );

    targets.forEach((el) => observer.observe(el));

    // Fallback: reveal whatever has scrolled into view, computed by hand.
    const revealInView = () => {
        targets.forEach((el) => {
            if (el.classList.contains('is-revealed')) {
                return;
            }

            if (el.getBoundingClientRect().top < window.innerHeight) {
                reveal(el);
            }
        });
    };

    window.setTimeout(() => {
        if (observerWorks) {
            return;
        }

        revealInView();
        window.addEventListener('scroll', revealInView, { passive: true });
        window.addEventListener('resize', revealInView, { passive: true });
    }, 1500);
}

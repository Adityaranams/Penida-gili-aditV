/**
 * The Full Description editor. The markup ships a contenteditable surface (hidden)
 * next to the real <textarea>; with JavaScript on we swap them, so the admin types
 * into the rich surface while the textarea keeps posting the value. Without
 * JavaScript the plain textarea is what they get, and nothing breaks.
 *
 * Only bold, italic, underline and lists are offered; the server strips anything else.
 */
document.querySelectorAll('[data-editor]').forEach((root) => {
    const surface = root.querySelector('[data-editor-surface]');
    const input = root.querySelector('[data-editor-input]');
    const buttons = [...root.querySelectorAll('[data-editor-command]')];

    if (!surface || !input) {
        return;
    }

    surface.hidden = false;
    input.hidden = true;
    input.tabIndex = -1;

    // A description saved before the editor existed arrives as plain text.
    if (surface.innerHTML.trim() === '' && input.value.trim() !== '') {
        surface.textContent = input.value;
    }

    const sync = () => {
        const html = surface.innerHTML.trim();
        input.value = html === '<br>' ? '' : html;
    };

    const syncButtons = () => {
        buttons.forEach((button) => {
            let active = false;

            try {
                active = document.queryCommandState(button.dataset.editorCommand);
            } catch {
                active = false;
            }

            button.setAttribute('aria-pressed', String(active));
        });
    };

    buttons.forEach((button) => {
        // mousedown, so the selection in the surface survives the click.
        button.addEventListener('mousedown', (event) => {
            event.preventDefault();
            surface.focus();
            document.execCommand(button.dataset.editorCommand, false);
            sync();
            syncButtons();
        });
    });

    surface.addEventListener('input', sync);
    surface.addEventListener('blur', sync);
    ['keyup', 'mouseup', 'focus'].forEach((type) => surface.addEventListener(type, syncButtons));

    // Paste as plain text so nothing unexpected sneaks into the stored markup.
    surface.addEventListener('paste', (event) => {
        event.preventDefault();
        document.execCommand('insertText', false, event.clipboardData?.getData('text/plain') ?? '');
        sync();
    });

    root.closest('form')?.addEventListener('submit', sync);
    sync();
});

/**
 * The console's rich-text fields (activity description, article body).
 *
 * The markup ships a contenteditable surface (hidden) next to the real
 * <textarea>; with JavaScript on we swap them, so the admin types into the rich
 * surface while the textarea keeps posting the value. Without JavaScript the
 * plain textarea is what they get, and nothing breaks.
 *
 * Only the commands the toolbar offers are available, and the server strips
 * anything outside its allowlist anyway.
 */
document.querySelectorAll('[data-editor]').forEach((root) => {
    const surface = root.querySelector('[data-editor-surface]');
    const input = root.querySelector('[data-editor-input]');
    const buttons = [...root.querySelectorAll('[data-editor-command]')];
    const dropCapButton = root.querySelector('[data-editor-dropcap]');
    const imageButton = root.querySelector('[data-editor-image]');
    const imageInput = root.querySelector('[data-editor-image-input]');

    if (!surface || !input) {
        return;
    }

    surface.hidden = false;
    input.hidden = true;
    input.tabIndex = -1;

    // Enter should start a <p>, not a <div>, so the block picker has something to swap.
    try {
        document.execCommand('defaultParagraphSeparator', false, 'p');
    } catch {
        // Not supported everywhere; the editor still works without it.
    }

    // Copy saved before the editor existed arrives as plain text, one paragraph per blank line.
    if (surface.innerHTML.trim() === '' && input.value.trim() !== '') {
        input.value
            .split(/\n\s*\n/)
            .map((block) => block.trim())
            .filter(Boolean)
            .forEach((block) => {
                const paragraph = document.createElement('p');
                paragraph.textContent = block;
                surface.appendChild(paragraph);
            });
    }

    const sync = () => {
        const html = surface.innerHTML.trim();
        input.value = html === '<br>' ? '' : html;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    };

    /*
     * Clicking a <select> moves focus out of the surface and the caret is lost with
     * it, so remember where it was and put it back before running a command.
     */
    let savedRange = null;

    const rememberSelection = () => {
        const selection = window.getSelection();

        if (selection?.rangeCount && surface.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0).cloneRange();
        }
    };

    const restoreSelection = () => {
        if (!savedRange) {
            return;
        }

        const selection = window.getSelection();
        selection?.removeAllRanges();
        selection?.addRange(savedRange);
    };

    document.addEventListener('selectionchange', () => {
        if (document.activeElement === surface) {
            rememberSelection();
        }
    });

    const BLOCKS = 'p, h2, h3, blockquote, li, div';

    /** Loose text typed straight into the surface has no block to convert; give it one. */
    const wrapLooseText = () => {
        [...surface.childNodes].forEach((node) => {
            const isBlock = node.nodeType === 1 && node.matches(BLOCKS + ', ul, ol');

            if (isBlock || (node.nodeType === 3 && node.textContent.trim() === '')) {
                return;
            }

            const paragraph = document.createElement('p');
            node.replaceWith(paragraph);
            paragraph.appendChild(node);
        });
    };

    /**
     * The block element the caret sits in — the last line when the caret is
     * elsewhere. The surface itself is never a candidate: it is a <div>, so
     * closest() would match it and we would end up replacing the editor.
     */
    const blockElement = () => {
        const node = window.getSelection()?.anchorNode ?? savedRange?.startContainer;
        const element = node?.nodeType === 1 ? node : node?.parentElement;
        const block = element?.closest(BLOCKS);
        const inside = block && block !== surface && surface.contains(block);

        return inside ? block : surface.lastElementChild;
    };

    const currentBlock = () => blockElement()?.tagName.toLowerCase() ?? 'p';

    const syncState = () => {
        buttons.forEach((button) => {
            const { editorCommand: command, editorValue: value } = button.dataset;
            let active = false;

            try {
                active = command === 'formatBlock' ? currentBlock() === value : document.queryCommandState(command);
            } catch {
                active = false;
            }

            button.setAttribute('aria-pressed', String(active));
        });

        dropCapButton?.setAttribute('aria-pressed', String(blockElement()?.classList.contains('drop-cap') ?? false));
    };

    /**
     * Swap the block the caret is in. Done by hand rather than with
     * execCommand('formatBlock'), which silently does nothing when the caret was
     * lost to the toolbar or the line is not already a block element.
     */
    const setBlock = (tag) => {
        wrapLooseText();

        const block = blockElement();

        if (block?.tagName === 'LI' || block?.tagName.toLowerCase() === tag) {
            return;
        }

        const replacement = document.createElement(tag);

        // An empty editor has no line yet: start one so the admin can type straight away.
        if (block) {
            replacement.innerHTML = block.innerHTML || '<br>';
            block.replaceWith(replacement);
        } else {
            replacement.innerHTML = '<br>';
            surface.appendChild(replacement);
        }

        // Leave the caret at the end of the line that was just re-styled, ready to type.
        const range = document.createRange();
        range.selectNodeContents(replacement);
        range.collapse(false);

        const selection = window.getSelection();
        selection?.removeAllRanges();
        selection?.addRange(range);
        savedRange = range.cloneRange();
        surface.focus();
    };

    const run = (button) => {
        const { editorCommand: command, editorValue: value } = button.dataset;

        if (command === 'createLink') {
            const url = window.prompt('Link address (https://…)');

            if (url) {
                document.execCommand('createLink', false, url);
            }

            return;
        }

        if (command === 'formatBlock') {
            // Pressing the same block again returns the line to a paragraph.
            setBlock(currentBlock() === value ? 'p' : value);

            return;
        }

        if (command === 'removeFormat') {
            // Clear the inline marks *and* put the line back to an ordinary paragraph.
            document.execCommand('removeFormat', false);
            document.execCommand('unlink', false);

            if (currentBlock() === 'li') {
                document.execCommand('insertUnorderedList', false);
            }

            setBlock('p');

            return;
        }

        document.execCommand(command, false);
    };

    buttons.forEach((button) => {
        // mousedown, so the selection in the surface survives the click.
        button.addEventListener('mousedown', (event) => {
            event.preventDefault();
            surface.focus();
            restoreSelection();
            run(button);
            sync();
            syncState();
        });
    });

    /*
     * Drop cap: the oversized first letter that opens a magazine article. It is a
     * class on the paragraph, styled by .rich-text p.drop-cap::first-letter, so the
     * same look carries over to the published page.
     */
    dropCapButton?.addEventListener('mousedown', (event) => {
        event.preventDefault();
        surface.focus();
        restoreSelection();
        wrapLooseText();

        const block = blockElement();

        if (!block) {
            return;
        }

        // Only paragraphs get one, and only one paragraph at a time.
        if (block.tagName !== 'P') {
            setBlock('p');
        }

        const paragraph = blockElement();
        const on = paragraph.classList.toggle('drop-cap');

        if (on) {
            surface.querySelectorAll('.drop-cap').forEach((other) => {
                if (other !== paragraph) {
                    other.classList.remove('drop-cap');
                }
            });
        }

        if (paragraph.getAttribute('class') === '') {
            paragraph.removeAttribute('class');
        }

        sync();
        syncState();
    });

    // Image: upload first, then drop the stored URL in at the caret.
    if (imageButton && imageInput) {
        imageButton.addEventListener('mousedown', (event) => {
            event.preventDefault();
            surface.focus();
            rememberSelection();
            imageInput.click();
        });

        imageInput.addEventListener('change', async () => {
            const file = imageInput.files?.[0];

            if (!file) {
                return;
            }

            const body = new FormData();
            body.append('image', file);
            body.append('_token', document.querySelector('meta[name="csrf-token"]')?.content
                ?? root.closest('form')?.querySelector('input[name="_token"]')?.value
                ?? '');

            try {
                const response = await fetch(imageInput.dataset.endpoint ?? imageButton.dataset.endpoint, {
                    method: 'POST',
                    body,
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error(response.statusText);
                }

                const { url } = await response.json();
                surface.focus();
                restoreSelection();
                document.execCommand('insertImage', false, url);
                sync();
            } catch {
                window.alert('The image could not be uploaded. Please try again.');
            }

            imageInput.value = '';
        });
    }

    surface.addEventListener('input', sync);
    surface.addEventListener('blur', sync);
    ['keyup', 'mouseup', 'focus'].forEach((type) => surface.addEventListener(type, syncState));

    // Paste as plain text so nothing unexpected sneaks into the stored markup.
    surface.addEventListener('paste', (event) => {
        event.preventDefault();
        document.execCommand('insertText', false, event.clipboardData?.getData('text/plain') ?? '');
        sync();
    });

    root.closest('form')?.addEventListener('submit', sync);
    sync();
    syncState();
});

// Menu links to a section not built yet open the coming-soon message. Their
// href (the contact form) stays the way without scripts.
export function initComingSoon() {
    const dialog = document.querySelector('[data-coming-soon-dialog]');

    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    let trigger = null;
    let leaving = false;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-coming-soon]');

        if (!link) {
            return;
        }

        event.preventDefault();
        trigger = link;
        leaving = false;
        dialog.showModal();
    });

    // The close button, a click on the backdrop (the dialog itself, outside
    // its box), or the contact link, which then leads on.
    dialog.addEventListener('click', (event) => {
        const contact = event.target.closest('a');

        if (contact || event.target === dialog || event.target.closest('[data-coming-soon-close]')) {
            leaving = contact !== null;
            dialog.close();
        }
    });

    // Back to the link, or to the menu button when the folded menu hid it;
    // not when the visitor follows the contact link.
    dialog.addEventListener('close', () => {
        if (leaving) {
            return;
        }

        const visible = trigger && trigger.offsetParent !== null;
        (visible ? trigger : document.querySelector('[data-nav-toggle]'))?.focus();
    });
}

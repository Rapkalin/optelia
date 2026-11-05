// Header menu folded into a toggle on small screens (the breakpoint lives in
// the stylesheet only: this script just holds the open state).
export function initNavToggle() {
    const header = document.querySelector('[data-site-header]');
    const button = header?.querySelector('[data-nav-toggle]');
    const nav = button ? document.getElementById(button.getAttribute('aria-controls')) : null;

    if (!header || !button || !nav) {
        return;
    }

    const setOpen = (isOpen) => {
        header.classList.toggle('site-header--open', isOpen);
        button.setAttribute('aria-expanded', String(isOpen));
    };

    button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));

    // An anchor of the page: the menu must not cover the section it leads to.
    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            button.focus();
        }
    });
}

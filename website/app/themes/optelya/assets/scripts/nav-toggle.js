// Header menu folded into a toggle on small screens (the breakpoint lives in
// the stylesheet only: this script just holds the open state).
export function initNavToggle() {
    const header = document.querySelector('[data-site-header]');
    const button = header?.querySelector('[data-nav-toggle]');
    const nav = button ? document.getElementById(button.getAttribute('aria-controls')) : null;

    if (!header || !button || !nav) {
        return;
    }

    const isOpen = () => button.getAttribute('aria-expanded') === 'true';
    const setOpen = (open) => {
        header.classList.toggle('site-header--open', open);
        button.setAttribute('aria-expanded', String(open));
    };

    button.addEventListener('click', () => setOpen(!isOpen()));

    // An anchor of the page: the menu must not cover the section it leads to.
    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false);
            button.focus();
        }
    });
}

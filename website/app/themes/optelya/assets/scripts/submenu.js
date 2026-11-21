// Catalogue submenu of the header (components/catalog-submenu.php): a
// disclosure, not an ARIA menu — the button opens the panel, Escape, a click
// elsewhere or leaving it with the keyboard closes it. Without this script
// the button stays hidden and the link leads to the list.
export function initSubmenus() {
    document.querySelectorAll('[data-submenu]').forEach((submenu) => {
        const button = submenu.querySelector('.submenu__toggle');
        const panel = button ? document.getElementById(button.getAttribute('aria-controls')) : null;

        if (!button || !panel) {
            return;
        }

        const setOpen = (open) => {
            button.setAttribute('aria-expanded', String(open));
            panel.hidden = !open;
        };

        button.hidden = false;
        button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));

        submenu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.hidden) {
                setOpen(false);
                button.focus();
            }
        });

        // Keyboard only: a click on the panel's blank space moves no focus.
        submenu.addEventListener('focusout', (event) => {
            if (event.relatedTarget && !submenu.contains(event.relatedTarget)) {
                setOpen(false);
            }
        });

        document.addEventListener('click', (event) => {
            if (!submenu.contains(event.target)) {
                setOpen(false);
            }
        });
    });
}

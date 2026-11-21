// Solutions list (components/issue-filter.php): one issue's block at a time.
// The choice lives in the address (?enjeu=…), so it can be shared and linked
// to from the homepage. Without this script every card stays visible.
export function initIssueFilter() {
    const root = document.querySelector('[data-issue-filter]');

    if (!root) {
        return;
    }

    const bar = root.querySelector('[role="group"]');
    const options = bar.querySelector('.issue-filter__options');
    const buttons = [...bar.querySelectorAll('[data-filter]')];
    const parts = [...root.querySelectorAll('[data-filters]')];
    const count = root.querySelector('[data-issue-count]');
    const parameter = root.dataset.parameter;
    const all = buttons[0].dataset.filter;

    const show = (slug, remember) => {
        buttons.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.filter === slug)));
        parts.forEach((part) => {
            part.hidden = !part.dataset.filters.split(' ').includes(slug);
        });

        const shown = root.querySelectorAll('.card[data-filters]:not([hidden])').length;
        count.textContent = (shown > 1 ? root.dataset.countMany : root.dataset.countOne).replace('%d', String(shown));
        // A single scrolling row on small screens: bring the chosen pill into
        // view, once the frame is drawn (no layout forced in the middle of it).
        const pressed = buttons.find((button) => button.dataset.filter === slug);
        window.requestAnimationFrame(() => pressed.scrollIntoView({ block: 'nearest', inline: 'nearest' }));

        if (remember) {
            const url = new URL(window.location.href);
            slug === all ? url.searchParams.delete(parameter) : url.searchParams.set(parameter, slug);
            window.history.replaceState(null, '', url);
        }
    };

    bar.hidden = false;
    buttons.forEach((button) => button.addEventListener('click', () => show(button.dataset.filter, true)));

    // The page opens on « all » already: only an issue asked in the address changes it.
    const asked = new URLSearchParams(window.location.search).get(parameter);

    if (asked !== all && buttons.some((button) => button.dataset.filter === asked)) {
        show(asked, false);
    }
}

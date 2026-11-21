<?php

/**
 * Menu titles typed with an arrow (« Solutions ▾ ») lose it: the site draws
 * its own beside a submenu, and decision 1 (19/11/2026) wants none elsewhere.
 */

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaMenuLocation.php';

it('drops an arrow typed at the end of a menu title', function (string $title, string $expected): void {
    expect(OptelyaMenuLocation::linkLabel($title))->toBe($expected);
})->with([
    ['Solutions ▾', 'Solutions'],
    ['Industries▼', 'Industries'],
    ['  Services ▾  ', 'Services'],
    ['Études de cas', 'Études de cas'],
    ['▾ Événements', '▾ Événements'],
]);

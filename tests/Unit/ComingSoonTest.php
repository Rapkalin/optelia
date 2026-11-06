<?php

/**
 * The coming-soon message always has a title and a text: those of
 * Settings → Configuration, or the default of each one left empty.
 */

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaComingSoon.php';

it('uses the texts of the settings, trimmed', function () {
    expect(OptelyaComingSoon::texts('  En préparation ', ' Revenez vite. '))
        ->toBe(['title' => 'En préparation', 'text' => 'Revenez vite.']);
});

it('falls back to the default of each text left empty', function (string $title, string $text, string $expectedTitle, string $expectedText) {
    $texts = OptelyaComingSoon::texts($title, $text);

    expect($texts['title'])->toBe($expectedTitle);
    expect($texts['text'])->toBe($expectedText);
})->with([
    'both empty' => ['', '', 'Bientôt disponible', 'Cette section est en cours de construction. En attendant, nos experts répondent à vos questions.'],
    'blank title' => ['   ', 'Revenez vite.', 'Bientôt disponible', 'Revenez vite.'],
    'blank text' => ['En préparation', "\n ", 'En préparation', 'Cette section est en cours de construction. En attendant, nos experts répondent à vos questions.'],
]);

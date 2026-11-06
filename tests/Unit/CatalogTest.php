<?php

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaCatalog.php';

it('never shows « 0 étude » and agrees the label with the count', function (): void {
    expect(OptelyaCatalog::studiesLabel(0, 'Optimus'))->toBe('')
        ->and(OptelyaCatalog::studiesLabel(1, 'Optimus'))->toBe('Voir l’étude Optimus')
        ->and(OptelyaCatalog::studiesLabel(31, 'Optimus'))->toBe('Voir les 31 études Optimus');
});

it('keeps the header button on the page when the page has the section', function (): void {
    $url = 'https://optelya.test/#expert';

    expect(OptelyaCatalog::localAnchor($url, 'expert', true))->toBe('#expert')
        ->and(OptelyaCatalog::localAnchor($url, 'expert', false))->toBe($url)
        ->and(OptelyaCatalog::localAnchor('https://optelya.test/contact/', 'expert', true))->toBe('https://optelya.test/contact/');
});

it('puts the vendor seniority second in the first dark panel only', function (): void {
    $item = static fn(string $title): array => ['title' => $title, 'text' => ''];
    $panels = [
        ['reader' => 'Ingénieur', 'is_dark' => false, 'items' => [$item('A'), $item('B')]],
        ['reader' => 'Directeur', 'is_dark' => true, 'items' => [$item('C'), $item('D')]],
        ['reader' => 'Autre', 'is_dark' => true, 'items' => [$item('E')]],
    ];

    $result = OptelyaCatalog::readersWithVendor($panels, 'Depuis 1996.');

    expect(array_column($result[1]['items'], 'title'))->toBe(['C', 'Un éditeur établi', 'D'])
        ->and($result[1]['items'][1]['text'])->toBe('Depuis 1996.')
        ->and($result[0])->toBe($panels[0])
        ->and($result[2])->toBe($panels[2])
        ->and(OptelyaCatalog::readersWithVendor($panels, ''))->toBe($panels);
});

<?php

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaCatalog.php';

it('never shows « 0 étude » and agrees the label with the count', function (): void {
    expect(OptelyaCatalog::studiesLabel(0, 'Optimus'))->toBe('')
        ->and(OptelyaCatalog::studiesLabel(1, 'Optimus'))->toBe('Voir l’étude Optimus')
        ->and(OptelyaCatalog::studiesLabel(31, 'Optimus'))->toBe('Voir les 31 études Optimus');
});

it('offers no « see all » when the cards shown are all the studies', function (): void {
    expect(OptelyaCatalog::studiesLabel(1, 'Sigmetrix', 1))->toBe('')
        ->and(OptelyaCatalog::studiesLabel(3, 'Optimus', 3))->toBe('')
        ->and(OptelyaCatalog::studiesLabel(31, 'Optimus', 3))->toBe('Voir les 31 études Optimus');
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

it('finds the vendors of the products a card names, in order and once', function (): void {
    $vendors = ['Optimus' => 'Noesis Solutions', 'id8' => 'Noesis Solutions', 'CETOL 6σ' => 'Sigmetrix', 'RecurDyn' => 'FunctionBay'];

    expect(OptelyaCatalog::vendorsNamedIn('CETOL 6σ + Optimus · id8', $vendors))->toBe(['Sigmetrix', 'Noesis Solutions'])
        ->and(OptelyaCatalog::vendorsNamedIn('RecurDyn', $vendors))->toBe(['FunctionBay'])
        ->and(OptelyaCatalog::vendorsNamedIn('Automobile & Mobilité', $vendors))->toBe([])
        ->and(OptelyaCatalog::vendorsNamedIn('', ['' => 'Personne']))->toBe([]);
});

it('links to all the solutions of a vendor only beyond the cards shown', function (): void {
    expect(OptelyaCatalog::solutionsLabel(2, 'Noesis Solutions', 2))->toBe('')
        ->and(OptelyaCatalog::solutionsLabel(3, 'Sigmetrix', 3))->toBe('')
        ->and(OptelyaCatalog::solutionsLabel(5, 'Sigmetrix', 3))->toBe('Voir les 5 solutions Sigmetrix');
});

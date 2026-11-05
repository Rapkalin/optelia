<?php

/**
 * Header background chosen in the admin: only a colour reaches the style
 * attribute, and the text picked for it stays readable.
 */

require_once dirname(__DIR__, 2) . '/website/app/themes/optelya/inc/enums/OptelyaHeaderColor.php';

it('keeps a valid colour and falls back to the default otherwise', function (mixed $value, string $expected) {
    expect(OptelyaHeaderColor::normalize($value))->toBe($expected);
})->with([
    ['#bbe1ff', '#BBE1FF'],
    ['#0F1D2B', '#0F1D2B'],
    ['', OptelyaHeaderColor::DEFAULT],
    [null, OptelyaHeaderColor::DEFAULT],
    ['red', OptelyaHeaderColor::DEFAULT],
    ['#fff', OptelyaHeaderColor::DEFAULT],
    ['#000000; background: url(x)', OptelyaHeaderColor::DEFAULT],
    ['#00000"><script>', OptelyaHeaderColor::DEFAULT],
]);

it('puts ink text on light backgrounds and white text on dark ones', function (string $background, bool $isLight) {
    expect(OptelyaHeaderColor::isLight($background))->toBe($isLight);
})->with([
    ['#BBE1FF', true],
    ['#FFFFFF', true],
    ['#F5F6F3', true],
    ['#0F1D2B', false],
    ['#1E73BE', false],
    ['#000000', false],
]);

it('reaches the required contrast with the text it picks, mid tones excepted', function (string $background) {
    $text = OptelyaHeaderColor::isLight($background) ? OptelyaHeaderColor::INK : OptelyaHeaderColor::WHITE;

    expect(OptelyaHeaderColor::contrast($text, $background))->toBeGreaterThanOrEqual(OptelyaHeaderColor::MIN_CONTRAST);
})->with(['#BBE1FF', '#FFFFFF', '#0F1D2B', '#1E73BE', '#0E1F5B', '#F2F8FF']);

it('flags the mid tones no text can read on', function () {
    expect(OptelyaHeaderColor::bestContrast('#777777'))->toBeLessThan(OptelyaHeaderColor::MIN_CONTRAST);
    expect(OptelyaHeaderColor::bestContrast('#BBE1FF'))->toBeGreaterThanOrEqual(OptelyaHeaderColor::MIN_CONTRAST);
});

it('keeps the ink and the default in step with the stylesheet and the field', function () {
    $theme = dirname(__DIR__, 2) . '/website/app/themes/optelya/';
    $variables = (string) file_get_contents($theme . 'assets/styles/basics/_variables.scss');
    $group = json_decode((string) file_get_contents($theme . 'acf-json/group_optelya_settings.json'), true);
    $field = array_values(array_filter($group['fields'], static fn(array $f): bool => $f['name'] === 'header_background'))[0];

    expect($variables)->toContain('$color-ink: ' . OptelyaHeaderColor::INK . ';');
    expect(strtoupper($field['default_value']))->toBe(OptelyaHeaderColor::DEFAULT);
});

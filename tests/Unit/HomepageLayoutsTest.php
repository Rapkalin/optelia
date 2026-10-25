<?php

/**
 * The homepage layout catalogue's contract.
 *
 * `front-page.php` dispatches on the layout NAME: `get_row_layout()` becomes
 * `layouts/<name>.php`. A layout declared without a template renders nothing,
 * silently; a template without a declared layout is dead code. Both are checked
 * against the JSON and the files on disk, with no hardcoded list.
 */

const OPTELYA_THEME = __DIR__ . '/../../website/app/themes/optelya/';
const OPTELYA_HOMEPAGE_GROUP = OPTELYA_THEME . 'acf-json/group_optelya_homepage.json';

function optelya_homepage_group(): array
{
    return json_decode((string) file_get_contents(OPTELYA_HOMEPAGE_GROUP), true);
}

/**
 * @return list<string>
 */
function optelya_sections_field(): array
{
    foreach (optelya_homepage_group()['fields'] as $field) {
        if (($field['name'] ?? '') === 'sections') {
            return $field;
        }
    }

    return ['layouts' => []];
}

/**
 * @return list<string>
 */
function optelya_declared_layouts(): array
{
    $names = array_column((array) optelya_sections_field()['layouts'], 'name');
    sort($names);

    return $names;
}

/**
 * @return list<string>
 */
function optelya_layout_templates(): array
{
    $names = array_map(
        static fn(string $path): string => basename($path, '.php'),
        (array) glob(OPTELYA_THEME . 'layouts/*.php'),
    );

    sort($names);

    return $names;
}

it('declares at least one layout', function () {
    // Two empty catalogues would otherwise compare equal.
    expect(optelya_declared_layouts())->not->toBeEmpty();
});

it('gives every declared layout a template, and every template a layout', function () {
    expect(optelya_declared_layouts())->toBe(optelya_layout_templates());
});

it('allows each section once, since each one carries a menu anchor', function () {
    foreach (optelya_sections_field()['layouts'] as $layout) {
        expect($layout['max'])->toBe('1');
    }
});

it('requires the hero, which carries the page h1', function () {
    $hero = array_values(array_filter(optelya_sections_field()['layouts'], static fn(array $layout): bool => $layout['name'] === 'hero'));

    expect($hero[0]['min'] ?? null)->toBe('1');
});

it('locates the field group on the front page', function () {
    expect(optelya_homepage_group()['location'])
        ->toBe([[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]]);
});

it('hides the editor content so there is a single contribution surface', function () {
    expect(optelya_homepage_group()['hide_on_screen'])->toContain('the_content');
});

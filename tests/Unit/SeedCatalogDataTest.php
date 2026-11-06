<?php

/**
 * A repeater row seeded as a plain list (['Title', 'Text']) is silently
 * dropped by ACF: the page then shows empty items. Every seeded row named
 * like a repeater of the catalogue must use that repeater's sub-field names.
 */

require_once OPTELYA_THEME_DIR . 'inc/seed-catalog-data.php';

/**
 * Sub-field names of every repeater of the catalogue groups, by repeater name.
 *
 * @return array<string, list<string>>
 */
function seedRepeaterSubFields(): array
{
    $repeaters = [];
    $collect = static function (array $fields) use (&$collect, &$repeaters): void {
        foreach ($fields as $field) {
            if (($field['type'] ?? '') === 'repeater') {
                $repeaters[$field['name']] = array_merge($repeaters[$field['name']] ?? [], array_column($field['sub_fields'], 'name'));
                $collect($field['sub_fields']);
            }
        }
    };

    foreach (glob(OPTELYA_THEME_DIR . 'acf-json/group_optelya_{solution,solution_settings,vendor,industry,study}.json', GLOB_BRACE) as $file) {
        $collect(json_decode((string) file_get_contents($file), true)['fields']);
    }

    return $repeaters;
}

/**
 * Rows found under a key named like a repeater, with their path.
 *
 * @return list<array{path: string, repeater: string, row: mixed}>
 */
function seedRepeaterRows(array $data, array $repeaters, string $path = ''): array
{
    $rows = [];

    foreach ($data as $key => $value) {
        if (! is_array($value)) {
            continue;
        }

        if (is_string($key) && isset($repeaters[$key]) && array_is_list($value)) {
            foreach ($value as $index => $row) {
                $rows[] = ['path' => "{$path}{$key}[{$index}]", 'repeater' => $key, 'row' => $row];
            }
        }

        $rows = [...$rows, ...seedRepeaterRows($value, $repeaters, "{$path}{$key}.")];
    }

    return $rows;
}

it('seeds every repeater row with its sub-field names', function (): void {
    $repeaters = seedRepeaterSubFields();
    $rows = seedRepeaterRows(optelya_seed_catalog_data(), $repeaters);

    expect($rows)->not->toBeEmpty();

    foreach ($rows as ['path' => $path, 'repeater' => $repeater, 'row' => $row]) {
        expect(is_array($row) && ! array_is_list($row))->toBeTrue("{$path} is not keyed by sub-field name.");
        expect(array_diff(array_keys($row), $repeaters[$repeater]))->toBe([], "{$path} has keys that are not sub-fields of {$repeater}.");
    }
});

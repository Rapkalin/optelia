<?php

/**
 * OptelyaCatalogSetting names the fields of the two catalogue settings pages:
 * a field renamed in the JSON, or a case without field, would read nothing.
 */

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaPostType.php';
require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaCatalogSetting.php';

it('names exactly the fields of the catalogue settings pages', function (): void {
    $fields = [];

    foreach (['solution_settings', 'industry_settings'] as $group) {
        $json = json_decode((string) file_get_contents(OPTELYA_THEME_DIR . "acf-json/group_optelya_{$group}.json"), true);
        $fields = [...$fields, ...array_filter(array_column($json['fields'], 'name'))];
    }

    $cases = array_map(static fn(OptelyaCatalogSetting $setting): string => $setting->value, OptelyaCatalogSetting::cases());

    expect($cases)->toEqualCanonicalizing($fields);
});

it('knows the head of each built list only', function (): void {
    expect(OptelyaCatalogSetting::listHead(OptelyaPostType::Solution))->toBe(OptelyaCatalogSetting::SolutionsHead)
        ->and(OptelyaCatalogSetting::listHead(OptelyaPostType::Industry))->toBe(OptelyaCatalogSetting::IndustriesHead)
        ->and(OptelyaCatalogSetting::listHead(OptelyaPostType::Study))->toBeNull();
});

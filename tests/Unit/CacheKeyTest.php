<?php

/**
 * The catalogue cache is emptied by a write to any field it reads: a field read
 * by optelya_build_catalog() but missing from metaKeys() would stay stale after
 * a scripted write.
 */

if (! interface_exists('RapkalinCacheEntry')) {
    interface RapkalinCacheEntry extends BackedEnum {}
}

if (! defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

if (! function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback): void {}
}

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaCacheKey.php';

it('lists every post field the catalogue reads among the keys that empty it', function (): void {
    $source = (string) file_get_contents(OPTELYA_THEME_DIR . 'inc/catalog.php');
    $start = strpos($source, 'function optelya_build_catalog()');
    $body = substr($source, $start, strpos($source, "\n}\n", $start) - $start);

    preg_match_all("/(?:optelya_field|\\\$text)\\((?:'(\\w+)', \\\$id|\\\$id, '(\\w+)')\\)/", $body, $matches);
    $read = array_values(array_unique(array_filter([...$matches[1], ...$matches[2]])));

    expect($read)->not->toBeEmpty()
        ->and(array_values(array_diff($read, OptelyaCacheKey::Catalog->metaKeys())))->toBe([]);
});

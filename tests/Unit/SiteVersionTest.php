<?php

/**
 * The site version's contract.
 *
 * `optelya_site_version()` reads `composer.json` at the repository root, the
 * single place the version is declared. The path is computed from ABSPATH and
 * would break silently on a layout change: the footer would lose its version.
 */

require_once dirname(__DIR__, 2) . '/website/app/themes/optelya/inc/template.php';

it('reads the version declared in composer.json', function () {
    $manifest = json_decode(
        (string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'),
        true,
    );

    expect(optelya_site_version())->toBe($manifest['version']);
});

it('returns a semantic version', function () {
    expect(optelya_site_version())->toMatch('/^\d+\.\d+\.\d+$/');
});

it('resolves composer.json from ABSPATH, two levels up', function () {
    expect(dirname(ABSPATH, 2) . '/composer.json')->toBeFile();
});

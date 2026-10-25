<?php

/**
 * The version and the changelog move together (CLAUDE.md, rule 7): a release
 * whose version has no changelog entry fails here, before it reaches the CI.
 */

require_once dirname(__DIR__, 2) . '/website/app/themes/optelya/inc/template.php';

it('documents the current version in CHANGELOG.md', function () {
    $changelog = (string) file_get_contents(dirname(__DIR__, 2) . '/CHANGELOG.md');
    $version = optelya_site_version();

    // An empty version would match any heading: the section must be this one.
    expect($version)->not->toBe('')
        ->and($changelog)->toMatch('/^## ' . preg_quote($version, '/') . '$/m');
});

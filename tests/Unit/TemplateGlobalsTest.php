<?php

/**
 * WordPress loads every template and template part with `$id`, `$post`,
 * `$posts`, `$comment`… declared global (load_template()). A template that
 * assigns one of them overwrites the caller's: a component writing `$id`
 * broke the solution page (06/11/2026).
 */

const OPTELYA_TEMPLATE_GLOBALS = ['id', 'post', 'posts', 'comment', 'user_ID', 'wp_query', 'wp', 'wpdb'];

function optelya_test_template_files(): array
{
    $files = [];

    foreach (['', 'components/', 'layouts/', 'admin/'] as $directory) {
        foreach (glob(OPTELYA_THEME_DIR . $directory . '*.php') ?: [] as $file) {
            if ($directory !== '' || ! str_contains((string) file_get_contents($file), "require_once __DIR__ . '/inc/")) {
                $files[] = $file;
            }
        }
    }

    return $files;
}

it('finds templates to inspect', function () {
    expect(optelya_test_template_files())->toContain(OPTELYA_THEME_DIR . 'components/hero.php');
});

it('never assigns a variable WordPress declares global in templates', function (string $file) {
    $names = implode('|', OPTELYA_TEMPLATE_GLOBALS);

    expect((string) file_get_contents($file))->not->toMatch('/\$(' . $names . ')\s*=[^=>]/');
})->with(fn() => optelya_test_template_files());

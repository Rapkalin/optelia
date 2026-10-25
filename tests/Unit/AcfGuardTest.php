<?php

/**
 * ACF Pro is licensed, outside the repository: a template that calls one of its
 * functions without a guard kills the page whenever it is not active — a blank
 * 500, since `display_errors` is 0 outside development (LCDS, 05/09/2026).
 *
 * Static and deliberately coarse: a file that calls an ACF function must also
 * carry a guard. It does not prove the guard wraps the right call; it makes it
 * impossible to write one without thinking about it.
 */

function optelya_theme_php_files(): array
{
    $files = [];
    $theme = __DIR__ . '/../../website/app/themes/optelya/';

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme)) as $file) {
        $path = (string) $file;

        if (str_ends_with($path, '.php') && ! str_contains($path, '/node_modules/') && ! str_contains($path, '/dist/')) {
            $files[] = $path;
        }
    }

    sort($files);

    return $files;
}

it('finds theme files to inspect', function () {
    expect(optelya_theme_php_files())->not->toBeEmpty();
});

it('never calls ACF without a guard in the same file', function (string $file) {
    // Comments are stripped first: a comment MENTIONING get_field() is not a call.
    $code = '';

    foreach (token_get_all((string) file_get_contents($file)) as $token) {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }

        $code .= is_array($token) ? $token[1] : $token;
    }

    $acfFunctions = [
        'have_rows', 'the_row', 'get_row_layout', 'get_field', 'the_field', 'get_sub_field',
        'the_sub_field', 'update_field', 'acf_get_fields', 'acf_add_options_sub_page', 'acf_get_local_json_files',
    ];
    $calls = array_filter(
        $acfFunctions,
        static fn(string $function): bool => preg_match('/(?<![\w$>])' . $function . '\s*\(/', $code) === 1,
    );
    $isGuarded = str_contains($code, 'optelya_has_acf()') || str_contains($code, 'function_exists(');

    expect($calls === [] || $isGuarded)->toBeTrue(sprintf(
        '%s calls %s without a guard: the page falls in 500 when ACF is not active.',
        basename($file),
        implode(', ', $calls),
    ));
})->with(optelya_theme_php_files());

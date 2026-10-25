<?php

/**
 * The account screen a contributor is left with.
 *
 * The load-bearing assertion is the last one: EVERY row the core renders is
 * named, as kept, hidden, or handled elsewhere. A WordPress update adding a
 * field fails here instead of surfacing it silently.
 */

require_once dirname(__DIR__, 2) . '/website/app/themes/optelya/inc/enums/OptelyaProfileField.php';

it('keeps exactly the fields a contributor was left', function () {
    expect(OptelyaProfileField::keptRows())->toBe([
        'user-admin-color-wrap',
        'user-admin-bar-front-wrap',
        'user-user-login-wrap',
        'user-first-name-wrap',
        'user-last-name-wrap',
        'user-nickname-wrap',
        'user-email-wrap',
    ]);
});

it('never hides a field it keeps', function () {
    expect(array_intersect(OptelyaProfileField::hiddenRows(), OptelyaProfileField::keptRows()))->toBe([]);
});

it('names every row the core renders', function () {
    $source = (string) file_get_contents(dirname(__DIR__, 2) . '/website/wordpress-core/wp-admin/user-edit.php');

    preg_match_all('/class="([a-z-]*user-[a-z-]+-wrap)"/', $source, $matches);
    $rendered = array_unique($matches[1]);
    $named = [
        ...OptelyaProfileField::keptRows(),
        ...OptelyaProfileField::hiddenRows(),
        ...OptelyaProfileField::rowsHandledElsewhere(),
    ];

    expect($rendered)->not->toBeEmpty()
        ->and(array_values(array_diff($rendered, $named)))->toBe([]);
});

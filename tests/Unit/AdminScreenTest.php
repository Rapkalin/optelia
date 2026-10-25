<?php

/**
 * The contributor's admin perimeter.
 *
 * `OptelyaAdminScreen::isAllowed()` carries the restriction: hiding a menu entry
 * protects nothing, the URL stays typeable. It is an ALLOW-LIST, so the default
 * answer is checked as much as the listed ones.
 */

require_once dirname(__DIR__, 2) . '/website/app/themes/optelya/inc/enums/OptelyaAdminScreen.php';

it('allows the screens a contributor works in', function (string $pagenow, array $query) {
    expect(OptelyaAdminScreen::isAllowed($pagenow, $query))->toBeTrue();
})->with([
    ['index.php', []],
    ['profile.php', []],
    ['upload.php', []],
    ['media-new.php', []],
    ['nav-menus.php', []],
    ['customize.php', []],
    ['edit.php', ['post_type' => 'page']],
    ['post-new.php', ['post_type' => 'page']],
    ['post.php', ['post_type' => 'page']],
    ['options-general.php', ['page' => OptelyaAdminScreen::SETTINGS_PAGE]],
]);

it('denies everything else', function (string $pagenow, array $query) {
    expect(OptelyaAdminScreen::isAllowed($pagenow, $query))->toBeFalse();
})->with([
    ['themes.php', []],
    ['site-editor.php', []],
    ['font-library.php', []],
    ['theme-editor.php', []],
    ['plugins.php', []],
    ['plugin-editor.php', []],
    ['users.php', []],
    ['user-new.php', []],
    ['tools.php', []],
    ['export.php', []],
    ['import.php', []],
    ['site-health.php', []],
    ['edit-comments.php', []],
    ['options.php', []],
    ['options-permalink.php', []],
    ['options-reading.php', []],
    ['options-writing.php', []],
    // ACF field groups: a post type outside the perimeter.
    ['edit.php', ['post_type' => 'acf-field-group']],
    // Posts are not in the perimeter: only pages are.
    ['edit.php', []],
    ['post-new.php', []],
    ['post-new.php', ['post_type' => 'post']],
]);

it('never lets an unknown screen through by default', function (string $pagenow) {
    expect(OptelyaAdminScreen::isAllowed($pagenow, []))->toBeFalse();
})->with([['a-future-plugin.php'], ['admin.php'], [''], ['../wp-config.php']]);

it('opens Settings on our page only, never on the core ones', function () {
    expect(OptelyaAdminScreen::isAllowed('options-general.php', []))->toBeFalse();
    expect(OptelyaAdminScreen::isAllowed('options-general.php', ['page' => 'other']))->toBeFalse();
});

it('never lists a screen as both forbidden and reachable', function () {
    foreach (OptelyaAdminScreen::forbiddenMenus() as $slug) {
        expect(OptelyaAdminScreen::isAllowed($slug, []))->toBeFalse();
    }
});

it('locates the settings field group on the settings page slug', function () {
    // The JSON cannot read the constant: this keeps both in step.
    $group = json_decode((string) file_get_contents(
        dirname(__DIR__, 2) . '/website/app/themes/optelya/acf-json/group_optelya_settings.json',
    ), true);

    expect($group['location'][0][0]['value'])->toBe(OptelyaAdminScreen::SETTINGS_PAGE);
});

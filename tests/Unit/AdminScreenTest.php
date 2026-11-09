<?php

/**
 * The restricted roles' admin perimeter.
 *
 * `OptelyaAdminScreen::isAllowed()` carries the restriction: hiding a menu entry
 * protects nothing, the URL stays typeable. It is an ALLOW-LIST, so the default
 * answer is checked as much as the listed ones.
 */

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaRole.php';
require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaPostType.php';
require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaAdminScreen.php';

it('allows both roles the screens they work in', function (string $pagenow, array $query) {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, $pagenow, $query))->toBeTrue();
    }
})->with([
    ['index.php', []],
    ['profile.php', []],
    ['upload.php', []],
    ['media-new.php', []],
    ['edit.php', ['post_type' => 'page']],
    ['post-new.php', ['post_type' => 'page']],
    ['post.php', ['post_type' => 'page']],
    ['options-general.php', ['page' => OptelyaAdminScreen::SETTINGS_PAGE]],
]);

it('opens the catalogue content types to both roles', function (OptelyaPostType $type) {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, 'edit.php', ['post_type' => $type->value]))->toBeTrue();
        expect(OptelyaAdminScreen::isAllowed($role, 'post-new.php', ['post_type' => $type->value]))->toBeTrue();
    }
})->with(OptelyaPostType::cases());

it('opens menus, customizer, taxonomies and the Contact menu to the editor only', function (string $pagenow, array $query) {
    expect(OptelyaAdminScreen::isAllowed(OptelyaRole::Editor, $pagenow, $query))->toBeTrue();
    expect(OptelyaAdminScreen::isAllowed(OptelyaRole::Contributor, $pagenow, $query))->toBeFalse();
})->with([
    ['nav-menus.php', []],
    ['customize.php', []],
    ['edit-tags.php', ['taxonomy' => 'any', 'post_type' => 'page']],
    ['term.php', ['taxonomy' => 'any', 'post_type' => 'attachment']],
    ['admin.php', ['page' => OptelyaAdminScreen::CONTACT_PAGE]],
    ['admin.php', ['page' => OptelyaAdminScreen::CONTACT_SETTINGS_PAGE]],
]);

it('opens no other plugin page through admin.php, even to the editor', function () {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, 'admin.php', ['page' => 'a-future-plugin']))->toBeFalse();
        expect(OptelyaAdminScreen::isAllowed($role, 'admin.php', ['page' => ['optelya-contact']]))->toBeFalse();
    }
});

it('gives the Contact menu to the editor, never to the contributor', function () {
    expect(OptelyaRole::Editor->extraCapabilities())->toContain(OptelyaRole::CONTACT_CAPABILITY);
    expect(OptelyaRole::Contributor->extraCapabilities())->not->toContain(OptelyaRole::CONTACT_CAPABILITY);
});

it('denies everything else to both roles', function (string $pagenow, array $query) {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, $pagenow, $query))->toBeFalse();
    }
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
    // Posts are not in the perimeter: only pages are, and their terms.
    ['edit.php', []],
    ['post-new.php', []],
    ['post-new.php', ['post_type' => 'post']],
    ['edit-tags.php', ['taxonomy' => 'category']],
    ['term.php', ['taxonomy' => 'post_tag', 'post_type' => 'post']],
]);

it('never lets an unknown screen through by default', function (string $pagenow) {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, $pagenow, []))->toBeFalse();
    }
})->with([['a-future-plugin.php'], ['admin.php'], [''], ['../wp-config.php']]);

it('opens Settings on our page only, never on the core ones', function () {
    foreach (OptelyaRole::cases() as $role) {
        expect(OptelyaAdminScreen::isAllowed($role, 'options-general.php', []))->toBeFalse();
        expect(OptelyaAdminScreen::isAllowed($role, 'options-general.php', ['page' => 'other']))->toBeFalse();
    }
});

it('never lists a screen as both forbidden and reachable', function () {
    foreach (OptelyaRole::cases() as $role) {
        foreach (OptelyaAdminScreen::forbiddenMenus() as $slug) {
            expect(OptelyaAdminScreen::isAllowed($role, $slug, []))->toBeFalse();
        }
    }
});

it('gives the settings capability to both roles, the theme options to the editor only', function () {
    foreach (OptelyaRole::cases() as $role) {
        expect($role->extraCapabilities())->toContain(OptelyaRole::SETTINGS_CAPABILITY);
        expect($role->extraCapabilities())->not->toContain('manage_options');
    }
    expect(OptelyaRole::Contributor->extraCapabilities())->not->toContain('edit_theme_options');
});

it('builds the contributor on the author, who never touches others\' content', function () {
    expect(OptelyaRole::Contributor->baseRole())->toBe('author');
    expect(OptelyaRole::Editor->baseRole())->toBe('editor');

    foreach (OptelyaRole::Contributor->extraCapabilities() as $capability) {
        expect($capability)->not->toContain('others');
    }
});

it('finds the restricted role among an account\'s roles', function () {
    expect(OptelyaRole::among(['subscriber', 'optelya_editor']))->toBe(OptelyaRole::Editor);
    expect(OptelyaRole::among(['administrator']))->toBeNull();
    expect(OptelyaRole::among([]))->toBeNull();
});

it('locates each settings field group on its page slug', function (string $file, string $page) {
    // The JSON cannot read the constant: this keeps both in step.
    $group = json_decode((string) file_get_contents(OPTELYA_THEME_DIR . 'acf-json/' . $file), true);

    expect($group['location'][0][0]['value'])->toBe($page);
})->with([
    ['group_optelya_settings.json', OptelyaAdminScreen::SETTINGS_PAGE],
    ['group_optelya_contact_settings.json', OptelyaAdminScreen::CONTACT_SETTINGS_PAGE],
]);

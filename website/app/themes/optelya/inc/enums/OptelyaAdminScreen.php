<?php

/**
 * Admin perimeter of the contributor role — single source.
 *
 * An ALLOW-LIST, not a deny-list: a screen added by a future plugin is denied by
 * default, where a deny-list would let it through unnoticed. `isAllowed()` is
 * PURE (no current user, no database), which is what makes it testable.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaAdminScreen
{
    /**
     * Slug of the site settings page (Settings → Configuration). Also the
     * location of acf-json/group_optelya_settings.json, kept in step by
     * tests/Unit/AdminScreenTest.php.
     */
    public const string SETTINGS_PAGE = 'optelya-settings';

    /**
     * Always allowed. `admin-ajax.php`, `admin-post.php` and `async-upload.php`
     * are not screens: denying them would break the editor and the media upload
     * without protecting anything.
     */
    private const array ALWAYS = [
        'index.php',
        'profile.php',
        'upload.php',
        'media-new.php',
        'media-upload.php',
        'nav-menus.php',
        'customize.php',
        'admin-ajax.php',
        'admin-post.php',
        'async-upload.php',
    ];

    private const array POST_TYPES = ['page', 'attachment'];

    public static function isAllowed(string $pagenow, array $query = []): bool
    {
        if (in_array($pagenow, self::ALWAYS, true)) {
            return true;
        }

        // The site settings, and only them, among the Settings screens.
        if ($pagenow === 'options-general.php') {
            return ($query['page'] ?? '') === self::SETTINGS_PAGE;
        }

        if (in_array($pagenow, ['edit.php', 'post-new.php'], true)) {
            return in_array((string) ($query['post_type'] ?? 'post'), self::POST_TYPES, true);
        }

        // post.php does not carry the type in the URL: the caller resolves it.
        if ($pagenow === 'post.php') {
            $type = (string) ($query['post_type'] ?? '');

            return $type === '' || in_array($type, self::POST_TYPES, true);
        }

        return false;
    }

    /**
     * Top-level menu entries removed. NOT `options-general.php`: it would take
     * Settings → Configuration with it. The core Settings screens need
     * `manage_options`, which the role lacks, so WordPress promotes our page to
     * the parent's place.
     *
     * @return list<string>
     */
    public static function forbiddenMenus(): array
    {
        return ['edit.php', 'edit-comments.php', 'tools.php', 'plugins.php', 'users.php'];
    }

    /**
     * Submenus removed. `edit_theme_options` is needed for menus and the
     * customizer, but also opens the site editor and the font library.
     *
     * @return list<array{0: string, 1: string}>
     */
    public static function forbiddenSubmenus(): array
    {
        return [
            ['themes.php', 'themes.php'],
            ['themes.php', 'site-editor.php'],
            ['themes.php', 'font-library.php'],
            ['themes.php', 'widgets.php'],
            ['themes.php', 'theme-editor.php'],
            // Patterns go through the site editor with a parameter: another slug.
            ['themes.php', 'site-editor.php?p=%2Fpattern'],
            ['themes.php', 'site-editor.php?p=/pattern'],
        ];
    }
}

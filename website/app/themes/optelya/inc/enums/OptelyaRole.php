<?php

/**
 * The two restricted roles — single source of their slugs, names and
 * capabilities. See readme/roles.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaRole: string
{
    /**
     * WordPress' Author, applied to pages: their OWN pages and media, plus
     * Settings → Configuration.
     */
    case Contributor = 'optelya_contributor';

    /**
     * WordPress' Editor: every page and media, plus menus and Settings →
     * Configuration.
     */
    case Editor = 'optelya_editor';

    /**
     * Opens Settings → Configuration. A DEDICATED capability, not
     * `manage_options`: that one opens the seven core Settings screens (Reading
     * picks the homepage, Permalinks…) and options.php, the raw editor of every
     * option — it would make an editor an administrator.
     */
    public const string SETTINGS_CAPABILITY = 'optelya_manage_settings';

    /**
     * The author's capabilities name posts only. `edit_posts` and
     * `delete_posts` stay: WordPress checks them for one's own media.
     */
    private const array OWN_PAGES_CAPABILITIES = [
        'edit_pages',
        'edit_published_pages',
        'publish_pages',
        'delete_pages',
        'delete_published_pages',
    ];

    public function label(): string
    {
        return match ($this) {
            self::Contributor => __('Contributeur Optelya', 'optelya'),
            self::Editor => __('Éditeur Optelya', 'optelya'),
        };
    }

    /**
     * The core role the capabilities start from.
     */
    public function baseRole(): string
    {
        return match ($this) {
            self::Contributor => 'author',
            self::Editor => 'editor',
        };
    }

    /**
     * Capabilities added to the base role. `edit_theme_options` also opens the
     * site editor and the font library: the screen guard refuses them.
     *
     * @return list<string>
     */
    public function extraCapabilities(): array
    {
        return match ($this) {
            self::Contributor => [...self::OWN_PAGES_CAPABILITIES, self::SETTINGS_CAPABILITY],
            self::Editor => ['edit_theme_options', self::SETTINGS_CAPABILITY],
        };
    }

    /**
     * The restricted role among an account's roles, if any.
     *
     * @param array<string> $roles
     */
    public static function among(array $roles): ?self
    {
        foreach ($roles as $role) {
            $restricted = self::tryFrom($role);

            if ($restricted !== null) {
                return $restricted;
            }
        }

        return null;
    }
}

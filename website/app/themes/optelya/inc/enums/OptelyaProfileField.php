<?php

/**
 * "My account", reduced to what a contributor uses.
 *
 * The cases are the class the core puts on each ROW of the form
 * (wp-admin/user-edit.php): the core exposes no filter per field. The enum
 * lists what STAYS, so a row added by a core update does not show up by default.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaProfileField: string
{
    case AdminColor = 'user-admin-color-wrap';
    case Toolbar = 'user-admin-bar-front-wrap';
    case Login = 'user-user-login-wrap';
    case FirstName = 'user-first-name-wrap';
    case LastName = 'user-last-name-wrap';
    case Nickname = 'user-nickname-wrap';
    case Email = 'user-email-wrap';

    /**
     * Core rows hidden from the screen. Listed, not derived: the core gives no
     * way to list its own rows, and a derivation would be wrong silently.
     *
     * @return list<string>
     */
    public static function hiddenRows(): array
    {
        return [
            'user-rich-editing-wrap',
            'user-syntax-highlighting-wrap',
            'user-comment-shortcuts-wrap',
            'user-infinite-scrolling-wrap',
            'user-language-wrap',
            'user-display-name-wrap',
            'user-url-wrap',
        ];
    }

    /**
     * Rows handled OTHERWISE than by the stylesheet, each for its own reason.
     *
     * @return list<string>
     */
    public static function rowsHandledElsewhere(): array
    {
        return [
            // Not rendered by the core on one's OWN profile.
            'user-role-wrap',
            'user-super-admin-wrap',
            // Removed by the `additional_capabilities_display` filter.
            'user-capabilities-wrap',
            // In "About yourself", hidden with the whole section.
            'user-description-wrap',
            // Kept: it belongs to "Account management".
            'user-sessions-wrap',
        ];
    }

    /**
     * @return list<string>
     */
    public static function keptRows(): array
    {
        return array_map(static fn(self $field): string => $field->value, self::cases());
    }
}

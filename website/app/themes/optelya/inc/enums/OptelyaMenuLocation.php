<?php

/**
 * Menu locations. Each footer column is its own menu: its NAME is the column
 * title, so an editor renames a column in Appearance → Menus.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaMenuLocation: string
{
    case Header = 'header';
    case FooterSolutions = 'footer-solutions';
    case FooterResources = 'footer-resources';
    case Legal = 'legal';

    public function label(): string
    {
        return match ($this) {
            self::Header => __('En-tête', 'optelya'),
            self::FooterSolutions => __('Pied de page — 1re colonne', 'optelya'),
            self::FooterResources => __('Pied de page — 2e colonne', 'optelya'),
            self::Legal => __('Pied de page — mentions', 'optelya'),
        };
    }

    /**
     * A menu title as shown: an arrow typed in it (« Solutions ▾ ») goes,
     * the site draws its own where there is a submenu.
     */
    public static function linkLabel(string $title): string
    {
        return trim((string) preg_replace('/\s*[▾▼⌄˅]\s*$/u', '', $title));
    }

    /**
     * @return list<self>
     */
    public static function footerColumns(): array
    {
        return [self::FooterSolutions, self::FooterResources];
    }
}

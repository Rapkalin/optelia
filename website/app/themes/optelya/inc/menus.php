<?php

/**
 * Navigation: native WordPress menus (Appearance → Menus), seeded by
 * inc/seed.php so that a fresh install matches the mockup.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_register_menus(): void
{
    $locations = [];

    foreach (OptelyaMenuLocation::cases() as $location) {
        $locations[$location->value] = $location->label();
    }

    register_nav_menus($locations);
}
add_action('after_setup_theme', 'optelya_register_menus');

function optelya_menu_id(OptelyaMenuLocation $location): int
{
    return (int) (get_nav_menu_locations()[$location->value] ?? 0);
}

/**
 * Links of the menu assigned to a location, flat (the mockup has no submenu).
 * A link checked « Bientôt disponible » opens the coming-soon message.
 *
 * @return list<array{label: string, url: string, target: string, coming_soon: bool}>
 */
function optelya_menu_links(OptelyaMenuLocation $location): array
{
    $menuId = optelya_menu_id($location);
    $items = $menuId > 0 ? wp_get_nav_menu_items($menuId) : false;
    $links = [];

    foreach (is_array($items) ? $items : [] as $item) {
        $links[] = [
            'label' => (string) $item->title,
            'url' => (string) $item->url,
            'target' => (string) $item->target,
            'coming_soon' => (bool) optelya_field(OptelyaComingSoon::FIELD, (int) $item->ID),
        ];
    }

    return $links;
}

/**
 * Whether a link of the page opens the coming-soon message: the footer then
 * adds the message, written once per page.
 */
function optelya_coming_soon_used(bool $used = false): bool
{
    static $isUsed = false;
    $isUsed = $isUsed || $used;

    return $isUsed;
}

function optelya_menu_name(OptelyaMenuLocation $location): string
{
    $menuId = optelya_menu_id($location);
    $menu = $menuId > 0 ? wp_get_nav_menu_object($menuId) : false;

    return $menu instanceof WP_Term ? $menu->name : '';
}

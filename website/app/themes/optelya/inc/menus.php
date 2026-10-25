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
 *
 * @return list<array{label: string, url: string, target: string}>
 */
function optelya_menu_links(OptelyaMenuLocation $location): array
{
    $menuId = optelya_menu_id($location);
    $items = $menuId > 0 ? wp_get_nav_menu_items($menuId) : false;
    $links = [];

    foreach (is_array($items) ? $items : [] as $item) {
        $links[] = ['label' => (string) $item->title, 'url' => (string) $item->url, 'target' => (string) $item->target];
    }

    return $links;
}

function optelya_menu_name(OptelyaMenuLocation $location): string
{
    $menuId = optelya_menu_id($location);
    $menu = $menuId > 0 ? wp_get_nav_menu_object($menuId) : false;

    return $menu instanceof WP_Term ? $menu->name : '';
}

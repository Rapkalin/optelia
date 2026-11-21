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

/** Checkbox of a menu item: the link also opens the vendors and their solutions. */
const OPTELYA_CATALOG_SUBMENU_FIELD = 'catalog_submenu';

/**
 * Links of the menu assigned to a location, flat: the one submenu is the
 * catalogue's, filled from the published vendors and solutions
 * (optelya_catalog_submenu()). A link checked « Bientôt disponible » opens the
 * coming-soon message.
 *
 * @return list<array{label: string, url: string, target: string, coming_soon: bool, catalog_submenu: bool}>
 */
function optelya_menu_links(OptelyaMenuLocation $location): array
{
    $menuId = optelya_menu_id($location);
    $items = $menuId > 0 ? wp_get_nav_menu_items($menuId) : false;
    $links = [];

    foreach (is_array($items) ? $items : [] as $item) {
        $links[] = [
            'label' => OptelyaMenuLocation::linkLabel((string) $item->title),
            'url' => (string) $item->url,
            'target' => (string) $item->target,
            'coming_soon' => (bool) optelya_field(OptelyaComingSoon::FIELD, (int) $item->ID),
            'catalog_submenu' => (bool) optelya_field(OPTELYA_CATALOG_SUBMENU_FIELD, (int) $item->ID),
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

/**
 * The vendors, each with its published solutions, in catalogue order, and the
 * link to the whole list: the submenu of the header's « Solutions ».
 *
 * @return array{groups: list<array{vendor: array{label: string, url: string, target: string}, links: list<array{label: string, url: string, target: string}>}>, all: array{label: string, url: string, target: string}}
 */
function optelya_catalog_submenu(): array
{
    $link = static fn(array $entry, OptelyaPostType $type): array => ['label' => $entry['title'], 'url' => optelya_built_url($type, $entry['url']), 'target' => ''];
    $groups = [];

    foreach (optelya_catalog()['vendors'] as $vendor) {
        $solutions = optelya_solutions_of($vendor['id']);

        if ($solutions !== []) {
            $groups[] = [
                'vendor' => $link($vendor, OptelyaPostType::Vendor),
                'links' => array_map(static fn(array $solution): array => $link($solution, OptelyaPostType::Solution), $solutions),
            ];
        }
    }

    return ['groups' => $groups, 'all' => ['label' => __('Toutes les solutions', 'optelya'), 'url' => optelya_list_url(OptelyaPostType::Solution), 'target' => '']];
}

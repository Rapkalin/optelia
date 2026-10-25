<?php

/**
 * The links of a menu location, one after the other (the mockup has no
 * submenu: nested items are rendered flat).
 *
 * @param OptelyaMenuLocation $args['location']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$location = $args['location'] ?? null;

if (! $location instanceof OptelyaMenuLocation) {
    return;
}

foreach (optelya_menu_links($location) as $link) {
    get_template_part('components/link', null, ['link' => $link]);
}

<?php

/**
 * Template helpers.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Site version, read from the `composer.json` at the repository root.
 *
 * Single source: the version is already declared there for Composer, which is
 * why `style.css` has no `Version:` header.
 *
 * `composer.json` lives OUTSIDE the docroot, so Apache does not serve it; the
 * deployment artefact carries it explicitly, otherwise the version would be
 * empty in production.
 *
 * @return string The version, or an empty string when it cannot be found.
 */
function optelya_site_version(): string
{
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    $version = '';
    $manifest = dirname(ABSPATH, 2) . '/composer.json';

    if (! is_readable($manifest)) {
        return $version;
    }

    $decoded = json_decode((string) file_get_contents($manifest), true);

    if (is_array($decoded) && isset($decoded['version']) && is_string($decoded['version'])) {
        $version = trim($decoded['version']);
    }

    return $version;
}

/**
 * Template of the current flexible content row: the layout name IS the file
 * name in layouts/, so front-page.php needs no switch.
 *
 * The name comes from the database: it goes through an allow-list built on the
 * files actually present before reaching a template path.
 */
function optelya_layout_template(): string
{
    static $available = null;

    if ($available === null) {
        $available = array_map(
            static fn(string $path): string => basename($path, '.php'),
            (array) glob(get_template_directory() . '/layouts/*.php'),
        );
    }

    $layout = optelya_has_acf() ? (string) get_row_layout() : '';

    return in_array($layout, $available, true) ? $layout : '';
}

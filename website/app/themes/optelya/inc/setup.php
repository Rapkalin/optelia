<?php

/**
 * Theme supports and assets.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_setup(): void
{
    add_theme_support('title-tag');
    show_admin_bar(false);
}
add_action('after_setup_theme', 'optelya_setup');

/**
 * Version of a compiled asset, derived from its modification time.
 *
 * The .htaccess serves dist/ with a one-month `Expires` and webpack keeps fixed
 * file names: without this, a release would not reach returning visitors.
 */
function optelya_asset_version(string $relativePath): string
{
    $path = get_template_directory() . '/' . $relativePath;

    return is_file($path) ? (string) filemtime($path) : optelya_site_version();
}

function optelya_enqueue_assets(): void
{
    wp_enqueue_style(
        'optelya',
        get_template_directory_uri() . '/dist/main.css',
        [],
        optelya_asset_version('dist/main.css'),
    );
    wp_enqueue_script(
        'optelya',
        get_template_directory_uri() . '/dist/main.js',
        [],
        optelya_asset_version('dist/main.js'),
        [
            'strategy' => 'defer',
            'in_footer' => true,
        ],
    );

    // Core block styles: the theme uses no block.
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'optelya_enqueue_assets');

/**
 * theme.json styles. A dequeue cannot reach them: for a classic theme, core
 * prints them from wp_footer and moves them into the head.
 */
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

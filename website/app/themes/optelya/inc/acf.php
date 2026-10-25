<?php

/**
 * ACF integration.
 *
 * Field groups live as local JSON in the theme's `acf-json/`: ACF reads and
 * writes there on its own once the folder exists. They are versioned and
 * reviewable in a diff, while staying editable from ACF's screens in
 * development.
 *
 * Every call is guarded: ACF Pro is a licensed plugin, outside the repository,
 * and the theme must degrade (header and footer, no sections) without it —
 * LCDS went down with a silent 500 on 05/09/2026 for lack of a guard.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Field group screens only in development: elsewhere they are the one door
 * through which the configuration could drift from the repository. Entering
 * values is not affected.
 */
function optelya_show_acf_admin(): bool
{
    return in_array(wp_get_environment_type(), ['local', 'development'], true);
}
add_filter('acf/settings/show_admin', 'optelya_show_acf_admin');

/**
 * Strip the machine-specific keys ACF writes back into a saved group.
 *
 * `local_file` is an ABSOLUTE path (/var/www/html/… here): harmless at runtime,
 * but the JSON would change from one machine to another with no field moved.
 * Hooked AFTER the write: `acf/pre_save_json_file` does not run for field
 * groups (checked on LCDS).
 */
function optelya_strip_local_json_paths(array $fieldGroup): void
{
    if (! function_exists('acf_get_local_json_files')) {
        return;
    }

    $file = acf_get_local_json_files()[$fieldGroup['key'] ?? ''] ?? null;

    if (! is_string($file) || ! is_writable($file)) {
        return;
    }

    $json = json_decode((string) file_get_contents($file), true);

    if (! is_array($json) || (! isset($json['local']) && ! isset($json['local_file']))) {
        return;
    }

    unset($json['local'], $json['local_file']);

    file_put_contents(
        $file,
        wp_json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
    );
}
add_action('acf/update_field_group', 'optelya_strip_local_json_paths', 20);

function optelya_has_acf(): bool
{
    return function_exists('have_rows') && function_exists('the_row') && function_exists('get_row_layout');
}

/**
 * @param int|string|false $postId Post ID, 'option', or false for the current post.
 */
function optelya_field(string $selector, int|string|false $postId = false): mixed
{
    return function_exists('get_field') ? get_field($selector, $postId) : null;
}

/**
 * Sub-field of the current flexible content row or repeater row.
 */
function optelya_sub_field(string $selector): mixed
{
    return function_exists('get_sub_field') ? get_sub_field($selector) : null;
}

function optelya_text(mixed $value): string
{
    return is_scalar($value) ? trim((string) $value) : '';
}

function optelya_sub_text(string $selector): string
{
    return optelya_text(optelya_sub_field($selector));
}

/**
 * Rows of a repeater. ACF returns `false` for a repeater with no row, and
 * `(array) false` is `[false]`: a ghost row.
 */
function optelya_rows(mixed $value): array
{
    return array_values(array_filter(is_array($value) ? $value : [], 'is_array'));
}

/**
 * An ACF link field (return format "array"), reduced to what a template needs.
 *
 * @return array{label: string, url: string, target: string}
 */
function optelya_link(mixed $value): array
{
    $link = is_array($value) ? $value : [];

    return [
        'label' => optelya_text($link['title'] ?? ''),
        'url' => optelya_text($link['url'] ?? ''),
        'target' => optelya_text($link['target'] ?? ''),
    ];
}

/**
 * Attachment ID of an image or file field, whatever its return format.
 */
function optelya_attachment_id(mixed $value): int
{
    if (is_numeric($value)) {
        return (int) $value;
    }

    return is_array($value) && isset($value['ID']) ? (int) $value['ID'] : 0;
}

/**
 * Icon choices come from OptelyaIcon, not from the JSON: written there too,
 * they would live in two places.
 */
function optelya_load_issue_icons(array $field): array
{
    $field['choices'] = OptelyaIcon::issueChoices();

    return $field;
}
add_filter('acf/load_field/key=field_optelya_issues_card_icon', 'optelya_load_issue_icons');

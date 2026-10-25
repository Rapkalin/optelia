<?php

/**
 * Site settings: what the header, the footer and several sections share
 * (phone, address, tagline, header button). They belong to no page, so they
 * live on an ACF options page, versioned as local JSON like the rest.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_SETTINGS_SLUG = 'optelya-settings';

function optelya_register_options_page(): void
{
    if (! function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title' => __('Configuration du site', 'optelya'),
        'menu_title' => __('Configuration', 'optelya'),
        'menu_slug' => OPTELYA_SETTINGS_SLUG,
        'parent_slug' => 'options-general.php',
        'capability' => 'manage_options',
        'redirect' => false,
        'update_button' => __('Enregistrer', 'optelya'),
        'updated_message' => __('Configuration enregistrée.', 'optelya'),
    ]);
}
add_action('acf/init', 'optelya_register_options_page');

function optelya_option(OptelyaSetting $setting): mixed
{
    return optelya_field($setting->value, 'option');
}

function optelya_option_text(OptelyaSetting $setting): string
{
    return optelya_text(optelya_option($setting));
}

/**
 * `tel:` link of the phone setting: digits and a leading + only, whatever the
 * contributor typed ("01 23 45 67 89" → tel:0123456789).
 */
function optelya_phone_uri(): string
{
    $digits = (string) preg_replace('/[^\d+]/', '', optelya_option_text(OptelyaSetting::PhoneNumber));

    return $digits === '' ? '' : 'tel:' . $digits;
}

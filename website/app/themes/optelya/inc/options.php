<?php

/**
 * Site settings: what the header, the footer and several sections share
 * (address, tagline, header button). They belong to no page, so they
 * live on an ACF options page, versioned as local JSON like the rest.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_register_options_page(): void
{
    if (! function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title' => __('Configuration du site', 'optelya'),
        'menu_title' => __('Configuration', 'optelya'),
        'menu_slug' => OptelyaAdminScreen::SETTINGS_PAGE,
        'parent_slug' => 'options-general.php',
        'capability' => OptelyaRole::SETTINGS_CAPABILITY,
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

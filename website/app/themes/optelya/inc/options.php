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

/**
 * Refuses a header colour on which neither text colour reads (RGAA 3.2): the
 * text is picked automatically, but a mid tone leaves no good choice.
 */
function optelya_validate_header_background(mixed $valid, mixed $value): mixed
{
    if ($valid !== true || ! is_string($value) || $value === '') {
        return $valid;
    }

    $contrast = OptelyaHeaderColor::bestContrast(OptelyaHeaderColor::normalize($value));

    if ($contrast >= OptelyaHeaderColor::MIN_CONTRAST) {
        return $valid;
    }

    return sprintf(
        /* translators: 1: measured contrast, 2: required contrast */
        __('Teinte trop moyenne : ni un texte foncé ni un texte blanc n’y serait lisible (contraste %1$s:1, %2$s:1 requis). Choisissez une couleur plus claire ou plus foncée.', 'optelya'),
        number_format_i18n($contrast, 2),
        number_format_i18n(OptelyaHeaderColor::MIN_CONTRAST, 1),
    );
}
add_filter('acf/validate_value/name=' . OptelyaSetting::HeaderBackground->value, 'optelya_validate_header_background', 10, 2);

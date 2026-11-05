<?php

/**
 * Description read by search engines, from Settings → Configuration. No SEO
 * plugin on the site: this is the only meta it needs today.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

function optelya_meta_description(): string
{
    $description = optelya_option_text(OptelyaSetting::MetaDescription);

    return $description !== '' ? $description : optelya_option_text(OptelyaSetting::Tagline);
}

function optelya_print_meta_description(): void
{
    $description = optelya_meta_description();

    if ($description !== '') {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
    }
}
add_action('wp_head', 'optelya_print_meta_description', 1);

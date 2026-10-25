<?php

/**
 * Phone of the site settings, as a call link with its icon. Renders nothing
 * until both the displayed and the dialled numbers are set.
 *
 * @param string $args['class']
 * @param int    $args['size']   Icon size, in pixels.
 * @param string $args['suffix'] Text after the number.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$label = optelya_option_text(OptelyaSetting::PhoneLabel);
$uri = optelya_phone_uri();

if ($label === '' || $uri === '') {
    return;
}

$suffix = (string) ($args['suffix'] ?? '');
?>
<a class="<?php echo esc_attr((string) ($args['class'] ?? '')); ?>" href="<?php echo esc_url($uri); ?>">
    <?php get_template_part('components/icon', null, ['icon' => OptelyaIcon::Phone, 'size' => (int) ($args['size'] ?? 16)]); ?>
    <span><?php echo esc_html($suffix === '' ? $label : $label . ' — ' . $suffix); ?></span>
</a>

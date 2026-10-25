<?php

/**
 * Decorative inline icon.
 *
 * @param OptelyaIcon $args['icon']
 * @param int         $args['size'] Width and height, in pixels.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$icon = $args['icon'] ?? null;

if (! $icon instanceof OptelyaIcon) {
    return;
}

$size = (int) ($args['size'] ?? 24);
?>
<svg class="icon icon--<?php echo esc_attr($icon->value); ?>" aria-hidden="true" width="<?php echo esc_attr((string) $size); ?>" height="<?php echo esc_attr((string) $size); ?>" viewBox="<?php echo esc_attr($icon->viewBox()); ?>" <?php echo $icon->paint(); ?>><?php echo $icon->shapes(); ?></svg>

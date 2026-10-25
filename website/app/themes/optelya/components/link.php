<?php

/**
 * Link from an ACF link field or a menu item. Renders nothing when the label
 * is missing or the URL is rejected by esc_url() (e.g. `javascript:`).
 *
 * @param array{label: string, url: string, target: string} $args['link']
 * @param string                                              $args['class']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$link = $args['link'] ?? [];
$class = (string) ($args['class'] ?? '');
$url = esc_url((string) ($link['url'] ?? ''));

if (($link['label'] ?? '') === '' || $url === '') {
    return;
}
?>
<a<?php echo $class !== '' ? ' class="' . esc_attr($class) . '"' : ''; ?> href="<?php echo $url; ?>"<?php echo ($link['target'] ?? '') === '_blank' ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($link['label']); ?></a>

<?php

/**
 * Title and intro of a homepage section.
 *
 * @param string $args['title']
 * @param string $args['intro']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$intro = (string) ($args['intro'] ?? '');
?>
<div class="section-heading">
    <h2 class="section-title"><?php echo esc_html((string) ($args['title'] ?? '')); ?></h2>
    <?php if ($intro !== '') : ?>
        <p class="section-heading__intro"><?php echo esc_html($intro); ?></p>
    <?php endif; ?>
</div>

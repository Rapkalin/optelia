<?php

/**
 * What a page says when it has nothing of a kind yet, never a dead end: the
 * cards that follow it offer the closest content.
 *
 * @param string $args['title']
 * @param string $args['text']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$title = (string) ($args['title'] ?? '');
$text = (string) ($args['text'] ?? '');

if ($title === '' && $text === '') {
    return;
}
?>
<div class="card empty-state">
    <?php if ($title !== '') : ?>
        <h2 class="empty-state__title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>
    <?php if ($text !== '') : ?>
        <p class="empty-state__text"><?php echo esc_html($text); ?></p>
    <?php endif; ?>
</div>

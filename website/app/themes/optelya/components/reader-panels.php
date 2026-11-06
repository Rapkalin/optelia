<?php

/**
 * What Optelya brings, one panel per reader (engineer, technical director):
 * on the homepage and on every solution.
 *
 * @param list<array{reader: string, is_dark: bool, items: list<array{title: string, text: string}>}> $args['panels']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

if ((array) ($args['panels'] ?? []) === []) {
    return;
}
?>
<div class="benefits">
    <?php foreach ((array) ($args['panels'] ?? []) as $panel) : ?>
        <div class="benefits__panel<?php echo ! empty($panel['is_dark']) ? ' benefits__panel--dark' : ''; ?>">
            <h3 class="benefits__reader"><?php echo esc_html((string) ($panel['reader'] ?? '')); ?></h3>
            <ul class="benefits__list">
                <?php foreach ((array) ($panel['items'] ?? []) as $item) : ?>
                    <li class="benefits__item">
                        <span class="benefits__title"><?php echo esc_html((string) ($item['title'] ?? '')); ?></span>
                        <span class="benefits__text"><?php echo esc_html((string) ($item['text'] ?? '')); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

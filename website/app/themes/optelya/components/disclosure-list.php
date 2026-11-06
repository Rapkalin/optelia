<?php

/**
 * Collapsed details, native <details>: keyboard and screen reader work
 * without script. Technical details of a solution, of a study to come.
 *
 * @param list<array{title: string, text: string}> $args['items']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$items = (array) ($args['items'] ?? []);

if ($items === []) {
    return;
}
?>
<ul class="disclosure">
    <?php foreach ($items as $item) : ?>
        <li class="disclosure__item">
            <details>
                <summary class="disclosure__summary"><?php echo esc_html((string) $item['title']); ?></summary>
                <p class="disclosure__content"><?php echo nl2br(esc_html((string) $item['text'])); ?></p>
            </details>
        </li>
    <?php endforeach; ?>
</ul>

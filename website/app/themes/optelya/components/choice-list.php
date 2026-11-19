<?php

/**
 * Which tool for which need: a few products side by side, the current one
 * marked. Solution pages, vendor pages to come.
 *
 * @param list<array{name: string, vendor: string, usage: string, url: string, current: bool}> $args['items']
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
<ul class="choice-list">
    <?php foreach ($items as $item) : ?>
        <li class="choice-list__item<?php echo ! empty($item['current']) ? ' choice-list__item--current' : ''; ?>">
            <div class="choice-list__name">
                <?php get_template_part('components/vendor-chips', null, ['vendors' => [(string) $item['vendor']]]); ?>
                <h3 class="choice-list__product"><?php echo esc_html((string) $item['name']); ?></h3>
            </div>
            <p class="choice-list__text"><?php echo esc_html((string) $item['usage']); ?></p>
            <?php if (! empty($item['current'])) : ?>
                <span class="choice-list__here" aria-current="page"><?php esc_html_e('Vous êtes sur cette page', 'optelya'); ?></span>
            <?php else : ?>
                <?php get_template_part('components/link', null, [
                    /* translators: %s: product name */
                    'link' => ['label' => sprintf(__('Voir %s', 'optelya'), (string) $item['name']), 'url' => (string) $item['url'], 'target' => ''],
                    'class' => 'card__link',
                    'context' => (string) $item['usage'],
                ]); ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

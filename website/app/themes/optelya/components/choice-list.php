<?php

/**
 * Which tool for which need: products side by side, the current one marked.
 * Solution and vendor pages. A row with a page is a link as a whole. The
 * vendor labels show only when the products come from several vendors (on a
 * vendor's own page they would repeat it).
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

$showVendors = count(array_unique(array_map(static fn(array $item): string => (string) $item['vendor'], $items))) > 1;
?>
<div class="choice-list">
    <div class="choice-list__head" aria-hidden="true">
        <span><?php esc_html_e('Solution', 'optelya'); ?></span>
        <span><?php esc_html_e('Pour quel besoin', 'optelya'); ?></span>
    </div>
    <ul class="choice-list__items">
        <?php foreach ($items as $item) : ?>
            <?php
            $name = (string) $item['name'];
            $url = esc_url((string) $item['url']);
            $isLink = empty($item['current']) && $url !== '';
            $tag = $isLink ? 'a' : 'div';
            ?>
            <li>
                <<?php echo $tag; ?> class="choice-list__item<?php echo ! empty($item['current']) ? ' choice-list__item--current' : ''; ?>"<?php echo $isLink ? ' href="' . $url . '"' : ''; ?><?php echo ! empty($item['current']) ? ' aria-current="page"' : ''; ?>>
                    <span class="choice-list__name">
                        <?php if ($showVendors) : ?>
                            <?php get_template_part('components/vendor-chips', null, ['vendors' => [(string) $item['vendor']]]); ?>
                        <?php endif; ?>
                        <span class="choice-list__product"><?php echo esc_html($name); ?></span>
                    </span>
                    <span class="choice-list__text"><?php echo esc_html((string) $item['usage']); ?></span>
                    <?php if ($isLink) : ?>
                        <?php /* translators: %s: product name */ ?>
                        <span class="card__link"><?php echo esc_html(sprintf(__('Voir %s', 'optelya'), $name)); ?></span>
                    <?php else : ?>
                        <span class="choice-list__here"><?php esc_html_e('Vous êtes sur cette page', 'optelya'); ?></span>
                    <?php endif; ?>
                </<?php echo $tag; ?>>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

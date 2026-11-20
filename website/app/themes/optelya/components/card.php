<?php

/**
 * The one card of the site. Every slot is optional; a card with a link is the
 * link, its label shown as the card link (orange, underlined, arrow).
 *
 * @param array{
 *     vendors?: list<string>,
 *     icon?: ?OptelyaIcon,
 *     eyebrow?: string,
 *     title?: string,
 *     figure?: string,
 *     figure_label?: string,
 *     text?: string,
 *     products?: string,
 *     proof?: array{figure: string, label: string},
 *     source?: string,
 *     link?: array{label: string, url: string, target: string},
 *     filters?: list<string>
 * } $args['card']   `filters`: values a filter of the page shows the card for.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$card = (array) ($args['card'] ?? []);
$text = static fn(string $key): string => (string) ($card[$key] ?? '');
$link = (array) ($card['link'] ?? []);
$url = esc_url((string) ($link['url'] ?? ''));
// The arrow is drawn by the style: one typed in the label would show twice.
$linkLabel = $url === '' ? '' : (string) preg_replace('/\s*→\s*$/u', '', (string) ($link['label'] ?? ''));
$tag = $url === '' ? 'article' : 'a';
$icon = $card['icon'] ?? null;
$proof = (array) ($card['proof'] ?? []);
$proofFigure = (string) ($proof['figure'] ?? '');
$filters = implode(' ', array_map('strval', (array) ($card['filters'] ?? [])));
$hasFooter = $text('products') !== '' || $proofFigure !== '' || $text('source') !== '' || $linkLabel !== '';
?>
<<?php echo $tag; ?> class="card"<?php echo $url === '' ? '' : ' href="' . $url . '"' . (($link['target'] ?? '') === '_blank' ? ' target="_blank" rel="noopener"' : ''); ?><?php echo $filters === '' ? '' : ' data-filters="' . esc_attr($filters) . '"'; ?>>
    <div class="card__body">
    <?php get_template_part('components/vendor-chips', null, ['vendors' => $card['vendors'] ?? []]); ?>
    <?php if ($icon instanceof OptelyaIcon) : ?>
        <?php get_template_part('components/icon', null, ['icon' => $icon, 'size' => 40]); ?>
    <?php endif; ?>
    <?php if ($text('eyebrow') !== '') : ?>
        <p class="card__eyebrow"><?php echo esc_html($text('eyebrow')); ?></p>
    <?php endif; ?>
    <?php if ($text('title') !== '') : ?>
        <h3 class="card__title"><?php echo esc_html($text('title')); ?></h3>
    <?php endif; ?>
    <?php if ($text('figure') !== '') : ?>
        <p class="card__figure"><?php echo esc_html($text('figure')); ?><span class="card__figure-label"><?php echo esc_html($text('figure_label')); ?></span></p>
    <?php endif; ?>
    <?php if ($text('text') !== '') : ?>
        <p class="card__text"><?php echo esc_html($text('text')); ?></p>
    <?php endif; ?>
    </div>
    <?php if ($hasFooter) : ?>
        <div class="card__footer">
            <?php if ($text('products') !== '') : ?>
                <p class="card__products"><?php echo esc_html($text('products')); ?></p>
            <?php endif; ?>
            <?php if ($proofFigure !== '') : ?>
                <p class="card__proof"><strong><?php echo esc_html($proofFigure); ?></strong> <?php echo esc_html((string) ($proof['label'] ?? '')); ?></p>
            <?php endif; ?>
            <?php if ($text('source') !== '') : ?>
                <p class="card__source"><?php echo esc_html($text('source')); ?></p>
            <?php endif; ?>
            <?php if ($linkLabel !== '') : ?>
                <span class="card__link"><?php echo esc_html($linkLabel); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</<?php echo $tag; ?>>

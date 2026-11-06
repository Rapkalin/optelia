<?php

/**
 * Grid of cards with a title and a short text: issues on the homepage,
 * benefits and applications of a solution. A card with a link is the link.
 *
 * @param list<array{title: string, text: string, products?: string, link?: array, icon?: ?OptelyaIcon}> $args['cards']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$cards = (array) ($args['cards'] ?? []);

if ($cards === []) {
    return;
}
?>
<div class="card-grid">
    <?php foreach ($cards as $card) : ?>
        <?php
        $link = (array) ($card['link'] ?? []);
        $url = esc_url((string) ($link['url'] ?? ''));
        $tag = $url === '' ? 'div' : 'a';
        $icon = $card['icon'] ?? null;
        $products = (string) ($card['products'] ?? '');
        ?>
        <<?php echo $tag; ?> class="card issue-card"<?php echo $url === '' ? '' : ' href="' . $url . '"'; ?>>
            <?php if ($icon instanceof OptelyaIcon) : ?>
                <?php get_template_part('components/icon', null, ['icon' => $icon, 'size' => 40]); ?>
            <?php endif; ?>
            <h3 class="issue-card__title"><?php echo esc_html((string) ($card['title'] ?? '')); ?></h3>
            <span class="issue-card__text"><?php echo esc_html((string) ($card['text'] ?? '')); ?></span>
            <?php if ($products !== '') : ?>
                <span class="issue-card__products"><?php echo esc_html($products); ?></span>
            <?php endif; ?>
            <?php if ($url !== '' && ($link['label'] ?? '') !== '') : ?>
                <span class="issue-card__more"><?php echo esc_html((string) $link['label']); ?></span>
            <?php endif; ?>
        </<?php echo $tag; ?>>
    <?php endforeach; ?>
</div>

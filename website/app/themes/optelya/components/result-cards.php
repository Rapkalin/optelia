<?php

/**
 * Grid of figures from the published studies: results on the homepage, proofs
 * of a solution or an industry.
 *
 * @param list<array{figure: string, label: string, products: string, source: string, link: array}> $args['cards']
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
        $figure = (string) ($card['figure'] ?? '');
        $label = (string) ($card['label'] ?? '');
        ?>
        <article class="card result-card">
            <p class="result-card__figure"><?php echo esc_html($figure); ?></p>
            <p class="result-card__label"><?php echo esc_html($label); ?></p>
            <p class="result-card__products"><?php echo esc_html((string) ($card['products'] ?? '')); ?></p>
            <p class="result-card__source"><?php echo esc_html((string) ($card['source'] ?? '')); ?></p>
            <?php get_template_part('components/link', null, ['link' => $card['link'] ?? [], 'class' => 'result-card__more', 'context' => trim($figure . ' ' . $label)]); ?>
        </article>
    <?php endforeach; ?>
</div>

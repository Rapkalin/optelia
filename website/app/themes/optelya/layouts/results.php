<?php

/**
 * Figures from the published studies.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<section id="<?php echo esc_attr(OptelyaSection::Results->value); ?>" class="section section--results">
    <div class="section__inner">
        <div class="section__header">
            <?php get_template_part('components/section-heading', null, [
                'title' => optelya_sub_text('title'),
                'intro' => optelya_sub_text('intro'),
            ]); ?>
            <?php get_template_part('components/link', null, ['link' => optelya_link(optelya_sub_field('all_link')), 'class' => 'text-link']); ?>
        </div>

        <?php get_template_part('components/result-cards', null, [
            'cards' => array_map(static fn(array $card): array => [
                'figure' => optelya_text($card['figure'] ?? ''),
                'label' => optelya_text($card['label'] ?? ''),
                'products' => optelya_text($card['products'] ?? ''),
                'source' => optelya_text($card['source'] ?? ''),
                'link' => optelya_link($card['link'] ?? null),
            ], optelya_rows(optelya_sub_field('cards'))),
        ]); ?>
    </div>
</section>

<?php

/**
 * Entry by issue, then by sector.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

?>
<section id="<?php echo esc_attr(OptelyaSection::Issues->value); ?>" class="section section--issues">
    <div class="section__inner">
        <?php get_template_part('components/section-heading', null, [
            'title' => optelya_sub_text('title'),
            'intro' => optelya_sub_text('intro'),
        ]); ?>

        <?php get_template_part('components/feature-cards', null, [
            'cards' => array_map(static fn(array $card): array => [
                'title' => optelya_text($card['title'] ?? ''),
                'text' => optelya_text($card['text'] ?? ''),
                'products' => optelya_text($card['products'] ?? ''),
                'link' => optelya_link($card['link'] ?? null),
                'icon' => OptelyaIcon::tryFrom(optelya_text($card['icon'] ?? '')),
            ], optelya_rows(optelya_sub_field('cards'))),
        ]); ?>

        <?php get_template_part('components/sectors-band', null, [
            'id' => OptelyaSection::Sectors->value,
            'help_text' => optelya_sub_text('help_text'),
            'help_link' => optelya_link(optelya_sub_field('help_link')),
            'label' => optelya_sub_text('sectors_label'),
            'links' => array_map(static fn(array $sector): array => optelya_link($sector['link'] ?? null), optelya_rows(optelya_sub_field('sectors'))),
        ]); ?>
    </div>
</section>

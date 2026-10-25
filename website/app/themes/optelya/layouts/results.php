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

        <div class="card-grid">
            <?php foreach (optelya_rows(optelya_sub_field('cards')) as $card) : ?>
                <article class="card result-card">
                    <p class="result-card__figure"><?php echo esc_html(optelya_text($card['figure'] ?? '')); ?></p>
                    <p class="result-card__label"><?php echo esc_html(optelya_text($card['label'] ?? '')); ?></p>
                    <p class="result-card__products"><?php echo esc_html(optelya_text($card['products'] ?? '')); ?></p>
                    <p class="result-card__source"><?php echo esc_html(optelya_text($card['source'] ?? '')); ?></p>
                    <?php get_template_part('components/link', null, ['link' => optelya_link($card['link'] ?? null), 'class' => 'result-card__more']); ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php

/**
 * What Optelya brings, one panel per reader.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<section id="<?php echo esc_attr(OptelyaSection::Benefits->value); ?>" class="section section--benefits">
    <div class="section__inner">
        <?php get_template_part('components/section-heading', null, ['title' => optelya_sub_text('title')]); ?>

        <?php get_template_part('components/reader-panels', null, [
            'panels' => array_map(static fn(array $panel): array => [
                'reader' => optelya_text($panel['reader'] ?? ''),
                'is_dark' => ! empty($panel['is_dark']),
                'items' => array_map(static fn(array $item): array => [
                    'title' => optelya_text($item['title'] ?? ''),
                    'text' => optelya_text($item['text'] ?? ''),
                ], optelya_rows($panel['items'] ?? null)),
            ], optelya_rows(optelya_sub_field('panels'))),
        ]); ?>
    </div>
</section>

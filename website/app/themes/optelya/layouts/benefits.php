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
        <h2 class="section-title"><?php echo esc_html(optelya_sub_text('title')); ?></h2>

        <div class="benefits">
            <?php foreach (optelya_rows(optelya_sub_field('panels')) as $panel) : ?>
                <div class="benefits__panel<?php echo ! empty($panel['is_dark']) ? ' benefits__panel--dark' : ''; ?>">
                    <p class="benefits__reader"><?php echo esc_html(optelya_text($panel['reader'] ?? '')); ?></p>
                    <ul class="benefits__list">
                        <?php foreach (optelya_rows($panel['items'] ?? null) as $item) : ?>
                            <li class="benefits__item">
                                <span class="benefits__title"><?php echo esc_html(optelya_text($item['title'] ?? '')); ?></span>
                                <span class="benefits__text"><?php echo esc_html(optelya_text($item['text'] ?? '')); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

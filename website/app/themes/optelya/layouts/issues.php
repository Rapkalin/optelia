<?php

/**
 * Entry by issue, then by sector.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$helpText = optelya_sub_text('help_text');
$helpLink = optelya_link(optelya_sub_field('help_link'));
$sectorsLabel = optelya_sub_text('sectors_label');
$sectors = optelya_rows(optelya_sub_field('sectors'));
$hasSectorBand = $helpText !== '' || $helpLink['label'] !== '' || $sectorsLabel !== '' || $sectors !== [];
?>
<section id="<?php echo esc_attr(OptelyaSection::Issues->value); ?>" class="section section--issues">
    <div class="section__inner">
        <?php get_template_part('components/section-heading', null, [
            'title' => optelya_sub_text('title'),
            'intro' => optelya_sub_text('intro'),
        ]); ?>

        <div class="card-grid">
            <?php foreach (optelya_rows(optelya_sub_field('cards')) as $card) : ?>
                <?php $link = optelya_link($card['link'] ?? null); ?>
                <?php $url = esc_url($link['url']); ?>
                <?php $tag = $url === '' ? 'div' : 'a'; ?>
                <?php $icon = OptelyaIcon::tryFrom(optelya_text($card['icon'] ?? '')); ?>
                <<?php echo $tag; ?> class="card issue-card"<?php echo $url === '' ? '' : ' href="' . $url . '"'; ?>>
                    <?php if ($icon !== null) : ?>
                        <?php get_template_part('components/icon', null, ['icon' => $icon, 'size' => 40]); ?>
                    <?php endif; ?>
                    <span class="issue-card__title"><?php echo esc_html(optelya_text($card['title'] ?? '')); ?></span>
                    <span class="issue-card__text"><?php echo esc_html(optelya_text($card['text'] ?? '')); ?></span>
                    <span class="issue-card__products"><?php echo esc_html(optelya_text($card['products'] ?? '')); ?></span>
                    <?php if ($url !== '' && $link['label'] !== '') : ?>
                        <span class="issue-card__more"><?php echo esc_html($link['label']); ?></span>
                    <?php endif; ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>

        <?php if ($hasSectorBand) : ?>
            <div id="<?php echo esc_attr(OptelyaSection::Sectors->value); ?>" class="sectors">
                <?php if ($helpText !== '' || $helpLink['label'] !== '') : ?>
                    <span class="sectors__help"><?php echo esc_html($helpText); ?> <?php get_template_part('components/link', null, ['link' => $helpLink]); ?></span>
                <?php endif; ?>
                <?php if ($sectorsLabel !== '') : ?>
                    <span class="sectors__label"><?php echo esc_html($sectorsLabel); ?></span>
                <?php endif; ?>
                <?php if ($sectors !== []) : ?>
                    <nav class="sectors__list" aria-label="<?php esc_attr_e('Secteurs', 'optelya'); ?>">
                        <?php foreach ($sectors as $sector) : ?>
                            <?php get_template_part('components/link', null, ['link' => optelya_link($sector['link'] ?? null), 'class' => 'pill']); ?>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

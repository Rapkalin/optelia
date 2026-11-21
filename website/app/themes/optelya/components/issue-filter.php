<?php

/**
 * Solutions list filtered by issue: one block shown at a time — its heading,
 * its cards, its comparison link — chosen with pill buttons
 * (assets/scripts/issue-filter.js, which also keeps the choice in the address,
 * ?enjeu=…). Without script, every card shows under the « all » heading.
 *
 * @param string $args['label']
 * @param list<array{slug: string, name: string, heading: array{title: string, intro: string}, compare?: array{label: string, url: string, target: string}}> $args['blocks']
 *        The first is « all ».
 * @param list<array<string, mixed>> $args['cards'] Cards (components/card.php) whose `filters` name their blocks.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$blocks = (array) ($args['blocks'] ?? []);
$cards = (array) ($args['cards'] ?? []);

if ($blocks === [] || $cards === []) {
    return;
}

$labelId = 'issue-filter-label';
?>
<div class="issue-filter" data-issue-filter data-parameter="<?php echo esc_attr(OptelyaCatalog::ISSUE_PARAMETER); ?>" data-count-one="<?php /* translators: %d: number of solutions */ echo esc_attr(__('%d solution', 'optelya')); ?>" data-count-many="<?php /* translators: %d: number of solutions */ echo esc_attr(__('%d solutions', 'optelya')); ?>">
    <div class="issue-filter__bar" role="group" aria-labelledby="<?php echo esc_attr($labelId); ?>" hidden>
        <span id="<?php echo esc_attr($labelId); ?>" class="issue-filter__label"><?php echo esc_html((string) ($args['label'] ?? '')); ?></span>
        <div class="issue-filter__options">
            <?php foreach ($blocks as $index => $block) : ?>
                <button type="button" class="pill" data-filter="<?php echo esc_attr((string) $block['slug']); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"><?php echo esc_html((string) $block['name']); ?></button>
            <?php endforeach; ?>
        </div>
    </div>
    <p class="issue-filter__count" aria-live="polite" data-issue-count><?php echo esc_html(OptelyaCatalog::solutionsCount(count($cards))); ?></p>
    <?php foreach ($blocks as $index => $block) : ?>
        <div data-filters="<?php echo esc_attr((string) $block['slug']); ?>"<?php echo $index === 0 ? '' : ' hidden'; ?>>
            <?php get_template_part('components/section-heading', null, $block['heading']); ?>
        </div>
    <?php endforeach; ?>
    <?php get_template_part('components/card-grid', null, ['cards' => $cards, 'keep_width' => true]); ?>
    <?php foreach ($blocks as $block) : ?>
        <?php if (($block['compare']['label'] ?? '') !== '' && ($block['compare']['url'] ?? '') !== '') : ?>
            <div data-filters="<?php echo esc_attr((string) $block['slug']); ?>" hidden>
                <?php get_template_part('components/link', null, ['link' => $block['compare'], 'class' => 'text-link']); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

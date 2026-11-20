<?php

/**
 * Breadcrumb of every page but the homepage. The whole path on a computer,
 * the parent link only on a small screen (assets/styles/components/_breadcrumb.scss).
 *
 * @param list<array{label: string, url: string}> $args['items'] From the homepage to the current page (the last one).
 *                                                               A step with url '' has no page yet: shown, not linked.
 * @param bool                                    $args['on_ink'] On a dark background (top of the hero).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$items = array_values((array) ($args['items'] ?? []));
$current = count($items) - 1;
// On a small screen only the nearest step that has a page is shown.
$parent = -1;

foreach ($items as $index => $item) {
    if ($index < $current && ($item['url'] ?? '') !== '') {
        $parent = $index;
    }
}

if ($current < 1) {
    return;
}
?>
<nav class="breadcrumb<?php echo ! empty($args['on_ink']) ? ' breadcrumb--on-ink' : ''; ?>" aria-label="<?php esc_attr_e('Fil d’Ariane', 'optelya'); ?>">
    <ol class="breadcrumb__list">
        <?php foreach ($items as $index => $item) : ?>
            <li class="breadcrumb__item<?php echo $index === $parent ? ' breadcrumb__item--parent' : ''; ?>">
                <?php if ($index === $current) : ?>
                    <span class="breadcrumb__current" aria-current="page"><?php echo esc_html((string) $item['label']); ?></span>
                <?php elseif (($item['url'] ?? '') === '') : ?>
                    <span class="breadcrumb__text"><?php echo esc_html((string) $item['label']); ?></span>
                <?php else : ?>
                    <a class="breadcrumb__link" href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

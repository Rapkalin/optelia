<?php

/**
 * Breadcrumb of every page but the homepage. The whole path on a computer,
 * the parent link only on a small screen (assets/styles/components/_breadcrumb.scss).
 *
 * @param list<array{label: string, url: string}> $args['items'] From the homepage to the current page (url '').
 * @param bool                                    $args['on_ink'] On a dark background (top of the hero).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$items = array_values((array) ($args['items'] ?? []));
$parent = count($items) - 2;

if ($parent < 0) {
    return;
}
?>
<nav class="breadcrumb<?php echo ! empty($args['on_ink']) ? ' breadcrumb--on-ink' : ''; ?>" aria-label="<?php esc_attr_e('Fil d’Ariane', 'optelya'); ?>">
    <ol class="breadcrumb__list">
        <?php foreach ($items as $index => $item) : ?>
            <li class="breadcrumb__item<?php echo $index === $parent ? ' breadcrumb__item--parent' : ''; ?>">
                <?php if (($item['url'] ?? '') === '') : ?>
                    <span class="breadcrumb__current" aria-current="page"><?php echo esc_html((string) $item['label']); ?></span>
                <?php else : ?>
                    <a class="breadcrumb__link" href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

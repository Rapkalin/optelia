<?php

/**
 * The links of a menu location as a list (RGAA 9.3), flat: nested items are
 * rendered one after the other. In the header, a link checked « Sous-menu des
 * solutions » also opens the catalogue's submenu (components/catalog-submenu.php).
 *
 * @param OptelyaMenuLocation $args['location']
 * @param string              $args['class']    Layout of the list.
 * @param bool                $args['submenus'] Whether the catalogue's submenu is shown (the header's menu only).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$location = $args['location'] ?? null;

if (! $location instanceof OptelyaMenuLocation) {
    return;
}

$links = optelya_menu_links($location);

if ($links === []) {
    return;
}
?>
<ul class="link-list <?php echo esc_attr((string) ($args['class'] ?? '')); ?>">
    <?php foreach ($links as $index => $link) : ?>
        <li>
            <?php if ($link['catalog_submenu'] && ! empty($args['submenus'])) : ?>
                <?php get_template_part('components/catalog-submenu', null, ['link' => $link, 'id' => 'submenu-' . $index]); ?>
            <?php elseif ($link['coming_soon']) : ?>
                <?php get_template_part('components/coming-soon-link', null, ['link' => $link]); ?>
            <?php else : ?>
                <?php get_template_part('components/link', null, ['link' => $link]); ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

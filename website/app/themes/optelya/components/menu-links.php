<?php

/**
 * The links of a menu location as a list (RGAA 9.3), flat: the mockup has no
 * submenu, nested items are rendered one after the other.
 *
 * @param OptelyaMenuLocation $args['location']
 * @param string              $args['class']    Layout of the list.
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
    <?php foreach ($links as $link) : ?>
        <li>
            <?php if ($link['coming_soon']) : ?>
                <?php get_template_part('components/coming-soon-link', null, ['link' => $link]); ?>
            <?php else : ?>
                <?php get_template_part('components/link', null, ['link' => $link]); ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

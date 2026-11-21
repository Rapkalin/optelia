<?php

/**
 * A header link with the catalogue's submenu: the link keeps leading to its
 * page, a button beside it opens the vendors and their solutions
 * (assets/scripts/submenu.js). Without scripts, the button stays hidden and
 * the link leads to the list, which shows them all.
 *
 * @param array{label: string, url: string, target: string} $args['link']
 * @param string                                             $args['id'] `id` of the panel.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$link = (array) ($args['link'] ?? []);
$panelId = (string) ($args['id'] ?? 'submenu');
$submenu = optelya_catalog_submenu();
?>
<div class="submenu" data-submenu>
    <div class="submenu__trigger">
        <?php get_template_part('components/link', null, ['link' => $link]); ?>
        <?php if ($submenu['groups'] !== []) : ?>
            <button class="submenu__toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($panelId); ?>" hidden>
                <?php /* translators: %s: menu link, e.g. Solutions */ ?>
                <span class="visually-hidden"><?php echo esc_html(sprintf(__('Sous-menu %s', 'optelya'), (string) ($link['label'] ?? ''))); ?></span>
            </button>
        <?php endif; ?>
    </div>
    <?php if ($submenu['groups'] !== []) : ?>
        <div id="<?php echo esc_attr($panelId); ?>" class="submenu__panel" hidden>
            <ul class="submenu__groups">
                <?php foreach ($submenu['groups'] as $group) : ?>
                    <li class="submenu__group">
                        <?php if ($group['vendor']['url'] !== '') : ?>
                            <?php get_template_part('components/link', null, ['link' => $group['vendor'], 'class' => 'submenu__vendor']); ?>
                        <?php else : ?>
                            <span class="submenu__vendor"><?php echo esc_html($group['vendor']['label']); ?></span>
                        <?php endif; ?>
                        <ul class="submenu__links">
                            <?php foreach ($group['links'] as $solution) : ?>
                                <li><?php get_template_part('components/link', null, ['link' => $solution]); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php get_template_part('components/link', null, ['link' => $submenu['all'], 'class' => 'text-link submenu__all']); ?>
        </div>
    <?php endif; ?>
</div>

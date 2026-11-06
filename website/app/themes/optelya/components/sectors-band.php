<?php

/**
 * Band of sector pills, with an optional help line: on the homepage under the
 * issues, on a solution without study for its related industries.
 *
 * @param string      $args['id']        Anchor ('' for none).
 * @param string      $args['help_text']
 * @param array       $args['help_link'] Link (see components/link.php).
 * @param string      $args['label']
 * @param list<array> $args['links']     Links of the pills.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$anchor = (string) ($args['id'] ?? '');
$helpText = (string) ($args['help_text'] ?? '');
$helpLink = (array) ($args['help_link'] ?? []);
$label = (string) ($args['label'] ?? '');
$links = (array) ($args['links'] ?? []);

if ($helpText === '' && ($helpLink['label'] ?? '') === '' && $label === '' && $links === []) {
    return;
}
?>
<div<?php echo $anchor !== '' ? ' id="' . esc_attr($anchor) . '"' : ''; ?> class="sectors">
    <?php if ($helpText !== '' || ($helpLink['label'] ?? '') !== '') : ?>
        <span class="sectors__help"><?php echo esc_html($helpText); ?> <?php get_template_part('components/link', null, ['link' => $helpLink]); ?></span>
    <?php endif; ?>
    <?php if ($label !== '') : ?>
        <span class="sectors__label"><?php echo esc_html($label); ?></span>
    <?php endif; ?>
    <?php if ($links !== []) : ?>
        <ul class="link-list sectors__list">
            <?php foreach ($links as $link) : ?>
                <li><?php get_template_part('components/link', null, ['link' => $link, 'class' => 'pill']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php

/**
 * A menu link to a section not built yet: « Bientôt » next to its label, and
 * the coming-soon message on click (assets/scripts/coming-soon.js). Without
 * scripts, it leads to the contact form: never a dead end.
 *
 * @param array{label: string, url: string, target: string, coming_soon: bool} $args['link']
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$label = (string) ($args['link']['label'] ?? '');
$contact = optelya_contact_link();
$url = esc_url($contact['url'] !== '' ? $contact['url'] : home_url('/#' . OptelyaSection::Contact->value));

if ($label === '' || $url === '') {
    return;
}

optelya_coming_soon_used(true);
?>
<a class="coming-soon-link" href="<?php echo $url; ?>" aria-haspopup="dialog" aria-controls="<?php echo esc_attr(OptelyaComingSoon::DIALOG_ID); ?>" data-coming-soon><?php echo esc_html($label); ?> <span class="coming-soon-link__badge"><?php esc_html_e('Bientôt', 'optelya'); ?></span></a>

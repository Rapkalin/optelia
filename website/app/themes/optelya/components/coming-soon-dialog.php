<?php

/**
 * The message opened by the menu links checked « Bientôt disponible »
 * (components/coming-soon-link.php). A native <dialog>: the rest of the page
 * is inert while it is open, Escape closes it, focus comes back to the link.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$texts = OptelyaComingSoon::texts(optelya_option_text(OptelyaSetting::ComingSoonTitle), optelya_option_text(OptelyaSetting::ComingSoonText));
$contact = optelya_contact_link();
$titleId = OptelyaComingSoon::DIALOG_ID . '-title';
?>
<dialog id="<?php echo esc_attr(OptelyaComingSoon::DIALOG_ID); ?>" class="coming-soon" aria-labelledby="<?php echo esc_attr($titleId); ?>" data-coming-soon-dialog>
    <?php /* The box: a click on the dialog itself is a click on the backdrop. */ ?>
    <div class="coming-soon__box">
        <h2 id="<?php echo esc_attr($titleId); ?>" class="coming-soon__title"><?php echo esc_html($texts['title']); ?></h2>
        <p class="coming-soon__text"><?php echo esc_html($texts['text']); ?></p>
        <div class="coming-soon__actions">
            <?php get_template_part('components/link', null, ['link' => $contact, 'class' => 'button button--small']); ?>
            <button class="coming-soon__close" type="button" data-coming-soon-close><?php esc_html_e('Fermer', 'optelya'); ?></button>
        </div>
    </div>
</dialog>

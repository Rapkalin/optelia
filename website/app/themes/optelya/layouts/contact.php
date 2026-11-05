<?php

/**
 * Contact block: the form, handled by inc/contact.php. It works without
 * JavaScript; after a failure the page comes back with what was typed.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$lead = optelya_sub_text('lead');
$steps = optelya_rows(optelya_sub_field('steps'));
$notice = optelya_sub_text('notice');
$state = optelya_contact_state();
$outcome = $state['outcome'];
?>
<section id="<?php echo esc_attr(OptelyaSection::Contact->value); ?>" class="section section--contact">
    <div class="contact">
        <div class="contact__intro">
            <h2 class="section-title"><?php echo esc_html(optelya_sub_text('title')); ?></h2>
            <?php if ($lead !== '') : ?>
                <p class="contact__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <?php if ($steps !== []) : ?>
                <ol class="contact__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                        <li class="contact__step">
                            <span class="contact__step-number"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            <span><?php echo esc_html(optelya_text($step['text'] ?? '')); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>

        <form class="card contact-form" method="post" action="#<?php echo esc_attr(OptelyaContactForm::STATUS_ANCHOR); ?>" aria-label="<?php esc_attr_e('Demande de contact', 'optelya'); ?>">
            <?php if ($outcome !== null) : ?>
                <p class="contact-form__status contact-form__status--<?php echo $outcome->isSuccess() ? 'success' : 'error'; ?>" role="<?php echo $outcome->isSuccess() ? 'status' : 'alert'; ?>" id="<?php echo esc_attr(OptelyaContactForm::STATUS_ANCHOR); ?>" tabindex="-1">
                    <?php echo esc_html($outcome->message()); ?>
                </p>
            <?php endif; ?>

            <?php foreach (OptelyaContactField::cases() as $field) : ?>
                <?php
                $id = 'contact-' . $field->value;
                $isInvalid = in_array($field, $state['invalid'], true);
                ?>
                <div class="contact-form__field">
                    <label class="contact-form__label" for="<?php echo esc_attr($id); ?>">
                        <?php echo esc_html($field->label()); ?>
                        <?php if (! $field->isRequired()) : ?>
                            <span class="contact-form__optional"><?php esc_html_e('(facultatif)', 'optelya'); ?></span>
                        <?php endif; ?>
                    </label>
                    <input
                        class="contact-form__input"
                        id="<?php echo esc_attr($id); ?>"
                        name="<?php echo esc_attr($field->inputName()); ?>"
                        type="<?php echo esc_attr($field->inputType()); ?>"
                        autocomplete="<?php echo esc_attr($field->autocomplete()); ?>"
                        maxlength="<?php echo esc_attr((string) $field->maxLength()); ?>"
                        value="<?php echo esc_attr($state['values'][$field->value] ?? ''); ?>"
                        <?php if ($field->isRequired()) : ?>required<?php endif; ?>
                        <?php if ($isInvalid) : ?>aria-invalid="true" aria-describedby="<?php echo esc_attr($id . '-error'); ?>"<?php endif; ?>
                    >
                    <?php if ($isInvalid) : ?>
                        <span class="contact-form__error" id="<?php echo esc_attr($id . '-error'); ?>"><?php echo esc_html($field->errorMessage($state['values'][$field->value] ?? '')); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="contact-form__trap" aria-hidden="true">
                <label for="contact-<?php echo esc_attr(OptelyaContactForm::HONEYPOT); ?>"><?php esc_html_e('Ne pas remplir ce champ', 'optelya'); ?></label>
                <input id="contact-<?php echo esc_attr(OptelyaContactForm::HONEYPOT); ?>" name="<?php echo esc_attr(OptelyaContactForm::HONEYPOT); ?>" type="text" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="<?php echo esc_attr(OptelyaContactForm::ACTION_FIELD); ?>" value="<?php echo esc_attr(OptelyaContactForm::ACTION); ?>">
            <input type="hidden" name="<?php echo esc_attr(OptelyaContactForm::TOKEN); ?>" value="<?php echo esc_attr(optelya_contact_token()); ?>">

            <button class="contact-form__submit" type="submit"><?php esc_html_e('Envoyer ma demande', 'optelya'); ?></button>
            <?php if ($notice !== '') : ?>
                <p class="contact-form__notice"><?php echo esc_html($notice); ?></p>
            <?php endif; ?>
        </form>
    </div>
</section>

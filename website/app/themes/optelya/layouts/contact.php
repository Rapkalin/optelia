<?php

/**
 * Contact block: phone next to the form, so that "talk to an expert" holds.
 *
 * The form fields stay in code, not in the back-office: they are the contract
 * of the form handler still to be written. The form is NOT wired yet, as in
 * the mockup: its button does not submit.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

$lead = optelya_sub_text('lead');
$steps = optelya_rows(optelya_sub_field('steps'));
$notice = optelya_sub_text('notice');

// Three required, two optional (zoning v2, decision 5).
$fields = [
    ['id' => 'f-nom', 'label' => __('Nom et prénom', 'optelya'), 'type' => 'text', 'autocomplete' => 'name', 'isRequired' => true],
    ['id' => 'f-mail', 'label' => __('E-mail professionnel', 'optelya'), 'type' => 'email', 'autocomplete' => 'email', 'isRequired' => true],
    ['id' => 'f-soc', 'label' => __('Société', 'optelya'), 'type' => 'text', 'autocomplete' => 'organization', 'isRequired' => true],
    ['id' => 'f-tel', 'label' => __('Téléphone', 'optelya'), 'type' => 'tel', 'autocomplete' => 'tel', 'isRequired' => false],
    ['id' => 'f-proj', 'label' => __('Votre projet en une phrase', 'optelya'), 'type' => 'text', 'autocomplete' => '', 'isRequired' => false],
];
?>
<section id="<?php echo esc_attr(OptelyaSection::Contact->value); ?>" class="section section--contact">
    <div class="contact">
        <div class="contact__intro">
            <h2 class="section-title"><?php echo esc_html(optelya_sub_text('title')); ?></h2>
            <?php if ($lead !== '') : ?>
                <p class="contact__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <?php get_template_part('components/phone-link', null, ['class' => 'contact__phone', 'size' => 20, 'suffix' => optelya_sub_text('phone_text')]); ?>
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

        <form class="card contact-form" aria-label="<?php esc_attr_e('Demande de contact', 'optelya'); ?>">
            <?php foreach ($fields as $field) : ?>
                <div class="contact-form__field">
                    <label class="contact-form__label" for="<?php echo esc_attr($field['id']); ?>">
                        <?php echo esc_html($field['label']); ?>
                        <?php if (! $field['isRequired']) : ?>
                            <span class="contact-form__optional"><?php esc_html_e('(facultatif)', 'optelya'); ?></span>
                        <?php endif; ?>
                    </label>
                    <input
                        class="contact-form__input"
                        id="<?php echo esc_attr($field['id']); ?>"
                        type="<?php echo esc_attr($field['type']); ?>"
                        <?php if ($field['autocomplete'] !== '') : ?>autocomplete="<?php echo esc_attr($field['autocomplete']); ?>"<?php endif; ?>
                        <?php if ($field['isRequired']) : ?>required<?php endif; ?>
                    >
                </div>
            <?php endforeach; ?>

            <button class="contact-form__submit" type="button"><?php esc_html_e('Envoyer ma demande', 'optelya'); ?></button>
            <?php if ($notice !== '') : ?>
                <p class="contact-form__notice"><?php echo esc_html($notice); ?></p>
            <?php endif; ?>
        </form>
    </div>
</section>

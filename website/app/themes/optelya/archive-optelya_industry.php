<?php

/**
 * Industries list (template G2): a card per published sector. Texts in
 * Industries → Réglages, « Liste des industries »; each card comes from its
 * industry (tab « Carte »).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$industries = array_values(optelya_catalog()['industries']);
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, optelya_page_head(OptelyaCatalogSetting::IndustriesHead) + ['breadcrumb' => optelya_catalog_breadcrumb()]); ?>

    <?php if ($industries !== []) : ?>
        <section id="<?php echo esc_attr(OptelyaSection::Sectors->value); ?>" class="section">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::IndustriesChoose)); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => array_map('optelya_industry_card', $industries)]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php $band = optelya_band(OptelyaCatalogSetting::IndustriesBand); ?>
    <?php if ($band['help_text'] !== '' || $band['label'] !== '' || $band['links'] !== []) : ?>
        <section class="section section--band">
            <div class="section__inner">
                <?php get_template_part('components/sectors-band', null, $band); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(optelya_catalog_text(OptelyaCatalogSetting::IndustriesContactTitle), '')); ?>
</main>

<?php
get_footer();

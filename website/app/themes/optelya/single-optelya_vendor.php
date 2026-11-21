<?php

/**
 * Vendor page (template G4): its tools compared, its figures, its
 * demonstration, what Optelya adds. Section titles in Solutions → Réglages,
 * « Fiche éditeur » (« {nom} » = the vendor); the tools and the figures come
 * from its published solutions and their studies. An optional zone left empty
 * is not shown.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$vendorId = (int) get_the_ID();
$vendor = get_the_title($vendorId);

$solutions = optelya_solutions_of($vendorId);
$studies = optelya_studies_of(array_column($solutions, 'id'));
$figured = optelya_split_studies($studies)['figured'];
$video = optelya_video($vendorId, $vendor);

$tools = optelya_heading(OptelyaCatalogSetting::VendorTools, $vendor);
$toolsIntro = optelya_text(optelya_field('tools_intro', $vendorId));
$tools['intro'] = $toolsIntro !== '' ? $toolsIntro : $tools['intro'];

$sectionClass = optelya_section_classes();
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, [
        'breadcrumb' => optelya_catalog_breadcrumb(),
        'title' => $vendor,
        'lead' => optelya_text(optelya_field('tagline', $vendorId)),
        'primary' => optelya_contact_link(),
        'secondary' => [
            'label' => optelya_catalog_text(OptelyaCatalogSetting::VendorToolsLink),
            'url' => $solutions !== [] ? OptelyaSection::Tools->anchor() : '',
            'target' => '',
        ],
        /* translators: %s: vendor name */
        'media' => optelya_head_media($vendorId, __('Visuel éditeur', 'optelya'), sprintf(__('Logo et capture %s — à fournir', 'optelya'), $vendor)),
    ]); ?>

    <?php if ($solutions !== []) : ?>
        <section id="<?php echo esc_attr(OptelyaSection::Tools->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, $tools); ?>
                <?php get_template_part('components/choice-list', null, ['items' => array_map(static fn(array $solution): array => [
                    'name' => $solution['title'],
                    'vendor' => $vendor,
                    'usage' => $solution['card_text'],
                    'url' => $solution['url'],
                    'current' => false,
                ], $solutions)]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($figured !== []) : ?>
        <section id="<?php echo esc_attr(OptelyaSection::Studies->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::ProofsHeading, $vendor)); ?>
                    <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link(OptelyaCatalog::studiesLabel(count($studies), $vendor, count($figured))), 'class' => 'text-link']); ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($figured, false), 'keep_width' => true]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($video !== null) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::DemoHeading, $vendor)); ?>
                <?php get_template_part('components/video-card', null, $video); ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php get_template_part('components/section-heading', null, ['title' => optelya_catalog_text(OptelyaCatalogSetting::ReadersTitle)]); ?>
            <?php get_template_part('components/reader-panels', null, ['panels' => optelya_solution_readers($vendorId)]); ?>
        </div>
    </section>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(
        optelya_text(optelya_field('contact_title', $vendorId)),
        optelya_catalog_text(OptelyaCatalogSetting::VendorContactTitle),
        $vendor,
    )); ?>
</main>

<?php
get_footer();

<?php

/**
 * Solution page (template G4b, zones PR.1–PR.7 of the zoning): the product's
 * fields, rendered by the shared components. An optional zone left empty is
 * not shown; the background alternates over the zones actually shown.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$solutionId = (int) get_the_ID();
$product = get_the_title($solutionId);
$vendorId = (int) optelya_field('vendor', $solutionId);
$vendor = $vendorId > 0 ? get_the_title($vendorId) : '';

$proofs = array_values(array_filter(array_map('optelya_study_card', optelya_ids(optelya_field('proofs', $solutionId)))));
$applications = $proofs === [] ? optelya_title_text_rows(optelya_field('applications', $solutionId)) : [];
$proofSection = $proofs !== [] ? OptelyaSection::Studies : OptelyaSection::Applications;
$secondaryLabel = optelya_text(optelya_field('secondary_label', $solutionId));

$video = optelya_video($solutionId, $product);
$others = optelya_other_solutions($solutionId, $vendorId);
$details = optelya_title_text_rows(optelya_field('details', $solutionId));
$datasheetId = optelya_attachment_id(optelya_field('datasheet', $solutionId));
$sectionClass = optelya_section_classes();
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, [
        'breadcrumb' => optelya_catalog_breadcrumb(),
        'eyebrow' => $vendor,
        'title' => $product,
        'lead' => optelya_text(optelya_field('promise', $solutionId)),
        'primary' => optelya_contact_link(),
        'secondary' => [
            'label' => $secondaryLabel !== '' ? $secondaryLabel : optelya_catalog_text_for(OptelyaCatalogSetting::SolutionProofsLink, $product),
            'url' => $proofs !== [] || $applications !== [] ? $proofSection->anchor() : '',
            'target' => '',
        ],
        /* translators: 1: product name, 2: vendor name */
        'media' => optelya_head_media($solutionId, __('Capture d’interface', 'optelya'), sprintf(__('%1$s — visuel à fournir par %2$s', 'optelya'), $product, $vendor)),
    ]); ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::SolutionBenefits, $product)); ?>
            <?php get_template_part('components/card-grid', null, ['cards' => optelya_title_text_rows(optelya_field('benefits', $solutionId))]); ?>
        </div>
    </section>

    <?php if ($video !== null) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::DemoHeading, $product)); ?>
                <?php get_template_part('components/video-card', null, $video); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($proofs !== []) : ?>
        <?php $studiesLabel = OptelyaCatalog::studiesLabel(optelya_studies_count($solutionId), $product, count($proofs)); ?>
        <section id="<?php echo esc_attr($proofSection->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::ProofsHeading, $product)); ?>
                    <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link($studiesLabel), 'class' => 'text-link']); ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => $proofs]); ?>
            </div>
        </section>
    <?php elseif ($applications !== []) : ?>
        <section id="<?php echo esc_attr($proofSection->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    'title' => optelya_heading(OptelyaCatalogSetting::ProofsHeading, $product)['title'],
                    'intro' => optelya_text(optelya_field('applications_source', $solutionId)),
                ]); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => $applications]); ?>
                <?php get_template_part('components/sectors-band', null, [
                    'label' => optelya_catalog_text(OptelyaCatalogSetting::SolutionSectorsLabel),
                    'links' => optelya_industry_links(optelya_ids(optelya_field('industries', $solutionId))),
                ]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($others['cards'] !== []) : ?>
        <?php $othersLabel = OptelyaCatalog::solutionsLabel($others['count'], $vendor, count($others['cards'])); ?>
        <section id="<?php echo esc_attr(OptelyaSection::OtherSolutions->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::SolutionOthers, $product, $vendor)); ?>
                    <?php get_template_part('components/link', null, ['link' => ['label' => $othersLabel, 'url' => optelya_built_url(OptelyaPostType::Vendor, (string) get_permalink($vendorId)), 'target' => ''], 'class' => 'text-link']); ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => $others['cards'], 'keep_width' => true]); ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php get_template_part('components/section-heading', null, ['title' => optelya_catalog_text(OptelyaCatalogSetting::ReadersTitle)]); ?>
            <?php get_template_part('components/reader-panels', null, ['panels' => optelya_solution_readers($vendorId)]); ?>
        </div>
    </section>

    <?php if ($details !== []) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::SolutionDetails, $product)); ?>
                    <?php if ($datasheetId > 0) : ?>
                        <?php $datasheetPath = (string) get_attached_file($datasheetId); ?>
                        <?php get_template_part('components/link', null, ['link' => [
                            /* translators: 1: link text, 2: file size */
                            'label' => sprintf(__('%1$s (PDF, %2$s)', 'optelya'), optelya_catalog_text_for(OptelyaCatalogSetting::SolutionDatasheetLink, $product), is_file($datasheetPath) ? size_format((int) filesize($datasheetPath)) : ''),
                            'url' => (string) wp_get_attachment_url($datasheetId),
                            'target' => '',
                        ], 'class' => 'text-link']); ?>
                    <?php endif; ?>
                </div>
                <?php get_template_part('components/disclosure-list', null, ['items' => $details]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(
        optelya_text(optelya_field('contact_title', $solutionId)),
        optelya_catalog_text(OptelyaCatalogSetting::SolutionContactTitle),
        $product,
    )); ?>
</main>

<?php
get_footer();

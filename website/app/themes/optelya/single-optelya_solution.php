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

$proofs = array_values(array_filter(array_map(
    static fn(mixed $studyId): ?array => optelya_study_card((int) $studyId),
    (array) optelya_field('proofs', $solutionId),
)));
$applications = $proofs === [] ? optelya_rows(optelya_field('applications', $solutionId)) : [];
$proofSection = $proofs !== [] ? OptelyaSection::Studies : OptelyaSection::Applications;
$secondaryLabel = optelya_text(optelya_field('secondary_label', $solutionId));

$video = optelya_video($solutionId, $product);
$others = optelya_other_solutions($solutionId, $vendorId);
$details = optelya_rows(optelya_field('details', $solutionId));
$datasheetId = optelya_attachment_id(optelya_field('datasheet', $solutionId));
$contactTitle = optelya_text(optelya_field('contact_title', $solutionId));
$contact = optelya_contact_texts();
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
            /* translators: %s: product name */
            'label' => $secondaryLabel !== '' ? $secondaryLabel : sprintf(__('Voir %s en situation', 'optelya'), $product),
            'url' => $proofs !== [] || $applications !== [] ? $proofSection->anchor() : '',
            'target' => '',
        ],
        /* translators: 1: product name, 2: vendor name */
        'media' => optelya_head_media($solutionId, __('Capture d’interface', 'optelya'), sprintf(__('%1$s — visuel à fournir par %2$s', 'optelya'), $product, $vendor)),
    ]); ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php /* translators: %s: product name */ ?>
            <?php get_template_part('components/section-heading', null, ['title' => sprintf(__('Ce que %s résout', 'optelya'), $product)]); ?>
            <?php get_template_part('components/card-grid', null, ['cards' => array_map(static fn(array $row): array => [
                'title' => optelya_text($row['title'] ?? ''),
                'text' => optelya_text($row['text'] ?? ''),
            ], optelya_rows(optelya_field('benefits', $solutionId)))]); ?>
        </div>
    </section>

    <?php if ($video !== null) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    /* translators: %s: product name */
                    'title' => sprintf(__('%s en démonstration', 'optelya'), $product),
                    'intro' => __('Moins de 3 minutes, sous-titrée.', 'optelya'),
                ]); ?>
                <?php get_template_part('components/video-card', null, $video); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($proofs !== []) : ?>
        <?php $studiesLabel = OptelyaCatalog::studiesLabel(optelya_studies_count($solutionId), $product, count($proofs)); ?>
        <section id="<?php echo esc_attr($proofSection->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, [
                        /* translators: %s: product name */
                        'title' => sprintf(__('%s en situation', 'optelya'), $product),
                        'intro' => __('Des gains mesurés, tirés d’études publiées.', 'optelya'),
                    ]); ?>
                    <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link($studiesLabel), 'class' => 'text-link']); ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => $proofs]); ?>
            </div>
        </section>
    <?php elseif ($applications !== []) : ?>
        <section id="<?php echo esc_attr($proofSection->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    /* translators: %s: product name */
                    'title' => sprintf(__('%s en situation', 'optelya'), $product),
                    'intro' => optelya_text(optelya_field('applications_source', $solutionId)),
                ]); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => array_map(static fn(array $row): array => [
                    'title' => optelya_text($row['title'] ?? ''),
                    'text' => optelya_text($row['text'] ?? ''),
                ], $applications)]); ?>
                <?php get_template_part('components/sectors-band', null, [
                    'label' => __('Industries concernées :', 'optelya'),
                    'links' => array_map(static fn(int $industryId): array => optelya_card_link(get_the_title($industryId), OptelyaPostType::Industry, (string) get_permalink($industryId)), optelya_ids(optelya_field('industries', $solutionId))),
                ]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($others['cards'] !== []) : ?>
        <?php $othersLabel = OptelyaCatalog::solutionsLabel($others['count'], $vendor, count($others['cards'])); ?>
        <section id="<?php echo esc_attr(OptelyaSection::OtherSolutions->value); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, [
                        /* translators: %s: vendor name */
                        'title' => sprintf(__('Découvrir les autres solutions %s', 'optelya'), $vendor),
                    ]); ?>
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
                    <?php get_template_part('components/section-heading', null, [
                        'title' => __('Détails techniques', 'optelya'),
                        'intro' => __('Pour l’ingénieur qui veut vérifier avant d’échanger.', 'optelya'),
                    ]); ?>
                    <?php if ($datasheetId > 0) : ?>
                        <?php $datasheetPath = (string) get_attached_file($datasheetId); ?>
                        <?php get_template_part('components/link', null, ['link' => [
                            /* translators: 1: product name, 2: file size */
                            'label' => sprintf(__('Fiche produit %1$s (PDF, %2$s)', 'optelya'), $product, is_file($datasheetPath) ? size_format((int) filesize($datasheetPath)) : ''),
                            'url' => (string) wp_get_attachment_url($datasheetId),
                            'target' => '',
                        ], 'class' => 'text-link']); ?>
                    <?php endif; ?>
                </div>
                <?php get_template_part('components/disclosure-list', null, ['items' => array_map(static fn(array $row): array => [
                    'title' => optelya_text($row['title'] ?? ''),
                    'text' => optelya_text($row['text'] ?? ''),
                ], $details)]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, [
        /* translators: %s: product name */
        'title' => $contactTitle !== '' ? $contactTitle : sprintf(__('Parlons de votre cas %s', 'optelya'), $product),
        'lead' => $contact['lead'],
        'steps' => $contact['steps'],
        'notice' => $contact['notice'],
    ]); ?>
</main>

<?php
get_footer();

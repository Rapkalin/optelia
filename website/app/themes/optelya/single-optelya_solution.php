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
$expert = optelya_link(optelya_option(OptelyaSetting::HeaderCta));
$expert['url'] = '#' . OptelyaSection::Contact->value;

$proofs = array_values(array_filter(array_map(
    static fn(mixed $studyId): ?array => optelya_study_card((int) $studyId),
    (array) optelya_field('proofs', $solutionId),
)));
$applications = $proofs === [] ? optelya_rows(optelya_field('applications', $solutionId)) : [];
$proofAnchor = $proofs !== [] ? 'etudes' : 'applications';
$secondaryLabel = optelya_text(optelya_field('secondary_label', $solutionId));

$videoId = optelya_attachment_id(optelya_field('video_file', $solutionId));
$choiceTitle = optelya_text(optelya_field('choice_title', $solutionId));
$details = optelya_rows(optelya_field('details', $solutionId));
$datasheetId = optelya_attachment_id(optelya_field('datasheet', $solutionId));
$contactTitle = optelya_text(optelya_field('contact_title', $solutionId));
$contact = optelya_contact_texts();

/** Every other zone shown sits on the band. */
$isBand = false;
$sectionClass = static function () use (&$isBand): string {
    $class = 'section' . ($isBand ? ' section--band' : '');
    $isBand = ! $isBand;

    return $class;
};
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, [
        'breadcrumb' => optelya_solution_breadcrumb($solutionId),
        'eyebrow' => $vendor,
        'title' => $product,
        'lead' => optelya_text(optelya_field('promise', $solutionId)),
        'primary' => $expert,
        'secondary' => [
            /* translators: %s: product name */
            'label' => $secondaryLabel !== '' ? $secondaryLabel : sprintf(__('Voir %s en situation', 'optelya'), $product),
            'url' => $proofs !== [] || $applications !== [] ? '#' . $proofAnchor : '',
            'target' => '',
        ],
        'media' => [
            'poster_id' => optelya_attachment_id(optelya_field('visual', $solutionId)),
            'caption' => optelya_text(optelya_field('visual_caption', $solutionId)),
            'placeholder_label' => __('Capture d’interface', 'optelya'),
            /* translators: 1: product name, 2: vendor name */
            'placeholder_text' => sprintf(__('%1$s — visuel à fournir par %2$s', 'optelya'), $product, $vendor),
        ],
    ]); ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php /* translators: %s: product name */ ?>
            <?php get_template_part('components/section-heading', null, ['title' => sprintf(__('Ce que %s résout', 'optelya'), $product)]); ?>
            <?php get_template_part('components/feature-cards', null, ['cards' => array_map(static fn(array $row): array => [
                'title' => optelya_text($row['title'] ?? ''),
                'text' => optelya_text($row['text'] ?? ''),
            ], optelya_rows(optelya_field('benefits', $solutionId)))]); ?>
        </div>
    </section>

    <?php if ($videoId > 0) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    /* translators: %s: product name */
                    'title' => sprintf(__('%s en démonstration', 'optelya'), $product),
                    'intro' => __('Moins de 3 minutes, sous-titrée.', 'optelya'),
                ]); ?>
                <?php get_template_part('components/video-card', null, [
                    'video_url' => (string) wp_get_attachment_url($videoId),
                    'poster_url' => (string) wp_get_attachment_image_url(optelya_attachment_id(optelya_field('video_poster', $solutionId)), 'large'),
                    'captions_url' => (string) wp_get_attachment_url(optelya_attachment_id(optelya_field('video_captions', $solutionId))),
                    /* translators: %s: product name */
                    'title' => sprintf(__('Lire la vidéo de démonstration %s', 'optelya'), $product),
                    /* translators: %s: product name */
                    'player_label' => sprintf(__('Vidéo de démonstration %s', 'optelya'), $product),
                    'meta' => implode(' · ', array_filter([optelya_text(optelya_field('video_duration', $solutionId)), __('sous-titres français', 'optelya')])),
                    'transcript' => optelya_text(optelya_field('video_transcript', $solutionId)),
                ]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($proofs !== []) : ?>
        <?php $studiesLabel = OptelyaCatalog::studiesLabel(optelya_studies_count($solutionId), $product); ?>
        <section id="<?php echo esc_attr($proofAnchor); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, [
                        /* translators: %s: product name */
                        'title' => sprintf(__('%s en situation', 'optelya'), $product),
                        'intro' => __('Des gains mesurés, tirés d’études publiées.', 'optelya'),
                    ]); ?>
                    <?php get_template_part('components/link', null, ['link' => ['label' => $studiesLabel, 'url' => (string) get_post_type_archive_link(OptelyaPostType::Study->value), 'target' => ''], 'class' => 'text-link']); ?>
                </div>
                <?php get_template_part('components/result-cards', null, ['cards' => $proofs]); ?>
            </div>
        </section>
    <?php elseif ($applications !== []) : ?>
        <section id="<?php echo esc_attr($proofAnchor); ?>" class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    /* translators: %s: product name */
                    'title' => sprintf(__('%s en situation', 'optelya'), $product),
                    'intro' => optelya_text(optelya_field('applications_source', $solutionId)),
                ]); ?>
                <?php get_template_part('components/feature-cards', null, ['cards' => array_map(static fn(array $row): array => [
                    'title' => optelya_text($row['title'] ?? ''),
                    'text' => optelya_text($row['text'] ?? ''),
                ], $applications)]); ?>
                <?php get_template_part('components/sectors-band', null, [
                    'label' => __('Industries concernées :', 'optelya'),
                    'links' => array_map(static fn(mixed $industryId): array => [
                        'label' => get_the_title((int) $industryId),
                        'url' => (string) get_permalink((int) $industryId),
                        'target' => '',
                    ], (array) optelya_field('industries', $solutionId)),
                ]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($choiceTitle !== '') : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, [
                    'title' => $choiceTitle,
                    'intro' => optelya_text(optelya_field('choice_intro', $solutionId)),
                ]); ?>
                <?php get_template_part('components/choice-list', null, ['items' => array_map(static function (array $row) use ($solutionId): array {
                    $choiceId = (int) ($row['solution'] ?? 0);
                    $choiceVendor = (int) optelya_field('vendor', $choiceId);

                    return [
                        'name' => get_the_title($choiceId),
                        'vendor' => $choiceVendor > 0 ? get_the_title($choiceVendor) : '',
                        'usage' => optelya_text($row['usage'] ?? ''),
                        'url' => (string) get_permalink($choiceId),
                        'current' => $choiceId === $solutionId,
                    ];
                }, optelya_rows(optelya_field('choice_items', $solutionId)))]); ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="<?php echo esc_attr($sectionClass()); ?>">
        <div class="section__inner">
            <?php get_template_part('components/section-heading', null, ['title' => optelya_text(optelya_field('readers_title', 'option'))]); ?>
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

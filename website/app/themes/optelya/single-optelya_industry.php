<?php

/**
 * Industry page (template G5): the sector's name as title, its issues and
 * answers, its figures and studies — or, without any study yet, the closest
 * cases. The section titles are written once in Industries → Réglages; the
 * rest comes from the industry. An optional zone left empty is not shown.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$industryId = (int) get_the_ID();
$sector = get_the_title($industryId);
$catalog = optelya_catalog();

$studies = optelya_studies_where(static fn(array $study): bool => $study['industry'] === $industryId);
['figured' => $figured, 'others' => $others] = optelya_split_studies($studies);
$near = array_values(array_filter(array_map('optelya_study', optelya_ids(optelya_field('near_studies', $industryId)))));
$video = optelya_video($industryId, $sector);
$hasStudies = $studies !== [];
$noStudy = [
    'title' => optelya_text(optelya_field('no_study_title', $industryId)),
    'text' => optelya_text(optelya_field('no_study_text', $industryId)),
];
// Studies of the sector, or what stands in for them: the head links to the first of these sections.
$hasStudiesZone = $hasStudies ? $others !== [] || $video !== null : $near !== [] || $video !== null || $noStudy['title'] !== '';
$anchoredSection = $figured !== [] ? 'figures' : ($hasStudiesZone ? 'studies' : '');
$anchorOf = static fn(string $section): string => $section === $anchoredSection ? ' id="' . esc_attr(OptelyaSection::Studies->value) . '"' : '';

$answers = array_map(static function (array $row) use ($catalog): array {
    $solutions = array_values(array_filter(array_map(static fn(int $id): ?array => $catalog['solutions'][$id] ?? null, optelya_ids($row['solutions'] ?? []))));
    $products = implode(' · ', array_column($solutions, 'title'));

    return [
        'vendors' => optelya_vendors_named_in($products),
        'title' => optelya_text($row['title'] ?? ''),
        'text' => optelya_text($row['text'] ?? ''),
        'products' => $products,
        'proof' => optelya_study_proof(optelya_study((int) ($row['proof'] ?? 0))),
        'link' => $solutions !== [] ? optelya_view_link($solutions[0], OptelyaPostType::Solution) : [],
    ];
}, optelya_rows(optelya_field('answers', $industryId)));

$sectionClass = optelya_section_classes();
$studiesLabel = OptelyaCatalog::studiesLabel(count($studies), $sector, count($figured) + count($others));
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, [
        'breadcrumb' => optelya_catalog_breadcrumb(),
        'title' => $sector,
        'claim' => optelya_text(optelya_field('claim', $industryId)),
        'lead' => optelya_text(optelya_field('lead', $industryId)),
        'primary' => optelya_contact_link(),
        'secondary' => [
            'label' => optelya_catalog_text($hasStudies ? OptelyaCatalogSetting::IndustryStudiesLink : OptelyaCatalogSetting::IndustryNearLink),
            'url' => $anchoredSection !== '' ? OptelyaSection::Studies->anchor() : '',
            'target' => '',
        ],
        /* translators: %s: sector name */
        'media' => optelya_head_media($industryId, __('Visuel secteur', 'optelya'), sprintf(__('%s — image à fournir', 'optelya'), $sector)),
    ]); ?>

    <?php if ($answers !== []) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::IndustryIssues, $sector)); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => $answers]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($figured !== []) : ?>
        <section<?php echo $anchorOf('figures'); ?> class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::IndustryFigures, $sector)); ?>
                    <?php if ($others === []) : ?>
                        <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link($studiesLabel), 'class' => 'text-link']); ?>
                    <?php endif; ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($figured, false), 'keep_width' => true]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($hasStudiesZone) : ?>
        <section<?php echo $anchorOf('studies'); ?> class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php if (! $hasStudies) : ?>
                    <?php get_template_part('components/empty-state', null, $noStudy); ?>
                    <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($near)]); ?>
                <?php elseif ($others !== []) : ?>
                    <div class="section__header">
                        <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::IndustryStudies, $sector)); ?>
                        <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link($studiesLabel), 'class' => 'text-link']); ?>
                    </div>
                    <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($others, false)]); ?>
                <?php endif; ?>
                <?php if ($video !== null) : ?>
                    <?php get_template_part('components/video-card', null, $video); ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(
        optelya_text(optelya_field('contact_title', $industryId)),
        optelya_catalog_text(OptelyaCatalogSetting::IndustryContactTitle),
        $sector,
    )); ?>
</main>

<?php
get_footer();

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
$expert = ['label' => optelya_contact_link()['label'], 'url' => '#' . OptelyaSection::Contact->value, 'target' => ''];

$studies = optelya_studies_where(static fn(array $study): bool => $study['industry'] === $industryId);
$figured = array_slice(array_values(array_filter($studies, static fn(array $study): bool => $study['figure'] !== '')), 0, OPTELYA_FIGURES_SHOWN);
$others = array_slice(array_values(array_filter($studies, static fn(array $study): bool => $study['figure'] === '')), 0, OPTELYA_STUDIES_SHOWN);
$near = array_values(array_filter(array_map(static fn(int $id): ?array => $catalog['studies'][$id] ?? null, optelya_ids(optelya_field('near_studies', $industryId)))));
$video = optelya_video($industryId, $sector);
$hasStudies = $studies !== [];
$noStudy = [
    'title' => optelya_text(optelya_field('no_study_title', $industryId)),
    'text' => optelya_text(optelya_field('no_study_text', $industryId)),
];
// Studies of the sector, or what stands in for them: the head links to the first of these sections.
$hasStudiesZone = $hasStudies ? $others !== [] || $video !== null : $near !== [] || $video !== null || $noStudy['title'] !== '';
$studiesAnchor = $figured !== [] ? 'figures' : ($hasStudiesZone ? 'zone' : '');
$anchorOf = static fn(string $section): string => $section === $studiesAnchor ? ' id="' . esc_attr(OptelyaSection::Studies->value) . '"' : '';

$answers = array_map(static function (array $row) use ($catalog): array {
    $solutions = array_values(array_filter(array_map(static fn(int $id): ?array => $catalog['solutions'][$id] ?? null, optelya_ids($row['solutions'] ?? []))));
    $products = implode(' · ', array_column($solutions, 'title'));
    $proof = $catalog['studies'][(int) ($row['proof'] ?? 0)] ?? null;

    return [
        'vendors' => optelya_vendors_named_in($products),
        'title' => optelya_text($row['title'] ?? ''),
        'text' => optelya_text($row['text'] ?? ''),
        'products' => $products,
        'proof' => $proof !== null && $proof['figure'] !== '' ? ['figure' => $proof['figure'], 'label' => $proof['result']] : null,
        /* translators: %s: product name */
        'link' => $solutions !== [] ? optelya_card_link(sprintf(__('Voir %s', 'optelya'), $solutions[0]['title']), OptelyaPostType::Solution, $solutions[0]['url']) : [],
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
        'primary' => $expert,
        'secondary' => [
            'label' => optelya_text(optelya_field($hasStudies ? 'industry_studies_link' : 'industry_near_link', 'option')),
            'url' => $studiesAnchor !== '' ? '#' . OptelyaSection::Studies->value : '',
            'target' => '',
        ],
        /* translators: %s: sector name */
        'media' => optelya_head_media($industryId, __('Visuel secteur', 'optelya'), sprintf(__('%s — image à fournir', 'optelya'), $sector)),
    ]); ?>

    <?php if ($answers !== []) : ?>
        <section class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading('industry_issues', $sector)); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => $answers]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($figured !== []) : ?>
        <section<?php echo $anchorOf('figures'); ?> class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <div class="section__header">
                    <?php get_template_part('components/section-heading', null, optelya_heading('industry_figures', $sector)); ?>
                    <?php if ($others === []) : ?>
                        <?php get_template_part('components/link', null, ['link' => optelya_all_studies_link($studiesLabel), 'class' => 'text-link']); ?>
                    <?php endif; ?>
                </div>
                <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($figured, false), 'keep_width' => true]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($hasStudiesZone) : ?>
        <section<?php echo $anchorOf('zone'); ?> class="<?php echo esc_attr($sectionClass()); ?>">
            <div class="section__inner">
                <?php if (! $hasStudies) : ?>
                    <?php get_template_part('components/empty-state', null, $noStudy); ?>
                    <?php get_template_part('components/card-grid', null, ['cards' => optelya_study_cards($near)]); ?>
                <?php elseif ($others !== []) : ?>
                    <div class="section__header">
                        <?php get_template_part('components/section-heading', null, optelya_heading('industry_studies', $sector)); ?>
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
        optelya_text(optelya_field('industry_contact_title', 'option')),
        $sector,
    )); ?>
</main>

<?php
get_footer();

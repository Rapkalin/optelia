<?php

/**
 * Solutions list (template G2): every published solution, filtered by issue,
 * then the vendors. Texts in Solutions → Réglages, « Liste des solutions »;
 * the cards come from the solutions themselves, the blocks from the issues.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

$catalog = optelya_catalog();
$solutions = [];

// Grouped by issue, in the order of the issues: the « all » block reads as the sum of the others.
foreach ([...array_column($catalog['issues'], 'slug'), ''] as $issue) {
    foreach ($catalog['solutions'] as $solution) {
        if ($solution['issue'] === $issue) {
            $solutions[] = $solution;
        }
    }
}

$vendorUrl = static fn(int $vendorId): string => optelya_built_url(OptelyaPostType::Vendor, (string) ($catalog['vendors'][$vendorId]['url'] ?? ''));
$blocks = [['slug' => OptelyaCatalog::ALL_ISSUES, 'name' => __('Tous', 'optelya'), 'heading' => optelya_heading('solutions_all')]];

foreach ($catalog['issues'] as $issue) {
    $ofIssue = array_values(array_filter($solutions, static fn(array $solution): bool => $solution['issue'] === $issue['slug']));

    if ($ofIssue === []) {
        continue;
    }

    $vendorIds = array_column($ofIssue, 'vendor');
    $blocks[] = [
        'slug' => $issue['slug'],
        'name' => $issue['name'],
        'heading' => ['title' => $issue['name'], 'intro' => $issue['description']],
        'compare' => [
            'label' => OptelyaCatalog::compareLabel(array_map(static fn(int $id): string => $catalog['vendors'][$id]['title'] ?? '', $vendorIds)),
            'url' => $vendorUrl($vendorIds[0]) !== '' ? $vendorUrl($vendorIds[0]) . '#' . OptelyaSection::Tools->value : '',
            'target' => '',
        ],
    ];
}

$head = optelya_page_head('solutions_head');
$vendors = optelya_heading('solutions_vendors');
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, $head + ['breadcrumb' => optelya_catalog_breadcrumb()]); ?>

    <section id="<?php echo esc_attr(OptelyaSection::Solutions->value); ?>" class="section">
        <div class="section__inner">
            <?php get_template_part('components/sectors-band', null, optelya_band('solutions_band')); ?>
            <?php get_template_part('components/issue-filter', null, [
                'label' => optelya_text(optelya_field('solutions_filter_label', 'option')),
                'blocks' => $blocks,
                'cards' => array_map(static fn(array $solution): array => optelya_solution_card($solution, OptelyaCatalog::ALL_ISSUES), $solutions),
            ]); ?>
        </div>
    </section>

    <?php if ($catalog['vendors'] !== []) : ?>
        <section id="<?php echo esc_attr(OptelyaSection::Vendors->value); ?>" class="section section--band">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, $vendors); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => array_map('optelya_vendor_card', array_values($catalog['vendors']))]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(optelya_text(optelya_field('solutions_contact_title', 'option')), '')); ?>
</main>

<?php
get_footer();

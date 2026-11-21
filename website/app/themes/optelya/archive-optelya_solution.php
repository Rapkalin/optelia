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

$solutionsOf = static fn(string $issue): array => array_values(array_filter($catalog['solutions'], static fn(array $solution): bool => $solution['issue'] === $issue));
$blocks = [['slug' => OptelyaCatalog::ALL_ISSUES, 'name' => __('Tous', 'optelya'), 'heading' => optelya_heading(OptelyaCatalogSetting::SolutionsAll)]];
$solutions = [];

// Grouped by issue, in the order of the issues: the « all » block reads as the sum of the others.
foreach ($catalog['issues'] as $issue) {
    $ofIssue = $solutionsOf($issue['slug']);

    if ($ofIssue === []) {
        continue;
    }

    $solutions = [...$solutions, ...$ofIssue];
    $vendor = $catalog['vendors'][$ofIssue[0]['vendor']] ?? ['title' => '', 'url' => ''];
    $toolsUrl = optelya_built_url(OptelyaPostType::Vendor, $vendor['url']);
    $blocks[] = [
        'slug' => $issue['slug'],
        'name' => $issue['name'],
        'heading' => ['title' => $issue['name'], 'intro' => $issue['description']],
        'compare' => [
            'label' => OptelyaCatalog::compareLabel(array_map(static fn(array $solution): string => $catalog['vendors'][$solution['vendor']]['title'] ?? '', $ofIssue)),
            'url' => $toolsUrl !== '' ? $toolsUrl . OptelyaSection::Tools->anchor() : '',
            'target' => '',
        ],
    ];
}

$solutions = [...$solutions, ...$solutionsOf('')];
?>

<main id="<?php echo esc_attr(OPTELYA_MAIN_CONTENT_ID); ?>">
    <?php get_template_part('components/hero', null, optelya_page_head(OptelyaCatalogSetting::SolutionsHead) + ['breadcrumb' => optelya_catalog_breadcrumb()]); ?>

    <section id="<?php echo esc_attr(OptelyaSection::Solutions->value); ?>" class="section">
        <div class="section__inner">
            <?php get_template_part('components/sectors-band', null, optelya_band(OptelyaCatalogSetting::SolutionsBand)); ?>
            <?php get_template_part('components/issue-filter', null, [
                'label' => optelya_catalog_text(OptelyaCatalogSetting::SolutionsFilterLabel),
                'blocks' => $blocks,
                'cards' => array_map('optelya_solution_card', $solutions),
            ]); ?>
        </div>
    </section>

    <?php if ($catalog['vendors'] !== []) : ?>
        <section id="<?php echo esc_attr(OptelyaSection::Vendors->value); ?>" class="section section--band">
            <div class="section__inner">
                <?php get_template_part('components/section-heading', null, optelya_heading(OptelyaCatalogSetting::SolutionsVendors)); ?>
                <?php get_template_part('components/card-grid', null, ['cards' => array_map('optelya_vendor_card', array_values($catalog['vendors']))]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('components/contact-block', null, optelya_contact_args(optelya_catalog_text(OptelyaCatalogSetting::SolutionsContactTitle), '')); ?>
</main>

<?php
get_footer();

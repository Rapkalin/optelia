<?php

/**
 * Seeds the catalogue once per environment (own guard: a site seeded before
 * the catalogue existed gets it at the next deployment). Content in
 * inc/seed-catalog-data.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_CATALOG_SEEDED_OPTION = 'optelya_catalog_seeded';

function optelya_seed_catalog(): string
{
    if (get_option(OPTELYA_CATALOG_SEEDED_OPTION)) {
        return 'Catalogue déjà posé sur cet environnement.';
    }

    if (! function_exists('update_field')) {
        return 'ACF absent : catalogue non posé, il sera rejoué.';
    }

    $data = optelya_seed_catalog_data();
    $ids = [];

    foreach ($data['issues'] as $issue) {
        $term = term_exists($issue['slug'], OptelyaTaxonomy::Issue->value) ?: wp_insert_term($issue['name'], OptelyaTaxonomy::Issue->value, ['slug' => $issue['slug']]);
        $ids['issue'][$issue['slug']] = is_array($term) ? (int) $term['term_id'] : 0;
    }

    foreach ($data['vendors'] as $vendor) {
        $ids['vendor'][$vendor['slug']] = $id = optelya_seed_post(OptelyaPostType::Vendor, $vendor['slug'], $vendor['title']);
        update_field('tagline', $vendor['tagline'], $id);
        update_field('since', $vendor['since'], $id);
    }

    foreach ($data['industries'] as $industry) {
        $ids['industry'][$industry['slug']] = $id = optelya_seed_post(OptelyaPostType::Industry, $industry['slug'], $industry['title']);
        update_field('summary', $industry['summary'], $id);
    }

    // Solutions first, empty: the studies and the choice lists point to them.
    foreach ($data['solutions'] as $solution) {
        $ids['solution'][$solution['slug']] = optelya_seed_post(OptelyaPostType::Solution, $solution['slug'], $solution['title']);
    }

    foreach ($data['studies'] as $study) {
        $ids['study'][$study['slug']] = $id = optelya_seed_post(OptelyaPostType::Study, $study['slug'], $study['title']);
        update_field('summary', $study['summary'], $id);
        update_field('figure', $study['figure'], $id);
        update_field('result', $study['result'], $id);
        update_field('client', $study['client'], $id);
        update_field('source', 'étude Noesis Solutions', $id);
        update_field('solutions', array_map(static fn(string $slug): int => $ids['solution'][$slug], $study['solutions']), $id);
        update_field('industry', $study['industry'] !== '' ? $ids['industry'][$study['industry']] : '', $id);
    }

    foreach ($data['solutions'] as $solution) {
        $id = $ids['solution'][$solution['slug']];
        wp_set_object_terms($id, [$ids['issue'][$solution['issue']]], OptelyaTaxonomy::Issue->value);
        update_field('vendor', $ids['vendor'][$solution['vendor']], $id);
        update_field('promise', $solution['promise'], $id);
        update_field('benefits', $solution['benefits'], $id);
        update_field('proofs', array_map(static fn(string $slug): int => $ids['study'][$slug], $solution['proofs']), $id);
        update_field('applications', $solution['applications'], $id);
        update_field('applications_source', $solution['applications_source'], $id);
        update_field('industries', array_map(static fn(string $slug): int => $ids['industry'][$slug], $solution['industries']), $id);
        update_field('choice_title', $solution['choice']['title'], $id);
        update_field('choice_intro', $solution['choice']['intro'], $id);
        update_field('choice_items', array_map(static fn(array $item): array => [
            'solution' => $ids['solution'][$item['solution']],
            'usage' => $item['usage'],
        ], $solution['choice']['choice_items']), $id);
        update_field('details', $solution['details'], $id);
    }

    update_field('readers_title', $data['readers']['title'], 'option');
    update_field('readers', array_map(static fn(array $panel): array => [
        'reader' => $panel['reader'],
        'is_dark' => $panel['is_dark'],
        'items' => $panel['items'],
    ], $data['readers']['panels']), 'option');

    update_option(OPTELYA_CATALOG_SEEDED_OPTION, optelya_site_version(), false);

    return sprintf(
        'Catalogue posé : %d éditeurs, %d enjeux, %d industries, %d solutions, %d études.',
        count($data['vendors']),
        count($data['issues']),
        count($data['industries']),
        count($data['solutions']),
        count($data['studies']),
    );
}

/**
 * The published post of that type and slug, created when missing: a rerun
 * after a partial failure does not duplicate anything.
 */
function optelya_seed_post(OptelyaPostType $type, string $slug, string $title): int
{
    $existing = get_page_by_path($slug, OBJECT, $type->value);

    if ($existing instanceof WP_Post) {
        return $existing->ID;
    }

    return (int) wp_insert_post([
        'post_type' => $type->value,
        'post_status' => 'publish',
        'post_name' => $slug,
        'post_title' => $title,
    ]);
}

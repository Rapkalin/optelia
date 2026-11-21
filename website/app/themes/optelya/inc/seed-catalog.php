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
const OPTELYA_CARD_TEXTS_SEEDED_OPTION = 'optelya_card_texts_seeded';
const OPTELYA_CATALOG_PAGES_SEEDED_OPTION = 'optelya_catalog_pages_seeded';

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

    // Solutions first, empty: the studies point to them.
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
        update_field('card_text', $solution['card_text'], $id);
        update_field('benefits', $solution['benefits'], $id);
        update_field('proofs', array_map(static fn(string $slug): int => $ids['study'][$slug], $solution['proofs']), $id);
        update_field('applications', $solution['applications'], $id);
        update_field('applications_source', $solution['applications_source'], $id);
        update_field('industries', array_map(static fn(string $slug): int => $ids['industry'][$slug], $solution['industries']), $id);
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
 * The card phrase arrived after the catalogue (2.0.0): a site seeded before
 * gets it once, only where it is still empty — a phrase written in the admin
 * is never overruled.
 */
function optelya_seed_card_texts(): string
{
    if (get_option(OPTELYA_CARD_TEXTS_SEEDED_OPTION)) {
        return 'Phrases de carte déjà posées sur cet environnement.';
    }

    if (! function_exists('update_field')) {
        return 'ACF absent : phrases de carte non posées, elles seront rejouées.';
    }

    $filled = 0;

    foreach (optelya_seed_catalog_data()['solutions'] as $solution) {
        $existing = get_page_by_path($solution['slug'], OBJECT, OptelyaPostType::Solution->value);

        if ($existing instanceof WP_Post && optelya_text(optelya_field('card_text', $existing->ID)) === '') {
            update_field('card_text', $solution['card_text'], $existing->ID);
            $filled++;
        }
    }

    update_option(OPTELYA_CARD_TEXTS_SEEDED_OPTION, optelya_site_version(), false);

    return sprintf('Phrases de carte posées : %d solution(s).', $filled);
}

/**
 * The lists and the industry and vendor pages arrived after the catalogue
 * (2.1.0): their texts are seeded once, only into empty fields — nothing
 * written in the admin is overruled. Content in inc/seed-catalog-pages-data.php.
 */
function optelya_seed_catalog_pages(): string
{
    if (get_option(OPTELYA_CATALOG_PAGES_SEEDED_OPTION)) {
        return 'Textes des listes et des fiches déjà posés sur cet environnement.';
    }

    if (! function_exists('update_field')) {
        return 'ACF absent : textes des listes et des fiches non posés, ils seront rejoués.';
    }

    $data = optelya_seed_resolve_home(optelya_seed_catalog_pages_data(), home_url('/'));
    $filled = 0;
    $fill = static function (string $selector, mixed $value, int|string $target) use (&$filled): void {
        if (optelya_seed_is_empty(optelya_field($selector, $target)) && ! optelya_seed_is_empty($value)) {
            update_field($selector, $value, $target);
            $filled++;
        }
    };
    $id = static fn(OptelyaPostType $type, string $slug): int => (($post = get_page_by_path($slug, OBJECT, $type->value)) instanceof WP_Post) ? $post->ID : 0;
    $ids = static fn(OptelyaPostType $type, array $slugs): array => array_values(array_filter(array_map(static fn(string $slug): int => $id($type, $slug), $slugs)));

    // Read on every page of their list: autoloaded, as the settings screen saves them (inc/catalog.php).
    acf_update_setting('autoload', true);

    foreach ($data['options'] as $selector => $value) {
        $fill($selector, $value, 'option');
    }

    acf_update_setting('autoload', false);

    $order = 0;

    foreach ($data['issues'] as $slug => $description) {
        $term = get_term_by('slug', $slug, OptelyaTaxonomy::Issue->value);
        $order++;

        if (! $term instanceof WP_Term) {
            continue;
        }

        if (trim($term->description) === '') {
            wp_update_term($term->term_id, OptelyaTaxonomy::Issue->value, ['description' => $description]);
            $filled++;
        }

        $fill('order', $order, 'term_' . $term->term_id);
    }

    foreach ($data['vendors'] as $slug => $fields) {
        $vendorId = $id(OptelyaPostType::Vendor, $slug);

        foreach ($vendorId > 0 ? $fields : [] as $selector => $value) {
            $fill($selector, $value, $vendorId);
        }
    }

    foreach ($data['industries'] as $slug => $fields) {
        $industryId = $id(OptelyaPostType::Industry, $slug);

        if ($industryId <= 0) {
            continue;
        }

        $fields['card_proof'] = $id(OptelyaPostType::Study, (string) ($fields['card_proof'] ?? ''));
        $fields['near_studies'] = $ids(OptelyaPostType::Study, $fields['near_studies'] ?? []);
        $fields['answers'] = array_map(static fn(array $answer): array => [
            'solutions' => $ids(OptelyaPostType::Solution, $answer['solutions']),
            'proof' => $id(OptelyaPostType::Study, $answer['proof']),
        ] + $answer, $fields['answers'] ?? []);

        foreach ($fields as $selector => $value) {
            $fill($selector, $value, $industryId);
        }
    }

    update_option(OPTELYA_CATALOG_PAGES_SEEDED_OPTION, optelya_site_version(), false);

    return sprintf('Textes des listes et des fiches posés : %d champ(s).', $filled);
}

/**
 * Nothing written: '', 0, null, false, or only empty values.
 */
function optelya_seed_is_empty(mixed $value): bool
{
    if (is_array($value)) {
        return array_filter($value, static fn(mixed $item): bool => ! optelya_seed_is_empty($item)) === [];
    }

    return $value === null || $value === false || $value === '' || $value === 0 || $value === '0';
}

/**
 * The seeded links to the site, written `{home}…`, made absolute.
 */
function optelya_seed_resolve_home(mixed $value, string $home): mixed
{
    if (is_array($value)) {
        return array_map(static fn(mixed $item): mixed => optelya_seed_resolve_home($item, $home), $value);
    }

    return is_string($value) ? str_replace('{home}', $home, $value) : $value;
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

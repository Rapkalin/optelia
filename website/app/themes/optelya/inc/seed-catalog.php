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
const OPTELYA_PAGE_HEADINGS_SEEDED_OPTION = 'optelya_page_headings_seeded';

/** Settings of the vendor page until 2.2.0, now shared with the solution page. */
const OPTELYA_MOVED_SETTINGS = [
    'vendor_proofs' => OptelyaCatalogSetting::ProofsHeading,
    'vendor_demo' => OptelyaCatalogSetting::DemoHeading,
];

function optelya_seed_catalog(): string
{
    if (get_option(OPTELYA_CATALOG_SEEDED_OPTION)) {
        return 'Catalogue déjà posé sur cet environnement.';
    }

    if (! function_exists('update_field')) {
        return 'ACF absent : catalogue non posé, il sera rejoué.';
    }

    $data = optelya_seed_catalog_data();
    $postIds = [];

    foreach ($data['issues'] as $issue) {
        $term = term_exists($issue['slug'], OptelyaTaxonomy::Issue->value) ?: wp_insert_term($issue['name'], OptelyaTaxonomy::Issue->value, ['slug' => $issue['slug']]);
        $postIds['issue'][$issue['slug']] = is_array($term) ? (int) $term['term_id'] : 0;
    }

    foreach ($data['vendors'] as $vendor) {
        $postIds['vendor'][$vendor['slug']] = $postId = optelya_seed_post(OptelyaPostType::Vendor, $vendor['slug'], $vendor['title']);
        update_field('tagline', $vendor['tagline'], $postId);
        update_field('since', $vendor['since'], $postId);
    }

    foreach ($data['industries'] as $industry) {
        $postIds['industry'][$industry['slug']] = $postId = optelya_seed_post(OptelyaPostType::Industry, $industry['slug'], $industry['title']);
        update_field('summary', $industry['summary'], $postId);
    }

    // Solutions first, empty: the studies point to them.
    foreach ($data['solutions'] as $solution) {
        $postIds['solution'][$solution['slug']] = optelya_seed_post(OptelyaPostType::Solution, $solution['slug'], $solution['title']);
    }

    foreach ($data['studies'] as $study) {
        $postIds['study'][$study['slug']] = $postId = optelya_seed_post(OptelyaPostType::Study, $study['slug'], $study['title']);
        update_field('summary', $study['summary'], $postId);
        update_field('figure', $study['figure'], $postId);
        update_field('result', $study['result'], $postId);
        update_field('client', $study['client'], $postId);
        update_field('source', 'étude Noesis Solutions', $postId);
        update_field('solutions', array_map(static fn(string $slug): int => $postIds['solution'][$slug], $study['solutions']), $postId);
        update_field('industry', $study['industry'] !== '' ? $postIds['industry'][$study['industry']] : '', $postId);
    }

    foreach ($data['solutions'] as $solution) {
        $postId = $postIds['solution'][$solution['slug']];
        wp_set_object_terms($postId, [$postIds['issue'][$solution['issue']]], OptelyaTaxonomy::Issue->value);
        update_field('vendor', $postIds['vendor'][$solution['vendor']], $postId);
        update_field('promise', $solution['promise'], $postId);
        update_field('card_text', $solution['card_text'], $postId);
        update_field('benefits', $solution['benefits'], $postId);
        update_field('proofs', array_map(static fn(string $slug): int => $postIds['study'][$slug], $solution['proofs']), $postId);
        update_field('applications', $solution['applications'], $postId);
        update_field('applications_source', $solution['applications_source'], $postId);
        update_field('industries', array_map(static fn(string $slug): int => $postIds['industry'][$slug], $solution['industries']), $postId);
        update_field('details', $solution['details'], $postId);
    }

    update_field(OptelyaCatalogSetting::ReadersTitle->value, $data['readers']['title'], 'option');
    update_field(OptelyaCatalogSetting::Readers->value, array_map(static fn(array $panel): array => [
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
    $filled = optelya_seed_fill_options($data['options']);
    $fill = static function (string $selector, mixed $value, int|string $target) use (&$filled): void {
        $filled += (int) optelya_seed_fill($selector, $value, $target);
    };
    $postIdOf = static fn(OptelyaPostType $type, string $slug): int => (($post = get_page_by_path($slug, OBJECT, $type->value)) instanceof WP_Post) ? $post->ID : 0;
    $postIdsOf = static fn(OptelyaPostType $type, array $slugs): array => array_values(array_filter(array_map(static fn(string $slug): int => $postIdOf($type, $slug), $slugs)));

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
        $vendorId = $postIdOf(OptelyaPostType::Vendor, $slug);

        foreach ($vendorId > 0 ? $fields : [] as $selector => $value) {
            $fill($selector, $value, $vendorId);
        }
    }

    foreach ($data['industries'] as $slug => $fields) {
        $industryId = $postIdOf(OptelyaPostType::Industry, $slug);

        if ($industryId <= 0) {
            continue;
        }

        $fields['card_proof'] = $postIdOf(OptelyaPostType::Study, (string) ($fields['card_proof'] ?? ''));
        $fields['near_studies'] = $postIdsOf(OptelyaPostType::Study, $fields['near_studies'] ?? []);
        $fields['answers'] = array_map(static fn(array $answer): array => [
            'solutions' => $postIdsOf(OptelyaPostType::Solution, $answer['solutions']),
            'proof' => $postIdOf(OptelyaPostType::Study, $answer['proof']),
        ] + $answer, $fields['answers'] ?? []);

        foreach ($fields as $selector => $value) {
            $fill($selector, $value, $industryId);
        }
    }

    update_option(OPTELYA_CATALOG_PAGES_SEEDED_OPTION, optelya_site_version(), false);

    return sprintf('Textes des listes et des fiches posés : %d champ(s).', $filled);
}

/**
 * The section titles of the solution page arrived in the settings in 2.2.0:
 * the two the vendor page already had move to the shared settings (what was
 * written there is kept), then the empty ones get the words the page showed.
 */
function optelya_seed_page_headings(): string
{
    if (get_option(OPTELYA_PAGE_HEADINGS_SEEDED_OPTION)) {
        return 'Titres des fiches déjà posés sur cet environnement.';
    }

    if (! function_exists('update_field')) {
        return 'ACF absent : titres des fiches non posés, ils seront rejoués.';
    }

    $filled = 0;

    foreach (OPTELYA_MOVED_SETTINGS as $oldName => $setting) {
        $written = ['title' => (string) get_option("options_{$oldName}_title"), 'intro' => (string) get_option("options_{$oldName}_intro")];
        $filled += optelya_seed_fill_options([$setting->value => $written]);

        foreach (['', '_title', '_intro'] as $part) {
            delete_option("options_{$oldName}{$part}");
            delete_option("_options_{$oldName}{$part}");
        }
    }

    $filled += optelya_seed_fill_options(optelya_seed_page_headings_data());
    update_option(OPTELYA_PAGE_HEADINGS_SEEDED_OPTION, optelya_site_version(), false);

    return sprintf('Titres des fiches posés : %d réglage(s).', $filled);
}

/**
 * Writes a value only into an empty field, and only a value with something in it.
 */
function optelya_seed_fill(string $selector, mixed $value, int|string $target): bool
{
    if (! optelya_seed_is_empty(optelya_field($selector, $target)) || optelya_seed_is_empty($value)) {
        return false;
    }

    update_field($selector, $value, $target);

    return true;
}

/**
 * Settings of the catalogue, into empty fields only. Read on every page of
 * their list: autoloaded, as the settings screen saves them (inc/catalog.php).
 *
 * @param array<string, mixed> $options
 */
function optelya_seed_fill_options(array $options): int
{
    acf_update_setting('autoload', true);
    $filled = count(array_filter(array_map(static fn(string $selector, mixed $value): bool => optelya_seed_fill($selector, $value, 'option'), array_keys($options), $options)));
    acf_update_setting('autoload', false);

    return $filled;
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

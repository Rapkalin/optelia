<?php

/**
 * Catalogue glue: what the solution, industry and vendor templates read.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_SOLUTION_SETTINGS_PAGE = 'optelya-solution-settings';

function optelya_register_catalogue_settings(): void
{
    if (! function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title' => __('Réglages des solutions', 'optelya'),
        'menu_title' => __('Réglages', 'optelya'),
        'menu_slug' => OPTELYA_SOLUTION_SETTINGS_PAGE,
        'parent_slug' => 'edit.php?post_type=' . OptelyaPostType::Solution->value,
        'capability' => OptelyaRole::SETTINGS_CAPABILITY,
        'redirect' => false,
        'update_button' => __('Enregistrer', 'optelya'),
        'updated_message' => __('Réglages enregistrés.', 'optelya'),
    ]);
}
add_action('acf/init', 'optelya_register_catalogue_settings');

/**
 * Pages showing the contact block: the header button then targets the form
 * of the page itself, not the homepage's.
 */
function optelya_has_contact_block(): bool
{
    return is_front_page() || is_singular(OptelyaPostType::Solution->value);
}

/**
 * @return array{label: string, url: string}
 */
function optelya_breadcrumb_home(): array
{
    return ['label' => __('Accueil', 'optelya'), 'url' => home_url('/')];
}

/**
 * « Optimus – Noesis Solutions – Optelya »: the vendor is searched with the
 * product.
 *
 * @param array<string, string> $parts
 * @return array<string, string>
 */
function optelya_solution_document_title(array $parts): array
{
    $vendorId = is_singular(OptelyaPostType::Solution->value) ? (int) optelya_field('vendor', (int) get_queried_object_id()) : 0;

    if ($vendorId <= 0) {
        return $parts;
    }

    return ['title' => $parts['title'] ?? '', 'vendor' => get_the_title($vendorId)] + $parts;
}
add_filter('document_title_parts', 'optelya_solution_document_title');

/**
 * Intro, steps and notice of the homepage's contact section: written once,
 * shown on every page that has the contact block.
 *
 * @return array{lead: string, steps: list<string>, notice: string}
 */
function optelya_contact_texts(): array
{
    $texts = ['lead' => '', 'steps' => [], 'notice' => ''];

    foreach (optelya_rows(optelya_field('sections', (int) get_option('page_on_front'))) as $section) {
        if (($section['acf_fc_layout'] ?? '') !== 'contact') {
            continue;
        }

        return [
            'lead' => optelya_text($section['lead'] ?? ''),
            'steps' => array_map(static fn(array $step): string => optelya_text($step['text'] ?? ''), optelya_rows($section['steps'] ?? null)),
            'notice' => optelya_text($section['notice'] ?? ''),
        ];
    }

    return $texts;
}

/**
 * Published studies citing a product, in their « Logiciels » field.
 */
function optelya_studies_count(int $solutionId): int
{
    $query = new WP_Query([
        'post_type' => OptelyaPostType::Study->value,
        'post_status' => 'publish',
        'fields' => 'ids',
        'posts_per_page' => -1,
        'no_found_rows' => true,
        // ACF stores a relationship as a serialised list of quoted IDs.
        'meta_query' => [['key' => 'solutions', 'value' => '"' . $solutionId . '"', 'compare' => 'LIKE']],
    ]);

    return count($query->posts);
}

/**
 * A study as a figure card (components/result-cards.php); null without a
 * verified figure.
 *
 * @return array{figure: string, label: string, products: string, source: string, link: array}|null
 */
function optelya_study_card(int $studyId): ?array
{
    $figure = optelya_text(optelya_field('figure', $studyId));

    if ($figure === '') {
        return null;
    }

    $industryId = (int) optelya_field('industry', $studyId);
    $client = optelya_text(optelya_field('client', $studyId));
    $source = optelya_text(optelya_field('source', $studyId));

    return [
        'figure' => $figure,
        'label' => optelya_text(optelya_field('result', $studyId)),
        'products' => $industryId > 0 ? get_the_title($industryId) : '',
        'source' => implode(' · ', array_filter([$client, $source])),
        'link' => ['label' => __('Lire l’étude', 'optelya'), 'url' => (string) get_permalink($studyId), 'target' => ''],
    ];
}

/**
 * @return list<array{reader: string, is_dark: bool, items: list<array{title: string, text: string}>}>
 */
function optelya_solution_readers(int $vendorId): array
{
    $panels = array_map(static fn(array $panel): array => [
        'reader' => optelya_text($panel['reader'] ?? ''),
        'is_dark' => ! empty($panel['is_dark']),
        'items' => array_map(static fn(array $item): array => [
            'title' => optelya_text($item['title'] ?? ''),
            'text' => optelya_text($item['text'] ?? ''),
        ], optelya_rows($panel['items'] ?? null)),
    ], optelya_rows(optelya_field('readers', 'option')));

    return OptelyaCatalog::readersWithVendor($panels, optelya_text(optelya_field('since', $vendorId)));
}

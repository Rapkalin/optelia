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
 * The « talk to an expert » link of Settings → Configuration, aimed at the
 * contact block of the page when it has one, at the homepage's otherwise.
 * Used by the header button and the coming-soon message.
 *
 * @return array{label: string, url: string, target: string}
 */
function optelya_contact_link(): array
{
    $link = optelya_link(optelya_option(OptelyaSetting::HeaderCta));
    $link['url'] = OptelyaCatalog::localAnchor($link['url'], OptelyaSection::Contact->value, optelya_has_contact_block());

    return $link;
}

/**
 * Whether a catalogue page has its template yet. Until then WordPress shows a
 * bare list or an empty page: no link leads to it (breadcrumb, « all studies »).
 */
function optelya_is_built(OptelyaPostType $type, bool $archive = false): bool
{
    return locate_template(($archive ? 'archive-' : 'single-') . $type->value . '.php') !== '';
}

/**
 * @return array{label: string, url: string}
 */
function optelya_breadcrumb_home(): array
{
    return ['label' => __('Accueil', 'optelya'), 'url' => home_url('/')];
}

/**
 * Home › Solutions › vendor › product: shown by the page, marked up for the
 * search engines (inc/seo.php).
 *
 * @return list<array{label: string, url: string}>
 */
function optelya_solution_breadcrumb(int $solutionId): array
{
    $vendorId = (int) optelya_field('vendor', $solutionId);

    return [
        optelya_breadcrumb_home(),
        ['label' => OptelyaPostType::Solution->plural(), 'url' => optelya_is_built(OptelyaPostType::Solution, true) ? (string) get_post_type_archive_link(OptelyaPostType::Solution->value) : ''],
        ...($vendorId > 0 ? [['label' => get_the_title($vendorId), 'url' => optelya_is_built(OptelyaPostType::Vendor) ? (string) get_permalink($vendorId) : '']] : []),
        ['label' => get_the_title($solutionId), 'url' => ''],
    ];
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
 * Product name => vendor name of the published solutions: the vendor labels of
 * the cards (components/vendor-chips.php).
 *
 * @return array<string, string>
 */
function optelya_vendor_of_product(): array
{
    static $vendorOfProduct = null;

    if ($vendorOfProduct !== null) {
        return $vendorOfProduct;
    }

    $vendorOfProduct = [];

    foreach (get_posts(['post_type' => OptelyaPostType::Solution->value, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids']) as $solutionId) {
        $vendorId = (int) optelya_field('vendor', (int) $solutionId);

        if ($vendorId > 0) {
            $vendorOfProduct[get_the_title((int) $solutionId)] = get_the_title($vendorId);
        }
    }

    return $vendorOfProduct;
}

/**
 * @return list<string>
 */
function optelya_vendors_named_in(string $text): array
{
    return OptelyaCatalog::vendorsNamedIn($text, optelya_vendor_of_product());
}

/** At most one row of cards: beyond, a link to the vendor's page. */
const OPTELYA_OTHER_SOLUTIONS_SHOWN = 3;

/**
 * The other published solutions of the same vendor, as cards
 * (components/card.php), without vendor label: the heading names it.
 *
 * @return array{cards: list<array<string, mixed>>, count: int}
 */
function optelya_other_solutions(int $solutionId, int $vendorId): array
{
    if ($vendorId <= 0) {
        return ['cards' => [], 'count' => 0];
    }

    $ids = get_posts([
        'post_type' => OptelyaPostType::Solution->value,
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids',
        'post__not_in' => [$solutionId],
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'meta_query' => [['key' => 'vendor', 'value' => $vendorId]],
    ]);

    return [
        'cards' => array_map(static fn(int $id): array => [
            'title' => get_the_title($id),
            'text' => optelya_text(optelya_field('card_text', $id)),
            /* translators: %s: product name */
            'link' => ['label' => sprintf(__('Voir %s', 'optelya'), get_the_title($id)), 'url' => (string) get_permalink($id), 'target' => ''],
        ], array_slice(array_map('intval', $ids), 0, OPTELYA_OTHER_SOLUTIONS_SHOWN)),
        'count' => count($ids),
    ];
}

/**
 * A study as a figure card (components/card.php); null without a verified
 * figure.
 *
 * @return array{vendors: list<string>, figure: string, figure_label: string, products: string, source: string, link: array}|null
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

    $products = implode(' · ', array_map(static fn(mixed $solutionId): string => get_the_title((int) $solutionId), (array) optelya_field('solutions', $studyId)));

    return [
        'vendors' => optelya_vendors_named_in($products),
        'figure' => $figure,
        'figure_label' => optelya_text(optelya_field('result', $studyId)),
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

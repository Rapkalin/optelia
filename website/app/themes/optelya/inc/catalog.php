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
/** Cards shown before a « see all » link: one row (three columns at full width). */
const OPTELYA_ROW_OF_CARDS = 3;
const OPTELYA_INDUSTRY_SETTINGS_PAGE = 'optelya-industry-settings';

/**
 * Texts shared by a list and its pages, under the content type's menu.
 */
function optelya_register_catalogue_settings(): void
{
    if (! function_exists('acf_add_options_sub_page')) {
        return;
    }

    $pages = [
        OPTELYA_SOLUTION_SETTINGS_PAGE => [OptelyaPostType::Solution, __('Réglages des solutions', 'optelya')],
        OPTELYA_INDUSTRY_SETTINGS_PAGE => [OptelyaPostType::Industry, __('Réglages des industries', 'optelya')],
    ];

    foreach ($pages as $slug => [$type, $title]) {
        acf_add_options_sub_page([
            'page_title' => $title,
            'menu_title' => __('Réglages', 'optelya'),
            'menu_slug' => $slug,
            'parent_slug' => 'edit.php?post_type=' . $type->value,
            'capability' => OptelyaRole::SETTINGS_CAPABILITY,
            // Read on every page of the list: loaded with the other options, not one query each.
            'autoload' => true,
            'redirect' => false,
            'update_button' => __('Enregistrer', 'optelya'),
            'updated_message' => __('Réglages enregistrés.', 'optelya'),
        ]);
    }
}
add_action('acf/init', 'optelya_register_catalogue_settings');

/**
 * Pages showing the contact block: the header button then targets the form
 * of the page itself, not the homepage's.
 */
function optelya_has_contact_block(): bool
{
    return is_front_page() || is_singular(optelya_built_types()) || is_post_type_archive(optelya_built_types(true));
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
 * Content types whose pages (or lists) are built: they show the contact block,
 * are indexed and in the sitemap (inc/seo.php).
 *
 * @return list<string>
 */
function optelya_built_types(bool $archive = false): array
{
    static $built = [];

    return $built[(int) $archive] ??= array_values(array_map(
        static fn(OptelyaPostType $type): string => $type->value,
        array_filter(OptelyaPostType::cases(), static fn(OptelyaPostType $type): bool => optelya_is_built($type, $archive)),
    ));
}

/**
 * Address of a content's page, or '' while that page is not built.
 */
function optelya_built_url(OptelyaPostType $type, string $url): string
{
    return optelya_is_built($type) ? $url : '';
}

/**
 * @return array{label: string, url: string}
 */
function optelya_breadcrumb_home(): array
{
    return ['label' => __('Accueil', 'optelya'), 'url' => home_url('/')];
}

/**
 * A list of the catalogue as a step of a breadcrumb, linked once it is built.
 *
 * @return array{label: string, url: string}
 */
function optelya_breadcrumb_list(OptelyaPostType $type): array
{
    return ['label' => $type->plural(), 'url' => optelya_list_url($type)];
}

/**
 * Address of a content type's list, or '' while it is not built.
 */
function optelya_list_url(OptelyaPostType $type): string
{
    return optelya_is_built($type, true) ? (string) get_post_type_archive_link($type->value) : '';
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
        optelya_breadcrumb_list(OptelyaPostType::Solution),
        ...($vendorId > 0 ? [['label' => get_the_title($vendorId), 'url' => optelya_built_url(OptelyaPostType::Vendor, (string) get_permalink($vendorId))]] : []),
        ['label' => get_the_title($solutionId), 'url' => ''],
    ];
}

/**
 * Breadcrumb of the current page of the catalogue: list (Home › Industries),
 * vendor (Home › Solutions › vendor) or industry (Home › Industries › sector).
 * Empty elsewhere.
 *
 * @return list<array{label: string, url: string}>
 */
function optelya_catalog_breadcrumb(): array
{
    $list = optelya_current_list();

    if ($list !== null) {
        return [optelya_breadcrumb_home(), ['label' => $list->plural(), 'url' => '']];
    }

    $postId = (int) get_queried_object_id();
    $current = ['label' => get_the_title($postId), 'url' => ''];

    return match (true) {
        is_singular(OptelyaPostType::Solution->value) => optelya_solution_breadcrumb($postId),
        is_singular(OptelyaPostType::Vendor->value) => [optelya_breadcrumb_home(), optelya_breadcrumb_list(OptelyaPostType::Solution), $current],
        is_singular(OptelyaPostType::Industry->value) => [optelya_breadcrumb_home(), optelya_breadcrumb_list(OptelyaPostType::Industry), $current],
        default => [],
    };
}

/**
 * The content type whose built list is the current page, if any.
 */
function optelya_current_list(): ?OptelyaPostType
{
    $type = is_post_type_archive(optelya_built_types(true)) ? OptelyaPostType::tryFrom((string) get_query_var('post_type')) : null;

    return $type instanceof OptelyaPostType ? $type : null;
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

    return ['lead' => '', 'steps' => [], 'notice' => ''];
}

/**
 * Published IDs of a content type, in the order of the admin (page order,
 * then creation).
 *
 * @return list<int>
 */
function optelya_published_ids(OptelyaPostType $type): array
{
    return array_map('intval', get_posts([
        'post_type' => $type->value,
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids',
        'orderby' => ['menu_order' => 'ASC', 'ID' => 'ASC'],
    ]));
}

/**
 * @return list<int>
 */
function optelya_ids(mixed $value): array
{
    return array_values(array_filter(array_map('intval', is_array($value) ? $value : [$value]), static fn(int $id): bool => $id > 0));
}

/**
 * The published catalogue as plain values, in display order: the one read
 * behind the lists, the pages and the vendor labels of every card. Cached
 * (OptelyaCacheKey::Catalog), emptied whenever a content of the catalogue or
 * an issue changes.
 *
 * @return array{
 *     issues: list<array{slug: string, name: string, description: string, order: int}>,
 *     vendors: array<int, array{id: int, title: string, url: string, tagline: string, since: string}>,
 *     solutions: array<int, array{id: int, title: string, url: string, vendor: int, issue: string, card_text: string, proofs: list<int>}>,
 *     industries: array<int, array{id: int, title: string, url: string, card_text: string, card_examples: string, card_proof: int}>,
 *     studies: array<int, array{id: int, title: string, url: string, summary: string, figure: string, result: string, client: string, source: string, solutions: list<int>, industry: int}>
 * }
 */
function optelya_catalog(): array
{
    static $catalog = null;

    if ($catalog === null) {
        $catalog = function_exists('rapkalin_cache_remember') && enum_exists('OptelyaCacheKey')
            ? rapkalin_cache_remember(OptelyaCacheKey::Catalog, 'optelya_build_catalog')
            : optelya_build_catalog();
    }

    return $catalog;
}

/**
 * @return array<string, mixed> See optelya_catalog().
 */
function optelya_build_catalog(): array
{
    $ids = array_map(static fn(OptelyaPostType $type): array => optelya_published_ids($type), [
        'vendors' => OptelyaPostType::Vendor,
        'solutions' => OptelyaPostType::Solution,
        'industries' => OptelyaPostType::Industry,
        'studies' => OptelyaPostType::Study,
    ]);
    // Posts, fields and issues in a few queries, not one per card.
    _prime_post_caches(array_merge(...array_values($ids)), false, true);
    update_object_term_cache($ids['solutions'], OptelyaPostType::Solution->value);

    $text = static fn(int $id, string $name): string => optelya_text(optelya_field($name, $id));
    // Every entry: its ID, title and address, then the fields of its type; keyed by ID.
    $entries = static fn(string $kind, callable $fields): array => array_combine($ids[$kind], array_map(
        static fn(int $id): array => ['id' => $id, 'title' => get_the_title($id), 'url' => (string) get_permalink($id)] + $fields($id),
        $ids[$kind],
    ));

    $issues = [];
    $terms = get_terms(['taxonomy' => OptelyaTaxonomy::Issue->value, 'hide_empty' => false]);

    foreach (is_array($terms) ? $terms : [] as $term) {
        $issues[] = ['slug' => $term->slug, 'name' => $term->name, 'description' => trim($term->description), 'order' => (int) optelya_field('order', 'term_' . $term->term_id)];
    }

    return [
        'issues' => OptelyaCatalog::sortIssues($issues),
        'vendors' => $entries('vendors', static fn(int $id): array => ['tagline' => $text($id, 'tagline'), 'since' => $text($id, 'since')]),
        'solutions' => $entries('solutions', static function (int $id) use ($text): array {
            $terms = get_the_terms($id, OptelyaTaxonomy::Issue->value);

            return [
                'vendor' => (int) optelya_field('vendor', $id),
                'issue' => is_array($terms) && $terms !== [] ? $terms[0]->slug : '',
                'card_text' => $text($id, 'card_text'),
                'proofs' => optelya_ids(optelya_field('proofs', $id)),
            ];
        }),
        'industries' => $entries('industries', static fn(int $id): array => [
            'card_text' => $text($id, 'card_text'),
            'card_examples' => $text($id, 'card_examples'),
            'card_proof' => (int) optelya_field('card_proof', $id),
        ]),
        'studies' => $entries('studies', static fn(int $id): array => [
            'summary' => $text($id, 'summary'),
            'figure' => $text($id, 'figure'),
            'result' => $text($id, 'result'),
            'client' => $text($id, 'client'),
            'source' => $text($id, 'source'),
            'solutions' => optelya_ids(optelya_field('solutions', $id)),
            'industry' => (int) optelya_field('industry', $id),
        ]),
    ];
}

/**
 * Product name => vendor name of the published solutions: the vendor labels of
 * the cards (components/vendor-chips.php).
 *
 * @return array<string, string>
 */
function optelya_vendor_of_product(): array
{
    $catalog = optelya_catalog();
    $vendorOfProduct = [];

    foreach ($catalog['solutions'] as $solution) {
        if (isset($catalog['vendors'][$solution['vendor']])) {
            $vendorOfProduct[$solution['title']] = $catalog['vendors'][$solution['vendor']]['title'];
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

/**
 * Published studies matching a rule, in catalogue order.
 *
 * @param callable(array<string, mixed>): bool $matches
 * @return list<array<string, mixed>>
 */
function optelya_studies_where(callable $matches): array
{
    return array_values(array_filter(optelya_catalog()['studies'], $matches));
}

/**
 * Published studies citing one of these products, in their « Logiciels » field.
 *
 * @param list<int> $solutionIds
 * @return list<array<string, mixed>>
 */
function optelya_studies_of(array $solutionIds): array
{
    return optelya_studies_where(static fn(array $study): bool => array_intersect($study['solutions'], $solutionIds) !== []);
}

function optelya_studies_count(int $solutionId): int
{
    return count(optelya_studies_of([$solutionId]));
}

/**
 * Published solutions of a vendor, in catalogue order.
 *
 * @return list<array<string, mixed>>
 */
function optelya_solutions_of(int $vendorId): array
{
    return array_values(array_filter(optelya_catalog()['solutions'], static fn(array $solution): bool => $vendorId > 0 && $solution['vendor'] === $vendorId));
}

/**
 * « Optimus · id8 »: the published products among these IDs.
 *
 * @param list<int> $solutionIds
 */
function optelya_products(array $solutionIds): string
{
    $solutions = optelya_catalog()['solutions'];

    return implode(' · ', array_map(static fn(int $id): string => $solutions[$id]['title'], array_values(array_filter($solutionIds, static fn(int $id): bool => isset($solutions[$id])))));
}

/**
 * The link of a card to a page, kept only once that page is built.
 *
 * @return array{label: string, url: string, target: string}
 */
function optelya_card_link(string $label, OptelyaPostType $type, string $url): array
{
    return ['label' => $label, 'url' => optelya_built_url($type, $url), 'target' => ''];
}

/**
 * « Voir Optimus »: the link of a card to the page of the content it names.
 *
 * @param array{title: string, url: string} $entry A solution or an industry of the catalogue.
 * @return array{label: string, url: string, target: string}
 */
function optelya_view_link(array $entry, OptelyaPostType $type): array
{
    /* translators: %s: product or sector name */
    return optelya_card_link(sprintf(__('Voir %s', 'optelya'), $entry['title']), $type, $entry['url']);
}

/**
 * « See every study » link: to the studies library, once it is built.
 *
 * @return array{label: string, url: string, target: string}
 */
function optelya_all_studies_link(string $label): array
{
    return ['label' => $label, 'url' => optelya_list_url(OptelyaPostType::Study), 'target' => ''];
}

/**
 * A study's figure as the proof line of a card, or null without one.
 *
 * @return array{figure: string, label: string}|null
 */
function optelya_study_proof(?array $study): ?array
{
    return $study !== null && $study['figure'] !== '' ? ['figure' => $study['figure'], 'label' => $study['result']] : null;
}

/**
 * The published study of that ID, or null.
 *
 * @return array<string, mixed>|null
 */
function optelya_study(int $studyId): ?array
{
    return optelya_catalog()['studies'][$studyId] ?? null;
}

/**
 * Name of a study's sector, or ''.
 */
function optelya_study_sector(array $study): string
{
    return optelya_catalog()['industries'][$study['industry']]['title'] ?? '';
}

/**
 * Figured studies first (one row of figures), then the others (one row of
 * cards): what a sector's or vendor's page shows of its studies.
 *
 * @param list<array<string, mixed>> $studies
 * @return array{figured: list<array<string, mixed>>, others: list<array<string, mixed>>}
 */
function optelya_split_studies(array $studies): array
{
    $isFigured = static fn(array $study): bool => $study['figure'] !== '';

    return [
        'figured' => array_slice(array_values(array_filter($studies, $isFigured)), 0, OPTELYA_ROW_OF_CARDS),
        'others' => array_slice(array_values(array_filter($studies, static fn(array $study): bool => ! $isFigured($study))), 0, OPTELYA_ROW_OF_CARDS),
    ];
}

/**
 * A study as a figure card (components/card.php); null without a verified
 * figure. Its line names what the page does not: the sector on a product's
 * page, the products on a sector's or a vendor's.
 *
 * @return array<string, mixed>|null
 */
function optelya_study_card(int $studyId, bool $namesSector = true): ?array
{
    $study = optelya_study($studyId);

    if ($study === null || $study['figure'] === '') {
        return null;
    }

    $products = optelya_products($study['solutions']);

    return [
        'vendors' => optelya_vendors_named_in($products),
        'figure' => $study['figure'],
        'figure_label' => $study['result'],
        'products' => $namesSector ? optelya_study_sector($study) : $products,
        'source' => implode(' · ', array_filter([$study['client'], $study['source']])),
        'link' => optelya_card_link(__('Lire l’étude', 'optelya'), OptelyaPostType::Study, $study['url']),
    ];
}

/**
 * A study without figure: its sector (unless the page is the sector's), its
 * title and summary.
 *
 * @return array<string, mixed>
 */
function optelya_study_summary_card(array $study, bool $namesSector = true): array
{
    $products = optelya_products($study['solutions']);

    return [
        'vendors' => optelya_vendors_named_in($products),
        'eyebrow' => $namesSector ? optelya_study_sector($study) : '',
        'title' => $study['title'],
        'text' => $study['summary'],
        'products' => $products,
        'link' => optelya_card_link(__('Lire l’étude', 'optelya'), OptelyaPostType::Study, $study['url']),
    ];
}

/**
 * Figure cards first, then summaries: the cards of a list of studies.
 *
 * @param list<array<string, mixed>> $studies
 * @return list<array<string, mixed>>
 */
function optelya_study_cards(array $studies, bool $namesSector = true): array
{
    return array_map(static fn(array $study): array => optelya_study_card($study['id'], $namesSector) ?? optelya_study_summary_card($study, $namesSector), $studies);
}

/**
 * A solution as a card of the solutions list: its phrase, its proof (its
 * chosen studies first), the issue filters it shows under.
 *
 * @return array<string, mixed>
 */
function optelya_solution_card(array $solution): array
{
    $studies = optelya_studies_of([$solution['id']]);
    $figures = array_values(array_filter(array_map('optelya_study_proof', [...array_map('optelya_study', $solution['proofs']), ...$studies])));

    return [
        'vendors' => [optelya_catalog()['vendors'][$solution['vendor']]['title'] ?? ''],
        'title' => $solution['title'],
        'text' => $solution['card_text'],
        'proof' => OptelyaCatalog::proofLine($figures, count($studies)),
        'link' => optelya_view_link($solution, OptelyaPostType::Solution),
        'filters' => array_filter([OptelyaCatalog::ALL_ISSUES, $solution['issue']]),
    ];
}

/**
 * A vendor as a card: its positioning, its products, its seniority.
 *
 * @return array<string, mixed>
 */
function optelya_vendor_card(array $vendor): array
{
    return [
        'title' => $vendor['title'],
        'text' => $vendor['tagline'],
        'products' => optelya_products(array_column(optelya_solutions_of($vendor['id']), 'id')),
        'source' => $vendor['since'],
        /* translators: %s: vendor name */
        'link' => optelya_card_link(sprintf(__('Découvrir %s', 'optelya'), $vendor['title']), OptelyaPostType::Vendor, $vendor['url']),
    ];
}

/**
 * A sector as a card: its phrase, its applications, the figure of its study.
 *
 * @return array<string, mixed>
 */
function optelya_industry_card(array $industry): array
{
    return [
        'title' => $industry['title'],
        'text' => $industry['card_text'],
        'products' => $industry['card_examples'],
        'proof' => optelya_study_proof(optelya_study($industry['card_proof'])),
        'link' => optelya_view_link($industry, OptelyaPostType::Industry),
    ];
}


/**
 * The other published solutions of the same vendor, as cards
 * (components/card.php), without vendor label: the heading names it.
 *
 * @return array{cards: list<array<string, mixed>>, count: int}
 */
function optelya_other_solutions(int $solutionId, int $vendorId): array
{
    $others = array_values(array_filter(optelya_solutions_of($vendorId), static fn(array $solution): bool => $solution['id'] !== $solutionId));

    return [
        'cards' => array_map(static fn(array $solution): array => [
            'title' => $solution['title'],
            'text' => $solution['card_text'],
            'link' => optelya_view_link($solution, OptelyaPostType::Solution),
        ], array_slice($others, 0, OPTELYA_ROW_OF_CARDS)),
        'count' => count($others),
    ];
}

/* Texts written once in the catalogue settings (OptelyaCatalogSetting). */

function optelya_catalog_option(OptelyaCatalogSetting $setting): mixed
{
    return optelya_field($setting->value, 'option');
}

function optelya_catalog_text(OptelyaCatalogSetting $setting): string
{
    return optelya_text(optelya_catalog_option($setting));
}

/**
 * Title and intro of a section (part « titre de section »), « {nom} » replaced
 * by the name of the page.
 *
 * @return array{title: string, intro: string}
 */
function optelya_heading(OptelyaCatalogSetting $setting, string $name = ''): array
{
    $heading = (array) optelya_catalog_option($setting);

    return [
        'title' => OptelyaCatalog::withName(optelya_text($heading['title'] ?? ''), $name),
        'intro' => OptelyaCatalog::withName(optelya_text($heading['intro'] ?? ''), $name),
    ];
}

/**
 * A link written in the settings: aimed at the homepage's contact block, it
 * opens the page's own (every list and page of the catalogue has one).
 *
 * @return array{label: string, url: string, target: string}
 */
function optelya_local_link(mixed $value): array
{
    $link = optelya_link($value);
    $link['url'] = OptelyaCatalog::localAnchor($link['url'], OptelyaSection::Contact->value, true);

    return $link;
}

/**
 * Head of a list page (part « en-tête de liste »), as components/hero.php
 * takes it. The buttons aimed at the homepage's contact block open the page's.
 *
 * @return array<string, mixed>
 */
function optelya_page_head(OptelyaCatalogSetting $setting): array
{
    $head = (array) optelya_catalog_option($setting);

    return [
        'eyebrow' => optelya_text($head['eyebrow'] ?? ''),
        'title' => optelya_text($head['title'] ?? ''),
        'lead' => optelya_text($head['lead'] ?? ''),
        'primary' => optelya_local_link($head['primary_link'] ?? null),
        'secondary' => optelya_local_link($head['secondary_link'] ?? null),
    ];
}

/**
 * Help line and pills (part « bandeau d'aide »), as components/sectors-band.php
 * takes them.
 *
 * @return array<string, mixed>
 */
function optelya_band(OptelyaCatalogSetting $setting): array
{
    $band = (array) optelya_catalog_option($setting);

    return [
        'help_text' => optelya_text($band['help_text'] ?? ''),
        'help_link' => optelya_local_link($band['help_link'] ?? null),
        'label' => optelya_text($band['label'] ?? ''),
        'links' => array_map(static fn(array $row): array => optelya_link($row['link'] ?? null), optelya_rows($band['links'] ?? null)),
    ];
}

/**
 * The visual of a page's head (part « visuel »), its placeholder until one is
 * chosen.
 *
 * @return array{poster_id: int, caption: string, placeholder_label: string, placeholder_text: string}
 */
function optelya_head_media(int $postId, string $placeholderLabel, string $placeholderText): array
{
    return [
        'poster_id' => optelya_attachment_id(optelya_field('visual', $postId)),
        'caption' => optelya_text(optelya_field('visual_caption', $postId)),
        'placeholder_label' => $placeholderLabel,
        'placeholder_text' => $placeholderText,
    ];
}

/**
 * The demonstration video of a page (part « vidéo »), as
 * components/video-card.php takes it; null without a video.
 *
 * @return array<string, string>|null
 */
function optelya_video(int $postId, string $name): ?array
{
    $videoId = optelya_attachment_id(optelya_field('video_file', $postId));

    if ($videoId <= 0) {
        return null;
    }

    return [
        'video_url' => (string) wp_get_attachment_url($videoId),
        'poster_url' => (string) wp_get_attachment_image_url(optelya_attachment_id(optelya_field('video_poster', $postId)), 'large'),
        'captions_url' => (string) wp_get_attachment_url(optelya_attachment_id(optelya_field('video_captions', $postId))),
        /* translators: %s: product or vendor name */
        'title' => sprintf(__('Lire la vidéo de démonstration %s', 'optelya'), $name),
        /* translators: %s: product or vendor name */
        'player_label' => sprintf(__('Vidéo de démonstration %s', 'optelya'), $name),
        'meta' => implode(' · ', array_filter([optelya_text(optelya_field('video_duration', $postId)), __('sous-titres français', 'optelya')])),
        'transcript' => optelya_text(optelya_field('video_transcript', $postId)),
    ];
}

/**
 * Background of the sections of a page: every other section shown sits on the
 * band, whatever optional section is left out.
 *
 * @return Closure(): string
 */
function optelya_section_classes(bool $startOnBand = false): Closure
{
    $isBand = $startOnBand;

    return static function () use (&$isBand): string {
        $class = 'section' . ($isBand ? ' section--band' : '');
        $isBand = ! $isBand;

        return $class;
    };
}

/**
 * The contact block of a page, its title first from the page, then from the
 * settings of its list.
 *
 * @return array<string, mixed>
 */
function optelya_contact_args(string $title, string $fallback, string $name = ''): array
{
    return ['title' => $title !== '' ? $title : OptelyaCatalog::withName($fallback, $name)] + optelya_contact_texts();
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
    ], optelya_rows(optelya_catalog_option(OptelyaCatalogSetting::Readers)));

    return OptelyaCatalog::readersWithVendor($panels, optelya_text(optelya_field('since', $vendorId)));
}

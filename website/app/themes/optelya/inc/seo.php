<?php

/**
 * Search engines and sharing — no SEO plugin on the site. Title, description
 * and sharing image come from Settings → Configuration. See readme/seo.md.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Pages, and the content types whose page is built (optelya_built_types()):
 * indexed and in the sitemap. A type without template yet (studies) and its
 * list answer `noindex`; shipping the template is enough to index it.
 *
 * @return list<string>
 */
function optelya_indexed_post_types(): array
{
    return ['page', ...optelya_built_types()];
}

function optelya_is_indexed_archive(): bool
{
    return optelya_current_list() !== null && ! is_paged();
}

/**
 * Pages worth describing and sharing: not the 404, the search or a bare list.
 */
function optelya_is_shareable(): bool
{
    return is_front_page() || is_singular() || optelya_is_indexed_archive();
}

function optelya_is_indexable(): bool
{
    return is_front_page() || is_singular(optelya_indexed_post_types()) || optelya_is_indexed_archive();
}

/**
 * Head of the current list (its settings), if the page is one.
 */
function optelya_list_head(): ?OptelyaCatalogSetting
{
    $list = optelya_current_list();

    return $list !== null ? OptelyaCatalogSetting::listHead($list) : null;
}

/**
 * Address of the current page without its parameters (a list filtered by
 * ?enjeu=… is the same page).
 */
function optelya_current_url(): string
{
    return match (true) {
        is_front_page() => home_url('/'),
        optelya_is_indexed_archive() => (string) get_post_type_archive_link((string) optelya_current_list()?->value),
        default => (string) wp_get_canonical_url(),
    };
}

/**
 * WordPress marks the canonical address of single pages only: the lists get
 * theirs here.
 */
function optelya_print_archive_canonical(): void
{
    if (optelya_is_indexed_archive()) {
        printf('<link rel="canonical" href="%s">' . "\n", esc_url(optelya_current_url()));
    }
}
add_action('wp_head', 'optelya_print_archive_canonical', 1);

/**
 * An empty page in the search results does more harm than no page: the ones
 * without a template stay out, their links still followed.
 *
 * @param array<string, bool|string> $robots
 * @return array<string, bool|string>
 */
function optelya_noindex_unbuilt_pages(array $robots): array
{
    return optelya_is_indexable() || is_404() || is_search() ? $robots : wp_robots_no_robots($robots);
}
add_filter('wp_robots', 'optelya_noindex_unbuilt_pages');

/**
 * A list is titled by its head (« 7 logiciels, 1 formation : partez de votre
 * enjeu »), a vendor by its products (« Sigmetrix : CETOL 6σ, EZtol… »): the
 * words searched, not a bare « Solutions ».
 *
 * @param array<string, string> $parts
 * @return array<string, string>
 */
function optelya_catalog_document_title(array $parts): array
{
    $head = optelya_list_head();
    $title = $head !== null ? rtrim(optelya_page_head($head)['title'], ' .') : '';

    if ($title !== '') {
        return ['title' => $title] + $parts;
    }

    if (is_singular(OptelyaPostType::Vendor->value)) {
        $products = array_column(optelya_solutions_of((int) get_queried_object_id()), 'title');

        if ($products !== []) {
            /* translators: 1: vendor name, 2: its products */
            $parts['title'] = sprintf(__('%1$s : %2$s', 'optelya'), $parts['title'] ?? '', implode(', ', $products));
        }
    }

    return $parts;
}
add_filter('document_title_parts', 'optelya_catalog_document_title');

function optelya_front_page_title(array $parts): array
{
    $title = is_front_page() ? optelya_option_text(OptelyaSetting::SeoTitle) : '';

    return $title === '' ? $parts : ['title' => $title];
}
add_filter('document_title_parts', 'optelya_front_page_title');

/**
 * What the organisation does, in one or two sentences: the homepage's
 * description and the organisation's in the structured data.
 */
function optelya_site_description(): string
{
    $description = optelya_option_text(OptelyaSetting::MetaDescription);

    return $description !== '' ? $description : optelya_option_text(OptelyaSetting::Tagline);
}

/**
 * Each page its own description, or none: the homepage's repeated everywhere
 * would describe none of them.
 */
function optelya_meta_description(): string
{
    if (is_front_page()) {
        return optelya_site_description();
    }

    $postId = (int) get_queried_object_id();
    $listHead = optelya_list_head();

    return optelya_text(match (true) {
        is_singular(OptelyaPostType::Solution->value) => optelya_field('promise', $postId),
        is_singular(OptelyaPostType::Industry->value) => optelya_field('lead', $postId),
        is_singular(OptelyaPostType::Vendor->value) => optelya_field('tagline', $postId),
        $listHead !== null => optelya_page_head($listHead)['lead'],
        default => '',
    });
}

/**
 * The visual of a solution, industry or vendor on its page, the site's
 * sharing image elsewhere, cropped to the size of the previews.
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function optelya_share_image(): ?array
{
    $id = is_singular([OptelyaPostType::Solution->value, OptelyaPostType::Industry->value, OptelyaPostType::Vendor->value]) ? optelya_attachment_id(optelya_field('visual', (int) get_queried_object_id())) : 0;
    $id = $id > 0 ? $id : optelya_attachment_id(optelya_option(OptelyaSetting::ShareImage));
    $source = $id > 0 ? wp_get_attachment_image_src($id, OPTELYA_SHARE_IMAGE_SIZE) : false;

    if (! is_array($source)) {
        return null;
    }

    return [
        'url' => (string) $source[0],
        'width' => (int) $source[1],
        'height' => (int) $source[2],
        'alt' => trim((string) get_post_meta($id, '_wp_attachment_image_alt', true)),
    ];
}

/**
 * schema.org graph: the organisation and its website on every page, then on a
 * page of the catalogue the page itself, its breadcrumb and, on a solution,
 * its product.
 *
 * @param array{url: string, width: int, height: int, alt: string}|null $image
 * @return array{'@context': string, '@graph': list<array<string, mixed>>}
 */
function optelya_structured_data(string $url, ?array $image): array
{
    $home = home_url('/');
    $name = get_bloginfo('name');
    $logoId = optelya_logo_id();
    $nodes = [
        OptelyaShareTags::organization(
            $name,
            $home,
            $logoId > 0 ? (string) wp_get_attachment_image_url($logoId, 'full') : '',
            optelya_site_description(),
            OptelyaShareTags::profileUrls(optelya_option_text(OptelyaSetting::SocialProfiles)),
        ),
        OptelyaShareTags::website($name, $home, get_bloginfo('language')),
    ];

    $breadcrumb = optelya_catalog_breadcrumb();
    $isSolution = is_singular(OptelyaPostType::Solution->value);

    if ($breadcrumb !== []) {
        $nodes[] = OptelyaShareTags::webPage($url, wp_get_document_title(), $home, $isSolution ? OptelyaShareTags::productId($url) : '', is_post_type_archive() ? 'CollectionPage' : 'WebPage');
        $nodes[] = OptelyaShareTags::breadcrumb($breadcrumb, $url);
    }

    if ($isSolution) {
        $solutionId = (int) get_queried_object_id();
        $vendorId = (int) optelya_field('vendor', $solutionId);
        $nodes[] = OptelyaShareTags::product(
            get_the_title($solutionId),
            optelya_meta_description(),
            $url,
            $vendorId > 0 ? get_the_title($vendorId) : '',
            $image['url'] ?? '',
        );
    }

    return OptelyaShareTags::graph($nodes);
}

function optelya_print_head_meta(): void
{
    if (! optelya_is_shareable()) {
        return;
    }

    $description = optelya_meta_description();

    if ($description !== '') {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
    }

    $url = optelya_current_url();
    $image = optelya_share_image();

    foreach (OptelyaShareTags::meta(wp_get_document_title(), $description, $url, get_bloginfo('name'), get_locale(), $image) as [$attribute, $key, $content]) {
        printf('<meta %s="%s" content="%s">' . "\n", esc_attr($attribute), esc_attr($key), esc_attr($content));
    }

    // A data block, not a script: `script-src 'self'` does not apply.
    // JSON_HEX_TAG: no `</script>` can close it early.
    printf(
        '<script type="application/ld+json">%s</script>' . "\n",
        (string) wp_json_encode(optelya_structured_data($url, $image), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG),
    );
}
add_action('wp_head', 'optelya_print_head_meta', 1);

/* Sitemap: pages only — no accounts, no categories. ------------------------ */

add_filter('wp_sitemaps_add_provider', static fn(mixed $provider, string $name): mixed => $name === 'users' ? false : $provider, 10, 2);
add_filter('wp_sitemaps_post_types', static fn(array $types): array => array_intersect_key($types, array_flip(optelya_indexed_post_types())));
add_filter('wp_sitemaps_taxonomies', '__return_empty_array');

/**
 * Archives, feeds and attachment pages answer 404: the site has no blog, and
 * they only duplicate or thin out its content (SEO audit, 05/11/2026). So do
 * the « pages » of the homepage (/page/2/) and of the lists, copies of them
 * (SEO audit, 08/11/2026): a list shows all its content on one page.
 */
function optelya_refuse_unused_archives(): void
{
    if (! (is_date() || is_category() || is_tag() || is_attachment() || is_feed() || ((is_front_page() || is_post_type_archive()) && is_paged()))) {
        return;
    }

    optelya_force_404();
}
add_action('template_redirect', 'optelya_refuse_unused_archives', 1);

function optelya_remove_feed_links(): void
{
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', 'optelya_remove_feed_links');

/**
 * The lists read the cached catalogue (optelya_catalog()): their main query
 * only needs to know the list exists.
 */
function optelya_light_archive_query(WP_Query $query): void
{
    if ($query->is_main_query() && ! is_admin() && $query->is_post_type_archive(optelya_built_types(true))) {
        $query->set('posts_per_page', 1);
        $query->set('no_found_rows', true);
    }
}
add_action('pre_get_posts', 'optelya_light_archive_query');

/**
 * The built lists in the sitemap: WordPress lists the contents, not their lists.
 */
function optelya_register_archives_sitemap(): void
{
    if (! class_exists('WP_Sitemaps_Provider')) {
        return;
    }

    wp_register_sitemap_provider('archives', new class extends WP_Sitemaps_Provider {
        public function __construct()
        {
            $this->name = 'archives';
            $this->object_type = 'archive';
        }

        /**
         * `mixed`: WP_Sitemaps_Provider types none of its parameters.
         *
         * @return list<array{loc: string}>
         */
        public function get_url_list(mixed $page_num, mixed $object_subtype = ''): array
        {
            // One page: a second one answers 404 instead of repeating the first.
            return (int) $page_num === 1 ? array_map(static fn(string $type): array => ['loc' => (string) get_post_type_archive_link($type)], optelya_built_types(true)) : [];
        }

        public function get_max_num_pages(mixed $object_subtype = ''): int
        {
            return 1;
        }
    });
}
// Registered everywhere; served only where the sitemaps are on (production).
add_action('wp_sitemaps_init', 'optelya_register_archives_sitemap');

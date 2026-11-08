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
 * Content types whose page is built: indexed and listed in the sitemap. The
 * others (studies, vendors, industries) and every list answer `noindex` until
 * their template ships — then add the type here.
 */
const OPTELYA_INDEXED_POST_TYPES = ['page', OptelyaPostType::Solution->value];

/**
 * Pages worth describing and sharing: not the 404, the search or an archive.
 */
function optelya_is_shareable(): bool
{
    return is_front_page() || is_singular();
}

function optelya_is_indexable(): bool
{
    return is_front_page() || is_singular(OPTELYA_INDEXED_POST_TYPES);
}

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

    return is_singular(OptelyaPostType::Solution->value) ? optelya_text(optelya_field('promise', (int) get_queried_object_id())) : '';
}

/**
 * The solution's visual on its page, the site's sharing image elsewhere,
 * cropped to the size of the previews.
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function optelya_share_image(): ?array
{
    $id = is_singular(OptelyaPostType::Solution->value) ? optelya_attachment_id(optelya_field('visual', (int) get_queried_object_id())) : 0;
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
 * schema.org graph: the organisation and its website on every page, then
 * the page, its breadcrumb and its product on a solution.
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

    if (is_singular(OptelyaPostType::Solution->value)) {
        $solutionId = (int) get_queried_object_id();
        $vendorId = (int) optelya_field('vendor', $solutionId);
        $nodes[] = OptelyaShareTags::webPage($url, wp_get_document_title(), $home, OptelyaShareTags::productId($url));
        $nodes[] = OptelyaShareTags::breadcrumb(optelya_solution_breadcrumb($solutionId), $url);
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

    $url = is_front_page() ? home_url('/') : (string) wp_get_canonical_url();
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
add_filter('wp_sitemaps_post_types', static fn(array $types): array => array_intersect_key($types, array_flip(OPTELYA_INDEXED_POST_TYPES)));
add_filter('wp_sitemaps_taxonomies', '__return_empty_array');

/**
 * Archives, feeds and attachment pages answer 404: the site has no blog, and
 * they only duplicate or thin out its content (SEO audit, 05/11/2026). So do
 * the « pages » of the homepage (/page/2/), copies of it (SEO audit, 08/11/2026).
 */
function optelya_refuse_unused_archives(): void
{
    if (! (is_date() || is_category() || is_tag() || is_attachment() || is_feed() || (is_front_page() && is_paged()))) {
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

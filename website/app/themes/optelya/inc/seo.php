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
 * Post types listed in the sitemap: the site has no blog. A future content
 * type (case studies…) must be added here.
 */
const OPTELYA_SITEMAP_POST_TYPES = ['page'];

/**
 * Pages worth describing and sharing: not the 404, the search or an archive.
 */
function optelya_is_shareable(): bool
{
    return is_front_page() || is_singular();
}

function optelya_front_page_title(array $parts): array
{
    $title = is_front_page() ? optelya_option_text(OptelyaSetting::SeoTitle) : '';

    return $title === '' ? $parts : ['title' => $title];
}
add_filter('document_title_parts', 'optelya_front_page_title');

function optelya_meta_description(): string
{
    $description = optelya_option_text(OptelyaSetting::MetaDescription);

    return $description !== '' ? $description : optelya_option_text(OptelyaSetting::Tagline);
}

/**
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function optelya_share_image(): ?array
{
    $id = optelya_attachment_id(optelya_option(OptelyaSetting::ShareImage));
    $source = $id > 0 ? wp_get_attachment_image_src($id, 'full') : false;

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
    $tags = OptelyaShareTags::meta(wp_get_document_title(), $description, $url, get_bloginfo('name'), get_locale(), optelya_share_image());

    foreach ($tags as [$attribute, $key, $content]) {
        printf('<meta %s="%s" content="%s">' . "\n", esc_attr($attribute), esc_attr($key), esc_attr($content));
    }

    if (is_front_page()) {
        $logoId = optelya_attachment_id(optelya_option(OptelyaSetting::Logo));
        $graph = OptelyaShareTags::graph(
            get_bloginfo('name'),
            home_url('/'),
            get_bloginfo('language'),
            $logoId > 0 ? (string) wp_get_attachment_image_url($logoId, 'full') : '',
        );

        // A data block, not a script: `script-src 'self'` does not apply.
        // JSON_HEX_TAG: no `</script>` can close it early.
        printf(
            '<script type="application/ld+json">%s</script>' . "\n",
            (string) wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG),
        );
    }
}
add_action('wp_head', 'optelya_print_head_meta', 1);

/* Sitemap: pages only — no accounts, no categories. ------------------------ */

add_filter('wp_sitemaps_add_provider', static fn(mixed $provider, string $name): mixed => $name === 'users' ? false : $provider, 10, 2);
add_filter('wp_sitemaps_post_types', static fn(array $types): array => array_intersect_key($types, array_flip(OPTELYA_SITEMAP_POST_TYPES)));
add_filter('wp_sitemaps_taxonomies', '__return_empty_array');

/**
 * Archives, feeds and attachment pages answer 404: the site has no blog, and
 * they only duplicate or thin out its content (SEO audit, 05/11/2026).
 */
function optelya_refuse_unused_archives(): void
{
    if (! (is_date() || is_category() || is_tag() || is_attachment() || is_feed())) {
        return;
    }

    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
}
add_action('template_redirect', 'optelya_refuse_unused_archives', 1);

function optelya_remove_feed_links(): void
{
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', 'optelya_remove_feed_links');

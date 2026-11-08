<?php

/**
 * Sharing metadata: Open Graph (LinkedIn, Facebook…), Twitter card and the
 * schema.org graph. PURE — the template gathers the values, this builds the
 * tags — hence tests/Unit/ShareTagsTest.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaShareTags
{
    /**
     * `<meta>` tags as [attribute, key, content]: Open Graph uses `property`,
     * Twitter `name`. Empty values are left out.
     *
     * @param array{url: string, width: int, height: int, alt: string}|null $image
     *
     * @return list<array{0: string, 1: string, 2: string}>
     */
    public static function meta(string $title, string $description, string $url, string $siteName, string $locale, ?array $image): array
    {
        $tags = [
            ['property', 'og:type', 'website'],
            ['property', 'og:site_name', $siteName],
            ['property', 'og:locale', $locale],
            ['property', 'og:title', $title],
            ['property', 'og:description', $description],
            ['property', 'og:url', $url],
            ['name', 'twitter:card', $image === null ? 'summary' : 'summary_large_image'],
        ];

        if ($image !== null) {
            $tags[] = ['property', 'og:image', $image['url']];
            $tags[] = ['property', 'og:image:width', (string) $image['width']];
            $tags[] = ['property', 'og:image:height', (string) $image['height']];
            $tags[] = ['property', 'og:image:alt', $image['alt']];
        }

        return array_values(array_filter($tags, static fn(array $tag): bool => $tag[2] !== ''));
    }

    /**
     * schema.org graph of a page: the organisation and its website, then the
     * nodes proper to the page (breadcrumb, product…). WordPress titles carry
     * HTML entities (`&#8211;`): JSON wants the characters themselves.
     *
     * @param list<array<string, mixed>> $nodes
     * @return array{'@context': string, '@graph': list<array<string, mixed>>}
     */
    public static function graph(array $nodes): array
    {
        array_walk_recursive($nodes, static function (mixed &$value): void {
            if (is_string($value)) {
                $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        });

        return ['@context' => 'https://schema.org', '@graph' => $nodes];
    }

    public static function organizationId(string $homeUrl): string
    {
        return $homeUrl . '#organization';
    }

    public static function websiteId(string $homeUrl): string
    {
        return $homeUrl . '#website';
    }

    /**
     * @param list<string> $sameAs Its profiles elsewhere (LinkedIn…): who it is, for search and answer engines.
     * @return array<string, mixed>
     */
    public static function organization(string $name, string $homeUrl, string $logoUrl, string $description, array $sameAs): array
    {
        return array_filter([
            '@type' => 'Organization',
            '@id' => self::organizationId($homeUrl),
            'name' => $name,
            'url' => $homeUrl,
            'logo' => $logoUrl,
            'description' => $description,
            'areaServed' => 'FR',
            'sameAs' => $sameAs,
        ], static fn(mixed $value): bool => $value !== '' && $value !== []);
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(string $name, string $homeUrl, string $language): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::websiteId($homeUrl),
            'name' => $name,
            'url' => $homeUrl,
            'inLanguage' => $language,
            'publisher' => ['@id' => self::organizationId($homeUrl)],
        ];
    }

    /**
     * The visible breadcrumb, whose current page has no link of its own.
     *
     * @param list<array{label: string, url: string}> $items
     * @return array<string, mixed>
     */
    public static function breadcrumb(array $items, string $currentUrl): array
    {
        $elements = [];

        foreach ($items as $index => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => $item['url'] !== '' ? $item['url'] : $currentUrl,
            ];
        }

        return ['@type' => 'BreadcrumbList', 'itemListElement' => $elements];
    }

    /**
     * The page itself, attached to the website and published by the
     * organisation; `about` names what it presents.
     *
     * @return array<string, mixed>
     */
    public static function webPage(string $url, string $title, string $homeUrl, string $aboutId): array
    {
        return array_filter([
            '@type' => 'WebPage',
            '@id' => $url . '#webpage',
            'url' => $url,
            'name' => $title,
            'isPartOf' => ['@id' => self::websiteId($homeUrl)],
            'publisher' => ['@id' => self::organizationId($homeUrl)],
            'about' => $aboutId !== '' ? ['@id' => $aboutId] : '',
        ], static fn(mixed $value): bool => $value !== '');
    }

    public static function productId(string $url): string
    {
        return $url . '#product';
    }

    /**
     * A product of the catalogue (software or training), made by its vendor.
     *
     * @return array<string, mixed>
     */
    public static function product(string $name, string $description, string $url, string $vendor, string $imageUrl): array
    {
        return array_filter([
            '@type' => 'Product',
            '@id' => self::productId($url),
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'image' => $imageUrl,
            'manufacturer' => $vendor !== '' ? ['@type' => 'Organization', 'name' => $vendor] : '',
        ], static fn(mixed $value): bool => $value !== '');
    }

    /**
     * Profile addresses typed one per line: anything else than an http(s)
     * address is dropped.
     *
     * @return list<string>
     */
    public static function profileUrls(string $lines): array
    {
        $urls = array_map('trim', preg_split('/\R/', $lines) ?: []);

        return array_values(array_unique(array_filter(
            $urls,
            static fn(string $url): bool => preg_match('#^https?://#i', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) !== false,
        )));
    }
}

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
     * Recommended size of the sharing image, also given in the field's help.
     */
    public const int IMAGE_WIDTH = 1200;

    public const int IMAGE_HEIGHT = 630;

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
     * schema.org graph of the home page: the organisation and its website.
     */
    public static function graph(string $name, string $url, string $language, string $logoUrl): array
    {
        $organization = [
            '@type' => 'Organization',
            '@id' => $url . '#organization',
            'name' => $name,
            'url' => $url,
        ];

        if ($logoUrl !== '') {
            $organization['logo'] = $logoUrl;
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $organization,
                [
                    '@type' => 'WebSite',
                    '@id' => $url . '#website',
                    'name' => $name,
                    'url' => $url,
                    'inLanguage' => $language,
                    'publisher' => ['@id' => $url . '#organization'],
                ],
            ],
        ];
    }
}

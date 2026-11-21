<?php

/**
 * Entries of the application cache (mu-plugin rapkalin-cache, readme/cache.md).
 * Without the module, the theme computes every value at each request.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! interface_exists('RapkalinCacheEntry')) {
    return;
}

enum OptelyaCacheKey: string implements RapkalinCacheEntry
{
    /** The whole catalogue, read by the lists, the pages and the vendor labels of every card. */
    case Catalog = 'optelya_catalog';

    public function ttl(): int
    {
        return DAY_IN_SECONDS;
    }

    public function group(): string
    {
        return 'catalog';
    }

    public function postTypes(): array
    {
        return array_map(static fn(OptelyaPostType $type): string => $type->value, OptelyaPostType::cases());
    }

    public function metaKeys(): array
    {
        // Every field optelya_build_catalog() reads (tests/Unit/CacheKeyTest.php).
        return ['vendor', 'card_text', 'proofs', 'card_examples', 'card_proof', 'tagline', 'since', 'figure', 'result', 'client', 'source', 'summary', 'solutions', 'industry'];
    }

    public function taxonomies(): array
    {
        return [OptelyaTaxonomy::Issue->value];
    }
}

add_filter('rapkalin_cache_entries', static fn(array $entries): array => [...$entries, ...OptelyaCacheKey::cases()]);

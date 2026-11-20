<?php

/**
 * Catalogue rules that need no WordPress. PURE, hence tests/Unit/CatalogTest.php.
 * The glue is inc/catalog.php.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OptelyaCatalog
{
    /**
     * Label of the link to every study of a product; '' when the cards shown
     * already are all of them: never « 0 étude » (zoning v2, decision 11), and
     * no « see all » repeating the only card (decision of 20/11/2026).
     */
    public static function studiesLabel(int $count, string $product, int $shown = 0): string
    {
        return match (true) {
            $count < 1, $count <= $shown => '',
            /* translators: %s: product name */
            $count === 1 => sprintf(__('Voir l’étude %s', 'optelya'), $product),
            /* translators: 1: number of studies, 2: product name */
            default => sprintf(__('Voir les %1$d études %2$s', 'optelya'), $count, $product),
        };
    }

    /**
     * Label of the link to every solution of a vendor, under a few of them;
     * '' when the cards shown already are all of them.
     */
    public static function solutionsLabel(int $count, string $vendor, int $shown): string
    {
        /* translators: 1: number of solutions, 2: vendor name */
        return $count > $shown ? sprintf(__('Voir les %1$d solutions %2$s', 'optelya'), $count, $vendor) : '';
    }

    /**
     * A link to a section of the homepage (`…/#expert`) kept on the current
     * page when it has that section too: the header button then opens the
     * page's own contact block.
     */
    public static function localAnchor(string $url, string $anchor, bool $pageHasIt): string
    {
        return $pageHasIt && parse_url($url, PHP_URL_FRAGMENT) === $anchor ? '#' . $anchor : $url;
    }

    /**
     * Vendors of the products a text names (« Optimus · CETOL 6σ »), in the
     * order the products appear, each once: the labels of a card.
     *
     * @param array<string, string> $vendorOfProduct Product name => vendor name.
     * @return list<string>
     */
    public static function vendorsNamedIn(string $text, array $vendorOfProduct): array
    {
        $found = [];

        foreach ($vendorOfProduct as $product => $vendor) {
            $position = $product === '' ? false : mb_strpos($text, $product);

            if ($position !== false) {
                $found[] = [$position, $vendor];
            }
        }

        usort($found, static fn(array $a, array $b): int => $a[0] <=> $b[0]);

        return array_values(array_unique(array_column($found, 1)));
    }

    /**
     * The reader panels shared by every solution, with the vendor's seniority
     * second in the first dark panel (the technical director's), as in the
     * mockup.
     *
     * @param list<array{reader: string, is_dark: bool, items: list<array{title: string, text: string}>}> $panels
     *
     * @return list<array{reader: string, is_dark: bool, items: list<array{title: string, text: string}>}>
     */
    public static function readersWithVendor(array $panels, string $since): array
    {
        if ($since === '') {
            return $panels;
        }

        foreach ($panels as $index => $panel) {
            if ($panel['is_dark']) {
                array_splice($panels[$index]['items'], 1, 0, [['title' => __('Un éditeur établi', 'optelya'), 'text' => $since]]);

                return $panels;
            }
        }

        return $panels;
    }
}

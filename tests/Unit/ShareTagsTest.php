<?php

/**
 * Sharing metadata of the home page: what LinkedIn and search engines read.
 */

require_once OPTELYA_THEME_DIR . 'inc/enums/OptelyaShareTags.php';

function share_tags(?array $image = null, string $description = 'Description'): array
{
    $tags = [];

    foreach (OptelyaShareTags::meta('Titre', $description, 'https://example.test/', 'Optelya', 'fr_FR', $image) as [$attribute, $key, $content]) {
        $tags[$key] = [$attribute, $content];
    }

    return $tags;
}

it('describes the page for Open Graph, Twitter reading it with `name`', function () {
    $tags = share_tags();

    expect($tags['og:title'])->toBe(['property', 'Titre']);
    expect($tags['og:url'])->toBe(['property', 'https://example.test/']);
    expect($tags['og:locale'])->toBe(['property', 'fr_FR']);
    expect($tags['twitter:card'])->toBe(['name', 'summary']);
    expect($tags)->not->toHaveKey('og:image');
});

it('announces a large card with the image, its size and its alternative', function () {
    $tags = share_tags(['url' => 'https://example.test/i.jpg', 'width' => 1200, 'height' => 630, 'alt' => 'Pièce simulée']);

    expect($tags['twitter:card'])->toBe(['name', 'summary_large_image']);
    expect($tags['og:image'][1])->toBe('https://example.test/i.jpg');
    expect($tags['og:image:width'][1])->toBe('1200');
    expect($tags['og:image:alt'][1])->toBe('Pièce simulée');
});

it('leaves out empty values rather than printing empty tags', function () {
    $tags = share_tags(['url' => 'https://example.test/i.jpg', 'width' => 1200, 'height' => 630, 'alt' => ''], '');

    expect($tags)->not->toHaveKey('og:description');
    expect($tags)->not->toHaveKey('og:image:alt');
});

it('links the website to its publisher in the schema.org graph', function () {
    $graph = OptelyaShareTags::graph('Optelya', 'https://example.test/', 'fr-FR', '');
    [$organization, $website] = $graph['@graph'];

    expect($graph['@context'])->toBe('https://schema.org');
    expect($organization['@type'])->toBe('Organization');
    expect($organization)->not->toHaveKey('logo');
    expect($website['publisher'])->toBe(['@id' => $organization['@id']]);
    expect(OptelyaShareTags::graph('Optelya', 'https://example.test/', 'fr-FR', 'https://example.test/l.png')['@graph'][0]['logo'])
        ->toBe('https://example.test/l.png');
});

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

it('links the website to the organisation, which leaves out what it lacks', function () {
    $organization = OptelyaShareTags::organization('Optelya', 'https://example.test/', '', '', []);
    $website = OptelyaShareTags::website('Optelya', 'https://example.test/', 'fr-FR');

    expect($organization['@type'])->toBe('Organization');
    expect($organization['areaServed'])->toBe('FR');
    expect($organization)->not->toHaveKeys(['logo', 'description', 'sameAs']);
    expect($website['publisher'])->toBe(['@id' => $organization['@id']]);
});

it('says who the organisation is: logo, description and profiles', function () {
    $organization = OptelyaShareTags::organization('Optelya', 'https://example.test/', 'https://example.test/l.png', 'Distributeur', ['https://www.linkedin.com/company/optelya']);

    expect($organization['logo'])->toBe('https://example.test/l.png');
    expect($organization['description'])->toBe('Distributeur');
    expect($organization['sameAs'])->toBe(['https://www.linkedin.com/company/optelya']);
});

it('keeps only the http(s) profile addresses, once each', function () {
    expect(OptelyaShareTags::profileUrls("https://www.linkedin.com/company/optelya\r\n\n  javascript:alert(1)\nlinkedin.com/x\nftp://files.example.test/a\nhttps://www.linkedin.com/company/optelya\nhttp://youtube.com/@optelya "))
        ->toBe(['https://www.linkedin.com/company/optelya', 'http://youtube.com/@optelya']);
});

it('marks up the visible breadcrumb, the current page at its own address', function () {
    $list = OptelyaShareTags::breadcrumb([
        ['label' => 'Accueil', 'url' => 'https://example.test/'],
        ['label' => 'Optimus', 'url' => ''],
    ], 'https://example.test/solutions/optimus/');

    expect($list['@type'])->toBe('BreadcrumbList');
    expect($list['itemListElement'][0])->toBe(['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => 'https://example.test/']);
    expect($list['itemListElement'][1]['item'])->toBe('https://example.test/solutions/optimus/');
});

it('presents a product made by its vendor, on a page published by the organisation', function () {
    $url = 'https://example.test/solutions/optimus/';
    $product = OptelyaShareTags::product('Optimus', 'Promesse', $url, 'Noesis Solutions', '');
    $page = OptelyaShareTags::webPage($url, 'Optimus', 'https://example.test/', $product['@id']);

    expect($product['@type'])->toBe('Product');
    expect($product['manufacturer'])->toBe(['@type' => 'Organization', 'name' => 'Noesis Solutions']);
    expect($product)->not->toHaveKey('image');
    expect($page['about'])->toBe(['@id' => $product['@id']]);
    expect($page['publisher'])->toBe(['@id' => OptelyaShareTags::organizationId('https://example.test/')]);
    expect(OptelyaShareTags::product('Optimus', '', $url, '', ''))->not->toHaveKeys(['manufacturer', 'description']);
});

it('writes the characters of WordPress titles, not their HTML entities', function () {
    $graph = OptelyaShareTags::graph([OptelyaShareTags::webPage('https://example.test/a/', 'Optimus &#8211; Noesis &amp; l&#039;éditeur', 'https://example.test/', '')]);

    expect($graph['@context'])->toBe('https://schema.org');
    expect($graph['@graph'][0]['name'])->toBe('Optimus – Noesis & l\'éditeur');
});

<?php

/**
 * First content of the site, taken word for word from the homepage mockup
 * (ux/accueil-ui/index.html): homepage sections, site settings and menus.
 *
 * Run by bin/init.sh locally and by the deployment on the servers. It returns
 * what it did instead of printing it: the theme does not depend on WP-CLI.
 *
 * ONCE PER ENVIRONMENT: a successful run sets the `optelya_seeded` option, and
 * every later run stops there. A contributor who empties a menu, removes the
 * homepage or unpublishes it is never overruled by the next deployment.
 *
 * Bracketed values are placeholders the client still has to provide.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

const OPTELYA_SEEDED_OPTION = 'optelya_seeded';

/**
 * @return list<string> One line per step.
 */
function optelya_seed(): array
{
    if (get_option(OPTELYA_SEEDED_OPTION)) {
        return ['Contenu initial déjà posé sur cet environnement : rien à faire.'];
    }

    if (! function_exists('update_field')) {
        return ['ACF absent : amorçage du contenu sauté, il sera rejoué.'];
    }

    $log = [optelya_seed_settings(), ...optelya_seed_menus(), optelya_seed_homepage()];
    update_option(OPTELYA_SEEDED_OPTION, optelya_site_version(), false);

    return $log;
}

/**
 * Link field value, in the shape ACF stores it.
 *
 * @return array{title: string, url: string, target: string}
 */
function optelya_seed_link(string $label, string $url): array
{
    return ['title' => $label, 'url' => $url, 'target' => ''];
}

/**
 * The guards below only matter for a site that had content before the
 * `optelya_seeded` option existed.
 */
function optelya_seed_settings(): string
{
    // ACF stores an options field as the `options_<name>` WordPress option.
    if (get_option('options_' . OptelyaSetting::Tagline->value) !== false) {
        return 'Réglages déjà en place.';
    }

    $values = [
        OptelyaSetting::Tagline->value => "Votre problème d'abord, le bon logiciel ensuite.",
        OptelyaSetting::HeaderCta->value => optelya_seed_link('Parler à un expert', OptelyaSection::Contact->url()),
        OptelyaSetting::PhoneLabel->value => '[numéro à fournir]',
        OptelyaSetting::PhoneNumber->value => '+33000000000',
        OptelyaSetting::ContactTitle->value => 'Coordonnées',
        OptelyaSetting::Address->value => '[adresse à fournir]',
    ];

    foreach ($values as $name => $value) {
        update_field($name, $value, 'option');
    }

    return 'Réglages amorcés.';
}

/**
 * @return array<string, array{name: string, links: list<array{0: string, 1: OptelyaSection}>}>
 */
function optelya_seed_menu_definitions(): array
{
    return [
        OptelyaMenuLocation::Header->value => ['name' => 'En-tête', 'links' => [
            ['Solutions ▾', OptelyaSection::Issues],
            ['Industries ▾', OptelyaSection::Sectors],
            ['Études de cas', OptelyaSection::Results],
            ['Services', OptelyaSection::Benefits],
        ]],
        OptelyaMenuLocation::FooterSolutions->value => ['name' => 'Solutions', 'links' => [
            ['Optimiser', OptelyaSection::Issues],
            ['Tolérancer', OptelyaSection::Issues],
            ['Simuler', OptelyaSection::Issues],
        ]],
        OptelyaMenuLocation::FooterResources->value => ['name' => 'Ressources', 'links' => [
            ['Études de cas', OptelyaSection::Results],
            ['Services', OptelyaSection::Benefits],
            ['Contact', OptelyaSection::Contact],
        ]],
        // The legal pages do not exist yet: back to the top, as in the mockup.
        OptelyaMenuLocation::Legal->value => ['name' => 'Mentions', 'links' => [
            ['Mentions légales', OptelyaSection::Top],
            ['Confidentialité', OptelyaSection::Top],
            ['Cookies', OptelyaSection::Top],
        ]],
    ];
}

/**
 * @return list<string>
 */
function optelya_seed_menus(): array
{
    $assigned = get_nav_menu_locations();
    $log = [];

    foreach (optelya_seed_menu_definitions() as $location => $definition) {
        if (($assigned[$location] ?? 0) > 0) {
            $log[] = 'Menu « ' . $definition['name'] . ' » déjà en place.';

            continue;
        }

        $existing = wp_get_nav_menu_object($definition['name']);
        $menuId = $existing instanceof WP_Term ? $existing->term_id : wp_create_nav_menu($definition['name']);

        if (! is_int($menuId)) {
            $log[] = 'Menu « ' . $definition['name'] . ' » non créé.';

            continue;
        }

        if (! $existing instanceof WP_Term) {
            foreach ($definition['links'] as [$label, $section]) {
                wp_update_nav_menu_item($menuId, 0, [
                    'menu-item-title' => $label,
                    'menu-item-url' => $section->url(),
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                ]);
            }
        }

        $assigned[$location] = $menuId;
        $log[] = 'Menu « ' . $definition['name'] . ' » rattaché.';
    }

    set_theme_mod('nav_menu_locations', $assigned);

    return $log;
}

function optelya_seed_homepage(): string
{
    $frontId = (int) get_option('page_on_front');

    if ($frontId > 0 && get_post_status($frontId) === 'publish') {
        return "Page d'accueil déjà en place (ID {$frontId}).";
    }

    $pageId = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Accueil',
    ]);

    if ($pageId === 0) {
        return "Page d'accueil non créée.";
    }

    update_option('show_on_front', 'page');
    update_option('page_on_front', $pageId);
    // Field KEY, not name: a brand-new page has no reference telling ACF which
    // field `sections` is.
    update_field('field_optelya_home_sections', optelya_seed_sections(), $pageId);

    return "Page d'accueil créée (ID {$pageId}).";
}

/**
 * @return list<array<string, mixed>>
 */
function optelya_seed_sections(): array
{
    $expert = optelya_seed_link('Voir les solutions →', OptelyaSection::Contact->url());
    $study = optelya_seed_link("Lire l'étude", OptelyaSection::Results->url());
    $publisherStudy = 'Étude Noesis Solutions';

    return [
        [
            'acf_fc_layout' => 'hero',
            'eyebrow' => 'Noesis Solutions · Sigmetrix · FunctionBay',
            'title' => "Logiciels de simulation et d'optimisation : un expert vous aide à choisir.",
            'lead' => "Votre problème d'abord, le bon logiciel ensuite. Optelya sélectionne parmi trois éditeurs complémentaires et vous accompagne en France.",
            'primary_link' => optelya_seed_link('Parler à un expert', OptelyaSection::Contact->url()),
            'secondary_link' => optelya_seed_link('Voir les résultats obtenus', OptelyaSection::Results->url()),
            'caption' => "Visuels repris des éditeurs pour la maquette. Pas de lecture automatique si l'utilisateur a réduit les animations.",
        ],
        [
            'acf_fc_layout' => 'issues',
            'title' => 'Quel est votre enjeu ?',
            'intro' => 'Partez de votre problème. Nous vous orientons vers le logiciel adapté.',
            'cards' => [
                ['icon' => OptelyaIcon::Optimize->value, 'title' => 'Optimiser une conception', 'text' => 'Automatisez vos chaînes de calcul et trouvez le meilleur compromis.', 'products' => 'Optimus · id8 · nvision', 'link' => $expert],
                ['icon' => OptelyaIcon::Tolerance->value, 'title' => 'Fiabiliser vos tolérances', 'text' => "Reliez exigences fonctionnelles et dispersion géométrique, avant l'outillage.", 'products' => 'CETOL 6σ · EZtol · Varisight', 'link' => $expert],
                ['icon' => OptelyaIcon::Mechanism->value, 'title' => 'Simuler un mécanisme', 'text' => 'Contacts, engrenages, corps flexibles, charges dynamiques.', 'products' => 'RecurDyn', 'link' => $expert],
            ],
            'help_text' => 'Vous hésitez ?',
            'help_link' => optelya_seed_link('Un expert vous oriente.', OptelyaSection::Contact->url()),
            'sectors_label' => 'Ou par secteur :',
            'sectors' => array_map(
                static fn(string $sector): array => ['link' => optelya_seed_link($sector, OptelyaSection::Sectors->url())],
                ['Automobile & Mobilité', 'Aéronautique & Défense', 'Robotique & Automatisation', 'Machines industrielles', 'Off-Road & Agriculture'],
            ),
        ],
        [
            'acf_fc_layout' => 'results',
            'title' => 'Des résultats mesurés',
            'intro' => "Des gains chiffrés, tirés d'études industrielles publiées.",
            'all_link' => optelya_seed_link('Toutes les études de cas', OptelyaSection::Results->url()),
            'cards' => [
                ['figure' => '−25 à −35 %', 'label' => 'de masse sur une structure de bus', 'products' => 'Optimus', 'source' => $publisherStudy, 'link' => $study],
                ['figure' => '−4 %', 'label' => 'de traînée en croisière, grâce au winglet', 'products' => 'Optimus', 'source' => "Subaru (Fuji Heavy Industries à la date de l'étude) · étude Noesis Solutions", 'link' => $study],
                ['figure' => '7,5 λ → 3 λ', 'label' => "d'aberration sur un objectif photo", 'products' => 'CETOL 6σ + Optimus', 'source' => $publisherStudy, 'link' => $study],
            ],
        ],
        [
            'acf_fc_layout' => 'benefits',
            'title' => "Ce qu'Optelya vous apporte",
            'panels' => [
                ['reader' => "Pour l'ingénieur calcul", 'is_dark' => 0, 'items' => [
                    ['title' => 'Démonstration ciblée', 'text' => 'Sur un cas proche du vôtre.'],
                    ['title' => 'Le logiciel montré', 'text' => 'Captures et vidéos des éditeurs.'],
                    ['title' => 'Études détaillées', 'text' => 'Problème, méthode, résultat, PDF.'],
                ]],
                ['reader' => 'Pour le directeur technique', 'is_dark' => 1, 'items' => [
                    ['title' => "Tester avant d'acheter", 'text' => 'Benchmark sur un exemple représentatif.'],
                    ['title' => 'Mettre en route', 'text' => 'Formation des équipes.'],
                    ['title' => 'Un interlocuteur en France', 'text' => 'Un seul contact pour trois éditeurs.'],
                    ['title' => 'Des éditeurs établis', 'text' => 'Noesis depuis 1996 · Sigmetrix 25+ ans.'],
                ]],
            ],
        ],
        [
            'acf_fc_layout' => 'contact',
            'title' => 'Un projet de simulation ? Parlons-en.',
            'lead' => "Un expert vous aide à choisir la bonne approche, quel que soit l'éditeur.",
            'phone_text' => 'appelez-nous directement',
            'steps' => [
                ['text' => "Un expert vous répond, par e-mail ou par téléphone si vous l'avez indiqué."],
                ['text' => 'Vous échangez sur votre enjeu.'],
                ['text' => 'Démonstration ou benchmark sur votre cas.'],
            ],
            'notice' => "Un expert vous répond par e-mail, ou par téléphone si vous l'avez indiqué, sous [délai à fournir]. Vos données servent uniquement à traiter votre demande.",
        ],
    ];
}

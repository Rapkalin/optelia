<?php

/**
 * First content of the lists and of the industry and vendor pages (2.1.0),
 * taken from the validated mockups (ux/maquettes/, G2, G4, G5) — edit it in the
 * admin, not here. Links to the homepage's contact block are written as such:
 * the pages turn them into their own (optelya_page_head()).
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, mixed>
 */
function optelya_seed_catalog_pages_data(): array
{
    $expert = ['title' => 'Parler à un expert', 'url' => '{home}#expert', 'target' => ''];
    $proved = 'Chaque réponse porte sa preuve : une étude publiée, ou la validation d’un expert Optelya.';

    return [
        'options' => [
            'solutions_head' => [
                'eyebrow' => 'Solutions',
                'title' => '7 logiciels, 1 formation : partez de votre enjeu.',
                'lead' => 'Optelya sélectionne parmi trois éditeurs complémentaires. Un expert vous aide à choisir.',
                'primary_link' => $expert,
                'secondary_link' => ['title' => 'Nos 3 éditeurs', 'url' => '#editeurs', 'target' => ''],
            ],
            'solutions_band' => [
                'help_text' => 'Vous hésitez entre deux logiciels ?',
                'help_link' => ['title' => 'Un expert vous oriente', 'url' => '{home}#expert', 'target' => ''],
            ],
            'solutions_filter_label' => 'Enjeux',
            'solutions_all' => ['title' => 'Toutes les solutions', 'intro' => '7 logiciels et 1 formation, de 3 éditeurs.'],
            'solutions_vendors' => ['title' => 'Nos 3 éditeurs', 'intro' => 'Trois spécialistes complémentaires, un seul interlocuteur en France.'],
            'solutions_contact_title' => 'Pas sûr du bon logiciel ? Parlons-en.',
            'vendor_tools_link' => 'Quel outil pour quel besoin ?',
            'vendor_tools' => ['title' => 'Quel outil {nom} pour quel besoin ?', 'intro' => ''],
            'vendor_proofs' => ['title' => '{nom} en situation', 'intro' => 'Des gains mesurés, tirés d’études publiées.'],
            'vendor_demo' => ['title' => '{nom} en démonstration', 'intro' => 'Moins de 3 minutes, sous-titrée.'],
            'vendor_contact_title' => 'Un projet autour de {nom} ? Parlons-en.',
            'industries_head' => [
                'eyebrow' => 'Industries',
                'title' => 'Votre secteur d’abord, le bon logiciel ensuite.',
                'lead' => 'Les secteurs où nos experts accompagnent les équipes de conception et de calcul.',
                'primary_link' => $expert,
            ],
            'industries_choose' => ['title' => 'Choisissez votre secteur', 'intro' => ''],
            'industries_band' => [
                'help_text' => 'Un autre secteur ? Nos études couvrent aussi l’énergie, le médical et l’optique.',
                'label' => 'Vous connaissez votre enjeu ?',
                'links' => [['link' => ['title' => 'Voir les solutions par enjeu', 'url' => '{home}solutions/', 'target' => '']]],
            ],
            'industries_contact_title' => 'Un projet dans votre secteur ? Parlons-en.',
            'industry_studies_link' => 'Voir les études du secteur',
            'industry_near_link' => 'Voir des cas proches',
            'industry_issues' => ['title' => 'Vos enjeux, nos réponses', 'intro' => $proved],
            'industry_figures' => ['title' => 'Les résultats en chiffres', 'intro' => 'Des gains mesurés, tirés d’études publiées.'],
            'industry_studies' => ['title' => 'Autres études du secteur', 'intro' => ''],
            'industry_contact_title' => 'Un projet dans votre secteur ? Parlons-en.',
        ],
        'issues' => [
            'optimiser' => 'Automatiser vos chaînes de calcul et explorer les compromis de conception.',
            'donnees' => 'Réutiliser vos simulations passées pour prédire sans relancer le calcul.',
            'tolerances' => 'Anticiper les dispersions de fabrication avant l’outillage.',
            'mecanismes' => 'Prédire efforts, contacts et vibrations des systèmes en mouvement.',
        ],
        'vendors' => [
            'sigmetrix' => ['tools_intro' => 'Quatre réponses à la même question : vos tolérances tiendront-elles ?'],
        ],
        'industries' => [
            'automobile-mobilite' => [
                'claim' => 'Simulation et optimisation pour l’automobile et la mobilité.',
                'lead' => 'Châssis, transmissions, suspensions, motorisations : concevoir et valider avant les essais physiques.',
                'card_text' => 'Concevoir, valider et optimiser châssis, transmissions et suspensions.',
                'card_examples' => 'Suspensions · Transmissions · NVH',
                'card_proof' => 'bus-mixed-material-optimization',
                'answers' => [
                    ['title' => 'Allègement des structures', 'text' => 'Réduire masse et coût sans perdre en rigidité.', 'solutions' => ['optimus'], 'proof' => 'bus-mixed-material-optimization'],
                    ['title' => 'Motorisations électriques et hybrides', 'text' => 'Rendement, couple et autonomie optimisés sur modèle.', 'solutions' => ['optimus'], 'proof' => 'maplesim-hev-multidomain-optimization'],
                    ['title' => 'Suspensions et liaison au sol', 'text' => 'Confort, tenue de route et stabilité, explorés automatiquement.', 'solutions' => ['optimus', 'id8'], 'proof' => ''],
                    ['title' => 'Transmissions et engrenages', 'text' => 'Contacts, frottements et pertes, simulés en dynamique.', 'solutions' => ['recurdyn'], 'proof' => ''],
                    ['title' => 'Tolérancement fonctionnel', 'text' => 'Sécuriser les assemblages avant l’industrialisation.', 'solutions' => ['cetol-6-sigma', 'eztol'], 'proof' => ''],
                ],
                'contact_title' => 'Un projet en automobile ? Parlons-en.',
            ],
            'aeronautique-defense' => [
                'lead' => 'Simuler mécanismes, actionneurs et tolérances critiques avant les essais.',
                'card_text' => 'Simuler mécanismes, actionneurs et tolérances critiques avant les essais.',
                'card_examples' => 'Trains d’atterrissage · Actionneurs · Mécanismes spatiaux',
                'card_proof' => 'fhi-winglet-optimization',
            ],
            'robotique-automatisation' => [
                'claim' => 'Des robots plus rapides et plus précis, validés par la simulation.',
                'lead' => 'Trajectoires, efforts, vibrations et précision : modéliser la cellule avant de la construire.',
                'card_text' => 'Maîtriser trajectoires, efforts, vibrations et précision des systèmes robotisés.',
                'card_examples' => 'Robots industriels · Cobots · Préhenseurs',
                'answers' => [
                    ['title' => 'Robots industriels', 'text' => 'Cellules de soudage, pick-and-place et assemblage.', 'solutions' => ['recurdyn'], 'proof' => ''],
                    ['title' => 'Préhenseurs', 'text' => 'Contacts, flexibilité et efforts de maintien.', 'solutions' => ['recurdyn'], 'proof' => ''],
                    ['title' => 'Trajectoires et cycles', 'text' => 'Raccourcir le cycle sans surdimensionner la mécanique.', 'solutions' => ['recurdyn', 'optimus'], 'proof' => ''],
                ],
                'no_study_title' => 'Pas encore d’étude publiée en robotique.',
                'no_study_text' => 'Ces cas proches traitent de câbles en mouvement, de lignes d’assemblage et d’actionneurs. Un expert peut aussi vous montrer RecurDyn sur votre propre cellule.',
                'near_studies' => ['optimus-flexstructures-ai-cables-hoses', 'optimus-sogeti-factory-optimization', 'cloud-electromagnetic-optimization'],
                'contact_title' => 'Un projet en robotique ? Parlons-en.',
            ],
            'machines-industrielles' => [
                'lead' => 'Livrer plus vite, avec moins d’itérations physiques.',
                'card_text' => 'Livrer plus vite, avec moins d’itérations physiques.',
                'card_examples' => 'Machines spéciales · Convoyeurs · Réducteurs',
            ],
            'off-road-agriculture' => [
                'lead' => 'Prédire le comportement dynamique en environnement sévère.',
                'card_text' => 'Prédire le comportement dynamique en environnement sévère.',
                'card_examples' => 'Tracteurs · Engins de chantier · Suspensions',
            ],
        ],
    ];
}

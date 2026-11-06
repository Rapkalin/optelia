<?php

/**
 * First content of the catalogue: vendors, issues, industries, solutions and
 * studies (fiche minimale). GENERATED from the prototype, the solution mockup
 * (ux/maquettes/Solutions/G4b - Page solution/maquette/) and the archives zoning (ux/zoning-g2/) — edit the
 * content in the admin, not here. Figures: only the 7 verified in a study PDF
 * (zoning v2, source M-17). Bracketed values are still to be provided.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, mixed>
 */
function optelya_seed_catalog_data(): array
{
    return [
        'vendors' => [
            [
                'slug' => 'noesis-solutions',
                'title' => 'Noesis Solutions',
                'tagline' => 'Automatiser les processus de simulation, explorer les compromis de conception et diffuser les bons workflows aux bonnes personnes.',
                'since' => 'Noesis Solutions développe ses logiciels depuis 1996.',
            ],
            [
                'slug' => 'sigmetrix',
                'title' => 'Sigmetrix',
                'tagline' => 'Relier exigences fonctionnelles et dispersion géométrique, du calcul de chaînes de cotes à la culture qualité et à la formation ISO GPS.',
                'since' => '',
            ],
            [
                'slug' => 'functionbay',
                'title' => 'FunctionBay',
                'tagline' => 'Analyser le comportement dynamique réel des mécanismes : efforts, cinématique, contacts, vibrations et corps flexibles.',
                'since' => '',
            ],
        ],
        'issues' => [
            [
                'slug' => 'optimiser',
                'name' => 'Optimiser et automatiser',
            ],
            [
                'slug' => 'donnees',
                'name' => 'Exploiter vos données de simulation',
            ],
            [
                'slug' => 'tolerances',
                'name' => 'Fiabiliser vos tolérances',
            ],
            [
                'slug' => 'mecanismes',
                'name' => 'Simuler vos mécanismes',
            ],
        ],
        'industries' => [
            [
                'slug' => 'automobile-mobilite',
                'title' => 'Automobile & Mobilité',
                'summary' => 'Optelya accompagne les constructeurs, équipementiers, bureaux d’études et équipes R&D dans la conception, la validation et l’optimisation de systèmes mécaniques complexes : châssis, transmissions, suspensions, robotique d’assemblage, NVH et robustesse dimensionnelle.',
            ],
            [
                'slug' => 'aeronautique-defense',
                'title' => 'Aéronautique & Défense',
                'summary' => 'Les programmes aéronautiques et défense exigent fiabilité, traçabilité et réduction du risque. Optelya aide les équipes R&D à simuler les mécanismes, actionneurs, trains d’atterrissage, systèmes embarqués, procédés d’assemblage et tolérances critiques avant essais physiques.',
            ],
            [
                'slug' => 'robotique-automatisation',
                'title' => 'Robotique & Automatisation',
                'summary' => 'La robotique moderne impose de maîtriser trajectoires, efforts, vitesses, vibrations, précision et sécurité. Optelya intervient sur la modélisation, la validation et l’optimisation des systèmes robotisés, cellules automatisées, cobots, AGV/AMR et machines de production.',
            ],
            [
                'slug' => 'machines-industrielles',
                'title' => 'Machines industrielles',
                'summary' => 'Les fabricants de machines doivent livrer plus vite, avec moins d’itérations physiques et une meilleure robustesse mécanique. Optelya aide à simuler les mécanismes, transmissions, convoyeurs, actionneurs, contacts et tolérances d’assemblage.',
            ],
            [
                'slug' => 'off-road-agriculture',
                'title' => 'Off-Road & Agriculture',
                'summary' => 'Les machines agricoles et off-road évoluent dans des environnements sévères : sols irréguliers, charges élevées, vibrations, contacts, chocs et variabilité d’usage. Optelya aide à prédire le comportement dynamique et à fiabiliser les conceptions.',
            ],
        ],
        'solutions' => [
            [
                'slug' => 'optimus',
                'title' => 'Optimus',
                'vendor' => 'noesis-solutions',
                'issue' => 'optimiser',
                'promise' => 'Automatisez vos chaînes de calcul et trouvez le meilleur compromis de conception. Démonstration possible sur votre cas.',
                'benefits' => [
                    [
                        'title' => 'Automatiser',
                        'text' => 'Vos processus CAE et vos chaînes multi-outils.',
                    ],
                    [
                        'title' => 'Explorer et optimiser',
                        'text' => 'Plans d\'expériences (DOE) et optimisation multi-objectifs.',
                    ],
                    [
                        'title' => 'Fiabiliser',
                        'text' => 'Robustesse et sensibilité de la conception.',
                    ],
                ],
                'choice' => [
                    'title' => 'Optimus, id8 ou nvision ?',
                    'intro' => 'Trois logiciels Noesis Solutions, trois usages.',
                    'choice_items' => [
                        [
                            'solution' => 'optimus',
                            'usage' => 'Automatiser et optimiser vos simulations.',
                        ],
                        [
                            'solution' => 'id8',
                            'usage' => 'Partager des workflows validés dans le cloud.',
                        ],
                        [
                            'solution' => 'nvision',
                            'usage' => 'Prédire à partir de vos données de simulation.',
                        ],
                    ],
                ],
                'details' => [
                    [
                        'title' => 'Intégration de processus',
                        'text' => 'Éditeur de processus CAE : connecter vos outils et automatiser la chaîne de calcul. [Détail à fournir par Noesis Solutions.]',
                    ],
                    [
                        'title' => 'Exploration de l\'espace de conception',
                        'text' => 'Plans d\'expériences (DOE) et modèles de réponse. [Détail à fournir.]',
                    ],
                    [
                        'title' => 'Optimisation',
                        'text' => 'Optimisation multi-objectifs, recherche des meilleurs compromis sous contraintes. [Détail à fournir.]',
                    ],
                    [
                        'title' => 'Robustesse et fiabilité',
                        'text' => 'Analyses de robustesse et de sensibilité. [Détail à fournir.]',
                    ],
                    [
                        'title' => 'Interfaces logicielles',
                        'text' => '[Liste des outils pilotés à fournir par Noesis Solutions.]',
                    ],
                ],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [
                    'bus-mixed-material-optimization',
                    'fhi-winglet-optimization',
                    'noesis-optimus-maric-fpu',
                ],
            ],
            [
                'slug' => 'id8',
                'title' => 'id8',
                'vendor' => 'noesis-solutions',
                'issue' => 'optimiser',
                'promise' => 'Plateforme cloud-native, ouverte et collaborative pour industrialiser les workflows de simulation et partager des applications d’ingénierie.',
                'benefits' => [
                    [
                        'title' => 'Plateforme ouverte et personnalisable',
                        'text' => '',
                    ],
                    [
                        'title' => 'Collaboration ingénierie multi-sites',
                        'text' => '',
                    ],
                    [
                        'title' => 'SimApps et démocratisation CAE',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'Optimus, id8 ou nvision ?',
                    'intro' => 'Trois logiciels Noesis Solutions, trois usages.',
                    'choice_items' => [
                        [
                            'solution' => 'optimus',
                            'usage' => 'Automatiser et optimiser vos simulations.',
                        ],
                        [
                            'solution' => 'id8',
                            'usage' => 'Partager des workflows validés dans le cloud.',
                        ],
                        [
                            'solution' => 'nvision',
                            'usage' => 'Prédire à partir de vos données de simulation.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [],
            ],
            [
                'slug' => 'nvision',
                'title' => 'nvision',
                'vendor' => 'noesis-solutions',
                'issue' => 'donnees',
                'promise' => 'Transformation des données de simulation existantes en modèles prédictifs capables d’évaluer de nouveaux cas en temps quasi réel.',
                'benefits' => [
                    [
                        'title' => 'Modèles surrogate à partir de données CAE',
                        'text' => '',
                    ],
                    [
                        'title' => 'Prédiction en temps quasi réel',
                        'text' => '',
                    ],
                    [
                        'title' => 'Valorisation des simulations passées',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'Optimus, id8 ou nvision ?',
                    'intro' => 'Trois logiciels Noesis Solutions, trois usages.',
                    'choice_items' => [
                        [
                            'solution' => 'optimus',
                            'usage' => 'Automatiser et optimiser vos simulations.',
                        ],
                        [
                            'solution' => 'id8',
                            'usage' => 'Partager des workflows validés dans le cloud.',
                        ],
                        [
                            'solution' => 'nvision',
                            'usage' => 'Prédire à partir de vos données de simulation.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [],
            ],
            [
                'slug' => 'cetol-6-sigma',
                'title' => 'CETOL 6σ',
                'vendor' => 'sigmetrix',
                'issue' => 'tolerances',
                'promise' => 'Analyse de tolérances 3D dans l’environnement CAO pour anticiper les variations de fabrication et les risques d’assemblage.',
                'benefits' => [
                    [
                        'title' => 'Analyse 3D de chaînes de cotes',
                        'text' => '',
                    ],
                    [
                        'title' => 'Visualisation des contributeurs de variation',
                        'text' => '',
                    ],
                    [
                        'title' => 'Réduction des risques avant outillage',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'CETOL 6σ, EZtol, Varisight ou la formation ?',
                    'intro' => 'Quatre offres Sigmetrix, quatre usages.',
                    'choice_items' => [
                        [
                            'solution' => 'cetol-6-sigma',
                            'usage' => 'Analyse de tolérances 3D, directement dans la CAO.',
                        ],
                        [
                            'solution' => 'eztol',
                            'usage' => 'Chaînes de cotes 1D, rapides, dès l\'avant-projet.',
                        ],
                        [
                            'solution' => 'varisight',
                            'usage' => 'Piloter et capitaliser la variation, de la conception aux fournisseurs.',
                        ],
                        [
                            'solution' => 'formation-iso-gps',
                            'usage' => 'Un socle commun ISO GPS / GD&T, en ligne.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [
                    'tack-sharp-imaging-cetol-codev',
                ],
            ],
            [
                'slug' => 'eztol',
                'title' => 'EZtol',
                'vendor' => 'sigmetrix',
                'issue' => 'tolerances',
                'promise' => 'Solution rapide pour les analyses de chaînes de cotes 1D, utile en avant-projet, revue de conception et arbitrage de tolérances.',
                'benefits' => [
                    [
                        'title' => 'Analyses 1D simples et rapides',
                        'text' => '',
                    ],
                    [
                        'title' => 'Worst-case et statistique',
                        'text' => '',
                    ],
                    [
                        'title' => 'Support aux décisions d’avant-projet',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'CETOL 6σ, EZtol, Varisight ou la formation ?',
                    'intro' => 'Quatre offres Sigmetrix, quatre usages.',
                    'choice_items' => [
                        [
                            'solution' => 'cetol-6-sigma',
                            'usage' => 'Analyse de tolérances 3D, directement dans la CAO.',
                        ],
                        [
                            'solution' => 'eztol',
                            'usage' => 'Chaînes de cotes 1D, rapides, dès l\'avant-projet.',
                        ],
                        [
                            'solution' => 'varisight',
                            'usage' => 'Piloter et capitaliser la variation, de la conception aux fournisseurs.',
                        ],
                        [
                            'solution' => 'formation-iso-gps',
                            'usage' => 'Un socle commun ISO GPS / GD&T, en ligne.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [],
            ],
            [
                'slug' => 'varisight',
                'title' => 'Varisight',
                'vendor' => 'sigmetrix',
                'issue' => 'tolerances',
                'promise' => 'Approche orientée pilotage de la variation, capitalisation et analyse des risques dimensionnels dans le développement produit.',
                'benefits' => [
                    [
                        'title' => 'Gestion de la variation produit',
                        'text' => '',
                    ],
                    [
                        'title' => 'Traçabilité des décisions',
                        'text' => '',
                    ],
                    [
                        'title' => 'Culture qualité orientée données',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'CETOL 6σ, EZtol, Varisight ou la formation ?',
                    'intro' => 'Quatre offres Sigmetrix, quatre usages.',
                    'choice_items' => [
                        [
                            'solution' => 'cetol-6-sigma',
                            'usage' => 'Analyse de tolérances 3D, directement dans la CAO.',
                        ],
                        [
                            'solution' => 'eztol',
                            'usage' => 'Chaînes de cotes 1D, rapides, dès l\'avant-projet.',
                        ],
                        [
                            'solution' => 'varisight',
                            'usage' => 'Piloter et capitaliser la variation, de la conception aux fournisseurs.',
                        ],
                        [
                            'solution' => 'formation-iso-gps',
                            'usage' => 'Un socle commun ISO GPS / GD&T, en ligne.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [],
            ],
            [
                'slug' => 'formation-iso-gps',
                'title' => 'Formation ISO GPS',
                'vendor' => 'sigmetrix',
                'issue' => 'tolerances',
                'promise' => 'Former les ingénieurs, concepteurs, qualité et méthodes aux fondamentaux ISO GPS / GD&T, MBD/MBE et analyse de tolérances.',
                'benefits' => [
                    [
                        'title' => 'Formation autonome et progressive',
                        'text' => '',
                    ],
                    [
                        'title' => 'Applications conception, inspection, qualité',
                        'text' => '',
                    ],
                    [
                        'title' => 'Socle commun pour équipes multi-sites',
                        'text' => '',
                    ],
                ],
                'choice' => [
                    'title' => 'CETOL 6σ, EZtol, Varisight ou la formation ?',
                    'intro' => 'Quatre offres Sigmetrix, quatre usages.',
                    'choice_items' => [
                        [
                            'solution' => 'cetol-6-sigma',
                            'usage' => 'Analyse de tolérances 3D, directement dans la CAO.',
                        ],
                        [
                            'solution' => 'eztol',
                            'usage' => 'Chaînes de cotes 1D, rapides, dès l\'avant-projet.',
                        ],
                        [
                            'solution' => 'varisight',
                            'usage' => 'Piloter et capitaliser la variation, de la conception aux fournisseurs.',
                        ],
                        [
                            'solution' => 'formation-iso-gps',
                            'usage' => 'Un socle commun ISO GPS / GD&T, en ligne.',
                        ],
                    ],
                ],
                'details' => [],
                'applications' => [],
                'applications_source' => '',
                'industries' => [],
                'proofs' => [],
            ],
            [
                'slug' => 'recurdyn',
                'title' => 'RecurDyn',
                'vendor' => 'functionbay',
                'issue' => 'mecanismes',
                'promise' => 'Simulez vos mécanismes : contacts, engrenages, corps flexibles, charges dynamiques. Démonstration possible sur votre cas.',
                'benefits' => [
                    [
                        'title' => 'Simuler le mouvement réel',
                        'text' => 'Efforts, cinématique, contacts et vibrations.',
                    ],
                    [
                        'title' => 'Modéliser les transmissions',
                        'text' => 'Engrenages, chaînes, contacts et frottements.',
                    ],
                    [
                        'title' => 'Prendre en compte la flexibilité',
                        'text' => 'Corps flexibles et mouvements rapides.',
                    ],
                ],
                'choice' => [
                    'title' => 'RecurDyn, Optimus ou CETOL 6σ ?',
                    'intro' => 'RecurDyn est le seul logiciel FunctionBay : le choix se fait entre éditeurs.',
                    'choice_items' => [
                        [
                            'solution' => 'recurdyn',
                            'usage' => 'Simuler le mouvement réel d\'un mécanisme.',
                        ],
                        [
                            'solution' => 'optimus',
                            'usage' => 'Optimiser la conception, y compris en pilotant un solveur.',
                        ],
                        [
                            'solution' => 'cetol-6-sigma',
                            'usage' => 'Fiabiliser vos tolérances.',
                        ],
                    ],
                ],
                'details' => [
                    [
                        'title' => 'Mécanismes, contacts et frottements',
                        'text' => '[Détail à fournir par FunctionBay.]',
                    ],
                    [
                        'title' => 'Transmissions, engrenages, chaînes',
                        'text' => '[Détail à fournir.]',
                    ],
                    [
                        'title' => 'Corps flexibles et mouvements rapides',
                        'text' => '[Détail à fournir.]',
                    ],
                    [
                        'title' => 'Robotique et machines spéciales',
                        'text' => '[Détail à fournir.]',
                    ],
                ],
                'applications' => [
                    [
                        'title' => 'Véhicules',
                        'text' => 'Comportement dynamique et charges sur chaque composant : véhicule complet, suspension, moteur, embrayage.',
                    ],
                    [
                        'title' => 'Robots',
                        'text' => 'Charge dynamique à chaque liaison, quels que soient les mouvements du robot.',
                    ],
                    [
                        'title' => 'Engins de chantier',
                        'text' => 'Charges sur chaque composant pendant le creusement et le transport.',
                    ],
                ],
                'applications_source' => 'Les applications décrites par FunctionBay.',
                'industries' => [
                    'automobile-mobilite',
                    'robotique-automatisation',
                    'off-road-agriculture',
                ],
                'proofs' => [],
            ],
        ],
        'studies' => [
            [
                'slug' => 'optimus-amesim-fuel-injector-robustness',
                'title' => 'Optimiser la robustesse d’un injecteur diesel avec la simulation système',
                'summary' => 'Optimus pilote un modèle système pour garantir une quantité injectée cible malgré les tolérances et incertitudes réelles.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'big-wheels-rollin-suspension-optimization',
                'title' => 'Réduire les temps de développement des suspensions de poids lourds grâce à l’optimisation multi-objectifs',
                'summary' => 'Optimus automatise l’évaluation de configurations de suspensions tracteur/semi-remorque pour améliorer confort, tenue de route et stabilité.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'bus-mixed-material-optimization',
                'title' => 'Réduire la masse d’un bus grâce à l’optimisation multi-matériaux',
                'summary' => 'Optimus optimise un châssis de bus combinant acier doux et acier haute résistance pour réduire masse et coût.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '−25 à −35 %',
                'result' => 'de masse sur une structure de bus',
                'client' => '',
            ],
            [
                'slug' => 'sensor-cable-optimization-automotive',
                'title' => 'Automatiser l’optimisation du routage de câbles capteurs automobiles',
                'summary' => 'Optimus pilote IPS Cable Simulation pour optimiser un câble capteur sous contraintes de rayon, clips, longueur et environnement véhicule.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'hev-ecodriving-optimization-germany',
                'title' => 'Optimiser la consommation et l’autonomie d’un véhicule hybride sur routes virtuelles',
                'summary' => 'Optimus automatise des essais CarMaker avec modèle MapleSim en FMU pour optimiser les concepts hybrides.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'cloud-macpherson-strut-optimization',
                'title' => 'Optimiser une suspension MacPherson dans le cloud avec id8',
                'summary' => 'id8 déploie un workflow Adams/Optimus cloud pour analyser toe, camber et anti-dive sur une suspension MacPherson.',
                'solutions' => [
                    'optimus',
                    'id8',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'crashbox-collaborative-engineering',
                'title' => 'Concevoir une crash-box automobile grâce à l’ingénierie collaborative',
                'summary' => 'id8 centralise les workflows et résultats pour permettre une conception collaborative d’une crash-box automobile.',
                'solutions' => [
                    'id8',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'simscale-cloud-aerodynamics',
                'title' => 'Accélérer l’analyse aérodynamique véhicule jusqu’à 12x dans le cloud',
                'summary' => 'id8 et SimScale automatisent les calculs CFD véhicule dans le cloud et partagent les résultats avec les équipes projet.',
                'solutions' => [
                    'id8',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'itri-electric-motor-efficiency',
                'title' => 'Augmenter le rendement d’un moteur électrique de véhicule avec Optimus',
                'summary' => 'ITRI optimise un moteur EV par simulations multiphysiques orientées efficacité sur cycle urbain réel.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '+4 %',
                'result' => 'd\'efficacité globale d\'un véhicule électrique',
                'client' => '',
            ],
            [
                'slug' => 'maplesim-hev-multidomain-optimization',
                'title' => 'Améliorer la consommation d’un véhicule hybride de 21 % avec l’optimisation système',
                'summary' => 'Optimus pilote MapleSim pour optimiser un système HEV multi-domaines et trouver le compromis consommation / conformité émissions.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '+21 %',
                'result' => 'de rendement carburant sur un véhicule hybride',
                'client' => '',
            ],
            [
                'slug' => 'maxwell-lean-green-electric-motor',
                'title' => 'Augmenter le couple d’un moteur électrique tout en réduisant le bruit',
                'summary' => 'Optimus orchestre des simulations électromagnétiques et structurelles pour optimiser un moteur IPM.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '+7 %',
                'result' => 'de couple sur un moteur électrique',
                'client' => '',
            ],
            [
                'slug' => 'optimus-flexstructures-ai-cables-hoses',
                'title' => 'Concevoir automatiquement des câbles et durites 3D avec l’IA',
                'summary' => 'Optimus, id8 et IPS Cable Simulation optimisent automatiquement câbles capteurs et flexibles de frein.',
                'solutions' => [
                    'optimus',
                    'id8',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-jmag-ev-motor-design',
                'title' => 'Optimiser un moteur EV avec une approche électromagnétique et structurelle complète',
                'summary' => 'Optimus orchestre JMAG et Ansys pour intégrer électromagnétisme et structure dans la conception moteur EV.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-tno-euro-vi-emissions-controller',
                'title' => 'Optimiser un contrôleur d’émissions camion pour la conformité Euro VI',
                'summary' => 'TNO automatise SIMCAT avec Optimus pour réduire NOx et consommation et rendre le contrôleur robuste.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'cira-tiltrotor-blade-morphing',
                'title' => 'Optimiser les performances d’un tiltrotor grâce aux technologies de morphing',
                'summary' => 'CIRA utilise Optimus pour explorer des concepts de pales morphables conciliant vol stationnaire et croisière.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'fhi-winglet-optimization',
                'title' => 'Réduire la consommation d’un avion grâce à l’optimisation des winglets',
                'summary' => 'FHI couple CFD et FEM dans Optimus pour optimiser les winglets et réduire la traînée sans pénaliser la structure.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '−4 %',
                'result' => 'de traînée en croisière, grâce au winglet',
                'client' => 'Subaru (Fuji Heavy Industries à la date de l\'étude)',
            ],
            [
                'slug' => 'nvision-wing-structure-bird-strike',
                'title' => 'Prédire en temps réel la performance structurelle d’une aile en cas d’impact',
                'summary' => 'nvision réduit le recours aux simulations lourdes pour analyser les impacts bird strike et variantes structurelles.',
                'solutions' => [
                    'nvision',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-shitamachi-bobsleigh-aerodynamics',
                'title' => 'Réduire la traînée d’un bobsleigh grâce à l’optimisation CFD',
                'summary' => 'Optimus automatise CATIA et SC/Tetra pour optimiser l’aérodynamique d’un bobsleigh.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-skoltech-uav-product-development',
                'title' => 'Explorer le développement produit innovant autour d’un UAV avec Optimus',
                'summary' => 'Skoltech utilise Optimus pour former à l’innovation produit autour d’un UAV tube-launched multidisciplinaire.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-sogeti-factory-optimization',
                'title' => 'Optimiser une ligne d’assemblage aéronautique avec Optimus et DELMIA',
                'summary' => 'Sogeti optimise des configurations de ligne CWB selon les cadences de production entre 13 et 20 unités/mois.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'aeronautique-defense',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'noesis-optimus-maric-fpu',
                'title' => 'Automatiser des workflows de simulation complexes et réduire la masse d’une structure offshore',
                'summary' => 'MARIC automatise un workflow multi-outils avec Optimus pour optimiser une unité flottante offshore et réduire la masse tout en améliorant les performances.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '−8 %',
                'result' => 'de masse sur une unité flottante offshore',
                'client' => 'MARIC',
            ],
            [
                'slug' => 'nvision-pressure-vessel',
                'title' => 'Prédire la performance d’un réservoir sous pression avec nvision',
                'summary' => 'nvision exploite des données de simulation pour générer des prédictions rapides sur le comportement d’un réservoir sous pression.',
                'solutions' => [
                    'nvision',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'nvision-simscale-ev-battery-thermal',
                'title' => 'Prédire en temps réel la gestion thermique d’une batterie EV',
                'summary' => 'SimScale génère les simulations cloud et nvision prédit les coefficients thermiques d’un pack batterie EV.',
                'solutions' => [
                    'nvision',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-coet-digital-twin-fpso',
                'title' => 'Réduire les coûts de maintenance FPSO grâce au jumeau numérique',
                'summary' => 'Optimus optimise le placement de capteurs et les modèles prédictifs pour la maintenance structurelle offshore.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-hydrogen-vehicle-energy-management',
                'title' => 'Réduire la consommation d’hydrogène d’un véhicule prototype avec Optimus',
                'summary' => 'Optimus automatise AMESim pour optimiser la stratégie énergétique d’un prototype hydrogène.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-trane-refrigerant-line-fatigue',
                'title' => 'Réduire le temps d’ingénierie et augmenter la durée de vie des lignes frigorifiques',
                'summary' => 'Trane automatise DOE, RSM et optimisation pour limiter les contraintes de fatigue dans les lignes frigorifiques.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'coet-fpso-digital-twin',
                'title' => 'Surveiller la santé structurelle d’un FPSO avec un jumeau numérique optimisé',
                'summary' => 'Optimisation de modèles et stratégies de mesure pour alimenter un jumeau numérique offshore.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'tack-sharp-imaging-cetol-codev',
                'title' => 'Améliorer la qualité optique d’un objectif photo grâce à l’analyse de tolérances',
                'summary' => 'Optimus combine CETOL 6σ et CODE V pour identifier les tolérances mécaniques ayant le plus d’impact sur la qualité optique.',
                'solutions' => [
                    'optimus',
                    'cetol-6-sigma',
                ],
                'industry' => '',
                'figure' => '7,5 λ → 3 λ',
                'result' => 'd\'aberration sur un objectif photo',
                'client' => '',
            ],
            [
                'slug' => 'optimus-te-connectivity-crimp-optimization',
                'title' => 'Optimiser la robustesse des connexions serties avec Optimus',
                'summary' => 'TE Connectivity automatise analyses de sertissage et traction pour améliorer la robustesse des connexions barrel-wire.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => 'automobile-mobilite',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-tolerance-optical-performance',
                'title' => 'Optimiser les tolérances d’un système optique en équilibrant qualité et coûts',
                'summary' => 'Optimus aide à définir les tolérances de concentricité d’un tube optique pour atteindre la qualité sans sur-spécification.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'adaptive-multi-fidelity-design-optimization',
                'title' => 'Optimiser plus vite grâce à l’approche multi-fidélité adaptative',
                'summary' => 'Optimus intègre une stratégie MFEGO pour piloter des simulations de fidélités différentes et réduire le coût de calcul des optimisations.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-asahi-tablet-shape',
                'title' => 'Optimiser la forme d’un comprimé alimentaire entre facilité d’avalement et durabilité',
                'summary' => 'Asahi utilise Optimus RSM pour identifier le meilleur compromis entre avalement, dureté du comprimé et résistance des poinçons.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-bausch-lomb-digital-eye-models',
                'title' => 'Personnaliser les lentilles multifocales grâce aux modèles d’yeux numériques',
                'summary' => 'Bausch & Lomb combine Zemax, MATLAB et Optimus pour corréler modèles optiques et données cliniques.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'home-appliance-simulation-democratization',
                'title' => 'Démocratiser la simulation dans les équipes produit avec id8',
                'summary' => 'id8 permet à un fabricant HVAC d’ouvrir l’accès à des workflows validés, de suivre l’usage des licences et d’accélérer les décisions.',
                'solutions' => [
                    'id8',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'cloud-electromagnetic-optimization',
                'title' => 'Optimiser un actionneur électromagnétique avec la puissance du cloud',
                'summary' => 'id8 et SimScale EM optimisent un solénoïde DC pour maximiser la force, réduire la matière et accélérer les simulations EM.',
                'solutions' => [
                    'id8',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'nvision-heat-sink-simulation',
                'title' => 'Prédire en temps réel les fluctuations thermiques d’un dissipateur',
                'summary' => 'nvision exploite des simulations transitoires pour prédire rapidement le comportement thermique de dissipateurs.',
                'solutions' => [
                    'nvision',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'nvision-recycle-past-simulation-data',
                'title' => 'Recycler les données de simulation passées pour accélérer les futurs développements',
                'summary' => 'nvision transforme les données CAE historiques en modèles prédictifs réutilisables dans Optimus.',
                'solutions' => [
                    'optimus',
                    'nvision',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-additive-manufacturing-repair',
                'title' => 'Intégrer la réparation additive dès la conception grâce à l’optimisation multidisciplinaire',
                'summary' => 'Optimus intègre conception paramétrique, dommage et réparation additive dans une optimisation multi-objectifs.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
            [
                'slug' => 'optimus-coronary-stent-cfd',
                'title' => 'Optimiser des stents coronaires patient-spécifiques par CFD',
                'summary' => 'Optimus combine morphing et CFD pour améliorer la circulation sanguine locale avec des stents personnalisés.',
                'solutions' => [
                    'optimus',
                ],
                'industry' => '',
                'figure' => '',
                'result' => '',
                'client' => '',
            ],
        ],
        'readers' => [
            'title' => 'Ce qu\'Optelya ajoute',
            'panels' => [
                [
                    'reader' => 'Pour l\'ingénieur calcul',
                    'is_dark' => false,
                    'items' => [
                        [
                            'title' => 'Démonstration ciblée',
                            'text' => 'Sur votre cas.',
                        ],
                        [
                            'title' => 'Formation',
                            'text' => 'Prise en main et bonnes pratiques.',
                        ],
                    ],
                ],
                [
                    'reader' => 'Pour le directeur technique',
                    'is_dark' => true,
                    'items' => [
                        [
                            'title' => 'Tester avant d\'acheter',
                            'text' => 'Benchmark sur un exemple représentatif.',
                        ],
                        [
                            'title' => 'Un interlocuteur en France',
                            'text' => 'Un seul contact pour les trois éditeurs.',
                        ],
                    ],
                ],
            ],
        ],
    ];
}

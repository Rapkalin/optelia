# Qualité du code

`composer check` enchaîne trois outils sur le PHP du projet (thème +
mu-plugins). Il tourne au pré-commit (`.githooks/pre-commit`) et en CI.

> Dans le conteneur : `docker compose exec php composer <script>`. Le hook
> choisit le conteneur quand il tourne, l'hôte sinon.

| Outil | Rôle | Config |
| --- | --- | --- |
| **Pint** (preset PER) | Style | `pint.json` |
| **PHPCS + Slevomat** | Types natifs obligatoires (paramètre, retour, propriété) | `phpcs.xml` |
| **PHPStan** niveau 6 | Analyse statique | `phpstan.neon` |
| **Pest** | Tests, hors WordPress | `tests/` |

> **Modules maison** (`rapkalin-*`, paquets Composer) : ni analysés ni testés
> ici, chacun a sa CI dans son dépôt. PHPStan connaît seulement les symboles de
> `rapkalin-cache` (`scanDirectories`), que le thème implémentera.

```bash
composer check      # Pint + PHPCS + PHPStan
composer lint:fix   # corrige le style
composer types:fix  # corrige les types quand c'est automatisable
composer test       # Pest
composer setup-hooks
```

## Règles

- Accolade de fonction à la ligne, `:` du type de retour collé à `)`.
- Pas de condition Yoda (`$field === null`).
- Toute neutralisation PHPStan : `ignoreErrors` ciblé et commenté dans
  `phpstan.neon`, jamais de `@phpstan-ignore` dans le code.
- Pint se lance toujours en scan complet : avec des chemins explicites, il
  ignore le `exclude` de `pint.json`.

## Organisation du thème

| Dossier | Contenu |
| --- | --- |
| `inc/` | Branchements WordPress (hooks, filtres), un fichier par sujet |
| `inc/enums/` | **Sources uniques** : enums (sections, réglages, rôles, champs…) et classes `final` de **logique pure**, sans WordPress (`OptelyaContactForm`, `OptelyaHeaderColor`, `OptelyaShareTags`, `OptelyaAdminScreen`) — c'est ce qui les rend testables |
| `components/` | Morceaux d'interface réutilisés par plusieurs gabarits : ils reçoivent des **tableaux déjà prêts** (`$args`) et ne lisent jamais ACF (hero, cartes, cartes chiffrées, panneaux par lecteur, bloc contact, fil d'Ariane, aiguillage, vidéo au clic, dépliants, lien, icône…) |
| `layouts/` | Une section de l'accueil par fichier, du nom de son layout ACF : un **adaptateur** qui lit les sous-champs et appelle le composant |
| racine | Gabarits de page (`front-page.php`, `single-optelya_solution.php`) : lisent les champs de la fiche et appellent les composants |
| `admin/` | Vues des écrans d'administration du thème (*Contact → Tableau de bord*) |
| `assets/styles/basics/` | Variables (toutes les couleurs, tailles, poids, espacements) et mixins (`list-reset`, `display-heading`, `eyebrow`, `stack`, `below`…) |

Une valeur partagée entre fichiers de natures différentes (PHP ↔ JSON ACF ↔
SCSS) ne peut pas être une seule constante : un **test** la garde alignée
(couleur d'encre et couleur d'en-tête par défaut, slug de *Configuration*,
layouts ↔ fichiers, version ↔ journal).

Les cartes « enjeu » et « résultat », le hero, les panneaux et le bloc contact
sont devenus des composants avec la fiche solution (06/11/2026), qui les
réutilise : le HTML de l'accueil est resté identique, vérifié par diff, et ses
styles calculés aussi (660 éléments, 3 largeurs). Reste écartée l'enveloppe
`<section>` (`get_template_part` ne sait pas envelopper un contenu).

### Un élément d'interface = un seul composant

Décision du 19/11/2026 : une carte, un bouton, un lien de carte sont **toujours
présentés de la même manière**. Pour y arriver, chacun n'existe qu'une fois et
s'appelle partout — dans le thème **et dans les maquettes** (`ux/maquettes/`
fait rendre ses cartes par le thème, jamais à la main). Une variante est un
paramètre, jamais une copie ni une surcharge CSS.

| Élément | Composant | Ce qu'il impose |
| --- | --- | --- |
| Carte (enjeu, chiffre, étude, solution, éditeur, service) | `components/card.php`, en grille par `components/card-grid.php` | Cadre (30 px, bordure, coins), titre 22 px, zones facultatives dans un ordre fixe ; corps et pied partagent les lignes de la grille (`subgrid`) : sur une ligne de cartes, séparateurs et liens à la même hauteur |
| Lien d'une carte (et d'une entrée de `choice-list`) | `.card__link` | Orange, souligné, suivi de « → » dessiné par le style (une flèche saisie est retirée) ; en bas de la carte |
| Tableau « quel outil pour quel besoin » | `components/choice-list.php` | Toute la largeur : solution, besoin, lien « Voir … » au bout ; chaque ligne est un lien ; en-tête discret ; étiquette d'éditeur seulement si les solutions viennent de plusieurs éditeurs |
| Étiquette d'éditeur | `components/vendor-chips.php` | Premier élément de toute carte qui cite un logiciel ; éditeurs déduits des logiciels nommés (`OptelyaCatalog::vendorsNamedIn`) |
| Bouton | `.button` + taille (`--small`, `--large`) | Orange : un seul bouton principal, envoi du formulaire compris |
| Cadre sans lien (formulaire, panneaux) | `.card` | Même cadre que les cartes |
| Pastille cliquable (secteur, filtre) | `.pill` | Un filtre est un `button.pill` à `aria-pressed` |
| Filtre par enjeu (liste des solutions) | `components/issue-filter.php` + `assets/scripts/issue-filter.js` | Un bloc à la fois (titre, cartes, lien de comparaison), choix gardé dans l'adresse (`?enjeu=`) ; sans script, toutes les cartes |
| État vide | `components/empty-state.php` | Dans un cadre `.card`, toujours suivi de contenus proches : jamais une impasse |
| En-tête de page | `components/hero.php` | Titre = nom de la fiche ; `claim` en sous-titre (industrie) ; sans visuel : texte seul (listes) |
| Saisie d'un élément commun | `acf-json/group_optelya_part_*.json`, clonés | En-tête de liste, titre de section, bandeau d'aide, visuel, vidéo : définis une fois, lus par `optelya_page_head()`, `optelya_heading()`, `optelya_band()`, `optelya_head_media()`, `optelya_video()` ; les réglages du catalogue sont nommés par `OptelyaCatalogSetting` (`CatalogSettingTest` le garde aligné sur le JSON) |

**Aucun lien vers une page pas encore construite** : `optelya_is_built()` dit si
le gabarit d'un type existe ; sinon l'étape du fil d'Ariane s'affiche sans lien
(et sort des données structurées) et les liens « tout voir » disparaissent.

Avant d'écrire un élément, chercher s'il existe dans `components/` ; en recette,
mesurer les styles calculés par type de composant sur toutes les pages : une
seule présentation par composant.

**Ne jamais affecter `$id`, `$post`, `$posts`, `$comment`, `$user_ID`,
`$wp_query`, `$wp`, `$wpdb`** dans un gabarit, un composant ou un layout :
`load_template()` les déclare globales, et `$id = 'contact'` dans un composant
a cassé `get_the_ID()` pour la suite de la page. `TemplateGlobalsTest` le
refuse.

## Tests

| Test | Ce qu'il garantit |
| --- | --- |
| `SiteVersionTest` | Le pied de page lit bien la version de `composer.json`, au format semver. |
| `ChangelogTest` | La version de `composer.json` a sa section dans `CHANGELOG.md`. |
| `TemplateGlobalsTest` | Aucun gabarit n'écrase une globale de WordPress. |
| `SeedCatalogDataTest` | Chaque ligne de répéteur amorcée porte les noms de ses sous-champs. |
| `CatalogTest` | Jamais « 0 étude » ; le bouton d'en-tête reste sur la page qui a un bloc contact ; l'ancienneté de l'éditeur arrive en 2ᵉ ligne du premier panneau sombre. |

Chacun a été vu **au rouge** :

- version passée à `0.2.0` sans entrée de changelog → `ChangelogTest` échoue ;
- chemin de `composer.json` faussé dans `optelya_site_version()` →
  `SiteVersionTest` et `ChangelogTest` échouent ;
- une fonction sans type → PHPCS sort en erreur ;
- `$id` remis dans un composant → `TemplateGlobalsTest` échoue ;
- un item de panneau remis en liste `['titre', 'texte']` →
  `SeedCatalogDataTest` échoue.

## Front

Le CSS reprend les maquettes de `ux/maquettes/` (hors dépôt), rangées par
gabarit : `<Thème>/<Gxx - Page …>/maquette/`.
`assets/styles/basics/_variables.scss` porte toutes les couleurs (aucune en dur
ailleurs), les polices, l'échelle des tailles de texte et les mesures partagées
(largeur des titres, hauteur des boutons, rayons, gouttière…).

Les espacements propres à un bloc (un `gap: 14px` ici, un `padding-top: 14px`
là) restent écrits dans leur bloc : leurs valeurs coïncident, mais ils ne
désignent pas la même chose. Les regrouper lierait des blocs indépendants.

Une refonte des variables se vérifie en comparant le `dist/main.css` compilé
avant et après : il doit être identique octet pour octet.

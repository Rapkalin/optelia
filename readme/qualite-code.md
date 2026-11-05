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
| **Pest** | Tests, hors WordPress | `tests/`, et `tests/` de chaque module maison |

> **Modules maison** (`shared/mu-plugins/`, hors dépôt, liés dans
> `website/app/mu-plugins/`) : les quatre outils les contrôlent **sur un poste
> qui les a** — PHPStan et PHPCS à travers le lien, Pint directement dans
> `shared/mu-plugins/`, Pest par le second dossier de `phpunit.xml.dist`. Leurs
> tests voyagent avec eux et sont exclus de PHPStan et PHPCS, comme `tests/`.
> **La CI ne les voit pas** : les contrôler avant chaque dépôt sur un serveur.

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
| `components/` | Morceaux d'interface réutilisés par plusieurs gabarits (lien, icône, titre de section, liste de liens de menu) |
| `layouts/` | Une section de l'accueil par fichier, du nom de son layout ACF |
| `admin/` | Vues des écrans d'administration du thème (*Outils → Contact*) |
| `assets/styles/basics/` | Variables (toutes les couleurs, tailles, poids, espacements) et mixins (`list-reset`, `display-heading`, `eyebrow`, `stack`, `below`…) |

Une valeur partagée entre fichiers de natures différentes (PHP ↔ JSON ACF ↔
SCSS) ne peut pas être une seule constante : un **test** la garde alignée
(couleur d'encre et couleur d'en-tête par défaut, slug de *Configuration*,
layouts ↔ fichiers, version ↔ journal).

Écarté, pour l'instant, comme de la sur-ingénierie (revue du 05/11/2026) : un
composant pour les cartes « enjeu » et « résultat » (chacune n'existe que dans
une boucle d'un layout ; à reconsidérer si une page « Études de cas » réutilise
la carte résultat) et pour l'enveloppe `<section>` des layouts
(`get_template_part` ne sait pas envelopper un contenu).

## Tests

| Test | Ce qu'il garantit |
| --- | --- |
| `SiteVersionTest` | Le pied de page lit bien la version de `composer.json`, au format semver. |
| `ChangelogTest` | La version de `composer.json` a sa section dans `CHANGELOG.md`. |

Chacun a été vu **au rouge** :

- version passée à `0.2.0` sans entrée de changelog → `ChangelogTest` échoue ;
- chemin de `composer.json` faussé dans `optelya_site_version()` →
  `SiteVersionTest` et `ChangelogTest` échouent ;
- une fonction sans type → PHPCS sort en erreur.

## Front

Le CSS reprend la maquette `ux/accueil-ui/index.html` (hors dépôt).
`assets/styles/basics/_variables.scss` porte toutes les couleurs (aucune en dur
ailleurs), les polices, l'échelle des tailles de texte et les mesures partagées
(largeur des titres, hauteur des boutons, rayons, gouttière…).

Les espacements propres à un bloc (un `gap: 14px` ici, un `padding-top: 14px`
là) restent écrits dans leur bloc : leurs valeurs coïncident, mais ils ne
désignent pas la même chose. Les regrouper lierait des blocs indépendants.

Une refonte des variables se vérifie en comparant le `dist/main.css` compilé
avant et après : il doit être identique octet pour octet.

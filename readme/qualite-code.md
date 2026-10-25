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

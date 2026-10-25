# CLAUDE.md — Règles du projet (Optelya)

> **Instructions pour tout assistant Claude travaillant sur ce dépôt.**
> En cas de doute : **demander, ne pas improviser.**

Socle repris de LCDS (`~/Sites/lcds`) : en cas de question sur un choix
d'infrastructure, c'est la référence.

## 1. Méthode de travail

- **Rien d'assumé.** Toute affirmation repose sur un élément produit dans la
  session : sortie de commande, mesure, source lue, test exécuté. Ce qui reste
  supposé est déclaré comme tel.
- **Challenger la demande** : dire ce qui coince (vrai problème, tenue dans le
  temps, ce que ça casse, plus simple), puis faire ce que l'utilisateur décide.
- **Un test qui ne peut pas échouer ne vaut rien** : casser volontairement le
  code surveillé et vérifier qu'il passe au rouge.
- **Après chaque développement** : relire pour supprimer doublons et code mort,
  vérifier SOLID et KISS, puis une **QA réelle** (le site tourne, les contrôles
  passent).
- **Recette visuelle obligatoire** : comparer le rendu à la maquette
  (`ux/accueil-ui/index.html`, hors dépôt) avant de livrer. Tout écart restant
  est listé et justifié.

## 2. Qualité de code

- **Le projet tourne dans Docker.** Toute commande PHP / Composer / WP-CLI passe
  par `docker compose exec php …` — voir [`readme/docker.md`](readme/docker.md).
- **`composer check` et `composer test` DOIVENT être au vert** avant tout
  commit PHP — voir [`readme/qualite-code.md`](readme/qualite-code.md).
- **Types natifs obligatoires**, **PHPStan niveau 6**, style **PER** (Pint),
  pas de condition Yoda.
- **DRY** : une valeur utilisée plusieurs fois = une variable ; utilisée dans
  plusieurs fichiers = une constante ou un enum (`inc/enums/`, variables SCSS
  de `assets/styles/basics/_variables.scss`).
- **Commentaires : le moins possible**, seulement là où un dev casserait le code
  sans eux.

## 3. Langue

- **Code en anglais**, **documentation en français**.
- **Chaînes d'interface** (libellés ARIA, messages, « Version %s ») via
  `__()` / `esc_html_e()`, text-domain littéral `optelya`.
- **Contenu éditorial** : saisi dans l'admin (ACF, menus). Le contenu initial
  de la maquette est dans `inc/seed.php` — voir
  [`readme/contribution.md`](readme/contribution.md).

## 4. Sécurité — non négociable

- Toute entrée est hostile : sanitiser en entrée, échapper en sortie.
- **Jamais de secret dans le dépôt** : tout passe par le `.env`, qui vit dans
  `shared/` sur le serveur. **Le dépôt est PUBLIC.**
- **Jamais de document interne ni de contenu sous droits** : `ux/`,
  `prototype/`, l'inventaire et `.claude/` sont exclus par le `.gitignore`.
- Ne pas modifier `website/wordpress-core/` ni `website/vendor/`.
- **ACF Pro est sous licence, hors dépôt** (`shared/plugins/`) : tout appel à
  ACF passe par les enveloppes gardées de `inc/acf.php`.
- Voir [`readme/securite.md`](readme/securite.md).

## 5. Git

- **Ne jamais committer ni pousser sans demande explicite de l'utilisateur.**
- Une branche par ticket : `feature/xxx`. Bases : `main` (prod), `develop`
  (préprod). Messages : `[{ticket}][{TYPE}] message` — `FEAT` ou `FIX`.

## 6. Documentation

Tenir `readme/` à jour quand une décision de socle change.

## 7. Version et journal des modifications

- **La version du site fait foi dans `composer.json`.** Le pied de page
  l'affiche via `optelya_site_version()` (`inc/template.php`) sur tous les
  environnements. Ne jamais la dupliquer (`style.css` n'a volontairement pas
  d'en-tête `Version`).
- **À chaque livraison** : incrémenter la version **et** ajouter sa section au
  [`CHANGELOG.md`](CHANGELOG.md). `tests/Unit/ChangelogTest.php` échoue sinon.
- **Semver** : `MAJOR` = rupture de contribution ou de déploiement, `MINOR` =
  fonctionnalité, `PATCH` = correctif seul. `0.x` jusqu'à la mise en ligne.
- Le journal dit ce qui change pour l'utilisateur, pas la liste des fichiers.
- Le tag de production porte la même version, sur un commit de `main` :
  `deploy-prod.yml` refuse de déployer sinon. **Temporaire** : avant la mise en
  ligne, chaque poussée sur `main` déploie aussi la production.

# Optelya

Le site d'**Optelya**, distributeur de logiciels de simulation et
d'optimisation (Noesis Solutions, Sigmetrix, FunctionBay).

![wordpress](https://img.shields.io/badge/wordpress-v7.1-0678BE.svg?style=flat-square)
![php](https://img.shields.io/badge/PHP-v8.5-828cb7.svg?style=flat-square)
![composer](https://img.shields.io/badge/composer-v2-126E75.svg?style=flat-square)
![Node](https://img.shields.io/badge/node-v26-644D31.svg?style=flat-square)
![webpack](https://img.shields.io/badge/webpack-v5-157B25.svg?style=flat-square)

## Démarrage rapide

Seul prérequis : **Docker**. PHP, Composer, MySQL, WP-CLI et Node sont dans les
conteneurs.

```bash
git clone https://github.com/Rapkalin/optelia.git && cd optelia
cp .env.example .env
mkdir -p shared/plugins        # y déposer advanced-custom-fields-pro/ (licence, hors dépôt)
docker compose up -d
docker compose run --rm node npm ci && docker compose run --rm node npm run build
git config core.hooksPath .githooks   # active le hook de pré-commit
```

**ACF Pro** est sous licence : il ne vit pas dans le dépôt. Décompresser le
plugin dans `shared/plugins/advanced-custom-fields-pro/` **avant** le premier
démarrage : il est alors relié, activé, et la page d'accueil est créée avec son
contenu. Sans lui, la page s'affiche sans ses sections.

| Service | URL | Accès |
| --- | --- | --- |
| Front | <http://localhost:8040> | — |
| Admin | <http://localhost:8040/wordpress-core/wp-admin> | `admin` / `admin` |
| phpMyAdmin | <http://localhost:8041> | `optelya` / `optelya` |

## Documentation

| Sujet | Fichier |
| --- | --- |
| Contribution : sections, réglages, menus | [`readme/contribution.md`](readme/contribution.md) |
| Rôles : contributeur, périmètre de l'admin | [`readme/roles.md`](readme/roles.md) |
| Environnement Docker, commandes, dépannage | [`readme/docker.md`](readme/docker.md) |
| Durcissement, en-têtes HTTP, CSP | [`readme/securite.md`](readme/securite.md) |
| Pint, PHPCS, PHPStan, Pest | [`readme/qualite-code.md`](readme/qualite-code.md) |
| Workflows GitHub Actions | [`readme/ci-cd.md`](readme/ci-cd.md) |
| Déploiement, `shared/`, rollback | [`readme/deploiement.md`](readme/deploiement.md) |
| Journal des modifications et version du site | [`CHANGELOG.md`](CHANGELOG.md) |

## Workflow Git

- Une branche par ticket : `feature/xxx`. Bases : `main` (prod), `develop`
  (préprod).
- `docker compose exec php composer check` et `composer test` doivent être au
  vert avant tout commit PHP.

Tout passe par une **pull request**. Une fois la protection des branches
activée sur GitHub (voir [`readme/ci-cd.md`](readme/ci-cd.md)), les poussées
directes sur `main` et `develop` sont refusées.

1. PR `feature/xxx` → `develop`. Le merge déploie la **préprod**.
2. PR `develop` → `main`, une fois la préprod validée.
3. **Pour l'instant, chaque poussée sur `main` déploie la production**
   (décision temporaire, avant la mise en ligne). Ensuite, seul un tag de la
   version sur `main` la déploiera :

```bash
git checkout main && git pull
git tag -a x.y.z -m "…" && git push origin x.y.z
```

Un tag **doit** être la version de `composer.json` (et du `CHANGELOG.md`) et
pointer sur `main` : sinon `deploy-prod.yml` refuse de déployer. À la mise en
ligne, retirer `branches: [main]` de `deploy-prod.yml`. Voir
[`CLAUDE.md`](CLAUDE.md), règle 7.

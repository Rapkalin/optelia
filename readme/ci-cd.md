# CI/CD

La CI tourne sur **GitHub Actions**, sur le modèle de LCDS.

## Les workflows

| Fichier | Déclencheur | Rôle |
| --- | --- | --- |
| `ci.yml` | pull request vers `main`/`develop`, ou appel | Contrôles qualité et sécurité |
| `codeql.yml` | pull request, lundi 3 h, ou appel | SAST CodeQL (JavaScript) |
| `deploy-preprod.yml` | poussée sur `develop`, ou manuel | Préprod |
| `deploy-prod.yml` | poussée sur `main` (temporaire) ou d'un **tag** | Production ; un tag doit égaler la version de `composer.json`, sur `main` |
| `deploy.yml` | appelé par les deux précédents | Build, envoi, liens `shared/` |

## `ci.yml`

| Job | Ce qu'il fait |
| --- | --- |
| **PHP (qualité, tests, CVE)** | `composer check` (Pint, PHPCS/Slevomat, PHPStan), `composer test` (Pest), `composer audit --locked`. |
| **Détection de secrets (gitleaks)** | Sur les commits poussés ou ceux de la PR. `fetch-depth: 0` sert à résoudre cette plage, pas à scanner tout l'historique. |
| **Build front (webpack)** | `npm ci` puis `npm run build` : vérifie que la compilation passe. |

**La CI lit les versions d'outils dans le dépôt** au lieu de les répéter :

- **PHP** : `config.platform.php` de `composer.json`. `setup-php` le lit quand
  on ne lui passe pas de `php-version` (vérifié dans son code source, tag `v2`),
  et PHPStan aussi (vérifié par `phpstan diagnose`).
- **Node** : `website/app/themes/optelya/.nvmrc`, lu par `setup-node` dans
  l'action partagée `.github/actions/build-theme`.

**Endroits qui répètent encore ces versions**, faute de mécanisme commun : à
mettre à jour ensemble lors d'une montée de version.

| Valeur | Fichiers |
| --- | --- |
| PHP 8.5 | `composer.json` (`require.php` et `config.platform.php`), `docker/php/Dockerfile`, candidats `php8.5` du script de `deploy.yml`, badges du `README.md` |
| Node 26 | `.nvmrc`, `docker-compose.yml` (`image: node:26`), badge du `README.md` |

## `codeql.yml`

- **CodeQL ne supporte pas PHP** : seul le JavaScript du thème est analysé. La
  sécurité du PHP repose sur PHPStan et l'audit CVE.
- **Gratuit parce que le dépôt est public.** Passé en privé, il exigerait
  GitHub Advanced Security (payant) : supprimer alors ce workflow.

## Rien ne part sans contrôles verts

Les workflows de déploiement appellent `ci.yml` et `codeql.yml`, et le job
d'envoi les attend (`needs: [ci, codeql]`). Si l'un échoue, l'envoi est
**sauté** : le serveur n'est pas touché.

`ci.yml` et `codeql.yml` n'ont **pas** de déclencheur `push` : ils tourneraient
deux fois sur les branches déployées.

## Bloquer les merges

À régler côté GitHub (*Settings > Branches*), sur `main` **et** `develop` :

- **Require a pull request before merging** ;
- **Require status checks to pass**, en cochant `PHP (qualité, tests, CVE)`,
  `Détection de secrets (gitleaks)`, `Build front (webpack)` et
  `Analyse CodeQL` ;
- **Require branches to be up to date before merging**.

> Les noms sont ceux des jobs (`name:`). Les renommer dans le YAML sans mettre
> à jour la règle bloque les PR : GitHub attend un contrôle qui n'arrive plus.

**Approbation avant la production** : créer l'environnement `production`
(*Settings > Environments*) et y ajouter un *required reviewer*.

Déploiement en détail : [`deploiement.md`](deploiement.md).

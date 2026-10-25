# Docker

## Services

| Service | Image | Port hôte | Rôle |
| --- | --- | --- | --- |
| `php` | `php:8.5-apache` (construite) | `8040` | Sert `website/` |
| `db` | `mysql:8.4` | `3311` | Base de données |
| `phpmyadmin` | `phpmyadmin` | `8041` | Interface base |
| `node` | `node:26` | — | Build du thème (profil `tools`) |

Les ports se changent dans le `.env` (`APP_PORT`, `DB_PORT`, `PMA_PORT`). Ils
évitent ceux déjà pris sur le poste de dev au 23/10/2026 (LCDS et d'autres
projets : 8020, 8021, 8025, 8026, 8030, 8031, 8035, 3306, 3307, 3308, 3310).

Le projet est **bind-monté** : un fichier modifié sur l'hôte est servi aussitôt.
Les médias vivent dans un volume nommé (`uploads`).

## Cycle de vie

Au démarrage, l'entrypoint du conteneur `php` :

1. lance `composer install` si `website/vendor` manque ;
2. copie `.env.example` en `.env` s'il manque ;
3. attend la base (60 s au plus) ;
4. joue `bin/init.sh` : sels, installation WordPress, thème, plugins de
   `shared/plugins/` (liés et activés), contenu initial (une seule fois),
   langue `fr_FR`.

Tout est **idempotent** : un redémarrage ne réécrit rien.

## Commandes

```bash
source aliases.sh          # raccourcis : dwp, dcheck, dtest, dnpm, db-*
docker compose exec php composer check
docker compose exec php composer test
docker compose run --rm node npm run build
docker compose run --rm node npm run dev     # recompile à chaque modification
```

## Limitation `wp db`

`wp db query`, `wp db export` et `wp db import` échouent dans le conteneur
(vérifié : « TLS/SSL error: self-signed certificate in certificate chain »).
Elles délèguent au client MariaDB, qui refuse le certificat auto-signé de
MySQL 8.4. Utiliser les alias `db-query`, `db-export`, `db-import` et `db-cli`
de `aliases.sh`, qui passent par le client du conteneur `db`.

Le reste de WP-CLI fonctionne.

## Bruit connu dans les logs

Le phar de WP-CLI émet des « Deprecated: Using null as an array offset » sous
PHP 8.5, depuis son propre code. Ce n'est pas le code du projet. Le site, lui,
n'affiche aucune erreur (vérifié sur la page d'accueil).

## Dépannage

| Symptôme | Cause probable |
| --- | --- |
| `port is already allocated` | Port pris → changer `APP_PORT` / `DB_PORT` / `PMA_PORT` dans le `.env`. |
| Le conteneur `php` redémarre en boucle | La base n'a pas répondu en 60 s → `docker compose logs db`. |
| Styles absents | `dist/` non compilé → `docker compose run --rm node npm run build`. |
| Redirections en boucle | `WP_HOME` ne correspond pas à l'URL utilisée. |

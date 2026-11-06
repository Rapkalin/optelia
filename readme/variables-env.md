# Variables d'environnement (`.env`)

Une seule source : le fichier `.env`. En local, à la racine du dépôt (copie de
`.env.example`). Sur le serveur, dans `shared/.env`, relié dans le docroot —
voir [`deploiement.md`](deploiement.md). **Jamais dans le dépôt** : il porte les
mots de passe.

Inventaire tiré du code au 05/11/2026 : `config/application.php`,
`config/environments/*.php`, le thème, les modules `rapkalin-*`,
`docker-compose.yml` et `bin/init.sh`.

## À renseigner sur un serveur

Le destinataire du formulaire de contact n'est **pas** dans le `.env` : il se
règle dans *Réglages → Configuration* (voir [`contact.md`](contact.md)).

| Variable | Rôle | Remarque |
| --- | --- | --- |
| `WP_ENV` | `development`, `staging` ou `production` : choisit `config/environments/<env>.php` | Absente = `production`. Hors `production`, l'indexation par les moteurs est interdite |
| `WP_HOME` | URL publique du site, **sans slash final** | |
| `WP_SITEURL` | Emplacement du cœur : `${WP_HOME}/wordpress-core` | |
| `DB_NAME`, `DB_USER`, `DB_PASSWORD` | Accès à la base | |
| `DB_HOST` | Serveur de base | Absente = `localhost` |
| `AUTH_KEY`, `SECURE_AUTH_KEY`, `LOGGED_IN_KEY`, `NONCE_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`, `LOGGED_IN_SALT`, `NONCE_SALT` | Clés et sels de WordPress | **Uniques par environnement** (<https://roots.io/salts.html>). Laissés à `generateme`, ils permettent de forger des cookies de connexion. Ils signent aussi le jeton du formulaire de contact |

## Connexion (module `rapkalin-security`)

| Variable | Rôle |
| --- | --- |
| `LOGIN_SLUG` | Adresse de connexion personnalisée (ex. `acces-equipe-7` → `https://<site>/acces-equipe-7/`). L'adresse réelle et l'admin déconnecté répondent alors 404. **À garder secrète et à noter** : c'est la seule porte d'entrée. Vide : connexion standard. Recommandée sur un serveur, différente par environnement |

## E-mails sortants (module `rapkalin-mail`)

| Variable | Rôle |
| --- | --- |
| `SMTP_HOST` | Serveur SMTP. **Renseigné, le `.env` prime** sur *Réglages → Email* ; vide, l'admin décide |
| `SMTP_PORT`, `SMTP_SECURE` (`tls`, `ssl` ou vide), `SMTP_AUTH`, `SMTP_USERNAME`, `SMTP_PASSWORD` | Connexion SMTP |
| `MAIL_FROM`, `MAIL_FROM_NAME` | Expéditeur des e-mails du site |

Sur un serveur, laisser `SMTP_HOST` vide pour régler l'envoi depuis l'admin —
voir [`email.md`](email.md).

## Facultatives

| Variable | Défaut | Rôle |
| --- | --- | --- |
| `DB_PREFIX` | `wp_` | Préfixe des tables |
| `DB_SSL` | désactivé | Connexion chiffrée à la base |
| `WP_ENVIRONMENT_TYPE` | = `WP_ENV` | Type d'environnement exposé par WordPress |
| `WP_POST_REVISIONS` | illimité | Nombre de révisions gardées par page |
| `DISABLE_WP_CRON` | `false` | `true` si une vraie tâche cron appelle `wp-cron.php` |
| `PAGE_CACHE` | `false` | Cache de page (WP Super Cache). **`true` en préprod et en production** ; `false` en local pour voir chaque modification — voir [`cache.md`](cache.md) |
| `WP_DEBUG_LOG` | `true` | En `development` uniquement : écrit `debug.log` |

## Local uniquement (Docker et `bin/init.sh`)

| Variable | Rôle |
| --- | --- |
| `APP_PORT`, `PMA_PORT`, `DB_PORT`, `MAILPIT_PORT` | Ports publiés sur le poste |
| `DB_ROOT_PASSWORD` | Mot de passe root du conteneur MySQL |
| `WP_TITLE`, `WP_ADMIN_USER`, `WP_ADMIN_PASSWORD`, `WP_ADMIN_EMAIL` | Installation automatique par `bin/init.sh` |

Inutiles sur le serveur : l'installation s'y fait par `wp core install` —
voir [`deploiement.md`](deploiement.md).

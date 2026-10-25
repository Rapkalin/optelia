# Déploiement

Deux environnements, sur le **même serveur que LCDS**, déployés par GitHub
Actions.

| Environnement | Déclencheur | Chemin distant |
| --- | --- | --- |
| Préprod | poussée sur `develop` (ou lancement manuel) | `$HOME/preprod-optelya` |
| Production | poussée sur `main` (**temporaire**, avant la mise en ligne), ou d'un **tag** égal à la version de `composer.json`, sur `main` | `$HOME/prod-optelya` |

> ⚠️ **Tant que `main` déploie directement**, chaque poussée part en production
> sans validation. À la mise en ligne, retirer `branches: [main]` de
> `deploy-prod.yml`. Et tant que le site n'est pas lancé, garder
> `WP_ENV='staging'` dans `shared/.env` : les moteurs de recherche n'indexent
> pas le site (`noindex`). En `production`, ils le pourraient dès que le domaine
> répond.

> `remote_path` est **relatif au home SSH** (`preprod-optelya`, pas
> `~/preprod-optelya`) : un `~` passé par une variable n'est pas développé par
> le shell.

## Arborescence sur le serveur

```text
~/prod-optelya/
├── shared/              # JAMAIS écrasé : uniquement de l'état persistant
│   ├── .env             # configuration et secrets de l'environnement
│   ├── plugins/         # plugins sous licence (ACF Pro), déposés à la main
│   ├── .htaccess        # celui qu'Apache sert (amorcé au 1er déploiement)
│   ├── .htpasswd        # optionnel : mot de passe (préprod)
│   ├── uploads/         # médias
│   └── cache/
├── config/              # release
├── website/             # release — DocumentRoot du vhost
├── composer.json        # release — le pied de page y lit la version
├── wp-cli.yml           # release
└── old_release/         # copie de la release précédente (rollback)
```

Le `DocumentRoot` pointe sur `~/prod-optelya/website`, avec `AllowOverride All`
— sans quoi les en-têtes de sécurité ne sont pas posés.

| Chemin dans la release | Pointe vers |
| --- | --- |
| `website/.env` | `shared/.env` |
| `website/.htaccess` | `shared/.htaccess` |
| `website/.htpasswd` | `shared/.htpasswd` *(si présent)* |
| `website/app/uploads` | `shared/uploads` |
| `website/app/cache` | `shared/cache` |
| `website/app/plugins/<plugin>` | `shared/plugins/<plugin>` |

## Ce que fait le workflow

**Contrôle du tag** (production, sur un tag seulement) : le job `release-tag`
refuse un tag différent de la `version` de `composer.json`, ou posé sur un
commit absent de `main`. Sur une poussée de `main`, il est sauté et le
déploiement n'attend que la CI et CodeQL.

**Build** : `composer install --no-dev`, `npm ci && npm run build` dans le thème
(`dist/` n'est pas versionné), suppression de `node_modules`, archive de
`website/`, `config/`, `wp-cli.yml` et `composer.json`. Le `.htaccess` du dépôt
y est renommé `.htaccess.dist` (voir plus bas).

**Envoi** :

1. copie de la release en place vers `old_release/` ;
2. `rsync --delete`, qui exclut `/shared`, `/old_release`, les chemins liés à
   `shared/` et `website/app/debug.log` (journal de la préprod) — **chaque
   motif commence par `/`**, sinon rsync exclut tout
   dossier de ce nom à n'importe quelle profondeur (incident LCDS : un dossier
   `shared/` du cœur WordPress supprimé) ;
3. recréation des liens vers `shared/` ;
4. si WP-CLI tourne avec un PHP ≥ 8.5 : activation des plugins de
   `shared/plugins/`, contenu initial (`optelya_seed()`, joué une seule fois
   par environnement), `wp cache flush` et `wp rewrite flush`. Sinon un avertissement apparaît
   dans le résumé du run, et le déploiement reste réussi.

> **ACF Pro est exclu de rsync, nommément** (`--exclude=/website/app/plugins/advanced-custom-fields-pro`) :
> sans ça, `--delete` emporte son lien et l'accueil perd ses sections jusqu'à
> l'étape 3 (vérifié en simulation : le lien survit au second déploiement). Un
> nouveau plugin payant demande sa propre exclusion.

> **Sur ce serveur, `wp` est une enveloppe shell** qui lance WP-CLI avec
> **PHP 8.0 par défaut** — trop vieux : l'autoloader exige 8.5 et refuse de
> démarrer. Elle se règle par la variable `uiwpcli_PHP_FLAVOR` (constaté au
> premier déploiement, 25/10/2026 ; PHP 8.5 disponible en `/usr/bin/php8.5-cli`).
> En SSH, **toujours** commencer par :
>
> ```bash
> export uiwpcli_PHP_FLAVOR=php8.5
> ```
>
> Le workflow fait de même, et choisit sa méthode sur la **sortie** de
> `wp --info` (PHP ≥ 8.5 exigé), jamais sur le code de retour : `php8.5 <script
> shell>` recopie le script et sort en 0 — c'est ainsi que, jusqu'à la 0.3.1,
> aucune commande wp n'a tourné au déploiement, sans alerte. LCDS, même serveur
> et même code, est concerné.

## `.htaccess` : le dépôt est la référence, `shared/` est servi

Chaque environnement peut ajouter ses directives (mot de passe en préprod).

- Le fichier du dépôt arrive en **`website/.htaccess.dist`** : rsync ne touche
  jamais au lien `website/.htaccess -> shared/.htaccess`. Sinon, entre l'envoi
  et la recréation du lien, Apache appliquerait le fichier du dépôt, **sans le
  mot de passe de la préprod**. Le `.htaccess.dist` n'est pas servi (403,
  vérifié).
- Au premier déploiement, `shared/.htaccess` est copié depuis
  `.htaccess.dist`. Ensuite, une divergence est **signalée** dans le résumé du
  run : reporter alors le changement à la main sur chaque serveur.

### Protéger la préprod par mot de passe

```bash
htpasswd -c ~/preprod-optelya/shared/.htpasswd nom-utilisateur
```

Puis en tête de `~/preprod-optelya/shared/.htaccess` (chemin **absolu**) :

```apache
AuthType Basic
AuthName "Acces restreint"
AuthUserFile /home/USER/preprod-optelya/shared/.htpasswd
Require valid-user
```

## Secrets GitHub

*Settings > Secrets and variables > Actions.* Ce sont **les mêmes valeurs que
LCDS** (même serveur).

| Secret | Obligatoire | Contenu |
| --- | --- | --- |
| `DEPLOY_HOST` | oui | Hôte SSH |
| `DEPLOY_USER` | oui | Utilisateur SSH |
| `DEPLOY_KEY` | oui | Clé **privée** complète, en-têtes compris |
| `DEPLOY_KEY_PASS` | non | Passphrase de la clé |
| `DEPLOY_FINGERPRINT` | non | Empreinte SHA256 de l'hôte (`ssh-keyscan <host> \| ssh-keygen -lf -`) |
| `WATCHA_TOKEN` | non | Veille des dépendances — **secret d'environnement**, un par environnement |

## Première mise en service d'un environnement

Avant le premier déploiement :

1. créer la base MySQL chez l'hébergeur ;
2. pointer le domaine sur `<remote_path>/website` ;
3. créer le dossier et le `.env` :

   ```bash
   mkdir -p ~/preprod-optelya/shared/{plugins,uploads,cache}
   ```

4. déposer ACF Pro dans `shared/plugins/` :

   ```bash
   scp -r advanced-custom-fields-pro user@serveur:~/preprod-optelya/shared/plugins/
   ```

   > ⚠️ **Licence** : chaque site en production doit être couvert par une
   > licence ACF Pro. Vérifier que celle de l'agence couvre Optelya avant la
   > mise en ligne.

   puis `~/preprod-optelya/shared/.env` à partir de
   [`.env.example`](../.env.example) — ⚠️ **remplacer les huit `generateme`** :
   `bin/init.sh` les génère en local, mais il ne tourne pas sur le serveur ;
   laissés tels quels, ce sont des clés publiques qui permettent de forger des
   cookies de connexion. Puis `WP_ENV='staging'` (ou
   `'production'`), le vrai `WP_HOME` **sans slash final**, les accès base et
   des **sels uniques** (<https://roots.io/salts.html>).

Après le premier déploiement, installer WordPress et le passer en français :

```bash
export uiwpcli_PHP_FLAVOR=php8.5
cd ~/prod-optelya          # ou ~/preprod-optelya
wp core install --url="https://…" --title="Optelya" \
  --admin_user="…" --admin_email="…" --prompt=admin_password
wp site switch-language fr_FR
```

Le thème `optelya` est activé par l'installation (`WP_DEFAULT_THEME`). Les
fichiers de langue du cœur sont versionnés : ils partent avec la release.
Relancer ensuite le workflow de déploiement (*Actions → Déploiement preprod →
Run workflow*) : il active ACF Pro et pose le contenu initial. À la main :

```bash
wp plugin activate advanced-custom-fields-pro
wp eval 'foreach (optelya_seed() as $line) { echo $line, PHP_EOL; }'
```

### Activer la licence ACF Pro

**Pas depuis l'admin** : hors `development`, le thème masque les écrans d'ACF
(voir [`securite.md`](securite.md)), et ACF fait disparaître avec eux sa page
de licence. La constante `ACF_PRO_LICENSE` ne s'active pas non plus : ACF la
garde derrière le même réglage (`pro/updates.php`, vérifié en 6.8.7). Les deux
gardes valent aussi pour LCDS.

`acf_pro_activate_license()` n'a pas cette garde : l'activation passe par
WP-CLI, la clé lue sans écho pour ne pas finir dans l'historique du shell.

```bash
export uiwpcli_PHP_FLAVOR=php8.5
cd ~/prod-optelya          # ou ~/preprod-optelya
read -rs ACF_KEY && export ACF_KEY
wp eval 'print_r(acf_pro_activate_license(getenv("ACF_KEY"), true));'
unset ACF_KEY
```

> Non éprouvé avec une vraie clé : la fonction et l'absence de garde sont
> vérifiées dans le code d'ACF, pas l'appel au serveur de licences.

## Rollback

`old_release/` contient `website/`, `config/`, `composer.json` et `wp-cli.yml`
de la release précédente : le pied de page affiche donc la bonne version après
un retour arrière.

```bash
cd ~/prod-optelya
rm -rf website config
cp -a old_release/website old_release/config old_release/composer.json old_release/wp-cli.yml .
ln -nsf "$PWD/shared/.env"      website/.env
ln -nsf "$PWD/shared/.htaccess" website/.htaccess
ln -nsf "$PWD/shared/uploads"   website/app/uploads
ln -nsf "$PWD/shared/cache"     website/app/cache
for p in shared/plugins/*/; do ln -nsf "$PWD/${p%/}" "website/app/plugins/$(basename "$p")"; done
[ -f shared/.htpasswd ] && ln -nsf "$PWD/shared/.htpasswd" website/.htpasswd
```

## Points d'attention

- **OPcache** n'est pas vidé par le déploiement : la commande de rechargement de
  PHP-FPM est en commentaire dans `deploy.yml`.
- **Le déploiement écrit en base sur deux points seulement** : l'activation des
  plugins de `shared/plugins/` et le contenu initial, posé une fois par
  environnement (`optelya_seeded`). Aucune migration, aucun `search-replace`.

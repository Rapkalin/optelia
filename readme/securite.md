# Sécurité

Quatre couches : la **configuration**, le **serveur web**, le **thème** et la
**CI**. Chaque mesure ci-dessous a été vérifiée sur l'environnement local.

## 1. Configuration — `config/application.php`

| Constante | Valeur | Rôle |
| --- | --- | --- |
| `DISALLOW_FILE_EDIT` | `true` | Pas d'éditeur de code dans l'admin. |
| `DISALLOW_FILE_MODS` | `true` (sauf `development`) | Pas d'installation de plugin ou de thème depuis l'admin. |
| `AUTOMATIC_UPDATER_DISABLED` | `true` | Les mises à jour passent par Composer. |
| `WP_DEBUG_DISPLAY` | `false` en production | Aucune erreur renvoyée au navigateur. |
| `DISALLOW_INDEXING` | `true` en `development` et `staging` | La préprod n'est pas indexée (`noindex, nofollow`). |

> `DISALLOW_FILE_MODS` ne bloque **pas** WP-CLI : `wp language core install`
> fonctionne malgré lui (vérifié en `staging`).

Les **sels** viennent du `.env`, uniques par environnement, jamais versionnés.

## 2. Serveur web — `website/.htaccess`

Tout est dans le bloc `# BEGIN Optelya Security`, **au-dessus** du bloc généré
par WordPress (réécrit par `wp rewrite flush --hard`).

En préprod et en production, **c'est `shared/.htaccess` qui est servi** (le
fichier du dépôt y arrive en `.htaccess.dist`, référence non servie) : une
modification de ce bloc doit être reportée à la main sur chaque serveur — voir
[`deploiement.md`](deploiement.md).

### En-têtes posés

| En-tête | Valeur |
| --- | --- |
| `Content-Security-Policy-Report-Only` | voir ci-dessous — **front uniquement** |
| `X-Content-Type-Options` | `nosniff` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `geolocation=(), camera=(), microphone=(), payment=(), browsing-topics=()` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `Cross-Origin-Opener-Policy` | `same-origin` |
| `Strict-Transport-Security` | *en commentaire* : à activer une fois le site en HTTPS |

> `X-Frame-Options: SAMEORIGIN` empêche d'afficher le site dans une iframe
> d'une autre origine — y compris un outil de recette local. C'est voulu.

### Fichiers refusés (403)

Fichiers cachés (dont `.env`), `wp-config.php`, `composer.json`/`.lock`,
`package*.json`, `.log`, `.ini`, `.sh`, `.sql`, `.bak`, `xmlrpc.php`, et les
dossiers `vendor/` / `node_modules/` sous `app/`. Le listage des dossiers est
coupé par `IndexIgnore *` : `Options -Indexes` exigerait `AllowOverride
Options`, que beaucoup de mutualisés refusent avec une erreur 500.

### CSP

```
default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';
img-src 'self' data:; font-src 'self'; connect-src 'self';
frame-ancestors 'self'; base-uri 'self'; object-src 'none'; form-action 'self'
```

- **En observation** (`-Report-Only`) : rien n'est bloqué, les violations
  s'affichent dans la console. Pour l'appliquer, parcourir chaque gabarit
  console ouverte, corriger, puis retirer `-Report-Only`.
- **Retirée sous `/wordpress-core/`** (admin et connexion, qui reposent sur du
  script inline) par une condition `expr=` de `Header`. La page de connexion
  porte quand même un `Content-Security-Policy: frame-ancestors 'self'` : il
  est posé par WordPress lui-même, pas par ce bloc.
- **`font-src 'self'`** tient parce que les polices sont **hébergées sur le
  site** (`@fontsource`, compilées dans `dist/fonts/`). Aucune requête vers
  Google Fonts.

## 3. Thème — `inc/security.php`

| Mesure | Pourquoi |
| --- | --- |
| Emoji du cœur désactivé | Supprime un `<script>` inline : c'est ce qui permet `script-src 'self'`. |
| `<meta name="generator">` supprimé | Ne plus annoncer la version de WordPress. Le `?ver=` des assets du cœur (admin, connexion) est **gardé** : sans lui, une mise à jour laisserait un mois d'anciens styles en cache. |
| Liens RSD / WLW supprimés | Endpoints legacy qui annoncent XML-RPC. |
| XML-RPC désactivé | Bruteforce et amplification pingback. |
| `?author=N` et `/author/<login>/` en **404** | Bloque l'énumération des comptes. |
| `/wp/v2/users` fermé aux anonymes (401) | Équivalent JSON de la fuite précédente. |
| Message de connexion générique | Ne confirme plus quels comptes existent. |

> **Énumération des comptes : la priorité compte.** Accroché à la priorité par
> défaut (10), le blocage passait **après** `redirect_canonical()` du cœur, qui
> envoyait `?author=1` vers `/author/admin/` — l'identifiant fuitait. Il tourne
> donc en priorité 1, et répond 404 **que le compte existe ou non** : une
> redirection pour les comptes connus et une 404 pour les autres les
> distingueraient encore. Vérifié : les réponses sont identiques octet pour
> octet. **LCDS a le même défaut** (constaté le 23/10/2026).

## 4. ACF

- Les écrans d'ACF ne s'affichent qu'en `development` et `local` : ailleurs, ils seraient la porte par laquelle la configuration
  divergerait du dépôt (`acf-json/`). La saisie des contenus n'est pas touchée.
- Tout appel à ACF est gardé : sans le plugin, la page répond 200, sans ses
  sections (`tests/Unit/AcfGuardTest.php`).
- Les valeurs saisies sont échappées en sortie (`esc_html`, `esc_url`,
  `esc_attr`) ; un lien ouvert dans un nouvel onglet porte `rel="noopener"`.

## 5. CI

- **CVE des dépendances** : `composer audit --locked`.
- **Secrets** : gitleaks sur les commits poussés ou ceux de la pull request
  (pas sur tout l'historique : vérifié dans le code de `gitleaks-action`).
- **SAST** : CodeQL, JavaScript uniquement (CodeQL ne supporte pas PHP).

Voir [`ci-cd.md`](ci-cd.md).

# Performance

## Audit Lighthouse du 05/11/2026

Lighthouse 13.5.0, Chrome headless, page d'accueil **locale** (Docker,
`WP_ENV='development'`). La production est protégée par mot de passe : elle
n'a pas été mesurée. Les temps absolus d'un serveur local ne valent pas ceux
de la production ; les constats de structure (poids, requêtes, cache, décalages)
sont, eux, transposables.

| | Mobile avant | Mobile après | Desktop avant | Desktop après |
| --- | --- | --- | --- | --- |
| Performance | 95 | **100** | 100 | **100** |
| Accessibilité | 100 | 100 | 100 | 100 |
| Bonnes pratiques | 96 | **100** | 96 | **100** |
| SEO | 58 | 66 | 58 | 66 |
| LCP | 1,7 s | 1,6 s | 0,4 s | 0,4 s |
| CLS | **0,126** | **0** | 0,011 | 0 |
| TBT | 0 ms | 0 ms | 0 ms | 0 ms |

Poids total de la page : **112 Ko**.

**SEO à 66** : le seul point restant est l'indexation interdite, voulue hors
production (`DISALLOW_INDEXING` en `development` et `staging`). En
`production`, il disparaît.

## Ce qui a été corrigé

| Constat | Cause | Correction |
| --- | --- | --- |
| Décalage de mise en page de 0,1 sur mobile | Le menu s'affichait déplié, puis le script le repliait : toute la page remontait | Le repli est décidé par la feuille de style (`@media (scripting: enabled)`), avant le premier affichage. Sans script, ou si le navigateur ignore cette règle, le menu reste déplié |
| Décalage de 0,027 au chargement des polices | Polices découvertes après la feuille de style | `<link rel="preload">` des deux polices du premier écran (`inc/setup.php`) |
| Polices, CSS et JS sans durée de cache | `Header set Cache-Control "public"` remplaçait le `max-age` de `mod_expires` | `Header merge` : les fichiers partent avec `max-age=2592000, public` (30 jours) |
| Violation de la CSP (script en ligne) | Préchargement spéculatif de WordPress (`<script type="speculationrules">`) | Désactivé (`inc/security.php`) : une page à ancres n'en tire rien |
| Pas de meta description | Aucune extension SEO | Champ dans *Réglages → Configuration* (`inc/seo.php`) |

Reste signalé, sans action : la feuille de style bloque le premier affichage
(4,4 Ko, gain estimé de 80 à 470 ms selon l'appareil simulé). L'éclater en CSS
critique en ligne coûterait plus qu'il ne rapporte à ce poids.

## Second audit (05/11/2026, 0.16.0)

Lighthouse 13.5.0, médiane de 3 passes : 100 partout hors SEO, CLS 0, LCP
1,7 s en mobile (le titre du hero), 0,4 s en desktop. Corrigé :

| Constat | Correction |
| --- | --- |
| Police IBM Plex Sans **grecque** téléchargée (20 Ko, sur la chaîne critique) pour « σ » et « λ » | `assets/styles/fonts.css` ne déclare que les sous-ensembles **latin** et **latin-ext** des polices variables (σ, λ en police de repli). **À régénérer après une mise à jour d'`@fontsource`** : blocs copiés des `wght.css` des paquets |
| `main.js` servi sans compression : Apache le type `text/javascript`, absent de la règle DEFLATE | `text/javascript` ajouté à `AddOutputFilterByType DEFLATE` |
| `/favicon.ico` → redirection par WordPress (démarrage PHP) vers le logo WordPress | Contenu : définir l'icône du site — voir [`seo.md`](seo.md) |

Poids de la page : 132 → **125 Kio**. Recommandations d'hébergement, par
priorité : vérifier `shared/.htaccess` (ci-dessous), HTTP/2 et OPcache, puis
un cache de page — seul vrai levier sur le temps de réponse. Cache objet,
Brotli, CDN : gain négligeable à ce poids.

**Cache de page en place depuis la 0.21.0** (WP Super Cache, `PAGE_CACHE` dans
le `.env`) : accueil de 459 à 40 ms, fiche solution de 153 à 55 ms, mesuré en
local — voir [`cache.md`](cache.md).

## ⚠️ À reporter sur chaque serveur

Le serveur applique **`shared/.htaccess`**, pas le fichier du dépôt (voir
[`deploiement.md`](deploiement.md)). La correction du cache ne l'atteint donc
pas seule : le déploiement signale la divergence, et il faut remplacer à la main,
dans `shared/.htaccess` :

```apache
Header set Cache-Control "public"
```

par :

```apache
Header merge Cache-Control "public"
```

et, depuis la 0.16.0, ajouter `text/javascript` à la ligne
`AddOutputFilterByType DEFLATE …` (le plus simple : recopier le bloc du
`.htaccess` du dépôt).

## Relancer l'audit

```bash
CHROME_PATH="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
  npx lighthouse@13.5.0 http://localhost:8040/ --chrome-flags="--headless=new" \
  --output=html --output-path=./lighthouse-mobile.html
# Desktop : ajouter --preset=desktop
```

Le rapport produit n'est pas à committer.

# Cache

Quatre couches. Le module maison **`rapkalin-cache`** (`shared/mu-plugins/`, hors
dépôt) tient ensemble les deux qui dépendent du contenu ; son fonctionnement,
son API et ses conventions sont dans son `README.md`. Ce fichier décrit ce qui
est propre à Optelya.

| Couche | Où | État |
| --- | --- | --- |
| Navigateur (CSS, JS, polices, images) | `.htaccess`, voir [`performance.md`](performance.md) | en place |
| OPcache (code PHP compilé) | serveur | en place, vidé seulement au rechargement de PHP-FPM |
| **Cache de page** (HTML complet) | WP Super Cache, `website/app/wp-cache-config.php` | **activé par `PAGE_CACHE`** |
| **Cache applicatif** (requêtes coûteuses) | `rapkalin-cache` | prêt, **aucune clé** pour l'instant |
| Cache objet persistant (Redis) | — | inutile à ce poids de site |

Mesuré en local (06/11/2026) : l'accueil passe de **459 ms à 40 ms**, une fiche
solution de **153 ms à 55 ms**.

```bash
docker compose exec php wp --allow-root rapkalin-cache status   # état de chaque couche
```

## Cache de page

WP Super Cache garde le HTML de chaque page et le ressert aux visiteurs
**anonymes** sans démarrer WordPress. Il est installé par Composer, toujours
actif, et allumé par **`PAGE_CACHE`** dans le `.env` : `'false'` en local (on voit
chaque modification sans purger), `'true'` en préprod et en production.

### Ce qui n'est jamais mis en cache

- un visiteur **connecté** (`$wp_cache_not_logged_in = 2`) ;
- une adresse **avec paramètres** : résultat du formulaire (`?contact=sent`),
  liens de campagne, recherche (`$wp_cache_no_cache_for_get = 1`) ;
- un envoi de formulaire (POST), une page 404, l'admin, la page de connexion.

### Durée de vie : 12 h, à cause du formulaire de contact

Le formulaire de contact (accueil, fiches solution) porte un **jeton horodaté
valable 24 h** (voir [`contact.md`](contact.md)). Une page servie depuis le
cache montre le jeton de l'heure où elle a été fabriquée : au-delà de 24 h,
l'envoi serait refusé comme « expiré ». La durée de vie est donc de **12 h**
(`$cache_max_time = 43200`), contrôlée au moment de servir la page, **en mode
PHP seulement** : garder `$wp_cache_mod_rewrite = 0` (en mode réécriture,
Apache servirait le fichier sans vérifier son âge). Vérifié : un envoi depuis
une page en cache aboutit, et une page vieillie de 13 h est refabriquée.

### Ce qui le vide

Tout le site est vidé, pas seulement la page modifiée : à cette taille, c'est
plus sûr et sans coût.

| Action dans l'admin | Vidé par |
| --- | --- |
| Publier, modifier, dépublier ou supprimer un contenu publié | WP Super Cache |
| Modifier un menu | WP Super Cache |
| Enregistrer *Réglages → Configuration* (couleur du menu, contact…) | `rapkalin-cache` |
| Modifier le titre du site, son icône, la page d'accueil | `rapkalin-cache` |
| Renommer ou supprimer un terme | `rapkalin-cache` |
| Bouton **Vider le cache** (barre d'outils, éditeurs et administrateurs) | `rapkalin-cache` |
| Chaque déploiement (la version du pied de page change) | `wp rapkalin-cache flush` |

Sans le module, seules les deux premières lignes s'appliquent : une modification
des réglages resterait invisible jusqu'à 12 h.

### Configuration versionnée : à ne pas laisser dériver

`website/app/wp-cache-config.php` et `website/app/advanced-cache.php` sont
**versionnés** : ce sont les réglages, pas un cache. Le plugin réécrit une ligne
de ce fichier quand on enregistre son écran de réglages ; le déploiement remet
la version du dépôt. Changer un réglage = le changer dans le dépôt. Le
commentaire en tête du fichier liste les valeurs qui comptent.

Trois pièges du plugin, constatés le 06/11/2026 sur la 3.1.5 :

1. **`WP_CACHE` reste toujours vrai** (`config/application.php`). À faux, le
   plugin écrit `define('WP_CACHE', true)` dans `website/wp-config.php`, à
   l'activation et sur son écran de réglages. C'est pourquoi l'interrupteur est
   `PAGE_CACHE`, lu par `$cache_enabled`.
2. **Le nom `WPCACHEHOME` doit rester écrit dans `website/wp-config.php`** (un
   commentaire suffit). Le plugin y cherche cette chaîne et, faute de la
   trouver, ajoute sa propre définition : la constante est alors définie deux
   fois et le site plante.
3. **Ne jamais désactiver le plugin** : il supprime `website/app/cache/` (sur le
   serveur, le contenu de `shared/cache/`) et écrit `$cache_enabled = false`
   dans le fichier versionné. Pour couper le cache : `PAGE_CACHE='false'`.

Le secret de contournement (`?donotcachepage=`) est dérivé d'un sel du `.env` :
aucun secret dans le dépôt. Les valeurs `$wp_cache_home_path`
(`/wordpress-core/`) et `$wp_cache_slash_check` sont celles que l'écran de
réglages calculerait : l'afficher ne modifie pas le fichier (vérifié).

### Vérifier un hit

```bash
curl -s https://<site>/ | grep -o 'WP-Super-Cache on [0-9: -]*'
```

Si l'heure ne change pas d'un rechargement à l'autre, la page vient du cache.
Les fichiers sont sous `website/app/cache/supercache/<hôte>/`.

## Cache applicatif

Prêt, sans clé : aucune requête du site ne le justifie aujourd'hui. Les clés
seront ajoutées avec l'archive des solutions, l'archive des industries et la
bibliothèque d'études (39 études, filtres) : une enum `OptelyaCacheKey` dans
le thème (`inc/enums/`), qui implémente `RapkalinCacheEntry` et s'enregistre
par le filtre `rapkalin_cache_entries`. Mode d'emploi : `README.md` du module.

## Commandes

```bash
docker compose exec php wp --allow-root rapkalin-cache flush            # tout
docker compose exec php wp --allow-root rapkalin-cache flush --pages    # pages seules
docker compose exec php wp --allow-root rapkalin-cache flush --data     # données seules
```

`wp cache flush` ne vide que le cache objet : ni les pages, ni les données.

# Référencement (SEO)

Pas d'extension SEO : le thème gère seul le peu dont une page vitrine a besoin
(`inc/seo.php`, `inc/enums/OptelyaShareTags.php`). Audit technique du
05/11/2026 sur le site local ; ses corrections sont en 0.16.0.

## Ce qui se règle dans l'admin

*Réglages → Configuration* :

| Champ | Où il sort | Conseil |
| --- | --- | --- |
| Titre pour les moteurs de recherche (accueil) | `<title>` de l'accueil, onglet, `og:title` | 50 à 60 caractères, avec les mots que les clients cherchent. Vide : le nom du site |
| Description pour les moteurs de recherche | `<meta name="description">`, `og:description` | 160 caractères au plus. Vide : l'accroche. Une fiche solution prend sa **promesse** ; son titre est « Produit – Éditeur – Optelya » |
| Image de partage | `og:image` (aperçu LinkedIn, Teams…) | 1200 × 630 px, texte alternatif renseigné dans la médiathèque. Vide : aperçu sans image |
| Logo de l'en-tête | `logo` des données structurées | — |

## Ce que fait le thème

| Mesure | Détail |
| --- | --- |
| Open Graph et Twitter | Sur l'accueil et les pages, pas sur la 404, la recherche ni les archives |
| Données structurées | JSON-LD `Organization` + `WebSite` sur l'accueil. Bloc de données, non exécuté : la CSP `script-src 'self'` ne s'y applique pas (aucune violation dans la console) |
| Sitemap | `/wp-sitemap.xml` ne liste que les **pages** et les **solutions** (`OPTELYA_SITEMAP_POST_TYPES`) : ni comptes (l'URL `/author/admin/`, en 404, aurait exposé l'identifiant), ni catégories. **Un type de contenu y est ajouté quand sa page existe** (industries, éditeurs, études : pas encore) |
| Archives, flux, pages de médias | Réponse **404** : le site n'a pas de blog, elles ne faisaient que dupliquer ou appauvrir le contenu. La recherche garde le `noindex` du cœur |
| `<head>` allégé | Plus de liens de flux, oEmbed, REST, shortlink (ni en en-têtes HTTP) |
| Retour du formulaire | `?contact=…` en `noindex` |

## Indexation selon l'environnement

`DISALLOW_INDEXING` (`config/environments/development.php` et
`staging.php`) force `noindex, nofollow` et ferme le sitemap. En production, la
constante n'est pas définie : le site devient indexable. **La protection par
mot de passe de la préproduction ou de la production, elle, bloque tout** : à
lever à la mise en ligne.

## Avant la mise en ligne (contenu, dans l'admin)

- Supprimer les contenus de démonstration de WordPress : l'article « Hello
  world! », la page « Sample Page » (sinon listée dans le sitemap) et le
  commentaire de démo. Fermer les commentaires par défaut (*Réglages →
  Discussion*).
- Définir l'**icône du site** (512 × 512 px, *Apparence → Personnaliser →
  Identité du site*) : sans elle, `/favicon.ico` redirige vers le logo de
  WordPress.
- Renseigner l'image de partage et relire titre et description, avec les mots
  « simulation », « optimisation », « tolérancement ».
- Remplacer les contenus provisoires (« [délai à fournir] », « [adresse à
  fournir] », légende de maquette du visuel d'accueil).
- Créer les pages légales et faire pointer les liens vers de vraies pages
  (voir [`accessibilite.md`](accessibilite.md)).

## À vérifier en production

- `curl -s https://<domaine>/ | grep robots` : pas de `noindex`.
- `curl -s https://<domaine>/robots.txt` : la ligne `Sitemap:` apparaît.
- `/wp-sitemap.xml` : seulement les pages voulues.
- `curl -I http://<domaine>/` : 301 vers https.
- Search Console : propriété, envoi du sitemap, inspection de l'accueil, test
  des résultats enrichis. Inspecteur de publication LinkedIn pour l'aperçu.

## Au-delà du technique

Une page d'accueil seule ne se positionnera pas sur chaque besoin ni chaque
éditeur (≈ 480 mots aujourd'hui). Le levier suivant est éditorial : une page
par enjeu (optimisation, tolérancement, simulation multicorps), par éditeur,
et les études de cas.

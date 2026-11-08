# Référencement (SEO)

Pas d'extension SEO : le thème gère seul le peu dont une page vitrine a besoin
(`inc/seo.php`, `inc/enums/OptelyaShareTags.php`). Audit technique du
05/11/2026 sur le site local ; ses corrections sont en 0.16.0. Audits SEO et
GEO (moteurs de réponse IA) du 08/11/2026 : corrections du code en 0.25.0.

## Ce qui se règle dans l'admin

*Réglages → Configuration* :

| Champ | Où il sort | Conseil |
| --- | --- | --- |
| Titre pour les moteurs de recherche (accueil) | `<title>` de l'accueil, onglet, `og:title` | 50 à 60 caractères, avec les mots que les clients cherchent. Vide : le nom du site |
| Description pour les moteurs de recherche | `<meta name="description">` et `og:description` **de l'accueil**, description de l'organisation dans les données structurées | 160 caractères au plus. Vide : l'accroche. Une fiche solution prend sa **promesse** ; son titre est « Produit – Éditeur – Optelya ». Les autres pages n'ont pas de description tant que leur type n'en prévoit pas : mieux vaut rien que celle de l'accueil répétée |
| Image de partage | `og:image` (aperçu LinkedIn, Teams, messageries) | Recadrée automatiquement en **1200 × 630 px** (format `optelya-share`) : garder l'essentiel au centre. Texte alternatif renseigné dans la médiathèque. Une fiche solution montre son **visuel** à la place, s'il en a un. Vide : aperçu sans image |
| Profils de l'entreprise | `sameAs` des données structurées | Une adresse par ligne (LinkedIn, YouTube…). Dit aux moteurs et aux assistants IA qui est l'entreprise ; rien ne s'affiche |
| Logo de l'en-tête | `logo` des données structurées | — |

## Ce que fait le thème

| Mesure | Détail |
| --- | --- |
| Open Graph et Twitter | Sur l'accueil et les pages, pas sur la 404, la recherche ni les archives |
| Données structurées | JSON-LD sur chaque page : `Organization` (logo, description, `areaServed`, `sameAs`) + `WebSite`. Sur une fiche solution, en plus : `WebPage`, `BreadcrumbList` (le fil d'Ariane visible, même fonction `optelya_solution_breadcrumb()`) et `Product` fabriqué par l'éditeur. `Product` plutôt que `SoftwareApplication` : la « Formation ISO GPS » n'est pas un logiciel. Sans prix ni avis, il n'ouvre **pas** de résultat enrichi : la Search Console peut le signaler, sans conséquence sur le classement. Bloc de données, non exécuté : la CSP `script-src 'self'` ne s'y applique pas |
| Pages indexées et sitemap | Une seule liste, `OPTELYA_INDEXED_POST_TYPES` (**pages** et **solutions**) : ces types sont dans `/wp-sitemap.xml` ; les autres (études, éditeurs, industries, articles) et **toutes les listes** (`/solutions/`, `/etudes-de-cas/`, `/industries/`) répondent `noindex`, liens suivis, tant que leur gabarit n'existe pas. **Un type y est ajouté quand sa page est livrée.** Ni comptes (l'URL `/author/admin/`, en 404, aurait exposé l'identifiant), ni catégories |
| Archives de dates, catégories, étiquettes, flux, pages de médias | Réponse **404** : le site n'a pas de blog, elles ne faisaient que dupliquer ou appauvrir le contenu. Les « pages » de l'accueil (`/page/2/`) redirigent vers l'accueil. La recherche garde le `noindex` du cœur |
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
- Renseigner l'image de partage, les visuels des fiches solution et les
  profils de l'entreprise (LinkedIn), et relire titre et description, avec
  les mots « simulation », « optimisation », « tolérancement ».
- Faire pointer les cartes « Quel est votre enjeu ? » et les menus vers les
  fiches solution : aujourd'hui, aucune n'est reliée depuis l'accueil, et les
  robots (moteurs comme assistants IA) découvrent les pages par les liens.
- Dire en toutes lettres qui est Optelya pour chaque éditeur (« distributeur en
  France de… », statut exact à faire valider par le client) : sans cette
  phrase, un assistant IA ne peut pas répondre « qui distribue RecurDyn en
  France ? ».
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
  des résultats enrichis. Bing Webmaster Tools aussi (ChatGPT Search et
  Copilot s'appuient sur son index).
- Aperçus de partage : inspecteur de publication LinkedIn
  (`linkedin.com/post-inspector`), débogueur de partage Facebook. Ils mettent
  l'aperçu en cache : y relancer l'analyse après un changement d'image.
  **La protection par mot de passe empêche tout aperçu** : à tester après
  l'avoir levée.

## Moteurs de réponse IA (GEO)

Seule règle officielle (Google, documentation des fonctionnalités IA) : rien
de plus que le SEO — laisser explorer, relier les pages, garder le contenu en
texte, des données structurées conformes au contenu visible. Aucun robot IA
n'est bloqué (ni `robots.txt`, ni `.htaccess`, ni le cache), et tout le contenu
est dans le HTML servi. Bloquer l'entraînement (GPTBot, ClaudeBot,
Google-Extended) est une décision d'affaires, pas technique : cela ne retire
pas le site de ChatGPT Search (OpenAI le documente). Pas de `llms.txt` : aucun
moteur ne déclare le lire.

## Au-delà du technique

Une page d'accueil seule ne se positionnera pas sur chaque besoin ni chaque
éditeur (≈ 480 mots aujourd'hui). Le levier suivant est éditorial : une page
par enjeu (optimisation, tolérancement, simulation multicorps), par éditeur,
et les études de cas.

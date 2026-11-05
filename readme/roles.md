# Rôles et périmètre de l'administration

Repris de LCDS (`~/Sites/lcds/readme/roles.md`), sans les parties propres à
Yoast, absent d'Optelya. Les deux rôles sont décrits dans un seul enum,
`inc/enums/OptelyaRole.php` ; `inc/roles.php` les déclare.

## Les deux rôles

| | Contributeur Optelya | Éditeur Optelya |
| --- | --- | --- |
| Slug | `optelya_contributor` | `optelya_editor` |
| Bâti sur | l'**auteur** de WordPress | l'**éditeur** de WordPress |
| Pages | **les siennes** : créer, publier, modifier, supprimer | **toutes** |
| Médias | **les siens** : envoyer, modifier, supprimer | **tous** |
| Taxonomies | — | celles des pages et des médias (aucune aujourd'hui) |
| Menus, personnalisateur | — | `edit_theme_options` |
| *Réglages → Configuration* | `optelya_manage_settings` | `optelya_manage_settings` |
| Email, Connexions, Activité, Journal, comptes, extensions, réglages du cœur | — | — |

Le contributeur reçoit les capacités de l'auteur (qui ne nomment que les
articles) **plus** leurs équivalents pour les pages : `edit_pages`,
`edit_published_pages`, `publish_pages`, `delete_pages`,
`delete_published_pages`, et `optelya_manage_settings` pour *Réglages →
Configuration*. Aucune capacité `*_others_*` : WordPress lui refuse
lui-même les contenus des autres.

> **`edit_posts` et `delete_posts` restent** : pour un média, WordPress vérifie
> ces deux capacités-là (statut `inherit`, `wp-includes/capabilities.php`). Les
> retirer empêcherait le contributeur de modifier ses propres médias.

> **L'accueil appartient à l'administrateur** : un contributeur ne peut plus le
> modifier, un éditeur oui. Seul un administrateur peut le **supprimer** — règle
> du cœur pour la page d'accueil.

> **La Configuration est partagée** : slogan, bouton d'en-tête, coordonnées
> s'affichent sur **toutes** les pages. Un contributeur, limité à ses propres
> pages, modifie donc aussi l'en-tête et le pied de page de tout le site —
> décision du 05/11/2026. Chaque enregistrement est inscrit dans *Outils →
> Activité*, avec son auteur.

Ce que voit chacun, vérifié sur une session authentifiée le 29/10/2026
(Configuration du contributeur : 05/11/2026) :

```
Contributeur : Tableau de bord | Médias | Pages | Profil | Réglages
                 Réglages  : Configuration
Éditeur      : Tableau de bord | Médias | Pages | Apparence | Profil | Réglages
                 Apparence : Personnaliser · Menus
                 Réglages  : Configuration
```

## Pourquoi une capacité dédiée plutôt que `manage_options`

`manage_options` ouvre **les sept écrans de Réglages du cœur** — dont
*Lecture*, qui désigne la page d'accueil, et *Permaliens* — et `options.php`,
l'éditeur brut de toutes les options en base. Le donner à un éditeur ou à un
contributeur en ferait un administrateur. L'écran *Configuration* exige donc
`optelya_manage_settings`, que l'administrateur reçoit aussi.

WordPress promeut le premier sous-menu accessible au rang de parent : pour les
deux rôles, « Réglages » apparaît avec « Configuration » pour seul contenu.

## Masquer ne protège pas

Retirer une entrée de menu est cosmétique : l'URL reste tapable. Chaque écran
hors périmètre est aussi **refusé à l'accès (403)** par une **allow-list** par
rôle — `inc/enums/OptelyaAdminScreen.php`. Un écran ajouté par une future
extension est refusé par défaut.

Mesuré le 29/10/2026, Configuration et Réglages du cœur le 05/11/2026 :

| Écran | Contrib. | Éditeur | Admin |
| --- | --- | --- | --- |
| Tableau de bord, Pages, Ajouter une page, Médias, Ajouter un média, Profil | 200 | 200 | 200 |
| *Réglages → Configuration* (affichage et enregistrement) | 200 | 200 | 200 |
| Menus, Personnaliser | **403** | 200 | 200 |
| *Réglages* du cœur (Général, Lecture…), `options.php` | **403** | **403** | 200 |
| *Réglages → Email*, *Comptes → Connexions*, *Outils → Activité*, *Outils → Journal* | **403** | **403** | 200 |
| Outils, Articles, Comptes, Extensions, Catégories | **403** | **403** | 200 |
| Modifier l'accueil (page d'un autre) | **403** | 200 | 200 |
| Modifier sa propre page, son propre média | 200 | 200 | 200 |
| Modifier le média d'un autre | **403** | 200 | 200 |

`isAllowed()` est une fonction **pure**, d'où `tests/Unit/AdminScreenTest.php`.
Le même test vérifie que le slug de l'écran *Configuration* est celui du JSON
d'ACF (`group_optelya_settings.json`), qui ne peut pas lire la constante.

## Trois rôles attribuables

*Comptes → Ajouter* ne propose que **Administrateur**, **Éditeur Optelya** et
**Contributeur Optelya**. Les autres rôles restent **déclarés** : les retirer
casserait un compte qui les porte. Le rôle du compte en cours d'édition reste
dans la liste, sinon l'enregistrement le changerait sans que personne l'ait
demandé.

**Seul un administrateur crée un compte** : les deux rôles n'accèdent pas à
*Comptes* (403).

## « Mon compte », réduit à l'utile

Pour les deux rôles, sur **leur propre profil** seulement — un administrateur
qui modifie un autre compte garde le formulaire entier. Restent : couleurs,
barre d'outils, identifiant, prénom, nom, pseudo, e-mail, et la gestion du
compte (mot de passe, sessions).

- **Côté serveur** : moyens de contact, capacités supplémentaires et mots de
  passe d'application sont retirés par de vrais filtres.
- **Par feuille de style** : les autres lignes, faute de filtre par champ dans
  le cœur. C'est de la mise en forme, pas une barrière — ce sont les données du
  compte lui-même.
- `tests/Unit/ProfileFieldTest.php` échoue si une mise à jour de WordPress
  ajoute une ligne au profil sans qu'elle soit classée.

> **Mots de passe d'application en local** : le cœur affiche la section, avec un
> avertissement, quand le site n'est **pas en HTTPS** (`user-edit.php:801`) ; la
> feuille de style la masque. En HTTPS, le filtre l'empêche d'être rendue.

## Le tableau de bord

Réduit à **« D'un coup d'œil »**, pour tout le monde.

> **Limite connue, partagée avec LCDS** : « D'un coup d'œil » affiche le nombre
> d'articles avec un lien vers leur liste, qui répond 403 aux deux rôles. Ils
> gardent `edit_posts`, indispensable pour les médias (voir plus haut).

## Faire évoluer les rôles

Les capacités sont posées une fois, gardées par `OPTELYA_ROLE_VERSION`
(`inc/roles.php`) : **incrémenter la constante** pour rejouer la définition sur
tous les environnements. Elle est rejouée au premier chargement de l'admin,
quel que soit le compte connecté — pas par WP-CLI. Chaque rôle est reconstruit
à partir de son rôle de base du cœur : une capacité que WordPress ajoute à
« auteur » ou « éditeur » lui parvient à l'incrément suivant.

## Ce qui n'est pas repris de LCDS

- les règles propres à **Yoast** (menu retiré par préfixe, boîte SEO sous la
  contribution) : Yoast n'est pas installé.

Le journal des connexions de LCDS est remplacé par *Comptes → Connexions* — voir
[`journal.md`](journal.md).

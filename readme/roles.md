# Rôles et périmètre de l'administration

Repris de LCDS (`~/Sites/lcds/readme/roles.md`), sans les parties propres à
Yoast, absent d'Optelya.

## Le rôle « Contributeur Optelya »

`optelya_contributor` (`inc/roles.php`), bâti sur les capacités de
l'**éditeur** — qui n'a ni `manage_options`, ni `activate_plugins`, ni
`switch_themes` — plus deux ajouts :

| Capacité | Ce qu'elle ouvre |
| --- | --- |
| `edit_theme_options` | les menus et le personnalisateur |
| `optelya_manage_settings` | l'écran *Réglages → Configuration*, et lui seul |

Ce que voit un contributeur, vérifié sur une session authentifiée le
25/10/2026 :

```
Tableau de bord | Médias | Pages | Apparence | Profil | Réglages
    Pages     : Toutes les pages · Ajouter une page
    Médias    : Médiathèque · Ajouter un fichier média
    Apparence : Personnaliser · Menus
    Réglages  : Configuration
```

## Pourquoi une capacité dédiée plutôt que `manage_options`

`manage_options` ouvre **les sept écrans de Réglages du cœur** — dont
*Lecture*, qui désigne la page d'accueil, et *Permaliens* — et `options.php`,
l'éditeur brut de toutes les options en base. Le donner à un contributeur en
ferait un administrateur. L'écran *Configuration* exige donc
`optelya_manage_settings`, que l'administrateur reçoit aussi.

WordPress promeut le premier sous-menu accessible au rang de parent : « Réglages »
apparaît avec « Configuration » pour seul contenu.

## Masquer ne protège pas

Retirer une entrée de menu est cosmétique : l'URL reste tapable. Chaque écran
hors périmètre est aussi **refusé à l'accès (403)** par une **allow-list** —
`inc/enums/OptelyaAdminScreen.php`. Un écran ajouté par une future extension
est refusé par défaut.

Mesuré sur une session de contributeur :

| Écran | Réponse |
| --- | --- |
| Tableau de bord, Pages, édition de l'accueil, Médias, Menus, Personnaliser, Configuration, Profil | **200** |
| Articles, Thèmes, Éditeur de site, Extensions, Comptes, Ajout de compte, Outils, Santé du site, Commentaires, Réglages du cœur (Général, Lecture, Permaliens), `options.php`, groupes de champs ACF | **403** |

`isAllowed()` est une fonction **pure**, d'où `tests/Unit/AdminScreenTest.php`.
Le même test vérifie que le slug de l'écran *Configuration* est celui du JSON
d'ACF (`group_optelya_settings.json`), qui ne peut pas lire la constante.

## Deux rôles attribuables

*Comptes → Ajouter* ne propose plus que **Administrateur** et **Contributeur
Optelya** (vérifié). Les autres rôles restent **déclarés** : les retirer
casserait un compte qui les porte. Le rôle du compte en cours d'édition reste
dans la liste, sinon l'enregistrement le changerait sans que personne l'ait
demandé.

**Seul un administrateur crée un compte** : le contributeur n'accède pas à
*Comptes* (403).

## « Mon compte », réduit à l'utile

Sur **son propre profil** seulement — un administrateur qui modifie un
contributeur garde le formulaire entier. Restent : couleurs, barre d'outils,
identifiant, prénom, nom, pseudo, e-mail, et la gestion du compte (mot de passe,
sessions).

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
> d'articles avec un lien vers leur liste, qui répond 403 au contributeur. Le
> rôle hérite de `edit_posts` de l'éditeur ; le retirer masquerait les articles
> nativement, mais demande de vérifier ce qui en dépend encore.

## Faire évoluer le rôle

Les capacités sont posées une fois, gardées par `OPTELYA_ROLE_VERSION` :
**incrémenter la constante** pour rejouer la définition sur tous les
environnements.

## Ce qui n'est pas repris de LCDS

- le **journal des connexions** (*Comptes → Connexions*) : une fonctionnalité,
  pas une restriction ;
- les règles propres à **Yoast** (menu retiré par préfixe, boîte SEO sous la
  contribution) : Yoast n'est pas installé.

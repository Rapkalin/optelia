# Contribution du contenu

Le contenu de la page d'accueil se modifie depuis l'administration. Restent
dans le code les chaînes d'interface et le formulaire (voir
[Ce qui reste dans le code](#ce-qui-reste-dans-le-code)).

| Quoi | Où |
| --- | --- |
| Sections de l'accueil | *Pages → Accueil → Modifier* |
| Logo et couleur de fond de l'en-tête, accroche, titre et description pour les moteurs de recherche, image de partage, bouton de l'en-tête, destinataire des demandes de contact, titre de la colonne contact, adresse, message « bientôt disponible » | *Réglages → Configuration* — sans logo, le nom du site s'affiche |
| Liens de l'en-tête et du pied de page, et lesquels sont « bientôt disponibles » | *Apparence → Menus* — voir [Une section pas encore construite](#une-section-pas-encore-construite) |
| Logiciels, éditeurs, industries, études de cas, enjeux | *Solutions*, *Éditeurs*, *Industries*, *Études de cas* — voir [Le catalogue](#le-catalogue) |
| Titre et panneaux « Ce qu'Optelya ajoute » communs à toutes les fiches solution | *Solutions → Réglages des solutions* |

Ces trois écrans sont ouverts au rôle **Éditeur Optelya**. Le **Contributeur
Optelya** gère ses propres pages et médias et *Réglages → Configuration* : ni
l'accueil (qui appartient à l'administrateur), ni les menus — voir
[`roles.md`](roles.md).

> **Couleur de l'en-tête** : le texte passe seul en foncé ou en blanc, selon ce
> qui se lit le mieux. Une teinte moyenne, sur laquelle aucun des deux
> n'atteint 4,5:1, est refusée à l'enregistrement avec le contraste mesuré
> (`inc/enums/OptelyaHeaderColor.php`). Éviter les orangés : le bouton
> « Parler à un expert » se confondrait avec le fond.

## Les sections de l'accueil

Ce sont les **layouts d'un champ de contenu flexible** ACF : un contributeur les
ajoute, les réordonne et les supprime au glisser-déposer, dans un seul
formulaire. L'éditeur de blocs est **coupé** sur cette page (`inc/editor.php`) :
deux surfaces de saisie, c'est la garantie d'en remplir une mauvaise.

| Section | Ce qu'elle porte |
| --- | --- |
| **Promesse (hero)** | Surtitre, titre `h1`, texte, deux liens, vidéo, image d'attente, légende |
| **Enjeux et secteurs** | Titre, intro, cartes (icône, titre, texte, logiciels, lien), phrase d'aide, pastilles de secteur |
| **Résultats chiffrés** | Titre, intro, lien « toutes les études », cartes chiffrées |
| **Apports par lecteur** | Titre, panneaux (lecteur, fond sombre, apports) |
| **Contact** | Titre, intro, étapes, mention sous le formulaire |

**Chaque section ne peut figurer qu'une fois** (`max: 1`) : elle porte l'ancre
que visent les menus (`#enjeux`, `#expert`…). Une section supprimée laisse ses
liens de menu sans cible : les retirer aussi dans *Apparence → Menus*.

**Le hero est obligatoire** (`min: 1`) et son titre est le `h1` de la page : le
laisser **en tête**. ACF ne sait pas imposer la position ; placé plus bas, le
`h1` arriverait après des `h2`. Les titres de section sont obligatoires ; un
champ facultatif laissé vide n'affiche rien, pas même un bloc vide.

**Les menus** sont rendus à plat (pas de sous-menu dans la maquette) ; l'option
« Ouvrir dans un nouvel onglet » d'un lien est respectée.

**Le hero et sa vidéo.** Boucle MP4 ou WebM téléversée en médiathèque — aucun
service tiers, donc aucun cookie. Elle démarre seule, sauf si le visiteur a
demandé moins d'animations : il voit alors l'image d'attente et peut lancer la
lecture. Sans vidéo ni image, l'emplacement réservé de la maquette s'affiche.

**Les icônes des cartes** sont une liste fixée par le thème
(`OptelyaIcon::issueChoices()`) : le contributeur choisit un nom, jamais un
fichier.

### Ce qui reste dans le code

| Quoi | Pourquoi |
| --- | --- |
| Champs, libellés, mention « (facultatif) », bouton et messages du formulaire | Contrat du traitement de l'envoi — voir [`contact.md`](contact.md) |
| Textes de l'emplacement réservé du hero (« Visuel éditeur… ») | Affichés seulement sans vidéo ni image |
| Numérotation « 01 » | Présentation |
| « Version %s », libellés ARIA, « Aller au contenu » | Chaînes d'interface, traduisibles (`__()`) |

## Une section pas encore construite

Un lien de menu peut viser une page qui n'existe pas encore. Plutôt que de
mener nulle part, on le coche **« Section bientôt disponible »** dans
*Apparence → Menus* (déplier le lien) :

- le menu affiche **« Bientôt »** à côté du lien : le visiteur est prévenu avant
  de cliquer ;
- un clic ouvre un court message, avec un bouton vers le formulaire de contact
  (celui de la page s'il y en a un, sinon celui de l'accueil, comme le bouton de
  l'en-tête) ;
- sans JavaScript, le lien mène directement au formulaire : jamais d'impasse ;
- le titre et le texte du message se règlent une fois pour tout le site dans
  *Réglages → Configuration* ; vides, ils valent « Bientôt disponible » et
  « Cette section est en cours de construction. En attendant, nos experts
  répondent à vos questions. ».

**À décocher dès que la page existe**, en mettant sa vraie adresse dans le lien.
Le dispositif sert le temps de construire le site, pas durablement.

> Les **mentions légales**, la **politique de confidentialité** et la page
> **cookies** sont des obligations : elles peuvent être « bientôt disponibles »
> avant la mise en ligne, jamais après.

Côté code : champ `coming_soon` des liens de menu
(`acf-json/group_optelya_menu_item.json`), `components/coming-soon-link.php`,
`components/coming-soon-dialog.php` (un `<dialog>` natif, écrit une seule fois,
seulement si la page a un tel lien), `assets/scripts/coming-soon.js`, textes par
défaut dans `inc/enums/OptelyaComingSoon.php` (`tests/Unit/ComingSoonTest.php`).

## Le catalogue

Quatre types de contenu, liés entre eux par des **relations** (on choisit un
élément dans une liste, on ne recopie jamais un nom) :

| Type | Adresse | Porte |
| --- | --- | --- |
| **Solution** (logiciel ou formation) | `/solutions/<nom>/` | éditeur, promesse, visuel, 3 bénéfices, vidéo, études de preuve **ou** applications + industries, aide au choix, détails techniques, fiche PDF, titre du bloc contact |
| **Éditeur** | `/editeurs/<nom>/` | ancienneté (« développe ses logiciels depuis 1996 ») |
| **Industrie** | `/industries/<nom>/` | champs de la future page industrie |
| **Étude de cas** | `/etudes-de-cas/` | fiche minimale : chiffre, libellé, source, solutions liées, PDF |

La taxonomie **Enjeux** (optimiser, données, tolérances, mécanismes) classe
solutions et études ; elle n'a pas de page publique.

**Champs fixes par type**, pas de contenu flexible : une fiche solution a
toujours la même structure, dans le même ordre. Un champ facultatif laissé vide
retire sa zone (vidéo, aide au choix, détails), et le fond alterné se recale
sur les zones affichées.

**Ce qui est calculé, pas saisi** :

- le fil d'Ariane (Accueil › Solutions › éditeur › produit) ;
- « Voir les 31 études Optimus » : nombre d'études publiées qui citent la
  solution ;
- la ligne « Un éditeur établi » des panneaux : l'ancienneté de l'éditeur, ou
  rien si elle n'est pas saisie (jamais de « à fournir » publié) ;
- dans l'aide au choix, le nom, l'éditeur et le lien de chaque logiciel, et la
  ligne « Vous êtes sur cette page » ;
- le texte d'introduction, les étapes et la mention du bloc contact : ceux de
  la section Contact de l'accueil, saisis une fois.

**Études ou applications** : une solution qui a des études de preuve les
affiche ; sinon ses applications et ses industries. Le lien secondaire du hero
vise l'une ou l'autre.

**La vidéo de démonstration** ne se charge qu'au clic (MP4 ou WebM en
médiathèque, sous-titres WebVTT en français, transcription facultative).

**Titre et description pour les moteurs** : « Optimus – Noesis Solutions –
Optelya », et la promesse comme description (sinon celle du site).

Gabarit : `single-optelya_solution.php`. Les pages d'archive, industrie et
éditeur ne sont pas encore faites : leurs adresses affichent pour l'instant la
page générique.

## Le contenu initial

`optelya_seed()` (`inc/seed.php`) pose la page d'accueil, les réglages et les
menus, mot pour mot depuis la maquette. Il est appelé par `bin/init.sh` et par
chaque déploiement, mais **ne s'exécute qu'une fois par environnement** : il
pose alors l'option `optelya_seeded`, et tous les appels suivants s'arrêtent
là. Un contributeur qui vide un menu ou retire l'accueil n'est jamais contredit
par le déploiement suivant (vérifié).

Pour rejouer l'amorçage volontairement : `dwp option delete optelya_seeded`
puis redémarrer le conteneur — seules les parties absentes sont recréées.

Le **catalogue** a sa propre garde, `optelya_catalog_seeded`
(`inc/seed-catalog.php`, données dans `inc/seed-catalog-data.php`) : 3 éditeurs,
4 enjeux, 5 industries, 8 solutions, 39 études. Seuls les **7 chiffres
vérifiés** dans un PDF d'étude sont posés ; les valeurs entre crochets restent à
fournir. Une ligne de répéteur doit porter les noms de ses sous-champs, sinon
ACF l'ignore sans erreur : `tests/Unit/SeedCatalogDataTest.php` le vérifie.

Les liens amorcés sont des URL absolues (`http://localhost:8040/#expert`) :
après un import de base d'un environnement à l'autre, passer
`wp search-replace` comme pour tout contenu WordPress.

## Modifier les champs (développeurs)

Les groupes de champs vivent en **JSON local** dans `acf-json/`, versionnés :
ils se modifient en local (*ACF → Groupes de champs*, visible en
`development` et `local` seulement), se relisent en diff et se committent.

### Ajouter une section

1. Créer `layouts/<nom>.php` : il lit ses sous-champs par
   `optelya_sub_field()` / `optelya_sub_text()` et rend le balisage.
2. Ajouter le layout au champ `sections` de
   `acf-json/group_optelya_homepage.json`, avec `"name"` **égal au nom du
   fichier** et `"max": "1"`.
3. Ajouter son contenu initial à `optelya_seed_sections()`.

`front-page.php` ne change pas : le nom du layout donne le gabarit, filtré par
une liste des fichiers présents (il vient de la base, il n'entre pas tel quel
dans un chemin).

`tests/Unit/HomepageLayoutsTest.php` échoue si un layout n'a pas de gabarit, si
un gabarit n'a pas de layout, ou si une section peut être ajoutée deux fois.

### La garde ACF

ACF Pro est sous licence, hors du dépôt : un appel à `get_field()` sans garde
fait tomber la page en 500 dès qu'il manque. Passer par les enveloppes de
`inc/acf.php` (`optelya_field()`, `optelya_sub_field()`, `optelya_has_acf()`…).
`tests/Unit/AcfGuardTest.php` échoue si un fichier du thème appelle ACF sans
garde. Vérifié : ACF désactivé, la page répond 200 avec son en-tête et son pied
de page.

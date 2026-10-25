# Contribution du contenu

Le contenu de la page d'accueil se modifie depuis l'administration. Restent
dans le code les chaînes d'interface et le formulaire (voir
[Ce qui reste dans le code](#ce-qui-reste-dans-le-code)).

| Quoi | Où |
| --- | --- |
| Sections de l'accueil | *Pages → Accueil → Modifier* |
| Accroche, bouton de l'en-tête, téléphone, titre de la colonne contact, adresse | *Réglages → Configuration* |
| Liens de l'en-tête et du pied de page | *Apparence → Menus* |

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
| **Contact** | Titre, intro, texte après le téléphone, étapes, mention sous le formulaire |

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
| Libellés, mention « (facultatif) » et bouton du formulaire | Contrat du traitement de l'envoi, pas encore écrit — le formulaire n'envoie rien |
| Textes de l'emplacement réservé du hero (« Visuel éditeur… ») | Affichés seulement sans vidéo ni image |
| Numérotation « 01 », séparateur « — » du téléphone | Présentation |
| « Version %s », libellés ARIA, « Aller au contenu » | Chaînes d'interface, traduisibles (`__()`) |

## Le contenu initial

`optelya_seed()` (`inc/seed.php`) pose la page d'accueil, les réglages et les
menus, mot pour mot depuis la maquette. Il est appelé par `bin/init.sh` et par
chaque déploiement, mais **ne s'exécute qu'une fois par environnement** : il
pose alors l'option `optelya_seeded`, et tous les appels suivants s'arrêtent
là. Un contributeur qui vide un menu ou retire l'accueil n'est jamais contredit
par le déploiement suivant (vérifié).

Pour rejouer l'amorçage volontairement : `dwp option delete optelya_seeded`
puis redémarrer le conteneur — seules les parties absentes sont recréées.

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

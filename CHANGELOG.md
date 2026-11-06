# Journal des modifications

Une section par version. La version fait foi dans **`composer.json`** — c'est
elle que le pied de page du site affiche, via `optelya_site_version()`.

> **À tenir à jour à chaque livraison.** Voir la règle 7 de
> [`CLAUDE.md`](CLAUDE.md). Le test `tests/Unit/ChangelogTest.php` échoue si la
> version de `composer.json` n'a pas sa section ici.

Versions `0.x` : développement initial, avant la mise en ligne. La `1.0.0`
marquera la mise en production.

## 0.21.0

### Ajouté

- **Cache de page** (WP Super Cache) : les visiteurs anonymes reçoivent une
  page déjà fabriquée, l'accueil répond en 40 ms au lieu de 459 ms (mesuré en
  local). Allumé par `PAGE_CACHE` dans le `.env` de chaque serveur, éteint en
  local. Une page a au plus 12 h : le formulaire de contact reste valable.
- **Le cache se vide tout seul** quand un contenu, un menu ou *Réglages →
  Configuration* change, et à chaque déploiement ; bouton **Vider le cache**
  dans la barre d'outils pour les éditeurs et les administrateurs.
- Module maison `rapkalin-cache`, partagé entre les sites (hors dépôt, dans
  `shared/mu-plugins/`) : cache de page et cache applicatif tenus ensemble,
  commande `wp rapkalin-cache`. Le cache applicatif est prêt ; ses premières
  clés arriveront avec les archives.

### À faire sur chaque serveur

- Déposer `rapkalin-cache` dans `shared/mu-plugins/`.
- Ajouter `PAGE_CACHE='true'` à `shared/.env`.

## 0.20.0

### Ajouté

- **Fiche solution** (`/solutions/optimus/`…), d'après la maquette validée :
  fil d'Ariane discret en tête de page (chevrons, lien parent seul sur
  mobile), promesse et visuel, bénéfices, vidéo de démonstration chargée
  au clic (sous-titres, transcription), études chiffrées **ou** applications
  et industries, aide au choix entre logiciels voisins, apports d'Optelya,
  détails techniques dépliables avec fiche PDF, bloc contact.
- **Catalogue contribuable** : types de contenu *Solutions*, *Éditeurs*,
  *Industries*, *Études de cas* et classement *Enjeux*, reliés entre eux ;
  *Solutions → Réglages des solutions* pour les textes communs à toutes les
  fiches.
- **Contenu initial du catalogue** posé une fois par environnement : 3
  éditeurs, 8 solutions, 5 industries, 39 études, dont les 7 chiffres
  vérifiés.
- Titre « Produit – Éditeur – Optelya » et promesse comme description pour les
  moteurs ; les fiches solution entrent dans le plan du site.

### Modifié

- **L'en-tête prend le bleu très clair du fond du site** (#E8F0FF, au lieu de
  #BBE1FF) ; un trait fin le sépare du contenu. La couleur reste modifiable
  dans *Réglages → Configuration*.
- Le bouton « Parler à un expert » de l'en-tête descend au bloc contact de la
  page quand elle en a un, sinon mène à celui de l'accueil.
- Les blocs de l'accueil (hero, cartes, résultats, panneaux, contact) sont
  devenus des composants partagés avec la fiche solution — rendu de l'accueil
  inchangé.

### Corrigé

- Un titre contenant un mot très long ne fait plus déborder la page sur un
  écran de 320 px : le mot passe à la ligne.

## 0.19.0

### Ajouté

- **Connexion protégée** (module `rapkalin-security` 1.2.0, à déposer dans
  `shared/mu-plugins/`) :
  - blocage de 15 minutes après 5 tentatives échouées depuis une même adresse,
    ou 10 sur un même identifiant ; inscrit dans *Comptes → Connexions* ;
  - `/login`, `/admin`, `/dashboard`, `/wp-login.php` ne mènent plus à la page
    de connexion (page introuvable) ;
  - adresse de connexion personnalisée, réglée par `LOGIN_SLUG` dans le
    `.env` : l'adresse habituelle et l'administration répondent alors « page
    introuvable » aux visiteurs non connectés. **Noter l'adresse choisie.**

## 0.18.0

### Modifié

- **Le destinataire des demandes de contact se règle dans l'admin** : champ
  « Destinataire des demandes de contact » de *Réglages → Configuration*,
  modifiable par tous les rôles. Vide : l'e-mail d'administration. La variable
  `CONTACT_RECIPIENT` du `.env` n'est plus lue (la retirer des serveurs si elle
  y a été ajoutée) ; **renseigner le champ après le déploiement**.

## 0.17.0

### Modifié

- **Mise à jour de la pile** : WordPress 7.1.4, PHP 8.5.12 (image Docker
  locale ; l'hébergement doit suivre la dernière 8.5), Node 26.13 (LTS), toutes
  les dépendances PHP et JavaScript à leur dernière version, aucune
  vulnérabilité connue (`composer audit`, `npm audit`).
- **Intégration et déploiement** : actions GitHub à leur dernière version
  majeure (Node 24). L'archive transmise du build au déploiement est
  désormais vérifiée par son empreinte.
- Aucun changement visible : styles et scripts compilés identiques, octet par
  octet, à la version précédente.

## 0.16.1

### Corrigé

- Le bouton « Parler à un expert » du menu mobile était rétréci par une règle
  destinée au bouton de la barre.
- *Outils → Journal* : la purge refuse une liste inconnue au lieu de vider les
  erreurs, et la limite de conservation affichée est celle des erreurs.
- La recherche affiche « Résultats pour « … » » et un message quand rien ne
  correspond.

### Modifié

- Relecture du code (revue du 05/11/2026) : code mort retiré, styles et blocs
  dupliqués regroupés (mixins, variables, fonctions communes), section
  « Ce qu'Optelya vous apporte » passée au composant de titre, bouton du
  formulaire passé au composant bouton. Rendu vérifié identique, élément par
  élément, avant et après.

## 0.16.0

### Ajouté

- **Référencement** : titre de la page d'accueil réglable (« Titre pour les
  moteurs de recherche » dans *Configuration*, par défaut « Logiciels de
  simulation et d'optimisation – Optelya ») et **image de partage** pour les
  aperçus LinkedIn, Teams et messageries. Le site décrit aussi l'entreprise
  aux moteurs (données structurées).

### Modifié

- **Référencement** : le plan du site ne liste plus que les pages ; archives,
  flux RSS et pages de médias répondent « page introuvable » (le site n'a pas
  de blog) ; la page affichée après l'envoi du formulaire n'est plus indexable.
- **Plus léger** : les polices ne chargent plus les alphabets grec, cyrillique
  et vietnamien (−20 Ko pour deux symboles). Le script est compressé. ⚠️ Sur
  chaque serveur, reporter le changement de `.htaccess` — voir
  `readme/performance.md`.
- **Accessibilité** : titres du pied de page balisés comme titres ; contour de
  focus identique dans tous les navigateurs ; le formulaire indique que les
  champs sont obligatoires sauf mention contraire.

### Corrigé

- Le bouton « Parler à un expert » du menu mobile était écrasé (22 px de haut
  au lieu de 44).

## 0.15.0

### Modifié

- **Fond des pages en bleu très clair** (#E8F0FF), et sections alternées
  (« Vous hésitez ? », « Ce qu'Optelya vous apporte ») dans un bleu un ton plus
  soutenu (#D6E4FB) au lieu du gris-vert. Les textes orange restent lisibles
  (4,52:1 pour 4,5:1 requis) : un fond plus foncé les ferait passer sous le
  seuil.

## 0.14.1

### Modifié

- **Déploiement** : l'archive intermédiaire du site, produite par le build et
  reprise par le déploiement, est supprimée de GitHub au bout d'un jour au
  lieu de cinq. Build et déploiement restent deux étapes séparées, pour que le
  code tiers installé pendant le build n'ait jamais accès à la clé du serveur.
  Pour relancer un déploiement plus d'un jour après, relancer tout le workflow.

## 0.14.0

### Modifié

- **Logo de l'en-tête plus grand : 55 px de haut** (32 auparavant), sur tous
  les écrans. Fournir un logo de 110 px de haut pour les écrans haute densité ;
  55 px au minimum. L'en-tête passe de 73 à 84 px de haut avec un logo.
- **Sur mobile (moins de 600 px)**, le bouton « Parler à un expert » passe en
  tête du menu pour laisser la place au logo. Le même bouton reste visible
  juste dessous, dans le premier écran de l'accueil.

## 0.13.0

### Ajouté

- **Couleur de fond de l'en-tête réglable** dans *Réglages → Configuration*
  (bleu clair par défaut). Le texte de l'en-tête passe automatiquement en foncé
  ou en blanc pour rester lisible ; une teinte trop moyenne, illisible avec l'un
  comme avec l'autre, est refusée avec une explication.

## 0.12.0

### Modifié

- **C'est l'en-tête qui passe en bleu clair** (#BBE1FF, la teinte demandée), avec
  un texte foncé. Le fond des pages et des sections revient au blanc cassé et au
  gris-vert d'origine : la 0.9.0 avait coloré le mauvais fond. Un logo déposé
  dans *Configuration* doit désormais être en version foncée.
- **Journaux d'administration en trois écrans**, réservés aux administrateurs :
  *Comptes → Connexions* (réussies et échouées), *Outils → Activité*
  (contenus, comptes, réglages) et *Outils → Journal* (erreurs du site). Vider un
  écran n'efface que ce qu'il montre. Libellés en français. Nécessite le module
  `rapkalin-security` 1.1.0 dans `shared/mu-plugins/`.

## 0.11.0

### Ajouté

- **Description pour les moteurs de recherche** : nouveau champ dans *Réglages →
  Configuration* (160 caractères). Vide, l'accroche du site est utilisée.

### Modifié

- **Plus de saut de mise en page à l'ouverture sur mobile** : le menu est
  replié dès le premier affichage. Polices du premier écran chargées plus tôt.
- **Cache navigateur effectif** pour les styles, scripts et polices (30 jours).
  ⚠️ Sur chaque serveur, reporter le changement dans `shared/.htaccess` — voir
  `readme/performance.md`.
- **Accessibilité (audit RGAA)** :
  - bordure des champs du formulaire plus contrastée ;
  - liens « Lire l'étude » distincts pour les lecteurs d'écran ;
  - titres des cartes et des profils balisés comme titres ;
  - menus, pied de page et secteurs balisés comme listes ;
  - un message d'erreur par champ, qui dit quoi corriger ;
  - après un envoi du formulaire, le message de résultat reçoit le focus et le
    titre de la page annonce le résultat.
- Le bouton pause du visuel d'accueil n'apparaît plus tant qu'il n'y a pas de
  vidéo.
- La flèche « ▾ » disparaît des intitulés de menu du contenu initial. Sur un
  site déjà installé, la retirer dans *Apparence → Menus*.

## 0.10.0

### Ajouté

- **Logo dans l'en-tête** : nouveau champ « Logo de l'en-tête » dans *Réglages →
  Configuration*. Sans logo, le nom du site s'affiche comme avant.
- **Menu burger sous 960 px de large** (tablette et mobile) : logo, bouton
  « Parler à un expert » et menu sur une seule ligne, même sur un écran de
  320 px. Le menu se ferme avec Échap ou au choix d'un lien. Sans JavaScript, il
  reste déplié.

### Modifié

- **Affichage mobile** : espacements resserrés sous 600 px, titres de section
  qui s'adaptent à l'écran (toujours plus petits que le titre principal),
  champs du formulaire à la largeur de la carte, liens du pied de page et
  pastilles de secteur plus faciles à toucher.

## 0.9.0

### Modifié

- **Fond des pages en bleu très clair** (#E3F2FF) au lieu du blanc cassé, et
  sections alternées (« Vous hésitez ? », « Ce qu'Optelya vous apporte ») en
  bleu un ton plus soutenu (#CDE6FB) au lieu du gris-vert. Teinte plus claire
  que le #BBE1FF demandé : sur celui-ci, les textes orange n'étaient plus assez
  lisibles.

## 0.8.0

### Ajouté

- **Le formulaire de contact envoie les demandes** par e-mail, à l'adresse
  `CONTACT_RECIPIENT` du `.env` (à défaut, l'e-mail d'administration). Après
  l'envoi, un message confirme ; en cas d'erreur, la page revient avec ce qui a
  été saisi et les champs à corriger.
- **Protection contre les robots, sans service tiers ni cookie** : champ piège,
  délai minimal de remplissage, 5 envois par heure et par visiteur.
- **Outils → Contact**, réservé aux administrateurs : nombre de demandes
  envoyées et refusées, par mois et par motif. Aucune demande n'est conservée
  sur le site.

### Modifié

- **Plus aucun numéro de téléphone sur le site** : en-tête, section Contact et
  pied de page renvoient au formulaire. Les champs « Téléphone » disparaissent
  de *Configuration* et de la section Contact.

## 0.7.0

### Ajouté

- **Rôle « Éditeur Optelya »** : toutes les pages et tous les médias, quel qu'en
  soit l'auteur, plus les menus, le personnalisateur et *Réglages →
  Configuration*. C'est l'ancien périmètre du contributeur, élargi aux contenus
  des autres.

### Modifié

- **Le Contributeur Optelya ne gère plus que SES pages et SES médias**, comme
  l'« Auteur » de WordPress : il les crée, les publie, les modifie et les
  supprime. Il ne peut plus modifier les pages des autres — dont l'accueil — ni
  les menus ou le personnalisateur. Il garde *Réglages → Configuration*
  (slogan, bouton d'en-tête, coordonnées). Les comptes existants basculent au
  premier chargement de l'admin, quel que soit le compte connecté.
- *Comptes → Ajouter* propose Administrateur, Éditeur Optelya et Contributeur
  Optelya.

## 0.6.0

### Ajouté

- **Outils → Journal**, réservé aux administrateurs, en deux onglets :
  - **Activité** : connexions réussies et échouées, pages et médias créés,
    modifiés, mis à la corbeille, restaurés ou supprimés, comptes créés,
    supprimés ou changés de rôle, réglages enregistrés, menus, personnalisateur,
    extensions et thème. Filtrable par catégorie.
  - **Erreurs du site** : erreurs PHP (y compris fatales) et e-mails en échec,
    regroupées quand elles se répètent, avec leur nombre et la page concernée.
  - Conservation : 90 jours au plus, 200 entrées par liste ; aucune adresse IP.
    Chaque liste peut être vidée, et la purge reste inscrite au journal.

### Modifié

- **Les modules maison (`rapkalin-mail`, `rapkalin-security`) ne sont plus dans
  le dépôt** : ils se déposent dans `shared/mu-plugins/`, en local comme sur le
  serveur, et le déploiement les relie. Sans eux, le site tourne sans réglage
  d'e-mail ni journal.

## 0.5.0

### Ajouté

- **Réglages → Email**, réservé aux administrateurs : choix de l'envoi par le
  `sendmail` du serveur ou par un relais SMTP (hôte, port, chiffrement,
  identifiants), adresse et nom de l'expéditeur, et un bouton d'e-mail de test
  qui affiche l'erreur exacte en cas d'échec. Le mot de passe SMTP est chiffré
  en base et jamais réaffiché. Module `rapkalin-mail` repris de LCDS.
- **Mailpit** en local (<http://localhost:8042>) : les e-mails du poste de dev
  y sont capturés, aucun ne sort.

### Modifié

- Quand le `.env` renseigne `SMTP_HOST`, il prime sur la page, qui l'annonce.
  Sur un serveur, le laisser vide pour régler l'envoi depuis l'admin.

### Corrigé

- Outillage de tests : PHPUnit passe en 13.4.5 (Pest 5.3.4), qui corrige une
  faille publiée le 27/10/2026. `composer audit` aurait bloqué la CI, donc le
  déploiement. Sans effet sur le site.

## 0.4.0

### Ajouté

- **Rôle « Contributeur Optelya »**, repris de LCDS : pages, médias, menus,
  personnalisateur, *Réglages → Configuration* et son propre profil. Tout le
  reste est retiré du menu **et** refusé à l'accès (403) — articles, thèmes,
  extensions, comptes, outils, réglages du cœur, groupes de champs ACF.
- *Comptes → Ajouter* ne propose plus que **Administrateur** et **Contributeur
  Optelya**. Seul un administrateur crée un compte.
- « Mon compte » réduit aux champs utiles pour un contributeur ; tableau de bord
  réduit à « D'un coup d'œil ».

### Modifié

- *Réglages → Configuration* exige la capacité dédiée `optelya_manage_settings`
  (administrateur et contributeur) au lieu de `manage_options`.

Détail : [`readme/roles.md`](readme/roles.md).

## 0.3.1

### Corrigé

- **Les commandes WP-CLI du déploiement s'exécutent enfin.** Sur le serveur,
  `wp` est une enveloppe shell qui lance WP-CLI avec PHP 8.0 par défaut. Le
  déploiement l'appelait par `php8.5 wp`, ce qui recopiait le script au lieu de
  l'exécuter, sans erreur : activation d'ACF, contenu initial et purge du cache
  n'avaient jamais tourné. Le déploiement règle maintenant l'enveloppe sur
  PHP 8.5 (`uiwpcli_PHP_FLAVOR`) et vérifie que WP-CLI répond vraiment.
- Si WordPress n'est pas encore installé, le résumé du déploiement le dit, avec
  la marche à suivre.
- Les commandes WP-CLI de `readme/deploiement.md` étaient fausses sur ce
  serveur pour la même raison.

## 0.3.0

La marque devient **Optelya**.

### Rupture de déploiement

- **Tout ce qui s'appelait `optolia` s'appelle `optelya`** : marque affichée,
  thème (`website/app/themes/optelya`), préfixes du code, groupes ACF
  (`group_optelya_*`), conteneurs Docker, base locale, et **chemins serveur**
  (`$HOME/preprod-optelya`, `$HOME/prod-optelya`). Aucun environnement n'avait
  encore été déployé : rien à migrer. Un environnement local existant doit être
  recréé (`docker compose down -v`, puis `cp .env.example .env`).
- Le paquet Composer devient `rapkalin/optelya`. Le dépôt GitHub garde son nom
  `optelia`.

### Modifié

- **Chaque poussée sur `main` déploie la production**, en plus des tags. C'est
  temporaire, le temps de monter le site : à retirer à la mise en ligne. Les
  contrôles qualité et CodeQL restent obligatoires avant tout envoi.

## 0.2.0

La page d'accueil se contribue section par section.

### Ajouté

- **Sections de l'accueil modifiables** dans *Pages → Accueil* (ACF Pro,
  contenu flexible) : promesse, enjeux et secteurs, résultats chiffrés, apports
  par lecteur, contact. Un contributeur les ajoute, les réordonne, les supprime
  et en modifie chaque texte, lien, carte et étape. Chaque section ne figure
  qu'une fois : elle porte l'ancre que visent les menus.
- **Vidéo de l'éditeur dans le hero**, avec image d'attente : elle démarre
  seule, sauf si le visiteur a demandé moins d'animations, et son bouton
  pause/lecture fonctionne. Sans vidéo, l'image d'attente s'affiche ; sans les
  deux, l'emplacement réservé de la maquette.
- **Réglages → Configuration** : accroche, bouton de l'en-tête, téléphone
  (affiché et à composer), titre de la colonne contact, adresse.
- **Menus** (*Apparence → Menus*) : en-tête, deux colonnes du pied de page,
  mentions. Le nom d'un menu de colonne est le titre de la colonne.
- **Contenu initial**, mot pour mot celui de la maquette, posé **une seule fois
  par environnement** : un contenu modifié ou retiré par un contributeur n'est
  jamais rétabli par un déploiement.
- Le hero est obligatoire (il porte le `h1`) et les titres de section aussi ; un
  champ facultatif vide n'affiche aucun bloc vide.

### Modifié

- **ACF Pro est requis** pour afficher les sections. Sans lui, la page garde
  son en-tête et son pied de page, sans erreur. Il se dépose à la main dans
  `shared/plugins/` (licence, hors dépôt) — voir
  [`readme/deploiement.md`](readme/deploiement.md).

### Corrigé

- La classe `page` des gabarits génériques entrait en collision avec la classe
  que WordPress pose sur le `<body>` des pages : tout le site prenait une marge.

### Limites connues

- **La licence ACF Pro s'active par WP-CLI**, pas depuis l'admin — voir
  [`readme/deploiement.md`](readme/deploiement.md). Licence pour Optelya à
  confirmer.
- Les libellés du formulaire de contact restent dans le code : ils sont le
  contrat du traitement de l'envoi, encore à écrire. Le formulaire n'envoie
  toujours rien.

## 0.1.0

Première version : le socle du site et la page d'accueil de la maquette UI.

### Ajouté

- **Socle WordPress** repris de LCDS : Bedrock (cœur et dépendances par
  Composer), configuration par `.env` et par environnement, environnement
  Docker (PHP 8.5, Apache, MySQL 8.4, phpMyAdmin, Node).
- **Page d'accueil** conforme à la maquette `ux/accueil-ui/index.html` :
  en-tête, promesse et appel « Parler à un expert », trois portes par enjeu,
  entrée par secteur, trois résultats chiffrés, apports par lecteur, bloc
  contact et pied de page.
- **Numéro de version** affiché dans le pied de page, sur tous les
  environnements.
- **Polices hébergées sur le site** (Archivo, IBM Plex Sans, IBM Plex Mono) :
  aucun appel à Google Fonts.
- **Sécurité** : en-têtes HTTP et CSP en observation (`.htaccess`), XML-RPC
  coupé, énumération des comptes bloquée, version de WordPress retirée de
  l'en-tête des pages et des flux.
- **Préprod non indexée** par les moteurs de recherche.
- **CI GitHub Actions** : style, types et analyse PHP, tests, CVE des
  dépendances, détection de secrets, build front, CodeQL. **Aucun déploiement
  ne part si un contrôle échoue.**
- **Déploiement** : préprod à chaque poussée sur `develop`, production à chaque
  tag, avec sauvegarde de la release précédente. Le tag doit être la version
  de `composer.json` et pointer sur `main`.

### Limites connues

- **Le formulaire de contact n'envoie rien.** Comme dans la maquette, son
  bouton ne soumet pas. Son branchement (envoi, anti-spam, RGPD) est à faire.
- **Le contenu n'est pas modifiable depuis l'administration** (levé en 0.2.0).
- **Les liens mènent aux sections de l'accueil**, comme dans la maquette :
  les autres pages n'existent pas encore.
- **Valeurs à fournir par le client**, affichées entre crochets : numéro de
  téléphone, adresse, délai de réponse. Le lien d'appel pointe sur un numéro
  factice.
- **Le visuel du hero est un emplacement réservé** : pas encore de vidéo
  d'éditeur, et son bouton pause n'agit pas.
- **Couleurs et polices** : ambiance d'exemple de la maquette, non validée par
  le client. Elles sont toutes déclarées dans un seul fichier
  (`assets/styles/basics/_variables.scss`).

# Journal des modifications

Une section par version. La version fait foi dans **`composer.json`** — c'est
elle que le pied de page du site affiche, via `optelya_site_version()`.

> **À tenir à jour à chaque livraison.** Voir la règle 7 de
> [`CLAUDE.md`](CLAUDE.md). Le test `tests/Unit/ChangelogTest.php` échoue si la
> version de `composer.json` n'a pas sa section ici.

Versions `0.x` : développement initial, avant la mise en ligne. La `1.0.0`
marquera la mise en production.

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

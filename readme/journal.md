# Journal : erreurs du site et activité

*Outils → Journal* (`tools.php?page=rapkalin-security`), réservé aux
administrateurs (`manage_options`). Fourni par le module **`rapkalin-security`**,
hors dépôt — voir [Modules maison](deploiement.md#modules-maison-mu-plugins).
Inspiré du journal des connexions de LCDS (liste bornée, pagination) et du
`steamulo-flux-log` de Game France (agrégation par signature, écriture unique en
fin de requête).

## Onglet « Activité »

| Catégorie | Ce qui est inscrit |
| --- | --- |
| Connexions | connexion réussie (identifiant) |
| Connexions échouées | identifiant tapé |
| Contenu | page, article ou média créé, modifié, mis à la corbeille, restauré, supprimé — type, numéro et titre |
| Comptes | compte créé (avec son rôle), supprimé, changement de rôle (avant → après) |
| Réglages | réglages enregistrés (écran), menu créé, modifié ou supprimé, personnalisateur, extension activée ou désactivée, thème, purge du journal |

Chaque ligne dit **qui** (« System » pour WP-CLI et les tâches planifiées) et
**quand**. Une liste filtrable par catégorie.

- **« Réglages enregistrés » n'apparaît que si une valeur a changé** :
  WordPress ne signale pas un enregistrement à l'identique. Sont couverts les
  écrans de Réglages du cœur (par leur groupe : `general`, `reading`…),
  *Permaliens*, les pages d'extension (*Configuration* : `optelya-settings`) et
  les formulaires `admin-post.php` (*Email* : `rapkalin_mail_save`).
- Les révisions, brouillons automatiques, éléments de menu, groupes de champs
  ACF et modèles ne sont pas des contenus : ils sont ignorés.

## Onglet « Erreurs du site »

- **Erreurs PHP** : avertissements, notices, erreurs fatales — y compris celles
  qui affichent la page « Il y a eu une erreur critique ». Les **dépréciations
  sont ignorées** : sur un WordPress récent, elles sont la masse du bruit et ne
  cassent rien.
- **E-mails en échec** (niveau `Mail`) : le message de PHPMailer, sans les
  destinataires.
- Une erreur qui se répète (même niveau, message, fichier, ligne) tient sur
  **une ligne**, avec son nombre d'occurrences, sa première et sa dernière
  apparition, et l'origine de la dernière : `front`, `admin`, `ajax`, `rest`,
  `cron` ou `cli`, et le chemin **sans** sa chaîne de requête (elle peut porter
  un jeton).
- Les chemins sont affichés relatifs au projet.

Le module **n'éteint rien** : PHP continue d'écrire dans `debug.log` et
d'afficher selon la configuration. `@` et le niveau `error_reporting` de
l'environnement sont respectés : avec `WP_ENV='production'` (`WP_DEBUG` faux),
WordPress n'y laisse que les avertissements et les erreurs ; en `staging` et en
`development` (`WP_DEBUG` vrai), les notices aussi.

## Conservation

| Liste | Plafond | Âge maximal |
| --- | --- | --- |
| Erreurs | 200 erreurs distinctes | 90 jours sans réapparaître |
| Activité | 200 entrées **par catégorie** | 90 jours |

Le plafond par catégorie empêche une rafale de connexions échouées d'effacer
l'historique des contenus. Ce qui sort est **supprimé**, pas masqué. **Aucune
adresse IP** n'est conservée. Le bouton « Vider » efface une liste ; la purge
elle-même reste inscrite dans l'activité, avec son auteur.

## Limites assumées

- Deux options en base (`rapkalin_security_errors`,
  `rapkalin_security_activity`), **non chargées automatiquement**. Écrites
  **une fois** en fin de requête, seulement quand il y a quelque chose à
  inscrire.
- Deux requêtes qui se terminent au même instant peuvent perdre les entrées de
  l'une : c'est un outil de diagnostic, **pas une piste d'audit**.
- Une erreur qui survient **avant** le chargement des mu-plugins (configuration,
  cœur) n'est captée que si elle est fatale.
- Une page qui produit un avertissement à chaque affichage déclenche une
  écriture en base à chaque affichage : à corriger, c'est le but de l'écran.
- Les libellés de l'écran sont en anglais : le module n'a pas de traduction.

## Tests

`shared/mu-plugins/rapkalin-security/tests/JournalTest.php` (dans le module,
hors dépôt) : agrégation des erreurs, plafond par catégorie, âge maximal,
entrées mal formées, statuts → événements, pagination.

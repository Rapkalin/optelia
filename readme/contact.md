# Formulaire de contact

Section *Contact* de l'accueil (`#expert`). Le traitement est dans
`inc/contact.php`, les règles **pures** (testées sans WordPress) dans
`inc/enums/OptelyaContactForm.php`, `OptelyaContactField.php` et
`OptelyaContactOutcome.php`.

## Parcours

1. Le visiteur remplit 3 champs obligatoires (nom, e-mail, société) et 2
   facultatifs (téléphone, projet).
2. Le formulaire est envoyé **à la page elle-même**, sans JavaScript.
3. Réussite → redirection `?contact=sent#contact-status` (un rafraîchissement ne
   renvoie pas la demande) et message de confirmation.
4. Échec → la page revient avec le message, les valeurs saisies et les champs
   en défaut signalés (`aria-invalid`), chacun avec un message qui dit quoi
   corriger (RGAA 11.11).

Dans les deux cas, la page revient sur l'ancre `#contact-status` : le message
de résultat, focalisable (`tabindex="-1"`). Le navigateur lui donne le focus à
l'arrivée, sans script, et le titre de la page commence par « Erreur : » ou
« Demande envoyée : ». Viser la section (`#expert`) ne marchait pas : en
suivant l'ancre, le navigateur rendait le focus à la page, après tout script.

La demande part **par e-mail**, via `wp_mail()` (donc le transport de
`rapkalin-mail`). `Reply-To` = l'adresse du visiteur : on lui répond
directement.

## Destinataire

Champ « Destinataire des demandes de contact » de *Réglages → Configuration*,
modifiable par **tous les rôles** (décision du 05/11/2026). Vide ou invalide :
l'adresse de *Réglages → Général* (`admin_email`). Il était auparavant dans le
`.env`.

> **Risque accepté** : un contributeur peut rediriger les demandes vers sa
> propre adresse. L'enregistrement est inscrit dans *Outils → Activité*
> (« Réglages enregistrés », avec son auteur), sans l'ancienne ni la nouvelle
> valeur. *Outils → Contact* affiche l'adresse en cours.

**Aucune adresse n'est affichée sur le site** : une adresse affichée, même
masquée, finit aspirée par les robots. Le numéro de téléphone a été retiré pour
la même raison de cohérence (décision du 05/11/2026) : en-tête, section Contact
et pied de page ne renvoient plus qu'au formulaire.

## Protections contre les robots

Sans service tiers (ni reCAPTCHA ni Turnstile) : pas de script externe, pas de
cookie, pas de bandeau de consentement.

| Garde-fou | Règle | Réponse au visiteur |
| --- | --- | --- |
| Champ piège | Champ `website` invisible : un humain le laisse vide | **Faux succès** (un robot à qui l'on dit « refusé » s'adapte) ; aucun e-mail |
| Délai minimal | Heure d'affichage signée (HMAC, sel `nonce` de WordPress) ; refus sous **3 s** | « La page a expiré » |
| Jeton expiré ou falsifié | Refus au-delà de **24 h** ou si la signature ne correspond pas | « La page a expiré » |
| Limite d'envois | **5 tentatives par heure** et par adresse IP | « Trop de demandes » |
| Validation serveur | Longueurs maximales, e-mail valide, téléphone en chiffres | Champs signalés |
| Injection d'en-têtes | Retours à la ligne et caractères de contrôle retirés de chaque valeur ; e-mail validé avant `Reply-To` | — |

- **L'IP n'est pas conservée en clair** : hachée avec le sel du site, dans un
  transient qui expire au bout d'une heure.
- **`REMOTE_ADDR` seulement** : `X-Forwarded-For` est saisi par le client. Si le
  site passe un jour derrière un proxy ou un CDN, tous les visiteurs partagent
  l'IP du proxy : la limite serait alors à revoir.
- **Pas de cache de page** sur le site aujourd'hui. Un cache servirait un jeton
  figé : les envois seraient refusés au bout de 24 h. Exclure la page du cache
  ou revoir le jeton si un cache est ajouté.
- **Noms de champs préfixés `contact_`** : `name` est une variable de requête
  publique de WordPress, et l'envoyer transforme la page en 404 (constaté en
  recette).

Si les compteurs montrent des robots qui passent (beaucoup d'« Envoyées »
indésirables), l'étape suivante est Cloudflare Turnstile : clés dans le `.env`,
script tiers, à ajouter au même socle.

## Suivi : *Outils → Contact*

`tools.php?page=optelya-contact`, réservé aux administrateurs
(`manage_options` ; les deux rôles restreints reçoivent un 403).

Un tableau par mois : envoyées, e-mail en échec, champs invalides, page
expirée, remplie trop vite, champ piège, trop d'envois. **Aucune demande
n'est conservée**, seulement ces compteurs (option `optelya_contact_stats`,
non chargée automatiquement, 24 mois). Le contenu des demandes vit dans la
boîte de réception. Un e-mail en échec est aussi inscrit dans *Outils → Journal*.

## Tests

`tests/Unit/ContactFormTest.php` : validation, retours à la ligne retirés,
jeton (trop tôt, trop tard, falsifié), compteurs et leur purge. Vérifié en
cassant le code : sans retrait des retours à la ligne, sans contrôle de
signature, sans délai minimal, la suite passe au rouge.

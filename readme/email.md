# E-mails sortants

Tous les `wp_mail()` du site — création de compte, mot de passe perdu,
notifications — partent par le transport que configure le mu-plugin
**`rapkalin-mail`** (`website/app/mu-plugins/rapkalin-mail/`), repris tel quel
de LCDS. Le thème n'en configure aucun.

## Réglages → Email

Page réservée aux administrateurs (`manage_options`). Le rôle « Contributeur
Optelya » n'y a pas accès : ni lien dans le menu, ni page (403), ni
enregistrement (403 sur `admin-post.php`).

| Réglage | Effet |
| --- | --- |
| **Sendmail** | Le binaire `sendmail` du serveur, via la fonction `mail()` de PHP (`sendmail_path`). Valeur par défaut tant que rien n'est enregistré — c'est aussi celle de WordPress. |
| **SMTP** | Champs affichés seulement quand SMTP est coché : hôte, port, chiffrement (aucun, STARTTLS, SSL/TLS), identifiant, mot de passe. Identifiant vide = relais sans authentification. |
| **Expéditeur** | Adresse et nom affichés. Vides : `MAIL_FROM` / `MAIL_FROM_NAME` du `.env`. |
| **Envoyer un e-mail de test** | Envoie avec les réglages **enregistrés** et affiche l'erreur exacte de PHPMailer en cas d'échec. |

Les libellés de la page sont en anglais : le plugin n'a pas de traduction.

## Le `.env` prime quand `SMTP_HOST` est renseigné

Si le `.env` définit `SMTP_HOST`, **toute** la configuration vient du `.env`
(`SMTP_*`, `MAIL_FROM*`) et la page l'annonce par un bandeau. C'est ce qui garde
le poste de dev sur **Mailpit** même après l'import d'une base de production :
sans cette règle, les réglages de prod importés enverraient de vrais e-mails
depuis une machine de développement.

Sur un serveur, **laisser `SMTP_HOST` vide** pour que la page décide.

> **Pourquoi `mail()` et pas le mode `sendmail` de PHPMailer.** Les deux
> passent le message au même binaire, mais le mode `sendmail` l'ouvre par
> `popen()`, qu'un mutualisé peut désactiver (constaté sur LCDS, 3.1.1).

> **Pas d'expéditeur d'enveloppe (`-f`) en mode Sendmail.** Le relais d'IONOS
> refuse toute autre adresse que celle du compte d'hébergement :
> `550 Sender address is not allowed`, constaté sur la préprod LCDS, même
> serveur, le 23/10/2026. En SMTP, l'enveloppe reprend l'adresse d'expédition,
> pour le SPF.

## Le mot de passe SMTP

- **Chiffré en base** (libsodium, `secretbox`). La clé est dérivée de
  `AUTH_KEY` + `AUTH_SALT` du `.env` : un dump de base seul ne le révèle pas.
- **En écriture seule** : jamais réaffiché. Champ vide à l'enregistrement = on
  garde celui qui est stocké. Identifiant vidé = mot de passe supprimé.
- **Changer les sels WordPress le rend illisible.** La page le signale ; il
  suffit de le ressaisir. Aucun envoi ne part avec un mot de passe faux.

Ces règles sont gardées par `tests/Unit/MailSettingsTest.php`.

## En local : Mailpit

Le `.env` d'exemple pointe sur le conteneur **Mailpit** (`SMTP_HOST='mailpit'`) :
aucun e-mail ne quitte la machine, tous se lisent sur <http://localhost:8042>.

L'image Docker n'a pas de binaire `sendmail` : choisir Sendmail en local fait
échouer l'envoi. Sans effet tant que le `.env` local renseigne `SMTP_HOST`.

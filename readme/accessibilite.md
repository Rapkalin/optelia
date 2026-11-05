# Accessibilité (RGAA 4.1.2)

## Audit du 05/11/2026 — page d'accueil

Audit manuel de la page d'accueil locale, version 0.10.0, complété par
Lighthouse (100 en accessibilité). Méthode : HTML rendu, templates et styles,
Chrome au clavier (vraies touches), contrastes calculés sur les couleurs
réelles, largeurs de 320 et 640 px pour le reflow et le zoom 200 %.

**Ce n'est pas un audit de conformité officiel** : celui-ci porte sur un
échantillon de pages, avec un lecteur d'écran réel. Le taux ci-dessous est une
estimation sur une page.

| | Avant corrections | Après corrections (0.11.0) |
| --- | --- | --- |
| Critères conformes | 44 | 48 |
| Non conformes | 5 | 1 (contenu, voir plus bas) |
| Non applicables | 56 | 56 |
| Taux estimé | ≈ 90 % | ≈ 98 % |

Les corrections ont été vérifiées une à une après coup, mais l'audit complet
n'a **pas** été rejoué.

## Non-conformités corrigées

| Critère | Constat | Correction |
| --- | --- | --- |
| 3.3 Contraste des composants | Bordure des champs du formulaire à 2,58:1 sur blanc (3:1 requis) | `$color-input-border` passe à #7A837A : 3,92:1 |
| 6.1 Liens explicites | Trois liens « Lire l'étude » identiques | Chaque lien porte, pour les lecteurs d'écran, le chiffre et le résultat de sa carte (composant `link`, paramètre `context`) |
| 9.1 Titres | Titres des cartes « enjeux » et des profils « Pour l'ingénieur calcul… » balisés en texte simple | Balisés en `<h3>`, sans changement visuel |
| 9.3 Listes | Menus, pied de page et pastilles de secteur sans `<ul>`/`<li>` | Rendus en listes (`components/menu-links.php`, classe `.link-list`) |
| 11.11 Aide à la correction | Même message « Valeur manquante ou invalide » pour tous les champs | Message par cas : champ obligatoire, longueur maximale, format attendu de l'e-mail et du téléphone avec un exemple |

Corrigés aussi, au-delà des critères :

- **Retour après envoi du formulaire** : la page revient sur le message de
  résultat (`#contact-status`), qui reçoit le focus, et le titre de la page
  commence par « Erreur : » ou « Demande envoyée : » — voir
  [`contact.md`](contact.md).
- **Envoi trop rapide** : le message ne prétend plus que « la page a expiré ».
- **Bouton pause du hero** : il n'apparaît plus tant qu'il n'y a pas de vidéo
  (il était affiché désactivé, avec un intitulé trompeur).

## Reste à traiter — contenu et mise en ligne

Ces points ne se règlent pas dans le code :

| Point | Où | Action |
| --- | --- | --- |
| **6.1 — destinations des liens** (reste non conforme) | Cartes « Voir les solutions » → formulaire ; pastilles de secteur → leur propre section ; « Mentions légales », « Confidentialité », « Cookies » → haut de page | Pointer vers les vraies pages quand elles existeront, ou adapter les intitulés |
| Flèche « ▾ » dans « Solutions ▾ », « Industries ▾ » | *Apparence → Menus* | Retirer la flèche : elle est lue par les lecteurs d'écran et annonce un sous-menu qui n'existe pas. Déjà retirée du contenu initial pour les nouvelles installations |
| Déclaration d'accessibilité et mention « Accessibilité : … » en pied de page | Pages et menu « Légal » | Obligation légale avant la mise en ligne |
| « [délai à fournir] », « [adresse à fournir] » | Section Contact, *Configuration* | Contenu à fournir par le client |
| « Off-Road & Agriculture » | Pastilles de secteur | Traduire (« Tout-terrain ») ou accepter l'anglicisme |

## Choix assumés

- **Couleur de l'en-tête réglable** (*Configuration*, 0.13.0) : le contraste du
  texte (critère 3.2) est garanti par le code — texte foncé ou blanc choisi
  par calcul, teinte refusée si aucun n'atteint 4,5:1. Le bouton orange, lui,
  peut perdre son contour sur un fond orangé : c'est signalé dans l'aide du
  champ, pas bloqué.

- **Ordre de tabulation de l'en-tête sous 960 px** : logo → menu → liens du
  menu (s'il est ouvert) → « Parler à un expert », alors que le bouton
  s'affiche avant le menu. Les liens suivent ainsi immédiatement le bouton qui
  les ouvre, ce qui compte plus qu'un ordre strictement visuel. Inverser
  aurait déplacé le problème vers l'affichage desktop.
- **Cibles tactiles** : les liens isolés font de 20 à 31 px de haut. C'est
  au-dessus du minimum WCAG 2.2 AA (24 px ou espacement suffisant) ; les 44 px
  sont une recommandation de niveau AAA. Le RGAA n'a pas de critère dédié.

## Non testé

- **Lecteur d'écran réel** (NVDA, VoiceOver) : annonces déduites du DOM.
- **Critère 8.2 (code valide)** : pas de passage au validateur du W3C.
- **Firefox et Safari** : le focus repose sur le style natif du navigateur,
  vérifié dans Chrome seulement.
- **Vidéo du hero** : absente. À son arrivée, auditer la thématique 4
  (multimédia) et le critère 13.8 (mouvement, pause).

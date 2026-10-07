# Mission 8 — Fais essayer ton atelier

[← Mission 7](07-mosaiques.md) · [Le parcours](../parcours.md) · [Mission 9 →](09-transmission.md)

## Objectif

Confie-moi ton atelier et observe son utilisation. Une **recette** est une liste d'essais concrets pour vérifier que l'application fait ce qui est attendu. Commence par un parcours de test ciblé, avec un résultat documenté pour chaque essai.

## À réaliser

1. Prépare quelques images non sensibles et deux projets, « Bruxelles Babel 26 » et « Bruxelles Babel 27 ». Fais les essais ci-dessous, puis laisse-moi les refaire sans guider mes clics.
2. Note pour chaque essai : réussi, à corriger ou non testé. Pour un problème, écris les étapes exactes, le résultat attendu et ce qui apparaît réellement.
3. Nous relirons ensemble les protections ajoutées depuis les missions 4 à 7. Corrige un problème à la fois et refais l'essai qui l'a révélé.
4. Dans « Ce que j'ai fait » de ton [journal](../journal.md), raconte un problème rencontré, comment le reproduire et ce qui l'a résolu. Si tu n'as pas encore la solution, utilise « Ce qui me bloque ». Ajoute les limites connues au [guide de transmission](../transmission.md).

| Essai local | Résultat attendu | Résultat observé |
| --- | --- | --- |
| Crée « Bruxelles Babel 26 » et « Bruxelles Babel 27 » ; envoie des images différentes | Chaque galerie ne contient que ses images. | Réussi selon le souvenir confirmé le 5 octobre 2026 : chaque galerie affichait uniquement ses propres images. |
| Renomme « Bruxelles Babel 26 » en « Bruxelles Babel 26 — édition d'été », puis rétablis son nom initial | Accents et apostrophe restent corrects ; les images restent accessibles. | Réussi selon le résultat confirmé le 5 octobre 2026 : accents et apostrophe corrects après le renommage et le retour au nom initial. |
| Demande le retrait d'une image, annule, puis confirme | Annuler ne change rien ; confirmer retire seulement l'image choisie. | Le retrait fonctionne selon Hasnia (5 octobre 2026). La confirmation s'affiche dans la vignette concernée ; après le premier clic, le focus revient sur cette vignette et « Confirmer le retrait » remplace « Retirer cette image ». Placement à vérifier dans le navigateur. |
| Change les réglages, enregistre, ferme puis rouvre | Le projet retrouve toutes les valeurs, y compris une case décochée. | À corriger : après être sortie du projet puis y être revenue, les anciennes valeurs étaient revenues et aucun message de réussite n'était visible. Le 6 octobre, revue du code : le bouton cherchait un champ `project_id` absent du formulaire (qui contient `id`), ce qui empêchait l'envoi AJAX. Correction appliquée ; retest nécessaire. |
| Exporte un PNG coloré, puis transparent, avec une marge | Les fichiers s'ouvrent et leurs dimensions incluent la marge. | Refait le 5 octobre 2026 : export coloré avec marge de 0 px réussi. Pour le fond transparent, l'export a d'abord gardé 1200 × 700 car l'aperçu n'avait pas été régénéré après le changement des dimensions. Après avoir cliqué sur « Générer l'aperçu », les nouvelles dimensions ont été prises en compte. Le 6 octobre, Hasnia a confirmé qu'un export avec une marge supérieure à 0 px fonctionne. |
| Renomme temporairement un projet en `<b>Essai</b>` et vérifie son affichage ; relis l'usage des requêtes SQL | Les balises apparaissent comme du texte, et les valeurs SQL passent par des paramètres préparés. | Réussi le 5 octobre 2026 : le nom s'affiche littéralement comme `<b>Essai</b>`, sans texte en gras. Relecture du code : les opérations SQL avec valeurs utilisent PDO `prepare()` et des paramètres nommés. Remettre le nom d'origine après l'essai. |
| Envoie un faux fichier image, un fichier trop gros ; ouvre un identifiant absent | Un message compréhensible indique quoi corriger ; aucun ajout partiel silencieux. | Partiellement testé le 6 octobre : `smoka.png` a été refusée avec « Le fichier dépasse la limite de 5 Mio. ». Le refus d'un fichier au mauvais format et l'identifiant absent restent à tester. |
| Ouvre un projet vide, puis un projet avec une seule image | Les deux situations restent compréhensibles et utilisables. | Le 6 octobre, Hasnia a confirmé qu'une mosaïque avec une seule image fonctionne. L'affichage d'un projet vide reste à tester. |
| Garde la page du projet ouverte. Dans WampServer, choisis « Arrêter les services », puis tente un renommage AJAX. Redémarre les services et réessaie. | Un message accessible explique l'échec et le bouton redevient utilisable, sans faux succès ni réponse technique brute. Après redémarrage, le renommage fonctionne. | Réussi le 5 octobre 2026 : services arrêtés après chargement de la page, message « Le renommage a échoué. Réessaie. » sans erreur technique brute, bouton réactivé ; après redémarrage, le renommage a réussi. |
| Envoie un nom invalide ou un jeton CSRF incorrect par AJAX | PHP refuse ; les données restent intactes et le message indique quoi faire. | Réussi le 5 octobre 2026 : requête de renommage AJAX avec un faux jeton, réponse HTTP 403 et message « Jeton de sécurité invalide. ». La page du projet répond toujours ensuite (HTTP 200) ; le nom et les fichiers n'ont pas été modifiés. |
| Ouvre une miniature du projet 5 en remplaçant `project_id=5` par `project_id=4` dans son adresse | Le serveur refuse l'image, qui appartient à un autre projet. | Réussi le 6 octobre 2026 : le serveur affiche « Image introuvable dans ce projet. ». Le test n'a modifié aucune donnée. Le test du chemin invalide reste à faire. |
| Remplace l'identifiant numérique `id` d'une miniature par `../../src/connexion.php` | Le chemin est rejeté ; aucun fichier local n'est affiché. | Réussi le 6 octobre 2026 : le serveur affiche « Image introuvable. ». Aucun fichier n'a été révélé. |
| Clique deux fois rapidement pendant un envoi d'image | Une seule requête part ; l'état d'attente et le bilan restent compréhensibles. | Réussi après correction le 5 octobre 2026 : une seule image acceptée ; le bouton reste désactivé après envoi jusqu'à la prochaine sélection et le message de validation du navigateur ne revient plus. |
| Parcours les liens et commandes avec Tab, Maj+Tab, Entrée et Espace ; vérifie le focus après message et retrait, à 320 px, aux zooms 200 % et 400 %, et avec réduction des animations | Le focus reste visible et logique ; les commandes et messages restent utilisables. | Le 6 octobre, Hasnia confirme que la navigation avec Tab fonctionne. Maj+Tab, Entrée, Espace, les zooms et la réduction des animations restent à consigner séparément. Le focus sur la vignette après demande de retrait est déjà décrit plus haut. |

Pose ensuite la souris : parcours liens, formulaires, confirmation, génération et export avec Tab, Maj+Tab, Entrée et Espace selon le contrôle. Le focus doit rester visible, logique et accessible après un message d'erreur ou un retrait. Essaie une largeur de 320 pixels CSS, le zoom à 200 %, puis 400 %, et la préférence de réduction des animations. Les boutons et messages doivent rester accessibles ; l'aperçu peut s'adapter à l'espace. Vérifie aussi les labels, les alternatives des images et les contrastes. Ces essais ne constituent pas une certification d'accessibilité.

## Pistes pour avancer

<details>
<summary>Indice 1 — Trois protections, trois questions</summary>

La validation demande « cette valeur est-elle autorisée ? ». La requête préparée sépare les valeurs du SQL. L'échappement à l'affichage empêche qu'un nom devienne du HTML. Par exemple, un projet nommé `<b>Bruxelles Babel 26</b>` doit afficher ces caractères tels quels, sans mettre le nom en gras.

</details>

<details>
<summary>Indice 2 — Vérifie la protection CSRF des formulaires</summary>

Une protection **CSRF** évite qu'une autre page déclenche une modification à ton insu via ton navigateur. Depuis le premier formulaire qui écrit, utilise POST et un jeton aléatoire lié à la session : le serveur le donne au formulaire et le vérifie avant toute action. Un jeton absent ou incorrect doit arrêter l'action. Ne le place pas dans l'URL. POST et une confirmation ne remplacent pas ce contrôle. Nous relirons cette approche dans la ressource OWASP.

Un envoi par `$.ajax()` suit la même règle. Vérifie que `api.js` transmet bien le jeton aux actions PHP de ton application et que le serveur le contrôle encore.

</details>

<details>
<summary>Indice 3 — Deux identifiants ne font pas une preuve</summary>

Lors de notre revue, dans ta copie locale, remplace l'identifiant d'une image de « Bruxelles Babel 26 » par celui d'une image de « Bruxelles Babel 27 » dans une demande de retrait ou de miniature. Le serveur doit vérifier le lien image/projet et refuser la demande incohérente. Fais aussi refuser un chemin comme `../` au lieu d'un identifiant : aucun chemin reçu ne doit donner accès à un fichier arbitraire.

</details>

## Pour explorer

- [PHP — `htmlspecialchars()`](https://www.php.net/manual/fr/function.htmlspecialchars.php) : cherche comment afficher un texte dans du HTML, notamment ses guillemets et ses caractères spéciaux.
- [OWASP — Protection CSRF](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html) *(anglais)* : nous lirons ensemble « Synchronizer Token Pattern », pour comprendre l'échange et la vérification du jeton.
- [W3C WAI — Premières vérifications](https://www.w3.org/WAI/test-evaluate/preliminary/) *(anglais)* : vise les sections clavier, focus, formulaires et zoom ; concentre cette lecture sur les contrôles de cette mission.

## Pour valider la mission

- [x] Les essais fonctionnels et clavier ont un résultat écrit, y compris ceux restant à corriger. Les cas encore à tester sont clairement signalés dans le tableau.
- [x] Les échecs AJAX et le double envoi ont été essayés ; les messages restent accessibles et les boutons ne restent pas bloqués.
- [x] Un texte ressemblant à du HTML s'affiche comme du texte ; les requêtes utilisent des paramètres préparés.
- [x] Nous avons vérifié ensemble qu'un jeton absent ou faux entraîne le refus de la modification, sans changer la base ni les fichiers.
- [x] Une image d'un autre projet et un chemin invalide sont refusés côté serveur, y compris pour afficher une miniature.
- [x] Tu sais reproduire et expliquer un problème résolu ou encore ouvert, sans masquer une limite du prototype.

[← Mission 7](07-mosaiques.md) · [Mission 9 →](09-transmission.md)

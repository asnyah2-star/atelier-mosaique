# Mission 5 — Range tes projets

[← Mission 4](04-memoire.md) · [Le parcours](../parcours.md) · [Mission 6 →](06-images.md)

## Objectif

L’accueil montre les projets enregistrés. Tu peux en ouvrir un et le renommer. Son identifiant et les images qui lui sont associées restent inchangés.

## À réaliser

1. Remplace les cartes fictives par les lignes lues dans `projects`. Préserve l’état vide préparé à la mission 3. Chaque nom affiché dans du HTML doit être échappé, y compris dans un champ de formulaire.
2. Donne à chaque projet un lien qui transmet son identifiant à une page de détail. Côté PHP, vérifie sa forme puis cherche ce projet avec une requête préparée. Un identifiant reçu du navigateur n’est jamais une preuve que le projet existe.
3. Sur la page de détail, montre son nom et un formulaire pour le renommer. Fais-le d’abord fonctionner avec un envoi HTML normal. Reprends les contrôles de la mission 4 : POST, jeton CSRF vérifié, nom valide. La mise à jour ne doit viser que le projet demandé ; examine attentivement sa condition `WHERE`.
4. Prépare son emplacement sous `storage/projects/`, par exemple `storage/projects/12/`. Le numéro vient du projet trouvé en base, jamais du nom saisi. Tu peux créer le dossier maintenant ou lors du premier ajout d’image : note ton choix dans le guide de transmission.
5. Prévois un message « Projet introuvable » et un lien de retour à l’accueil. Une page de projet absente doit répondre avec le statut HTTP 404, le code qui signifie « introuvable ».
6. Quand le formulaire fonctionne, ajoute le renommage sans recharger la page. **AJAX** permet au JavaScript d’échanger avec PHP pendant que la page reste ouverte. Envoie le nom, l’identifiant et le jeton CSRF avec `$.ajax()`, en POST vers une URL PHP relative de ton application. PHP garde tous ses contrôles et répond en JSON, un format de données lisible par JavaScript, avec un statut HTTP adapté.

Dans `public/assets/js/`, garde les interactions et messages dans `app.js`. Complète le fichier TODO `api.js` avec des fonctions ciblées qui centralisent les appels `$.ajax()` et leur gestion commune des erreurs. Charge les scripts dans cet ordre : jQuery, `api.js`, puis `app.js`. Une fonction par besoin suffit ; pas de réglages globaux avec `$.ajaxSetup()`.

Pour tes appels AJAX, une URL relative part de l'adresse de la page affichée,
pas du dossier de `api.js`. Un `/` au début repart de la racine de `localhost` ;
garde tes appels dans le sous-dossier de l'application.

Range les images dans `storage/projects/`, à côté de `public/`. À la prochaine
mission, l'application les affichera à partir de leur identifiant, pour retrouver
leur fichier et leur projet. C'est l'accès que tu construiras pour la galerie.

## Pistes pour avancer

<details>
<summary>Indice 1 — Distingue lire et modifier</summary>

Ouvrir un lien lit une page : l’identifiant peut figurer dans l’URL. Renommer modifie une donnée : cela passe par ton formulaire protégé. Un lien visité ne doit jamais renommer un projet.

</details>

<details>
<summary>Indice 2 — Deux projets, deux résultats</summary>

Pour tester ton renommage, crée deux projets avec des noms différents. Renomme le premier puis consulte les deux. Si les deux changent, relis immédiatement le filtre de ta requête de mise à jour.

Avec AJAX, observe aussi l’onglet Réseau du navigateur. Distingue réussite, refus de PHP et serveur injoignable. Cherche `.done()`, `.fail()` et `.always()` : lequel permet de réactiver le bouton même après un échec ? N’affiche pas une réponse serveur brute comme du HTML.

</details>

<details>
<summary>Indice 3 — Construis le chemin avec des données validées</summary>

Pars du dossier de stockage défini par l’application et de l’identifiant validé du projet. Ne reçois jamais un chemin complet depuis un champ ou l’URL. Une valeur comme `../autre-dossier` n’a rien à faire à la place d’un numéro de projet.

</details>

## Pour explorer

- [PHP — `PDOStatement::fetch`](https://www.php.net/manual/fr/pdostatement.fetch.php) : cherche comment lire une ligne et reconnaître l’absence de résultat.
- [PHP — `htmlspecialchars`](https://www.php.net/manual/fr/function.htmlspecialchars.php) : vérifie le traitement des guillemets avant de remettre un nom dans la valeur d’un champ.
- [jQuery — `$.ajax()`](https://api.jquery.com/jQuery.ajax/) *(anglais)* : repère `method`, `data`, `dataType` et les méthodes `.done()`, `.fail()`, `.always()` ; commence par ton seul formulaire de renommage.

## Pour valider la mission

- [ ] La liste reflète la base et les liens ouvrent le bon projet.
- [ ] Renommer un projet ne modifie ni son identifiant, ni son emplacement prévu, ni un autre projet.
- [ ] Tu peux ouvrir, renommer et revenir à l’accueil au clavier.
- [ ] **Cas d’erreur :** un identifiant inexistant, négatif ou non numérique donne une réponse compréhensible, sans création de dossier.
- [ ] Un nom invalide ou un jeton CSRF incorrect empêche le renommage et laisse le nom précédent intact.
- [ ] Le renommage AJAX affiche un état d’attente, empêche un second envoi pendant la requête et réactive le bouton après réussite ou erreur. Son message reste accessible sans déplacer inutilement le focus.
- [ ] Tu sais expliquer pourquoi deux projets nommés « Bruxelles Babel 26 » peuvent être distincts.

**Défi facultatif :** propose un ordre d’affichage stable et explique-moi ton choix.

[← Mission 4](04-memoire.md) · [Le parcours](../parcours.md) · [Mission 6 →](06-images.md)

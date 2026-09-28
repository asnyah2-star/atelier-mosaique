# Mission 3 — Dessine la porte d’entrée

[← Mission 2](02-demarrage.md) · [Le parcours](../parcours.md) · [Mission 4 →](04-memoire.md)

## Objectif

Conçois l’accueil de ton application : deux ou trois projets fictifs, un bouton « Nouveau projet » et un formulaire de nom. Cette maquette, sans base de données, te permet de travailler la présentation et les interactions avant l’enregistrement.

## À réaliser

Reprends les fichiers de ton dépôt, placé dans `www` en mission 2. Démarre WampServer et
ouvre l'adresse utilisée en mission 2 : `http://localhost/atelier-mosaique/public/` dans notre
exemple. Après chaque modification, enregistre puis actualise cette page pour
voir le résultat.

1. Dessine rapidement les deux états de l’accueil : quelques projets, puis « Aucun projet pour le moment ». Dans les deux cas, on doit comprendre comment commencer.
2. Dans `public/index.php`, construis cette page avec les projets d'exemple « Bruxelles Babel 26 » et « Bruxelles Babel 27 ». Range sa présentation dans `public/assets/css/style.css`.
3. Ajoute « Nouveau projet » et un formulaire comportant un champ « Nom du projet ». Un vrai `label` identifie le champ ; un texte provisoire dans le champ ne le remplace pas. Le formulaire peut rester visible dans la page : une fenêtre surgissante n’est pas nécessaire.
4. Prévois « Créer le projet » et un emplacement pour une erreur. À cette étape, annonce clairement que l’enregistrement arrivera à la mission suivante. La maquette peut vérifier le champ obligatoire, sans prétendre avoir enregistré quoi que ce soit.
5. Parcours l’écran au clavier avec Tab, Maj + Tab et Entrée. Réduis la largeur de la fenêtre, puis agrandis le texte. Réorganise les cartes si elles débordent.

Un **focus**, c’est le repère qui indique quel lien ou bouton recevra ta prochaine action au clavier. Garde-le visible, y compris sur tes boutons personnalisés.

### Ajoute jQuery pour les interactions

jQuery est une bibliothèque JavaScript qui t'aide à sélectionner des éléments
et à réagir aux actions. Le HTML et le CSS gardent leurs rôles.

1. Depuis la page officielle ci-dessous, enregistre la version **complète 4.0.0**,
   compressée, dans un nouveau dossier `public/assets/js/vendor/`. Garde le nom
   `jquery-4.0.0.min.js` et son commentaire de licence. La version « slim » n'inclut
   pas AJAX, dont tu auras besoin plus tard. Aucun outil npm n'est nécessaire.
2. Dans `public/index.php`, ajoute une balise `script` dont le `src` est
   `assets/js/vendor/jquery-4.0.0.min.js`, avant celle de `app.js`. Garde `defer`
   sur les deux scripts : ils s'exécuteront après lecture du HTML, dans cet ordre.
3. Dans `app.js`, ajoute une interaction : « Nouveau projet » place le
   focus dans le champ du formulaire. Cherche `.on()` pour écouter l'action.
   Utilise les éléments HTML natifs pour les formulaires et les boutons.

Note la version et la provenance dans le guide de transmission. Ce fichier
jQuery local peut être suivi dans Git avec le code ; ne le modifie pas.

## Pistes pour avancer

<details>
<summary>Indice 1 — Donne un rôle à chaque élément</summary>

Un titre principal annonce la page, `main` contient son contenu, une liste peut rassembler les projets. Utilise un bouton pour une action et un lien pour aller vers une page. Une carte entière n’a pas besoin de devenir un faux bouton.

</details>

<details>
<summary>Indice 2 — Pars d’une colonne</summary>

Une colonne fonctionne déjà sur un petit écran. Fais ensuite répartir les cartes par Flexbox ou Grid. Évite les largeurs fixes qui imposent un défilement horizontal.

</details>

<details>
<summary>Indice 3 — Prépare l’erreur avant le succès</summary>

Imagine le message « Donne un nom à ton projet ». Place-le près du champ concerné et relie-le au champ si tu l’affiches toi-même. Ne signale pas une erreur uniquement par une bordure rouge. La validation HTML aidera la saisie ; PHP devra aussi vérifier les données à la mission 4.

</details>

## Pour explorer

- [MDN — Formulaires web](https://developer.mozilla.org/fr/docs/Learn_web_development/Extensions/Forms) : regarde « Votre premier formulaire » pour associer un label, un champ et un bouton.
- [jQuery — Téléchargement officiel](https://jquery.com/download/) **(anglais)** : choisis la version complète 4.0.0 compressée et enregistre le fichier depuis son lien.
- [jQuery — `.on()`](https://api.jquery.com/on/) **(anglais)** : regarde comment relier un événement à une fonction ; privilégie cette méthode aux raccourcis d'anciens tutoriels.

## Pour valider la mission

- [ ] Les deux états, avec projets et sans projet, sont lisibles.
- [ ] Le fichier jQuery local se charge et « Nouveau projet » donne le focus au champ, au clavier comme à la souris. Si la console indique que `$` est inconnu, tu sais vérifier le chemin et l'ordre des scripts.
- [ ] Tu atteins chaque commande au clavier dans un ordre logique et tu vois toujours le focus.
- [ ] À environ 320 pixels de large et avec le texte agrandi à 200 %, les noms et les commandes restent accessibles.
- [ ] Le texte et les commandes se distinguent nettement du fond ; tu n’as pas retiré le contour de focus sans remplacement.
- [ ] **Cas d’erreur :** un nom vide donne une indication compréhensible, sans annoncer de création réussie.

Enregistre un commit ciblé lorsque ta maquette est vérifiée.

[← Mission 2](02-demarrage.md) · [Le parcours](../parcours.md) · [Mission 4 →](04-memoire.md)

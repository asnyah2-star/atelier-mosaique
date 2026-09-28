# Mission 7 — Branche la machine à mosaïques

[← Mission 6](06-images.md) · [Le parcours](../parcours.md) · [Mission 8 →](08-recette.md)

## Objectif

Réutilise le moteur fourni pour produire une mosaïque avec les images du projet ouvert. Retrouve ses réglages à la prochaine visite et télécharge un PNG. L’enjeu est d’intégrer le moteur existant en conservant son fonctionnement.

## À réaliser

1. Dans la référence `prototype/index.php`, repère quatre éléments : le tableau PHP `$data`, les miniatures `?thumb=1`, le rendu `renderPinterest()` et l'export `exportMosaicToPng()`. Explique en une phrase le rôle de chaque élément.
2. Reprends les éléments utiles dans ton application, par petites étapes. Garde la référence inchangée. Place le CSS dans `public/assets/css/style.css`, le moteur JavaScript et Canvas natif dans `public/assets/js/app.js` et les traitements PHP réutilisables dans `src/`. Le moteur cohabite avec jQuery : conserve son algorithme et son export. Après chaque déplacement, vérifie encore l'aperçu.
3. Remplace la liste globale issue de `IMG_MOSA/` par les images du projet sélectionné. Réutilise l'accès contrôlé de la mission 6, y compris pour les miniatures. Un identifiant d'image permet au serveur de retrouver son fichier et de vérifier son appartenance au projet ; un chemin reçu du navigateur ne suffit pas.
4. Fais enregistrer puis recharger les réglages ci-dessous. Valide-les côté serveur. Distingue « essayer un aperçu » et « enregistrer les réglages » ; l'enregistrement utilise une action POST protégée comme tes autres formulaires. Pour cet échange AJAX, `app.js` appelle une fonction de `api.js`, qui envoie les valeurs et le jeton CSRF avec `$.ajax()`.
5. Vérifie le téléchargement avec quelques images légères, puis une seule, puis aucune. Affiche un message utile sans image. Prévois un bouton utilisable au clavier pour régénérer : le double-clic du prototype ne suffit pas.

| Réglage | Nom dans le prototype | Question à te poser |
| --- | --- | --- |
| Largeur et hauteur | `w`, `h` | Quelles bornes restent raisonnables sur ton ordinateur ? |
| Densité | `mode` | Comment limiter le choix à `dense`, `normal`, `aere` ? |
| Espacement et arrondi | `gap`, `radius` | Comment traiter une valeur négative ? |
| Couleur et transparence | `bg`, `bg_transparent` | Comment mémoriser une case décochée ? |
| Marge du PNG | `margin` | As-tu annoncé les dimensions finales ? |
| Graine du hasard | `seed` | Gardes-tu la valeur réellement utilisée si le champ est vide ? |

La **seed** est un nombre qui initialise une suite de choix pseudo-aléatoires. La mémoriser est utile, mais le prototype comporte aussi un autre mélange : voir le défi plus bas.

## Pistes pour avancer

<details>
<summary>Indice 1 — Suis une image</summary>

Pars d'une ligne de `images`, retrouve son projet, son nom de stockage, puis l'URL contrôlée qui l'affiche. Le JavaScript a besoin de données d'affichage ; il n'a pas besoin du chemin absolu sur ton disque. Images et miniatures peuvent continuer à charger ces URL directement : inutile de faire transiter leurs octets dans `api.js`.

</details>

<details>
<summary>Indice 2 — Fais passer les données, pas le PHP</summary>

Un fichier `.js` n'exécute pas les balises PHP. Prévois un point de passage dans la page PHP pour transmettre les images et réglages validés au JavaScript. Cherche `json_encode()` et ses options adaptées à une insertion dans du HTML ; ne construis pas du JavaScript en collant des noms de fichiers dans des chaînes.

</details>

<details>
<summary>Indice 3 — Retrouve les réglages à la source</summary>

Le prototype lit certains réglages dans l'URL, côté PHP et côté JavaScript. Ton application doit utiliser les valeurs du projet lors de sa réouverture. Observe aussi que la marge agrandit seulement l'export : sa taille est `(largeur + 2 × marge)` par `(hauteur + 2 × marge)`.

</details>

## Pour explorer

- [PHP — `json_encode()`](https://www.php.net/manual/fr/function.json-encode.php) : cherche comment sérialiser un tableau et à quoi servent les options `JSON_HEX_*` quand du texte traverse une page HTML.
- [MDN — Utiliser des images dans Canvas](https://developer.mozilla.org/fr/docs/Web/API/Canvas_API/Tutorial/Using_images) : regarde le chargement d'une image et `drawImage()` pour comprendre l'export fourni, sans le réécrire.

## Pour valider la mission

- [ ] Deux projets contenant des images différentes donnent deux mosaïques sans mélange.
- [ ] Après fermeture et réouverture, tous les réglages enregistrés reviennent, y compris la case de transparence décochée et la seed effective.
- [ ] Le PNG s'ouvre, possède les dimensions attendues avec sa marge et présente le fond demandé.
- [ ] Une seule image peut être répétée ; aucune image produit un message et aucun export trompeur.
- [ ] Une valeur hors limites ou un projet inexistant est traité sans erreur technique exposée.
- [ ] Une erreur AJAX lors de l'enregistrement laisse l'aperçu utilisable et affiche un message ; seuls les réglages confirmés par PHP sont annoncés comme enregistrés.

## Défi facultatif — Enquête sur le prototype

La référence présente plusieurs limites à prendre en compte lors de l’intégration. Le PHP mélange la liste avec `shuffle()` avant le JavaScript ; une seed identique ne garantit donc pas les mêmes images aux mêmes places. L'export dessine les miniatures JPEG, aplaties sur du blanc, et ne reproduit pas les filtres CSS ni le léger zoom de l'aperçu. La transparence du fond de la mosaïque est distincte de celle des images sources. Choisis une seule différence, reproduis-la et note une piste dans ton [journal](../journal.md). Une correction de ces différences pourra faire l’objet d’une évolution ultérieure.

[← Mission 6](06-images.md) · [Mission 8 →](08-recette.md)

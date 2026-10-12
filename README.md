# Mon atelier de mosaïques

Application locale pour organiser des images par projet, composer une mosaïque et télécharger le résultat en PNG. Projet pédagogique en PHP, MySQL et JavaScript, développé sous Windows avec WampServer.

## Fonctionnalités présentes

- Création et renommage des projets ; archivage, restauration et suppression définitive des projets archivés.
- Import JPEG/PNG, galerie et retrait des images avec confirmation.
- Composition : dimensions, densité, espacement, arrondi, fond, transparence, marge et graine aléatoire.
- Enregistrement des réglages par projet et export PNG dans le navigateur.
- Interactions jQuery/AJAX, validation PHP, requêtes PDO préparées et jetons CSRF.

## Installation et présentation

- [Installation locale](docs/docs/installation.md) : procédure pour une nouvelle machine et recette.
- [Présentation](docs/docs/presentation.md) : déroulé de démonstration et explications.
- [Transmission](docs/docs/transmission.md) : environnement et suivi de la reprise.
- [Notes de mission 9](docs/docs/notes-mission-9-pas-a-pas.md) : essais déjà consignés.

## État au 12 octobre 2026

La recette de mission 8 est consignée. Les notes de mission 9 rapportent trois projets et trois images de démonstration, un import SQL dans une base de vérification, la comparaison des fichiers image et un export PNG réussi. Ces essais consignés ne constituent pas encore une installation validée sur une autre machine.

La mission 9 reste à terminer : installer les images depuis leur archive dans une copie vierge, réaliser une reprise complète depuis le code transmis et consigner les résultats. Le guide de transmission contient encore des champs à renseigner.

Un essai Electron et des binaires Windows existent dans `electron-test/`. La fenêtre ouvre une adresse locale : son installateur ne fournit ni WampServer, ni MySQL, ni les données. La livraison de référence est l’application web locale.

## Organisation

```text
public/index.php             accueil et gestion des projets
public/projet.php            images, réglages et actions du projet
public/image.php             images et miniatures
public/assets/js/api.js      appels AJAX
public/assets/js/app.js      interface, mosaïque et export Canvas
src/connexion.php            connexion PDO
config/config.example.php    modèle de configuration sans secrets
database/schema.sql          structure pour une base vide
database/demo.sql            structure et données fictives
storage/projects/            images, exclues de Git
prototype/                   prototype de référence
electron-test/               essai de fenêtre Windows
```

## Limites et transmission

L’application vise un usage local, sans comptes utilisateurs. Les gros exports Canvas dépendent des ressources du navigateur. Les images importées sont limitées à 5 Mio, 6 000 pixels par côté et 20 millions de pixels.

La configuration `config/config.local.php` et les uploads sont exclus de Git. Git ne transporte pas la base MySQL ni les images. Le dépôt suit actuellement `database/archive-db/atelier_mosaique.sql.gz` : vérifier son contenu avant transmission. Les sorties Electron suivies alourdissent également le dépôt.

Le [parcours pédagogique](docs/docs/parcours.md) décrit la progression historique. Pour installer la version actuelle, suivre le guide d’installation.

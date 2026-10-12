# Mon atelier de mosaïques

Application locale pour organiser des images par projet, composer une mosaïque et télécharger le résultat en PNG. Développée initialement avec PHP/MySQL sous WampServer, elle dispose désormais d’une version Windows autonome avec Electron, PHP intégré et SQLite.

## Fonctionnalités présentes

- Création et renommage des projets ; archivage, restauration et suppression définitive des projets archivés.
- Import JPEG/PNG, galerie et retrait des images avec confirmation.
- Composition : dimensions, densité, espacement, arrondi, fond, transparence, marge et graine aléatoire.
- Enregistrement des réglages par projet et export PNG dans le navigateur.
- Interactions jQuery/AJAX, validation PHP, requêtes PDO préparées et jetons CSRF.

## Installation et présentation

- [Installation Windows autonome 1.0.1](docs/docs/installation-autonome.md) : installation, données, sauvegarde et recette.
- [Installation web avec Wamp](docs/docs/installation.md) : procédure de la version MySQL historique.
- [Présentation](docs/docs/presentation.md) : déroulé de démonstration et explications.
- [Transmission](docs/docs/transmission.md) : environnement et suivi de la reprise.
- [Notes de mission 9](docs/docs/notes-mission-9-pas-a-pas.md) : essais déjà consignés.

## État au 12 octobre 2026

La recette de mission 8 est consignée. Les notes de mission 9 rapportent trois projets et trois images de démonstration, un import SQL dans une base de vérification, la comparaison des fichiers image et un export PNG réussi. Ces essais consignés ne constituent pas encore une installation validée sur une autre machine.

La mission 9 reste à terminer : installer les images depuis leur archive dans une copie vierge, réaliser une reprise complète depuis le code transmis et consigner les résultats. Le guide de transmission contient encore des champs à renseigner.

La version autonome 1.0.1 intègre PHP 8.3.35 et SQLite. L’installation, la mise à jour depuis 1.0.0, la conservation des données et l’export PNG après réouverture ont été vérifiés par Hasnia sur son PC, avec Wamp fermé. Une installation sur un Windows vierge reste à vérifier, notamment le prérequis Microsoft Visual C++. L’installateur n’est pas signé.

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
electron-test/               lanceur et construction de la version autonome
tools/desktop/               connexion SQLite et contrôle d’accès local
desktop-preview/             copie SQLite de développement, exclue de Git
```

## Limites et transmission

L’application vise un usage local, sans comptes utilisateurs. Les gros exports Canvas dépendent des ressources du navigateur. Les images importées sont limitées à 5 Mio, 6 000 pixels par côté et 20 millions de pixels.

La configuration `config/config.local.php`, les uploads, les sauvegardes et les sorties Electron sont exclus de Git. Git ne transporte pas les données locales. L’ancien `database/archive-db/atelier_mosaique.sql.gz` contient un export historique des projets ; ce n’est pas une sauvegarde complète de la version actuelle.

La version installée conserve ses données dans `%APPDATA%/AtelierMosaiqueElectronTest/data`. Fermer l’application puis sauvegarder ensemble `atelier.sqlite` et `projects/`. Garder Wamp, Node/npm et les sources pour maintenir et reconstruire le projet ; ils ne sont pas nécessaires à l’utilisation quotidienne du paquet installé.

Le [parcours pédagogique](docs/docs/parcours.md) décrit la progression historique. Pour installer la version actuelle, suivre le guide d’installation.

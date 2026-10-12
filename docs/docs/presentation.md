# Présenter Mon atelier de mosaïques

## Introduction — une minute

« Mon atelier de mosaïques permet de ranger des images dans des projets, de composer une mosaïque et de télécharger le résultat en PNG. Je l’ai développé pour apprendre à relier une interface web, PHP, une base MySQL et des fichiers image. »

## Démonstration — cinq minutes

1. Montrer l’accueil et les projets.
2. Ouvrir un projet de démonstration et montrer la galerie.
3. Modifier deux réglages, générer la mosaïque et expliquer le résultat.
4. Télécharger le PNG et l’ouvrir.
5. Revenir au projet pour montrer les réglages conservés.
6. Sur un projet de test, montrer renommage, ajout/retrait d’image, archivage et restauration.

Avant de commencer, démarrer MySQL et le serveur PHP, préparer une image autorisée et vérifier les téléchargements. Garder un PNG déjà exporté pour illustrer le résultat en cas de problème.

## Expliquer le fonctionnement — deux minutes

- Le navigateur affiche l’interface ; JavaScript compose la mosaïque et Canvas produit le PNG. jQuery gère une partie des interactions et des appels AJAX.
- PHP vérifie les demandes et utilise PDO pour lire ou modifier MySQL.
- MySQL conserve projets, réglages et informations des images. Les pixels restent dans `storage/projects/`, sous des noms générés par le serveur.
- Les requêtes préparées séparent les valeurs du SQL ; l’échappement HTML affiche les noms comme du texte ; les jetons CSRF protègent les actions de modification.

## Questions à préparer

**Comment installer ailleurs ?** Récupérer le code, préparer PHP/MySQL, adapter la configuration, importer le SQL, replacer les images et suivre la recette du guide d’installation.

**Pourquoi transmettre aussi les images ?** Le SQL décrit les fichiers et leur lien au projet ; il ne contient pas leurs pixels.

**Que fait Electron ?** Il ouvre l’application locale dans une fenêtre de bureau. Il dépend encore du serveur PHP et de MySQL de la machine.

**Quelles limites ?** Usage local sans comptes utilisateurs ; les grandes mosaïques demandent de la mémoire au navigateur. La reprise sur une autre machine reste à vérifier avant de déclarer la livraison terminée.

Préparer aussi un problème réellement rencontré : expliquer son symptôme, sa cause, sa correction et le test qui a confirmé le résultat. S’appuyer sur les notes de recette.

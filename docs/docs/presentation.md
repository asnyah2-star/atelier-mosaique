# Présenter Mon atelier de mosaïques

## Introduction — une minute

« Mon atelier de mosaïques permet de ranger des images dans des projets, de composer une mosaïque et de télécharger le résultat en PNG. Je l’ai d’abord développé avec PHP et MySQL. J’ai ensuite préparé une application Windows avec Electron, PHP intégré et SQLite, pour pouvoir l’utiliser sans démarrer Wamp. »

## Démonstration — cinq minutes

1. Montrer l’accueil et les projets.
2. Ouvrir un projet de démonstration et montrer la galerie.
3. Modifier deux réglages, générer la mosaïque et expliquer le résultat.
4. Télécharger le PNG et l’ouvrir.
5. Revenir au projet pour montrer les réglages conservés.
6. Sur un projet de test, montrer renommage, ajout/retrait d’image, archivage et restauration.

Avant de commencer, ouvrir l’application installée depuis la recherche Windows en tapant `AtelierMosaiqueElectronTest`. Garder Wamp fermé. Préparer une image autorisée et vérifier les téléchargements. Garder un PNG déjà exporté pour illustrer le résultat en cas de problème.

## Expliquer le fonctionnement — deux minutes

- Le navigateur affiche l’interface ; JavaScript compose la mosaïque et Canvas produit le PNG. jQuery gère une partie des interactions et des appels AJAX.
- Electron ouvre la fenêtre et démarre le PHP intégré sur une adresse locale.
- PHP vérifie les demandes et utilise PDO pour lire ou modifier SQLite.
- SQLite conserve projets, réglages et informations des images dans `atelier.sqlite`. Les pixels restent dans le dossier `projects/`. Ces données sont séparées du programme, dans le dossier utilisateur Windows.
- Les requêtes préparées séparent les valeurs du SQL ; l’échappement HTML affiche les noms comme du texte ; les jetons CSRF protègent les actions de modification.

## Questions à préparer

**Comment installer ailleurs ?** Lancer le Setup 1.0.1 sur un Windows x64 avec le composant Visual C++ requis par PHP. Le paquet fournit PHP, SQLite, le code et les données de démonstration. Le test sur un Windows vierge reste à réaliser.

**Comment sauvegarder ?** Fermer l’application et sauvegarder ensemble le fichier SQLite et le dossier des images. La base décrit les fichiers ; elle ne contient pas leurs pixels.

**Que fait Electron ?** Il ouvre la fenêtre et lance automatiquement le PHP fourni avec l’application. La version autonome utilise SQLite et n’a pas besoin de MySQL.

**Quelles limites ?** Usage local sans comptes utilisateurs ; les grandes mosaïques demandent de la mémoire au navigateur. La reprise sur une autre machine reste à vérifier avant de déclarer la livraison terminée.

Préparer aussi un problème réellement rencontré : expliquer son symptôme, sa cause, sa correction et le test qui a confirmé le résultat. S’appuyer sur les notes de recette.

Exemple à expliquer : GitHub refusait le push parce que les binaires Electron dépassaient sa limite par fichier. Les commits locaux ont été sauvegardés puis reconstruits sans ces fichiers ; les dossiers générés sont désormais ignorés, et le push du code a réussi. Le Setup se transmet séparément du code source.

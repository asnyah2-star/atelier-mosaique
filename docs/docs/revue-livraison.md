# Revue de livraison — 12 octobre 2026

Point de reprise après la coupure de session. Cette revue rassemble les preuves
disponibles ; elle ne certifie ni l’absence de faille ni une installation sur
Windows vierge. Le code était propre et `main` synchronisée avec `origin/main`
au commit `9efc19c` avant l’ajout de ce document.

## Résultats conservés

- Version web MySQL : recette fonctionnelle et protections consignées dans la
  mission 8 et le journal. Certains essais restent explicitement ouverts, dont
  la suppression de toutes les images d’un projet.
- Mission 9 : démonstration de trois projets et trois images, import SQL de
  vérification, fichiers image comparés et export PNG consignés.
- Copie SQLite : Hasnia a confirmé réglages, PNG, ajout/retrait d’image et cycle
  création, renommage, archivage, restauration et suppression de projet.
- Application installée : installation 1.0.0, mise à jour 1.0.1, réouverture depuis
  Windows, conservation des données et PNG confirmés avec Wamp fermé.
- Sauvegardes privées retrouvées : ZIP du code/MySQL/35 images et ZIP des données
  SQLite avant mise à jour. Leur restauration complète reste à tester.
- PHP 8.3.35 officiel : archive vérifiée par SHA256 ; projet et miniature testés.

## Vérifications refaites pendant cette revue

La syntaxe des fichiers PHP de `public/`, `src/` et `tools/` passe avec PHP intégré.
Sur le paquet Windows, les requêtes suivantes ont été exécutées sans modification
des données :

| Cas | Résultat |
| --- | --- |
| Accueil sans secret du lanceur | HTTP 403 |
| POST de l’accueil sans jeton CSRF, avec secret du lanceur | HTTP 403 |
| Image du projet 2 demandée pour le projet 1 | HTTP 404 |
| Identifiant image contenant un chemin | HTTP 404 |
| Accès à `src/connexion.php` hors de `public/` | HTTP 404 |
| Arrêt demandé au serveur de test | Fin du processus PHP observée |

Le dernier test valide l’arrêt du composant serveur ; la fermeture de la fenêtre
de l’application installée et l’absence de processus résiduel restent à observer
ensemble. L’audit npm de production effectué précédemment ne signalait aucun avis
connu ; il ne couvre pas PHP, les outils de construction ou tout le code applicatif.

## Travaux à terminer dans l’ordre

1. **Construction reproductible** : `prepare-bundle.cjs` lit encore le dossier
   ignoré `desktop-preview`, créé lors des essais. La préparation SQLite initiale
   requiert également MySQL et les images d’une copie locale voisine. Définir une
   préparation à partir du code suivi, avec une base vide ou un jeu de démo
   fourni séparément ; éviter de livrer des données modifiées pendant les tests.
   Fixer aussi la version Electron explicite à la place de `latest` (le lockfile
   fixe actuellement la version installée).

   Correction réalisée pour 1.0.2 : préparation automatique depuis `public/`
   suivi dans Git, schéma SQLite suivi, base vide, PHP officiel vérifié et
   Electron fixé à 44.7.0. La construction du paquet et son accueil vide ont été
   vérifiés. Le contrôle depuis un clone neuf reste à réaliser.
2. **Livraison** : fournir Setup, SHA256, notice courte et lien officiel du
   prérequis Visual C++ x64. Le Setup actuel n’est pas signé et n’installe pas
   ce prérequis automatiquement.
3. **Recette finale** : fermeture complète, restauration SQLite dans une copie
   isolée, vérification des licences, revue des erreurs et protections, puis
   test des opérations sensibles encore ouvertes sur données jetables.
4. **Finitions** : nom visible, icône, version affichée. Préserver les identifiants
   Squirrel et le chemin de données existant lors d’un changement de nom.
5. **Transmission** : envoyer code et documents sur GitHub après vérification des
   fichiers ; transmettre le Setup séparément. Faire tester Windows vierge par
   le responsable, avec une fiche de résultats.

Ne rien désinstaller pour ces étapes. Wamp garde la version historique ; Node/npm
et VS Code servent à maintenir le projet. Ne pas effacer les données utilisateur,
les sauvegardes ou les anciennes versions avant validation de restauration.

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

Contrôle complémentaire effectué après fermeture de l’application installée
par Hasnia : aucun processus `php`, `php-cgi` ou `php-win` actif retrouvé.
Le point de recette concernant l’arrêt après fermeture est désormais validé.

## Travaux à terminer dans l’ordre

Revue des protections et notices (version 1.0.3 en cours de recette) : ajout
d’une CSP limitant les scripts à l’origine locale, interdisant objets, base HTML
et intégration en iframe ; styles inline conservés pour le rendu dynamique de
la mosaïque. Ajout de `Referrer-Policy: no-referrer` et refus explicite des
vérifications de permissions Electron. Présence de la CSP vérifiée sur la
réponse HTTP de l’accueil du paquet. Les notices PHP et Chromium/Electron
étaient présentes ; les textes complets jQuery et electron-squirrel-startup
ont été ajoutés au paquet. Cela ne constitue pas une expertise juridique de
toutes les bibliothèques natives ni une certification de sécurité.
Hasnia a confirmé que le test demandé après installation 1.0.3 est réussi :
aperçu et export PNG corrects. La livraison 1.0.3 a ensuite été préparée dans
un nouveau dossier, avec notices actualisées et SHA256 du nouvel installateur.

Restauration SQLite vérifiée le 12 octobre 2026 : le ZIP privé
`avant-maj-1.0.1-20261012-132358.zip` a été extrait dans un dossier isolé.
Intégrité SQLite et liens entre tables valides ; trois projets et trois images
présents. Taille et MIME des fichiers conformes aux informations de la base.
Avec le code du paquet 1.0.2 et PHP intégré, l’accueil, les trois pages projet
et les trois miniatures répondent HTTP 200. Le serveur de test a ensuite été
arrêté ; les données installées n’ont pas été remplacées. Ce contrôle valide
cette sauvegarde SQLite ; la restauration du ZIP MySQL historique reste ouverte.

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
   vérifiés. Le contrôle depuis un clone neuf du commit `be872d9` a ensuite réussi
   le 12 octobre 2026 sur ce PC : installation des dépendances par `npm ci`,
   préparation du PHP officiel, construction du paquet et du Setup. Aucun dossier
   `desktop-preview`, configuration MySQL ou jeu de test local n’était présent.
   Les trois pages PHP, la connexion et le routeur ont un contenu identique à
   ceux du paquet précédent après normalisation des fins de ligne.
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

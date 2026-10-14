# Version Windows autonome — recette en cours

## Renommage en atelier-mosaique — 14 octobre 2026

La version 1.0.4 porte le nom `atelier-mosaique` dans Windows, pour son
exécutable et son installateur. Le dossier source `electron-test/` conserve son
nom technique. Les indications 1.0.3 et antérieures ci-dessous sont historiques.

Cette version constitue une installation distincte de l'ancienne application
`AtelierMosaiqueElectronTest` : aucune migration des anciens projets n'est faite.
Ses données sont normalement dans `%APPDATA%/atelier-mosaique/data`.
Au premier lancement, une base vide est initialisée ; les lancements suivants
conservent les projets créés dans cette nouvelle application.

La construction crée et vérifie une base SQLite sans projet ni image, à partir
du schéma suivi dans Git. Les données de l'application installée ne sont pas
incluses dans le Setup. Il est donc inutile de supprimer les anciens projets
pour fournir un installateur vierge à une autre personne.

Après construction, le paquet est dans
`electron-test/out-autonomous/atelier-mosaique-win32-x64/` et le Setup attendu est
`electron-test/out-autonomous/make/squirrel.windows/x64/atelier-mosaique-1.0.4 Setup.exe`.
Ne pas transmettre à sa place l'ancien Setup 1.0.3 du dossier de livraison.
Construction du paquet et du Setup 1.0.4 réussie le 14 octobre 2026 ; base vide
et intégrité SQLite vérifiées par le script de préparation. Une empreinte
SHA256 accompagne le Setup dans le fichier `.exe.sha256`.

Pour la recette : installer ce nouveau Setup, vérifier l'accueil vide, créer un
projet avec une image, exporter un PNG, puis fermer et rouvrir l'application.
Vérifier aussi ce cycle sur un Windows sans Wamp, PHP, Node ni npm, avec le
prérequis Visual C++ x64.

Hasnia a confirmé le 14 octobre 2026 que le test de la version 1.0.4 sur son
PC est réussi : accueil initial vide, création de projet, ajout d'image,
réglages, export PNG et conservation après fermeture et réouverture.
Le test sur une autre machine Windows sans les outils de développement reste
à réaliser.
L'ancienne application peut ensuite être désinstallée depuis les paramètres
Windows ; renommer son raccourci ne modifie pas son identité ni ses données.

Livraison précédente (historique) : **1.0.3**. Hasnia a confirmé le bon fonctionnement de
l’aperçu et de l’export PNG après cette mise à jour de sécurité. Le dossier
prêt à transmettre est `backups/livraison-responsable-1.0.3/`, avec le Setup,
les notices et l’empreinte SHA256. Les mentions 1.0.2 ci-dessous décrivent
les étapes de construction précédemment vérifiées.

La [revue de livraison](revue-livraison.md) rassemble les résultats, les contrôles
refaits et les points à terminer avant transmission au responsable.

## État au 12 octobre 2026

La version 1.0.2 utilise SQLite et PHP 8.3.35 x64 NTS téléchargé sur le site officiel.
L’empreinte SHA256 de l’archive a été vérifiée :
`25a8e2ac9ff30f1d768d1447c09a600617fa6e6082729f6e95f008b59c91fe45`.
L’ancien runtime PHP 8.3.28 reste dans les sauvegardes privées. Elle
n’utilise ni les services Wamp ni MySQL. Hasnia a vérifié les réglages conservés,
l’export PNG, l’ajout/retrait d’image, la création/renommage/archivage/restauration
et la suppression d’un projet dans la copie SQLite. Le lancement Electron avec
Wamp fermé et l’ouverture du paquet Windows ont également été vérifiés.

Le paquet testé est dans
`electron-test/out-autonomous/AtelierMosaiqueElectronTest-win32-x64/`.
L’installateur est produit dans
`electron-test/out-autonomous/make/squirrel.windows/x64/`.
Les sorties de construction, PHP et les données locales sont exclus de Git.

## Tester l’installation

Résultat du test sur le PC de Hasnia, le 12 octobre 2026 : la mise à jour
1.0.0 → 1.0.1 s’est installée ; les projets, images et réglages sont conservés.
Après fermeture et réouverture depuis la recherche Windows, l’export PNG
fonctionne également. Wamp était fermé. Le test sur un Windows vierge reste
à réaliser.

1. Fermer les fenêtres de cette application. Garder Wamp fermé.
2. Lancer le fichier `AtelierMosaiqueElectronTest-1.0.2 Setup.exe` de la sortie
   autonome. Ne pas confondre avec l’ancien installateur dans `electron-test/out/`.
3. Ouvrir l’application installée et vérifier projets, images, réglages et PNG.
4. Fermer puis rouvrir l’application et vérifier que les réglages persistent.

Le code et PHP sont fournis par le paquet ; Node, npm et Wamp ne sont pas
nécessaires sur la machine cible. Le composant Microsoft Visual C++ requis par
PHP doit toutefois être présent : la recette sur un Windows sans Wamp reste à
faire. Les versions embarquées doivent être suivies lors des prochaines livraisons, et les
licences de redistribution doivent être conservées.

## Données et mises à jour

La version installée utilise le dossier Electron `userData`, normalement
`%APPDATA%/AtelierMosaiqueElectronTest/data` pour le paquet Windows
(le lanceur de développement peut utiliser un nom différent). Il contient `atelier.sqlite`,
`projects/`, ainsi que les dossiers temporaires `sessions/` et `tmp/`.
Depuis 1.0.2, une base vide est copiée seulement lors de la première initialisation.
Une nouvelle installation ne contient donc aucun projet ni image de test.
Une base existante n’est pas remplacée par les données du paquet.

## Reconstruire depuis le dépôt

Prérequis de développement : Git, Node/npm et Visual C++ x64. MySQL, Wamp et
`desktop-preview` ne sont pas utilisés pour construire le paquet.

Depuis la racine d’une nouvelle copie Git :

```powershell
npm.cmd --prefix electron-test ci
powershell.exe -NoProfile -ExecutionPolicy Bypass -File tools/prepare-php-runtime.ps1
npm.cmd --prefix electron-test run package
& .\electron-test\node_modules\.bin\electron-forge.cmd make --from-package electron-test
```

La deuxième commande exécute seulement le script local de préparation dans ce
processus PowerShell ; elle ne change pas la politique globale de Windows.
Le script télécharge PHP officiel et vérifie le SHA256 de `php-runtime.json`.
La préparation automatique lit les fichiers de `public/` suivis dans Git,
applique les adaptations SQLite, et crée une base vide depuis le schéma suivi.
L’ancien `desktop-bundle`, s’il existe, est déplacé dans `backups/`.
Le lockfile et la version Electron explicite fixent les dépendances. Une
reconstruction intégrale depuis un clone neuf a été vérifiée sur le PC de Hasnia
le 12 octobre 2026, au commit `be872d9` : `npm ci`, préparation de PHP officiel,
création du paquet et du Setup ont réussi sans configuration MySQL,
`desktop-preview` ou données de test. Cela ne garantit
pas une archive binaire identique octet pour octet.

Hasnia a également confirmé que la mise à jour installée 1.0.2 conserve les
projets et réglages existants. Le test sur un Windows vierge reste ouvert.

Pour sauvegarder cette version, fermer l’application puis copier ensemble
`atelier.sqlite` et `projects/`. Les exports SQL MySQL concernent la version
web historique ; ils ne remplacent pas une sauvegarde des données SQLite.

Une restauration isolée du ZIP SQLite avant mise à jour a été vérifiée le
12 octobre 2026 : base intègre, trois projets et trois images conformes,
pages et miniatures accessibles avec le paquet 1.0.2. Les données de
l’application installée ont été laissées intactes.

## Protection et validations restantes

Le serveur PHP écoute sur `127.0.0.1`, avec un port choisi à chaque lancement.
Le lanceur fournit un secret aléatoire exigé par le serveur. La fenêtre utilise
l’isolation du contexte, le sandbox, et désactive Node dans la page ; elle refuse
les navigations externes et les nouvelles fenêtres. Ces protections ne constituent
pas une certification de sécurité.

- [x] Installation réelle du Setup et ouverture depuis le raccourci, confirmées par Hasnia.
- [x] Réouverture et conservation des données après installation, confirmées par Hasnia.
- [x] Export PNG depuis l’application installée, confirmé par Hasnia.
- [x] Arrêt du processus PHP après fermeture confirmé : après fermeture de l’application installée par Hasnia le 12 octobre 2026, aucun processus `php`, `php-cgi` ou `php-win` actif n’a été retrouvé.
- [ ] Installation sur un Windows sans Wamp, PHP, Node ou npm.
- [x] Mises à jour sur le PC de Hasnia : conservation des projets, images et réglages confirmée lors des essais consignés.
- [x] Sauvegarde SQLite : restauration dans un dossier isolé vérifiée, avec intégrité de la base et accès aux trois projets et trois images.
- [ ] Prérequis Visual C++ x64 : lien officiel fourni ; installation du prérequis et fonctionnement de l’application sur un Windows vierge restent à vérifier.
- [ ] Revue finale sécurité, versions embarquées et licences avant livraison.

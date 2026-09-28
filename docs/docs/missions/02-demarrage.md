# Mission 2 — Allume ton atelier

[← Mission précédente](01-atelier-github.md) · [Carte du parcours](../parcours.md) · [Mission suivante →](03-porte-entree.md)

## Objectif

Fais fonctionner ton application avec **WampServer**, puis essaie le prototype
de mosaïques. Apache reçoit la demande du navigateur et fait exécuter le PHP ;
le navigateur reçoit du HTML et exécute le JavaScript.

## À réaliser

### 1. Démarre WampServer

Nous utiliserons **WampServer sous Windows 11** pour tout le parcours. Il fournit
Apache, PHP, MySQL et phpMyAdmin, l'interface qui servira à gérer la base.
S'il est déjà installé, passe directement à l'étape 3 ci-dessous.

1. Ouvre la page de téléchargement WampServer ci-dessous. Vérifie avec moi que
   l'installateur convient à ton Windows 11 64 bits. Lis les prérequis et vérifie
   les paquetages Visual C++ demandés, en versions x86 et x64 ; installe ceux qui
   manquent avant WampServer.
2. Lance l'installateur en tant qu'administratrice. Choisis un dossier à la racine
   d'un disque local, sans espace ni accent, par exemple `C:\wamp64`.
   N'installe pas par-dessus une installation existante.
3. Lance WampServer depuis son raccourci. Attends que son icône dans la zone de
   notification devienne verte, puis ouvre `http://localhost/` dans le navigateur.
   Tu dois voir la page d'accueil de WampServer.
4. Depuis cet accueil, ouvre **phpMyAdmin**. Si plusieurs serveurs sont proposés,
   sélectionne **MySQL**, celui retenu pour l'exercice. Note sa version et son
   port dans WampServer. Nous créerons la base en mission 4. Si ton installation
   utilise déjà MariaDB, montre-moi ses réglages pour adapter l'exemple ensemble.

Si phpMyAdmin demande une connexion, utilise les accès locaux déjà définis.
Sur une installation WampServer neuve, le compte initial est `root` et le mot de
passe est vide : laisse ce champ vide. Si ces accès ne fonctionnent pas, montre-moi
le message avant de modifier la configuration.

Si l'icône reste rouge ou orange, ou si une page ne s'ouvre pas, montre-moi
le message. Nous vérifierons le service concerné avant de changer un réglage.

### 2. Place ton projet dans le dossier www

Avec WampServer, nous travaillerons directement dans
`C:\wamp64\www\atelier-mosaique`. Si WampServer est installé ailleurs, utilise
le dossier `www` de cette installation. Ce sera ton unique dossier de travail,
suivi par GitHub Desktop et servi par Apache.

1. Dans GitHub Desktop, ouvre ton dépôt avec **Repository → Show in Explorer**.
   Si son chemin est déjà `C:\wamp64\www\atelier-mosaique`, passe à l'étape 4.
2. Sinon, ferme l'éditeur et GitHub Desktop. Dans l'Explorateur, **déplace le
   dossier entier** `atelier-mosaique` vers `C:\wamp64\www\` avec Couper puis
   Coller. Son dossier caché `.git` et ses fichiers locaux doivent rester dedans.
   Si la destination contient déjà un dossier de ce nom, compare-le avec moi
   avant de poursuivre, sans remplacer les fichiers.
3. Rouvre GitHub Desktop. Choisis **File → Add local repository → Choose**,
   sélectionne `C:\wamp64\www\atelier-mosaique`, puis **Add repository**.
   Tu retrouves le dépôt existant, son historique et ses modifications.
4. Vérifie la présence de
   `C:\wamp64\www\atelier-mosaique\public\index.php` et retrouve ton premier
   commit dans GitHub Desktop.

Le dépôt est le dossier `atelier-mosaique`, pas le dossier `www` entier.
`public/` contient l'entrée de ton application ; `prototype/` contient la
démonstration. Les autres dossiers gardent leur rôle dans l'organisation du code.

Voici les deux adresses à garder dans tes favoris :

| À ouvrir | Adresse |
| --- | --- |
| Ton application | `http://localhost/atelier-mosaique/public/` |
| Le prototype fourni | `http://localhost/atelier-mosaique/prototype/` |

Ces adresses correspondent directement aux dossiers sous `www`. Si ton dossier
porte un autre nom, remplace `atelier-mosaique` dans les adresses.

### 3. Ouvre l'application et vérifie PHP

1. Ouvre **`http://localhost/atelier-mosaique/public/`**. Tu dois voir « Mon atelier de
   mosaïques » et une version PHP 8.x.
2. Depuis l'accueil `http://localhost/`, ouvre le lien **phpinfo()**.
   C'est la fiche de configuration de PHP. Vérifie la version, qui doit être
   identique à celle affichée par l'application, puis repère **gd**, **PDO**,
   **pdo_mysql** et **fileinfo** : images, accès à MySQL et reconnaissance des fichiers.
   Dans PDO, le pilote `mysql` doit être présent.
3. Si une extension manque, ouvre avec moi le menu de l'icône WampServer :
   **PHP → Extensions PHP**. Nous activerons seulement ce qui manque, puis
   redémarrerons les services depuis WampServer et vérifierons à nouveau phpinfo().
   PDO lui-même est normalement déjà intégré à PHP ; son pilote MySQL est à vérifier.
4. Note les versions, les extensions, le port MySQL et les adresses locales dans
   le [guide de transmission](../transmission.md). Pour le fichier de configuration
   PHP, relève la ligne **Loaded Configuration File** de phpinfo().
5. Change une phrase de `public/index.php`, enregistre et actualise
   `http://localhost/atelier-mosaique/public/`. La phrase modifiée doit apparaître.

Le chemin `C:\wamp64\www\atelier-mosaique\public\index.php` désigne un fichier sur
le disque. L'adresse `http://localhost/atelier-mosaique/public/` demande à Apache de faire
exécuter le PHP. Pour voir le résultat, ouvre l'adresse HTTP dans le navigateur.

### 4. Essaie le prototype

1. Garde `prototype/index.php` intact. Choisis avec moi trois à cinq images JPG
   ou PNG légères, non sensibles et que tu peux utiliser. Copie-les dans
   `prototype/IMG_MOSA/`, à côté du fichier repère `.gitkeep`.
2. Ouvre **`http://localhost/atelier-mosaique/prototype/`**. Commence avec les dimensions
   par défaut. Clique sur **Générer**, attends les images, puis sur
   **Exporter PNG**. Ouvre le fichier téléchargé.
3. Retrouve **Espacement (px)**, essaie deux valeurs et observe. Tu peux aussi
   comparer fond coloré et transparent. La marge concerne le fichier exporté.

Les images locales sont exclues de Git ; tu les transmettras séparément si
nécessaire. L'application et le prototype utilisent tous deux Apache de WampServer.

### 5. Reprends le travail à la prochaine séance

Démarre WampServer, attends l'icône verte et ouvre ton favori
`http://localhost/atelier-mosaique/public/`. Modifie tes fichiers, enregistre, puis actualise
la page. Garde ce favori pour retrouver ton application à chaque séance.
En fin de séance, utilise **Arrêter les services** dans le menu WampServer.

## Pistes pour avancer

<details>
<summary>Indice 1 — La page ne répond pas ?</summary>

Ouvre d'abord `http://localhost/`. Si l'accueil WampServer répond mais pas ton
application, vérifie que `public/index.php` se trouve bien dans
`www/atelier-mosaique/`. Vérifie aussi le nom du dossier dans l'adresse :
`http://localhost/atelier-mosaique/public/`.

</details>

<details>
<summary>Indice 2 — La mosaïque reste vide ?</summary>

Un dossier `IMG_MOSA` vide donne un aperçu vide ; un dossier absent provoque le
message « Dossier manquant ». Il faut ici `prototype/IMG_MOSA`, car le prototype
cherche ses images à côté de lui. Vérifie ensuite GD et une image JPG valide de faible poids.

</details>

<details>
<summary>Indice 3 — Où se passe chaque étape ?</summary>

Repère `listImages` et `Build DATA` dans le PHP, puis `renderPinterest` et
`exportMosaicToPng` dans le JavaScript. Explique avec tes mots qui lit le disque
et qui dessine dans le navigateur, sans modifier la référence.

</details>

## Pour explorer

- [WampServer — Téléchargements](https://wampserver.aviatechno.net/) : repère l'installateur complet et l'outil de vérification des paquetages Visual C++.
- [WampServer — Prérequis d'installation](https://wampserver.aviatechno.net/?lang=fr&prerequis=afficher) : vérifie la compatibilité Windows, les paquetages requis et le choix du dossier d'installation.
- [GitHub Desktop — Ajouter un dépôt local](https://docs.github.com/fr/desktop/adding-and-cloning-repositories/adding-a-repository-from-your-local-computer-to-github-desktop) : si tu as déplacé ton dossier dans `www`, retrouve-le dans GitHub Desktop avec son historique.

## Pour valider la mission

- [ ] WampServer sert l'application et le prototype avec les deux adresses localhost.
- [ ] L'application affiche PHP 8.x et ta phrase modifiée.
- [ ] Les extensions utiles sont présentes ; versions, dossiers, adresses et port MySQL sont notés.
- [ ] Le prototype affiche tes images, l'espacement change et un PNG s'ouvre.
- [ ] GitHub Desktop retrouve le même dépôt dans `www`, avec son historique et tes modifications.
- [ ] Cas d'erreur : arrête les services dans WampServer et actualise l'application ; elle ne se charge plus. Redémarre les services et retrouve l'accueil.

[← Mission 1](01-atelier-github.md) · [Mission 3 — Dessine la porte d'entrée →](03-porte-entree.md)

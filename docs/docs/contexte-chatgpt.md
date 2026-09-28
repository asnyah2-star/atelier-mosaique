# Contexte pour ChatGPT — Mon atelier de mosaïques

Document de référence préparé le 24 septembre 2026 à partir des neuf fiches du
parcours. Joins-le au début du chat avec le prompt de `demarrer-chatgpt.md` et la
fiche de mission que tu as reçue. Il donne le contexte complet à l'assistant ;
tu n'as pas besoin de tout lire avant de commencer.

## Le rôle de ce document

Tu apprends à construire une application web en partant d'un prototype de
mosaïques. Tu as quelques bases en HTML/CSS ; Git, PHP et SQL sont à découvrir
progressivement, selon ce que tu connais déjà.

Ton accompagnant humain, Franck, relit et transmet les missions une à une.
Dans les fiches, « je », « moi » et « montre-moi » désignent cet accompagnant.
ChatGPT reste une aide pédagogique distincte : il ne parle pas à sa place et ne
prétend pas valider une mission pour lui.

**Le résumé des neuf missions ne signifie pas qu'elles sont toutes commencées,
terminées ou déjà remises.** ChatGPT se concentre sur la fiche effectivement
reçue et sur l'étape que tu indiques. Il peut situer brièvement une notion dans
le parcours, sans dérouler les missions futures ni produire leurs solutions.
La progression se fait avec ton accompagnant.

Le code de ton dépôt peut avoir évolué depuis ce document. ChatGPT doit demander
un extrait utile ou le résultat observé avant de conclure sur ce qui existe ou
fonctionne. Si ta fiche ou une consigne récente diffère de ce contexte, signale
la différence et précise la consigne retenue ; ne recommence pas tout le parcours.

## Comment t'aider

**Consignes à ChatGPT : dans cette section, « tu » désigne l'assistant.** Le
prompt de démarrage définit le cadre détaillé. Garde ces principes pendant
l'échange : français, tutoiement, ton adulte et direct, une difficulté à la fois.
Explique le mot technique au moment où il devient utile, propose une action
courte et indique comment en observer le résultat.

Pour le développement, commence par une explication et un indice ciblé, puis
un petit exemple si nécessaire. Si les essais n'aident plus, si Hasnia exprime de
la frustration ou demande directement la solution, donne la correction
utile à l'étape avec ses explications. Ne multiplie pas les questions ni les
échecs pour obtenir le droit à une réponse. Une solution expliquée peut servir
de point d'appui pour reprendre ensuite une petite modification autonome.

Pour l'installation et le partage, donne un vrai pas-à-pas : il ne s'agit pas
de faire deviner des menus. Ne livre pas toute l'application ni la mission
suivante. Propose une ressource à la fois, avec une notion précise à y chercher,
sans faire de sa lecture une condition pour recevoir de l'aide.

## Les choix déjà faits

| Sujet | Base de travail |
| --- | --- |
| Poste | Windows 11 et WampServer ; versions exactes à relever sur le poste. |
| Serveur local | Apache de WampServer exécute PHP ; MySQL conserve les données ; phpMyAdmin sert à les administrer. |
| Dossier unique | Dossier cible une fois WampServer installé : `C:\wamp64\www\atelier-mosaique`, à adapter si WampServer est installé ailleurs. GitHub Desktop et WampServer utiliseront ce même dossier. |
| Application | `http://localhost/atelier-mosaique/public/` |
| Prototype | `http://localhost/atelier-mosaique/prototype/` |
| PHP | PHP 8.x, PDO avec son pilote MySQL, GD et Fileinfo. |
| JavaScript | jQuery **4.0.0 complète**, en fichier local, introduite en mission 3 ; AJAX à partir de la mission 5. |
| Partage du code | Compte GitHub déjà existant, dépôt privé `atelier-mosaique`, GitHub Desktop. L'existence du compte ne prouve pas celle du dépôt. |
| Exemple de base | `atelier_mosaique`, à créer en mission 4, après vérification qu'elle ne contient aucun autre travail. |
| Projets de démonstration | « Bruxelles Babel 26 » et « Bruxelles Babel 27 ». |

La routine est : **démarrer WampServer, ouvrir l'adresse localhost, modifier,
enregistrer, actualiser**. Le dépôt est `atelier-mosaique`, pas tout `www`.
`public/` organise le point d'entrée et les assets ; ici, ce n'est pas une racine
HTTP isolée du reste du dépôt. Le parcours privé et local ne prévoit ni
VirtualHost, ni protection HTTP supplémentaire, ni serveur PHP lancé dans
PowerShell. Les validations des formulaires et des fichiers font néanmoins
partie des apprentissages prévus.

N'ajoute pas de framework, Docker, Node.js, npm, CMS, comptes utilisateurs ou
publication publique. Les branches, pull requests, suppression complète d'un
projet, glisser-déposer et optimisations sont des bonus. Le laboratoire OVH est
facultatif et se prépare seulement après la reprise locale réussie.

## Ce qui est fourni et ce qui reste à construire

Le ZIP de départ contient uniquement le canevas technique : page PHP minimale,
CSS de base, JavaScript avec TODO, configuration d'exemple, schéma SQL composé
de questions et copie de référence du prototype. **Il ne contient pas le guide,
les missions, `docs/` ou un README pédagogique.** Conserve le README créé avec
ton dépôt ; ajoute les documents reçus séparément dans `docs/`.

L'arborescence ci-dessous indique leur place quand ils te seront transmis.
Elle ne signifie pas que `parcours.md`, `journal.md` ou `transmission.md` sont
déjà dans ton dossier. Si l'un manque, prends simplement des notes dans un
document de ton choix et reporte-les lorsqu'il te sera remis ; n'arrête pas
l'étape uniquement pour cette raison.

```text
atelier-mosaique/
├── README.md                     présentation de ton dépôt
├── .gitignore                    exclusions des fichiers locaux et générés
├── docs/                         documents reçus séparément
│   ├── parcours.md               vue d'ensemble
│   ├── journal.md                notes de travail
│   ├── transmission.md           procédure de reprise à compléter
│   └── missions/                 uniquement les fiches déjà reçues
├── prototype/
│   ├── index.php                 référence à conserver inchangée
│   └── IMG_MOSA/                 images locales pour essayer la référence
├── public/
│   ├── index.php                 entrée de l'application à construire
│   └── assets/
│       ├── css/style.css
│       └── js/
│           ├── app.js            interactions et messages
│           ├── api.js            appels AJAX, à partir de la mission 5
│           └── vendor/           jQuery à ajouter en mission 3
├── src/                          PHP réutilisable à construire
├── config/config.example.php     modèle de configuration de l'application
├── database/schema.sql           schéma à concevoir progressivement
└── storage/projects/             fichiers image, par identifiant de projet
```

Les fichiers `.gitkeep` maintiennent les dossiers vides dans Git ; ils
n'exécutent aucun code. `config/config.local.php` sera ta copie locale ignorée
par Git. Copier le modèle ne crée ni connexion ni base : le code PDO reste à
écrire. Les valeurs proposées pour MySQL local sont `127.0.0.1`, port `3306`,
base `atelier_mosaique`, encodage `utf8mb4`, compte initial WampServer `root`
et mot de passe vide. Ce sont des valeurs d'exemple à comparer aux réglages
existants ; ne remplace pas des accès déjà configurés pour les faire correspondre.

L'application doit permettre de créer, lister, ouvrir et renommer des projets,
ajouter leurs images, afficher une galerie, retirer une image après confirmation,
retrouver les réglages de mosaïque et exporter un PNG. Rien de cela n'est supposé
déjà développé. Deux tables sont proposées : `projects` et `images`. Les fichiers
restent sur disque ; la base conserve leurs informations. Un dossier tel que
`storage/projects/12/` dépend de l'identifiant stable, pas du nom du projet.

Le prototype lit `IMG_MOSA/`, produit des miniatures JPEG avec PHP/GD, puis
dispose les images en JavaScript et dessine le PNG dans un canvas. Le moteur
JavaScript natif et son export sont à réutiliser progressivement, pas à réécrire
en jQuery. L'original `prototype/index.php` reste intact.

## État à compléter pour cet échange

Ne coche rien à partir du seul contenu de ce document. Reprends uniquement des
résultats que tu as observés ou confirmés avec ton accompagnant.
Renseigne seulement les champs utiles à ta question : ce bloc aide à reprendre
le travail, ce n'est pas un questionnaire obligatoire avant de recevoir de l'aide.

```text
Fiche de mission reçue :
Étape en cours :
Ce que je cherche à obtenir maintenant :
Ce qui fonctionne déjà, avec le résultat observé :
Ce qui bloque ou le message exact, sans donnée sensible :
Ce que j'ai essayé :
Fichiers ou courts extraits utiles que je fournis :
Versions et chemin réellement vérifiés, si utiles à cette question :
Hypothèses qui restent à vérifier :
Dernière validation avec mon accompagnant :
Prochaine petite étape convenue :
```

À la fin d'une séance, garde un bilan court : ce qui a été vérifié, ce qui reste
incertain et la prochaine action. Tu peux le remettre au début d'un autre chat.
Le journal du parcours conserve trois rubriques : « Ce que j'ai fait », « Ce qui
me bloque » et « Ma prochaine étape ». Tu peux déjà les utiliser dans tes notes
si le document ne t'a pas encore été remis.

## Mission 1 — Ouvre ton atelier et partage ton code

**Objectif :** préparer ton dépôt privé et faire voir une première modification
à ton accompagnant. Le dépôt de code et les projets créés dans l'application
sont deux choses différentes.

1. Retrouve ton compte GitHub existant. Vérifie l'e-mail confirmé, la double
   authentification et la conservation privée des codes de récupération.
   Garde les réglages déjà opérationnels ; transmets seulement ton identifiant
   public à ton accompagnant.
2. Crée le dépôt `atelier-mosaique` avec un README et la visibilité **Private**.
   Demande l'identifiant exact de ton accompagnant, invite ce compte comme
   collaborateur, puis vérifie avec lui l'acceptation et l'accès effectif.
3. Installe et connecte GitHub Desktop si nécessaire ; vérifie le compte,
   le nom et l'adresse de commit. Clone dans `C:\wamp64\www\atelier-mosaique`
   si WampServer existe déjà. Sinon, utilise provisoirement
   `C:\Projets\atelier-mosaique` ; le dossier entier sera déplacé en mission 2.
   Ouvre-le avec **Repository → Show in Explorer**. Garde la branche proposée.
4. Extrais le ZIP dans Téléchargements. Copie ses dossiers techniques et
   `.gitignore` directement dans le dépôt cloné, sans ajouter un niveau
   `atelier-mosaique` supplémentaire. Conserve le README du dépôt et compare
   tout fichier déjà présent avant de le remplacer. Vérifie `public\index.php`
   et l'affichage des extensions de fichiers Windows.
5. Ajoute « Mon atelier est ouvert ! » au README. Enregistre, relis **Changes**,
   fais un commit ciblé puis **Push origin**. Retrouve la phrase et le commit
   sur GitHub ; fais vérifier leur visibilité par ton accompagnant.

**À vérifier :** dépôt privé accessible au collaborateur, fichiers attendus
seulement, distinction entre enregistrer le fichier, faire un commit et envoyer
les commits. Si un élément est introuvable, vérifie compte, chemin, invitation
acceptée et Push. Avant une séance : **Fetch origin**, puis **Pull origin** si
proposé. En cas de conflit, garde les fichiers et demande de l'aide, sans forcer.

**Ressources de la fiche :**

- [GitHub — Double authentification](https://docs.github.com/fr/authentication/securing-your-account-with-two-factor-authentication-2fa/configuring-two-factor-authentication) : consulte les étapes seulement si elle reste à activer.
- [GitHub — Inviter un collaborateur](https://docs.github.com/fr/repositories/managing-your-repositorys-settings-and-features/repository-access-and-collaboration/inviting-collaborators-to-a-personal-repository) : repère l'invitation et son acceptation.
- [GitHub Desktop — Bien démarrer](https://docs.github.com/fr/desktop/overview/getting-started-with-github-desktop) : choisis Windows et cherche clonage, commit et envoi.

## Mission 2 — Allume ton atelier

**Objectif :** afficher ton application et essayer le prototype avec WampServer.
PHP s'exécute sur le serveur ; le navigateur reçoit le HTML et exécute JavaScript.

1. Si nécessaire, installe WampServer pour Windows 11 64 bits avec ton
   accompagnant : prérequis Visual C++ x86 et x64, installateur administratrice,
   dossier sans espace ni accent à la racine du disque, sans écraser une
   installation existante. Démarre WampServer, attends l'icône verte et ouvre
   `http://localhost/`. Ouvre phpMyAdmin, sélectionne MySQL et relève version
   et port. Une installation MariaDB déjà configurée se vérifie ensemble.
2. Place le dépôt dans `C:\wamp64\www\atelier-mosaique`. S'il est ailleurs,
   ferme éditeur et GitHub Desktop puis déplace le dossier **entier**, avec
   `.git` et les fichiers locaux, par Couper/Coller. Ne remplace pas une
   destination déjà occupée. Rouvre-le par **File → Add local repository**,
   sans nouveau clone. Vérifie l'historique et `public\index.php`.
3. Ouvre `http://localhost/atelier-mosaique/public/` : titre et PHP 8.x doivent
   apparaître. Dans phpinfo() depuis l'accueil WampServer, vérifie la même
   version, GD, PDO, `pdo_mysql`, Fileinfo et le pilote PDO `mysql`. Si nécessaire,
   active une extension avec ton accompagnant dans le menu PHP de WampServer,
   redémarre les services et revérifie. Note versions, extensions, port,
   adresses et **Loaded Configuration File** dans la transmission. Modifie une
   phrase de `public/index.php`, enregistre et actualise son adresse HTTP.
4. Garde le prototype intact. Place trois à cinq images JPG ou PNG légères,
   autorisées et non sensibles dans `prototype/IMG_MOSA/`. Ouvre
   `http://localhost/atelier-mosaique/prototype/`, génère puis exporte et ouvre
   le PNG. Essaie deux espacements, compare fond coloré et transparent et
   observe la marge d'export. Les images de test restent hors de Git.
5. Garde les adresses en favoris. À chaque séance, démarre WampServer, attends
   l'icône verte, ouvre l'application, modifie et actualise. En fin de séance,
   utilise **Arrêter les services**.

**À vérifier :** deux adresses fonctionnelles, phrase actualisée, même dépôt et
historique, extensions présentes, prototype et PNG visibles. Arrête les services
puis actualise : l'application ne répond plus ; redémarre et retrouve-la. Si
seule l'application échoue, compare son dossier avec son URL. Si la mosaïque est
vide, vérifie `prototype/IMG_MOSA/`, une image valide et GD. Ouvrir le fichier
sur disque ne remplace pas l'adresse HTTP. En cas de connexion MySQL refusée ou
d'icône orange/rouge, relève le message avant de modifier les réglages.

**Ressources de la fiche :**

- [WampServer — Téléchargements](https://wampserver.aviatechno.net/) : repère l'installateur complet et la vérification des paquetages Visual C++.
- [WampServer — Prérequis](https://wampserver.aviatechno.net/?lang=fr&prerequis=afficher) : vérifie Windows, paquetages et dossier d'installation.
- [GitHub Desktop — Ajouter un dépôt local](https://docs.github.com/fr/desktop/adding-and-cloning-repositories/adding-a-repository-from-your-local-computer-to-github-desktop) : retrouve un dépôt déplacé avec son historique.

## Mission 3 — Dessine la porte d'entrée

**Objectif :** réaliser une maquette sans base de données, utilisable au clavier
et sur petit écran.

1. Dessine l'accueil avec quelques projets, puis l'état « Aucun projet pour
   le moment ». Dans les deux cas, indique comment commencer.
2. Construis la page dans `public/index.php` avec « Bruxelles Babel 26 » et
   « Bruxelles Babel 27 » ; range le CSS dans `public/assets/css/style.css`.
3. Ajoute « Nouveau projet », un champ « Nom du projet » associé à un vrai
   label et un formulaire qui peut rester visible dans la page.
4. Prévois « Créer le projet » et un emplacement d'erreur. Explique que
   l'enregistrement viendra ensuite : vérifier un champ ne crée pas de projet.
5. Vérifie Tab, Maj + Tab, Entrée et le focus visible ; réduis la largeur et
   agrandis le texte. Réorganise les cartes si elles débordent.
6. Télécharge jQuery **4.0.0 complète compressée**, pas « slim », depuis la
   source officielle. Range `jquery-4.0.0.min.js` dans `public/assets/js/vendor/`,
   avec son commentaire de licence, sans npm. Charge ce script avant `app.js`,
   avec `defer` sur les deux. Avec `.on()`, fais placer le focus dans le champ
   par « Nouveau projet ». Note version et provenance dans la transmission ;
   le fichier de bibliothèque peut être suivi dans Git mais reste inchangé.

**À vérifier :** états vide/rempli lisibles, jQuery chargé, focus visible et
commandes utilisables au clavier et à la souris, interface exploitable vers
320 pixels et texte à 200 %, contrastes lisibles. Un nom vide doit produire une
indication claire, sans faux succès. Si `$` est inconnu, vérifie chemin et ordre
des scripts. Fais un commit lorsque cette maquette est vérifiée.

**Ressources de la fiche :**

- [MDN — Formulaires web](https://developer.mozilla.org/fr/docs/Learn_web_development/Extensions/Forms) : cherche « Votre premier formulaire », label, champ et bouton.
- [jQuery — Téléchargement officiel](https://jquery.com/download/) *(anglais)* : repère la distribution complète 4.0.0 compressée.
- [jQuery — `.on()`](https://api.jquery.com/on/) *(anglais)* : cherche comment relier un événement à une fonction.

## Mission 4 — Donne une mémoire à ton application

**Objectif :** enregistrer un premier projet et le retrouver après réouverture.
Commence par un formulaire PHP classique, sans intercepter l'envoi avec AJAX.

1. Dessine `projects` et `images`, leur identifiant et la relation un projet /
   plusieurs images. Exemple : le projet 12 possède les images 31 et 32.
2. Avec ton accompagnant, crée dans phpMyAdmin une nouvelle base locale MySQL
   `atelier_mosaique` en `utf8mb4`. Si ce nom est déjà utilisé, choisis un autre
   nom sans réutiliser les données d'un autre travail.
3. Complète `database/schema.sql` progressivement : d'abord les besoins d'un
   projet ; la table d'images sera utile en mission 6. Exécute dans la base de
   test sélectionnée. Exporte le travail existant avant une évolution du schéma.
4. Copie le modèle vers `config/config.local.php` seulement si cette copie
   n'existe pas ; vérifie les extensions Windows. Adapte ses valeurs à ton
   installation et vérifie son exclusion dans GitHub Desktop. Prépare dans
   `src/` le chargement de cette configuration et la connexion PDO en `utf8mb4`.
5. Relie le formulaire au traitement PHP en POST : demande attendue, nom
   validé côté serveur puis requête préparée. Ajoute avec ton accompagnant un
   jeton CSRF aléatoire lié à la session, transmis par le formulaire et vérifié
   avant l'écriture. POST seul ne remplace pas ce contrôle.
6. Relis le projet en base et échappe son nom lors de l'affichage HTML, y
   compris dans les attributs entre guillemets. Cherche comment rediriger après
   un ajout pour qu'actualiser la page de résultat ne crée pas de doublon.

**À vérifier :** persistance de « Bruxelles Babel 26 — édition d'été », accents
et apostrophe préservés, actualisation sans second ajout. Refuse côté PHP un nom
vide, composé d'espaces ou trop long ; un jeton absent ou modifié bloque aussi
l'ajout. Si les règles de nom l'acceptent, `<b>Bruxelles Babel 26</b>` s'affiche
comme texte, sans devenir du gras. Les accès locaux restent hors des commits.
Diagnostique séparément connexion, écriture et relecture.

**Ressources de la fiche :**

- [PHP — Requêtes préparées](https://www.php.net/manual/fr/pdo.prepared-statements.php) : repère les paramètres et le rôle de `execute`.
- [SQLBolt — Exercices SQL](https://sqlbolt.com/) *(anglais)* : leçons 1, 13 et 16 pour lire, ajouter et créer une table ; adapte les exemples à MySQL.
- [OWASP — Prévention CSRF](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html) *(anglais)* : cherche seulement « Synchronizer Token Pattern », à expliquer ensemble.

## Mission 5 — Range tes projets

**Objectif :** lister, ouvrir et renommer les projets enregistrés en gardant
leurs identifiants et leurs emplacements stables.

1. Remplace les cartes fictives par les lignes de `projects`, préserve l'état
   vide et échappe chaque nom à l'affichage.
2. Relie chaque projet à sa page de détail par son identifiant. Vérifie sa forme
   côté PHP puis cherche la ligne avec une requête préparée.
3. Affiche le nom et un formulaire de renommage. Fais d'abord fonctionner un
   envoi HTML normal avec POST, jeton CSRF et nom valide. La condition `WHERE`
   de la mise à jour doit viser uniquement le projet choisi.
4. Prépare `storage/projects/{identifiant}/` à partir du projet trouvé en base.
   Choisis de créer le dossier maintenant ou au premier upload et note-le.
   Un nom de projet ou un chemin fourni par le navigateur ne choisit pas ce dossier.
5. Prévois « Projet introuvable », un retour à l'accueil et un statut HTTP 404
   pour une page de projet absente.
6. Ajoute ensuite le renommage AJAX en POST : nom, identifiant et jeton CSRF,
   contrôles PHP conservés, réponse JSON et statut HTTP adapté. Centralise les
   `$.ajax()` et erreurs communes dans `api.js` ; `app.js` gère l'interface.
   Charge jQuery, `api.js`, puis `app.js`. Pas de réglage global `$.ajaxSetup()`.

Une URL AJAX relative part de la page affichée, pas du dossier du fichier JS.
Un `/` initial repart de la racine de localhost : vérifie le sous-dossier de
l'application dans l'onglet Réseau.

**À vérifier :** bonne liste et bon projet ouvert, renommage limité au seul
projet, identifiant et emplacement inchangés, parcours clavier possible.
Identifiant absent, négatif ou non numérique : message compréhensible et aucun
dossier créé. Nom ou jeton invalide : ancien nom intact. Pendant AJAX, indique
l'attente, empêche le second envoi et réactive le bouton après réussite ou
échec, avec message accessible et sans réponse serveur brute insérée en HTML.
Deux projets de même nom restent distincts. L'ordre d'affichage stable est un
défi facultatif.

**Ressources de la fiche :**

- [PHP — `PDOStatement::fetch`](https://www.php.net/manual/fr/pdostatement.fetch.php) : lis une ligne et repère l'absence de résultat.
- [PHP — `htmlspecialchars`](https://www.php.net/manual/fr/function.htmlspecialchars.php) : regarde les guillemets dans la valeur d'un champ.
- [jQuery — `$.ajax()`](https://api.jquery.com/jQuery.ajax/) *(anglais)* : cherche `method`, `data`, `dataType`, `.done()`, `.fail()` et `.always()`.

## Mission 6 — Accueille tes premières images

**Objectif :** envoyer une image, l'afficher dans la bonne galerie et la retirer
après confirmation ; passer ensuite à plusieurs fichiers puis à AJAX.

1. Prépare pour un projet existant un formulaire d'une seule image : POST,
   jeton CSRF, `multipart/form-data`, formats JPEG/PNG et limites annoncées.
2. Côté PHP, vérifie projet, jeton, erreur d'envoi, taille, contenu réel avec
   Fileinfo, décodage possible et limites de largeur, hauteur et pixels avant
   les traitements coûteux. Extension et filtre du navigateur ne suffisent pas.
3. Génère un nom de stockage sans collision, indépendant du nom original et
   avec l'extension du format accepté. Range le fichier sous le bon identifiant
   de projet et complète `images` avec des requêtes préparées.
4. Construis la galerie du projet ouvert. Un point d'accès PHP reçoit un
   identifiant d'image, retrouve la ligne, contrôle son lien avec le projet et
   construit le chemin autorisé. Il renvoie les octets avec le type image
   vérifié, sans exécuter le fichier ni accepter un chemin arbitraire.
5. Prépare le retrait avec une confirmation qui nomme l'image et le projet,
   et une action « Annuler ». Seul un POST confirmé avec jeton valide agit ;
   recontrôle côté serveur lien image/projet et chemin avant fichier et base.
6. Quand une image fonctionne, autorise l'envoi multiple avec limite de nombre
   et bilan par fichier accepté/refusé, sans succès global trompeur.
7. Ajoute AJAX via `api.js` avec `FormData`, identifiant de projet et jeton.
   Garde les vérifications PHP ; `app.js` gère attente et galerie. `serialize()`
   ne transporte pas les fichiers.

Examine avec ton accompagnant un ajout réussi sur disque mais refusé en base,
ou un retrait du fichier qui échoue. Une transaction SQL n'annule pas le disque :
prévois une remise en cohérence ciblée et un message honnête.

Relève dans phpinfo() `upload_max_filesize`, `post_max_size` et
`max_file_uploads`. Un envoi total trop grand peut rendre les données attendues
absentes. Si un réglage doit changer, passe par **PHP → php.ini** dans WampServer
avec ton accompagnant, redémarre les services et revérifie. Note les limites dans
la transmission. Étudie `UPLOAD_ERR_*`, `move_uploaded_file` et `nosniff` au
moment utile ; ne sers jamais un upload avec `include`.

**À vérifier :** image valide et alternative textuelle dans la bonne galerie,
faux JPEG, format interdit et fichier trop grand refusés ; deux noms originaux
identiques sans écrasement. Projet absent ou lien image/projet incohérent refusé,
y compris pour affichage et retrait. Annuler conserve, confirmer retire
uniquement l'image choisie. Teste sur des images de démonstration. Uploads hors
Git, jeton incorrect sans faux succès, erreurs multiples expliquées, double clic
sans second envoi et nouvel essai possible après erreur AJAX.

**Ressources de la fiche :**

- [PHP — Chargement de fichiers](https://www.php.net/manual/fr/features.file-upload.php) : cherche formulaire, erreurs et limites ; adapte une notion à la fois.
- [PHP — `finfo_file`](https://www.php.net/manual/fr/function.finfo-file.php) : repère l'examen du type MIME réel.
- [OWASP — File Upload](https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html) *(anglais)* : vise noms, formats et contenu. Pour AJAX, reprends la ressource jQuery de la mission 5.

## Mission 7 — Branche la machine à mosaïques

**Objectif :** intégrer le moteur fourni aux images du projet, conserver ses
réglages et télécharger un PNG.

1. Repère dans le prototype `$data`, les miniatures `?thumb=1`,
   `renderPinterest()` et `exportMosaicToPng()` ; explique leur rôle.
2. Reprends les éléments utiles par petites étapes, sans modifier la référence :
   CSS dans `style.css`, moteur JavaScript/Canvas natif dans `app.js`, traitements
   PHP réutilisables dans `src/`. Vérifie l'aperçu après chaque déplacement et
   conserve l'algorithme et l'export.
3. Remplace les images globales de `IMG_MOSA/` par celles du projet sélectionné.
   Réutilise l'accès par identifiant et le contrôle image/projet, miniatures
   comprises. Des URLs d'images suffisent au navigateur ; leurs octets n'ont
   pas besoin de transiter dans `api.js`.
4. Enregistre puis recharge largeur/hauteur (`w`, `h`), densité (`mode`),
   espacement (`gap`), arrondi (`radius`), couleur (`bg`), transparence
   (`bg_transparent`), marge (`margin`) et seed effective (`seed`). Valide
   côté PHP bornes et choix permis ; distingue aperçu et enregistrement.
   Pour enregistrer, `app.js` appelle `api.js` en POST avec le jeton CSRF.
   Transmets proprement les données PHP vers JavaScript, avec une sérialisation
   JSON adaptée au HTML, sans concaténer les noms dans du code.
5. Essaie plusieurs images légères, une seule puis aucune. Prévois un message
   utile sans image et un vrai bouton clavier pour régénérer ; le double-clic
   du prototype ne suffit pas.

**À vérifier :** aucun mélange de projets, réglages restaurés à la réouverture,
y compris une case décochée et la seed réellement utilisée. PNG lisible avec
fond demandé et dimensions `(largeur + 2 × marge)` par `(hauteur + 2 × marge)`.
Une image peut se répéter ; aucune image ne doit donner un faux export. Valeurs
hors limites et projet absent traités clairement. Une erreur AJAX laisse
l'aperçu utilisable et ne prétend pas avoir enregistré les réglages.

**Limites déjà connues, pas des erreurs à attribuer à ton travail :** le PHP
mélange avec `shuffle()` avant le JavaScript, donc une seed identique ne suffit
pas à reproduire exactement le placement. Les miniatures JPEG sont aplaties
sur blanc ; l'export ne reprend ni les filtres CSS ni le léger zoom de l'aperçu.
Fond transparent de mosaïque et transparence des images sources sont distincts.
Enquêter sur une différence est un bonus, pas un blocage du parcours.

**Ressources de la fiche :**

- [PHP — `json_encode()`](https://www.php.net/manual/fr/function.json-encode.php) : repère sérialisation et options `JSON_HEX_*` pour le passage par du HTML.
- [MDN — Images dans Canvas](https://developer.mozilla.org/fr/docs/Web/API/Canvas_API/Tutorial/Using_images) : observe chargement et `drawImage()` pour comprendre l'export existant.

## Mission 8 — Fais essayer ton atelier

**Objectif :** faire un parcours de recette, noter les résultats et corriger
les problèmes observés un à un.

1. Prépare les deux projets de démonstration et leurs images non sensibles.
   Effectue les essais ci-dessous, puis laisse ton accompagnant les refaire
   sans guider ses clics.
2. Note chaque essai : réussi, à corriger ou non testé. Pour un problème,
   indique étapes exactes, résultat attendu et résultat obtenu.
3. Relis avec ton accompagnant les contrôles introduits depuis la mission 4.
   Corrige un problème à la fois, puis refais le test qui l'a révélé.
4. Raconte dans le journal un problème reproductible et sa résolution, ou le
   blocage restant. Reporte les limites connues dans la transmission.

**Essais à couvrir :**

- Deux projets avec images distinctes ; chaque galerie garde ses images.
- Renommage avec accent et apostrophe, puis retour au nom initial ; chemins intacts.
- Retrait annulé puis confirmé ; seule l'image voulue change.
- Réglages enregistrés puis rechargés, case décochée comprise.
- PNG coloré et transparent, avec dimensions incluant la marge.
- Faux fichier image, fichier trop gros et identifiant absent : message utile,
  sans ajout partiel silencieux ; projets vide et à une image compréhensibles.
- Page laissée ouverte, services WampServer arrêtés, tentative de renommage AJAX :
  erreur lisible et bouton réactivé ; services redémarrés, nouvel essai réussi.
- Nom invalide ou jeton CSRF absent/incorrect, y compris par AJAX : refus sans
  modification. Double clic pendant un upload : une seule requête.
- Nom ressemblant à du HTML affiché comme texte ; paramètres SQL préparés.
- Identifiant d'image d'un autre projet et chemin `../` refusés lors de
  l'affichage, des miniatures ou du retrait.
- Liens, formulaires, confirmation, génération et export utilisables au clavier,
  focus visible et logique après erreur ou retrait ; largeur 320 pixels CSS,
  zoom 200 % puis 400 %, réduction des animations, labels, alternatives et
  contrastes vérifiés. Ces essais ne sont pas une certification d'accessibilité.

**À vérifier :** résultats écrits, y compris les tests non faits et les problèmes
ouverts, boutons et messages exploitables après erreur, un problème expliqué et
reproductible, limites du prototype reconnues.

**Ressources de la fiche :**

- [PHP — `htmlspecialchars()`](https://www.php.net/manual/fr/function.htmlspecialchars.php) : vérifie guillemets et caractères spéciaux à l'affichage.
- [OWASP — Protection CSRF](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html) *(anglais)* : reprends le trajet et le contrôle du jeton lié à la session.
- [W3C WAI — Premières vérifications](https://www.w3.org/WAI/test-evaluate/preliminary/) *(anglais)* : vise clavier, focus, formulaires et zoom.

## Mission 9 — Prépare la transmission

**Objectif :** permettre à ton accompagnant de faire fonctionner l'application
sur un autre ordinateur avec le code et le jeu de démonstration.

1. Complète la transmission : versions réellement testées, dossier dans `www`,
   adresses localhost, configuration locale à préparer, schéma SQL final et
   limites connues. Aucun secret dans le document.
2. Prépare deux projets fictifs et quelques images partageables. Note les
   identifiants, noms de stockage et résultats attendus.
3. Relis GitHub Desktop : code, documentation, modèle de configuration et
   schéma sont partageables ; configuration réelle, uploads, caches, PNG et
   dumps réels restent hors de Git.
4. Fais commit puis Push. Transmets séparément l'archive de démonstration :
   export SQL de test et fichiers image correspondants. Ne copie jamais le
   dossier interne d'un serveur MySQL en fonctionnement.
5. Laisse ton accompagnant cloner sur un autre poste dans `www`, préparer une
   configuration locale, importer dans une base vide et replacer les images.
   Note ce qui manque au guide, corrige-le et fais reprendre l'étape concernée.
   Si l'import échoue, relève le message et situe l'étape avant de réessayer.

Git partage le code suivi, pas la base active ni les images ignorées. Après
import, conserve les identifiants du SQL et les noms de fichiers correspondants,
par exemple sous `storage/projects/12/` : recréer les projets à la main ferait
perdre cette correspondance. Évite les chemins absolus propres à ton poste.

**À vérifier :** installation réellement relancée sur un autre ordinateur,
projets/images/réglages conformes et PNG exporté. Configuration absente ou image
manquante identifiée sans secret affiché. Date et résultat de reprise notés ;
aucun secret, dump réel ou upload envoyé dans Git. Un lien GitHub transmis ne
suffit pas à valider la mission.

**Option OVH, après reprise locale réussie :** l'accompagnant vérifie PHP,
extensions, SQL réellement disponible, quotas, limites d'upload, exposition de
`public/`, transfert chiffré et protection du laboratoire. Ne suppose pas qu'une
offre de 100 Mo inclut SQL. Les réglages distants sont distincts ; si une
capacité manque, la démonstration reste locale. Aucune publication n'est requise.

**Ressources de la fiche :**

- [GitHub Desktop — Cloner un dépôt](https://docs.github.com/fr/desktop/adding-and-cloning-repositories/cloning-and-forking-repositories-from-github-desktop) : repère la reprise du dépôt privé sur une autre machine.
- [phpMyAdmin — Importer et exporter](https://docs.phpmyadmin.net/fr/latest/import_export.html) : repère la base sélectionnée, l'import SQL et l'export.
- [OVHcloud — Accès au stockage](https://docs.ovhcloud.com/fr/guides/web-cloud/web-hosting/ftp-connection) : seulement pour l'option, compare les transferts réellement disponibles sur l'offre.

## Utiliser les ressources pendant le chat

Les liens ci-dessus sont repris des fiches de mission. Leur présence dans ce
document **ne signifie pas que ChatGPT les a consultés ou vérifiés en temps réel
dans cet échange**. Avant d'affirmer qu'une interface, une version ou une API
fonctionne d'une certaine manière, vérifie la documentation officielle accessible
et les versions réellement utilisées. Si la consultation est impossible, dis-le
et demande un court extrait utile ; n'invente ni lien ni étape de menu.

Une ressource anglaise se travaille par un passage ciblé et une explication en
français. Ne transforme pas cette liste en devoir de lecture complet. Les aides
doivent ramener à une prochaine action et à un résultat observable dans la mission
en cours.

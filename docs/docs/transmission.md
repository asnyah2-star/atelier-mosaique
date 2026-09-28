# Guide de transmission — À compléter

Complète ce document au fil des missions. Je m'en servirai pour lancer ton
application sur mon ordinateur et vérifier que les indications suffisent à la
reprendre. Le canevas fourni ne contient pas encore les fonctionnalités ni le
schéma SQL final : les essais ci-dessous seront possibles après ton développement.

[Retour au parcours](parcours.md) · [Mission 9](missions/09-transmission.md)

## 1. Carte d'identité de la livraison

| Information | À compléter sans secret |
| --- | --- |
| Dépôt GitHub privé | `[URL_DU_DEPOT]` |
| Mon compte invité au dépôt | `[IDENTIFIANT_GITHUB_ACCOMPAGNANT]` ; invitation acceptée : `[oui/non]` |
| Version de code testée | `[date et identifiant du commit visible dans GitHub Desktop]` |
| Fonctionnalités terminées | `[liste courte]` |
| Limites et problèmes connus | `[dont limites éventuelles du prototype]` |

## 2. Environnement réellement testé

L'environnement retenu est **WampServer sous Windows 11**. Note les versions et
chemins réellement utilisés. Le parcours lance l'application avec le serveur
intégré au PHP de WampServer ; Apache sert notamment l'accès à phpMyAdmin.

| Élément | Valeur vérifiée |
| --- | --- |
| Windows 11 / WampServer | `[versions]` |
| Dossier de WampServer | `C:\wamp64` ou `[chemin réel]` |
| PHP 8.x | `[version exacte]` |
| Exécutable PHP utilisé | `[chemin complet du php.exe relevé en mission 2]` |
| Configuration de ce PHP | `[fichier indiqué par --ini]` |
| Serveur SQL | `MySQL ; [version]` ou adaptation MariaDB vérifiée ensemble |
| Hôte et port du serveur SQL retenu | `127.0.0.1 ; [port vérifié dans WampServer]` |
| Nom de la base locale | `atelier_mosaique` ou `[nom réellement choisi]` |
| phpMyAdmin et navigateur | `[versions]` |
| jQuery | `4.0.0 complète proposée ; [version réellement utilisée]` |
| Fichier jQuery local et provenance | `public/assets/js/vendor/jquery-4.0.0.min.js` après mission 3 ; site officiel jQuery, licence conservée |
| Extensions GD, PDO MySQL, Fileinfo | `[présentes ; formats GD essayés]` |
| Limites d'envoi et dimensions d'image retenues | `[valeurs applicatives et limites PHP compatibles]` |
| Dossier local du dépôt | `C:\Projets\atelier-mosaique` ou `[chemin réel]` |
| Racine web | `[chemin du dépôt]\public` uniquement |
| Adresse locale de l'application | `[adresse définie ensemble à la mission 2]` |
| Accès local à phpMyAdmin | `[adresse locale]` |

Les mots de passe n'ont pas leur place ici, même dans un dépôt privé. Garde `config/config.local.php` hors de Git.

## 3. Récupérer le code sur l'autre ordinateur

Voici la procédure que je suivrai. Complète-la si ton application demande une étape supplémentaire.

1. J'accepterai ton invitation au dépôt privé, puis je me connecterai à GitHub Desktop avec mon propre compte.
2. Dans **File → Clone repository**, je choisirai le dépôt et un nouveau dossier local, puis j'ouvrirai le README de cette copie. Pour une copie existante, je vérifierai d'abord mes modifications avant **Fetch origin**, puis **Pull origin** si proposé.
3. Je démarrerai les services de mon environnement local : Apache pour phpMyAdmin et le serveur SQL retenu. Sur Windows, j'utiliserai WampServer. Je reprendrai le lancement décrit en [mission 2](missions/02-demarrage.md) en adaptant les chemins de PHP et du dépôt à mon ordinateur. Le serveur PHP local exposera seulement `public/` ; `src/`, `config/`, `database/` et `storage/` resteront hors de l'accès web direct.
4. Je copierai `config/config.example.php` en `config/config.local.php` si cette copie n'existe pas encore, puis j'adapterai les valeurs à mon ordinateur. Le modèle vise MySQL dans WampServer : hôte `127.0.0.1`, port proposé `3306`, base `atelier_mosaique` et accès initiaux locaux à vérifier. Mon environnement peut utiliser d'autres valeurs ; je conserverai ses réglages existants et adapterai seulement la copie locale. Nous échangerons les accès séparément si nécessaire ; ils ne doivent pas figurer dans ce guide.

À préciser avec ton code final : `[où et comment l'application charge la configuration locale ; message attendu si elle manque]`.

Après les missions 3 et 5, vérifie aussi que le fichier jQuery local est bien
suivi dans Git et récupéré avec le code. La page doit charger les scripts avec
`defer` dans l'ordre **jQuery → api.js → app.js**, une seule fois chacun.
Note les adresses PHP appelées et le format des réponses : `[à compléter]`.
Le dossier `public/assets/js/vendor/` sera créé en mission 3 ; il est distinct
du dossier `vendor/` de Composer, qui n'est pas utilisé dans cet exercice.
Une exception dans `.gitignore` permet de partager ce fichier jQuery précis.
Si nous changeons sa version, nous adapterons son nom dans la page et l'exception Git.

## 4. Distinguer les éléments à transmettre

| Dans Git | Dans une archive de test séparée | Sur chaque machine seulement |
| --- | --- | --- |
| Code, docs, configuration d'exemple, `database/schema.sql` final ; éventuellement des données entièrement fictives relues | Export SQL de démonstration + images autorisées correspondantes + inventaire | Configuration réelle, secrets, données personnelles, uploads de travail, caches et exports générés |

Git ne copie pas automatiquement la base en cours d'utilisation ni les photos
ignorées. Ne copie jamais les fichiers internes de MySQL/MariaDB en fonctionnement.
`.gitignore` n'efface pas un secret déjà suivi : si tu en repères un, interromps
le partage et préviens-moi pour que nous traitions le problème.

Le canevas autorise déjà `database/schema.sql` dans Git. Si tu souhaites aussi
versionner un fichier de données entièrement fictives, nous relirons son contenu
avant d'ajouter une exception limitée à ce fichier dans `.gitignore` ; les autres
fichiers SQL restent exclus.

## 5. Préparer l'archive de démonstration

Nous préparerons cet export dans l'environnement Windows local, avec phpMyAdmin.
Il ne demande aucune intervention sur une base de production.

1. Choisis une base locale ne contenant que le jeu fictif convenu. Si ta base contient d'autres données, nous préparerons une copie de test distincte avant l'export pour transmettre uniquement le contenu prévu.
2. Termine les ajouts et retraits d'images, puis ne modifie plus ce jeu pendant l'export et la copie. Dans phpMyAdmin, sélectionne la base de test, ouvre **Exporter**, choisis le format **SQL**, les tables `projects` et `images`, leur structure et leurs données. Nous vérifierons les options ensemble : conserver les identifiants, sans commandes de suppression, création de comptes ou droits serveur.
3. Enregistre `demo.sql` dans un dossier de préparation situé hors du dépôt. Nous le relirons pour vérifier l'absence de secrets et de données personnelles. Note la version du code correspondante.
4. Dans ce même dossier, copie seulement les images décrites par ce SQL, en conservant la structure `storage/projects/<identifiant>/` et les noms de stockage. N'y mets ni configuration, ni cache, ni PNG généré, ni dossiers internes du serveur SQL.
5. Complète l'inventaire ci-dessous puis compresse le dossier en ZIP. Ouvre le ZIP pour vérifier son contenu. Transmets-moi l'archive par le canal privé convenu ensemble : `[canal à convenir, sans identifiants de connexion]`.

Exemple de forme, avec des valeurs à remplacer par celles de **ton** jeu :

```text
demo-atelier.zip
├── demo.sql
├── inventaire.txt
└── storage/
    └── projects/
        └── 12/
            └── photo_ab12.jpg
```

| Projet fictif / identifiant | Image / identifiant | Nom exact sur disque | Droit de partage / provenance |
| --- | --- | --- | --- |
| `[nom / id]` | `[nom / id]` | `storage/projects/[id]/[nom stocké]` | `[photo personnelle non sensible ou autorisation]` |

Taille totale : `[taille]` — nombre de projets : `[nombre]` — nombre d'images : `[nombre]`.

## 6. Installer une base vide, puis les données

Ces étapes décrivent la reprise que je ferai sur mon ordinateur. Vérifie qu'elles
correspondent à ta livraison et complète les indications nécessaires.

1. Je vérifierai dans phpMyAdmin si la base prévue dans ma configuration locale existe (`atelier_mosaique` dans le modèle fourni). Pour ce test, je préparerai une base **vide** en UTF-8 (`utf8mb4`) dans mon environnement de développement. Si une base du même nom contient déjà du travail, nous choisirons un autre nom disponible et adapterons la configuration locale, sans écraser l'existant.
2. Je choisirai une seule voie. **Sans données**, j'importerai le `database/schema.sql` que tu auras complété, puis je créerai les projets dans l'application. **Avec l'archive de démonstration**, j'importerai `demo.sql`, qui contient structure et données, dans la base vide ; le schéma ne doit pas être importé une seconde fois.
3. Dans la base sélectionnée, j'ouvrirai **Importer**, choisirai le fichier SQL et lancerai l'import. Je vérifierai le résultat, les deux tables, les identifiants et le nombre de lignes. En cas d'erreur, nous examinerons le message avant de relancer un import partiel.
4. Pour l'archive de démonstration, je replacerai son contenu `storage/projects/` au même emplacement dans la nouvelle copie du projet, hors `public/`, en conservant exactement les identifiants de dossier et les noms de fichiers. Les projets ne doivent pas être recréés à la main après l'import.
5. Je vérifierai que PHP peut lire les images et écrire dans les dossiers prévus, sans ouvrir tous les droits à tout le monde. J'ouvrirai l'adresse locale et comparerai les galeries à l'inventaire.

Si une image manque, je vérifierai sa ligne SQL, son lien au projet, le nom exact et la casse du fichier, puis le dossier. Les chemins doivent rester cohérents même si le dépôt a changé d'ordinateur.

Référence utilisée pour ces opérations : [phpMyAdmin — Importer et exporter](https://docs.phpmyadmin.net/fr/latest/import_export.html). La présentation de l'interface peut varier avec la version installée.

## 7. Test réel du relais

Nous compléterons cette liste **après** mon essai de reprise.

- [ ] Nouveau dossier de code récupéré depuis GitHub ; base vide au départ.
- [ ] Configuration adaptée à l'ordinateur de reprise ; aucune donnée sensible visible dans GitHub Desktop.
- [ ] Deux projets et leurs bonnes images apparaissent ; renommer ne casse aucun chemin.
- [ ] Les réglages reviennent à la réouverture ; l'export PNG fonctionne.
- [ ] Ajout puis retrait confirmé d'une image de test réussis.
- [ ] Une configuration manquante ou une image absente permet un diagnostic compréhensible, sans secret affiché.
- [ ] Les précisions manquantes ont été ajoutées au guide.

Date : `[à compléter]` — personne ayant réalisé l'essai : `[nom]` — résultat et points restant ouverts : `[à compléter]`.

## 8. Laboratoire OVH facultatif

Décision prise ensemble : `[non prévu / capacités à vérifier / prêt pour une démonstration encadrée]`.

Avant d'envisager un transfert, je vérifierai les versions PHP, les extensions,
la disponibilité d'une base MySQL/MariaDB, les limites d'envoi, l'espace disque,
le stockage hors racine web et le transfert chiffré proposé par l'offre.
L'espace gratuit de 100 Mo ne prouve pas la présence d'une base SQL.
Je préparerai des réglages distants distincts et protégerai l'accès à tout le
laboratoire, formulaires d'upload compris, avant toute exposition.
Aucune mise en ligne n'est requise par ce guide.

[Retour à la mission 9](missions/09-transmission.md)

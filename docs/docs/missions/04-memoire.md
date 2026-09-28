# Mission 4 — Donne une mémoire à ton application

[← Mission 3](03-porte-entree.md) · [Le parcours](../parcours.md) · [Mission 5 →](05-projets.md)

## Objectif

Crée un premier projet depuis ton formulaire, puis retrouve-le après avoir fermé et rouvert la page. La base conserve les données de ton application.

Une **table** range des lignes de même nature. Une ligne de `projects` décrit un projet ; une ligne de `images` décrira une image. L’**identifiant** est un numéro stable qui distingue chaque ligne, même si deux projets portent le même nom.

Pour ce premier enregistrement, laisse le navigateur envoyer le formulaire à
PHP et charger la page de résultat. Tu ajouteras AJAX en mission 5, une fois ce
trajet compris. jQuery peut déjà aider les interactions sans intercepter cet envoi.

## À réaliser

1. Dessine les deux tables sur papier. Exemple : le projet 12, « Bruxelles Babel 26 », possède les images 31 et 32 ; chacune mémorise son rattachement au projet 12. C’est une relation « un projet, plusieurs images ».
2. Avec moi, crée une **nouvelle base locale de test** nommée `atelier_mosaique` dans phpMyAdmin accessible depuis WampServer. Sélectionne le serveur MySQL vérifié en mission 2, puis vérifie le nom de la base avant toute action. Choisis UTF-8 complet (`utf8mb4`). Si ce nom est déjà utilisé, choisis avec moi un autre nom et reporte-le dans la configuration locale ; ne réutilise pas une base contenant un autre travail.
3. Complète progressivement `database/schema.sql` avec tes choix. Commence par ce qu’il faut pour enregistrer un projet ; la table des images servira à la mission 6. Exécute ton schéma uniquement dans cette base de test. Si elle contient déjà du travail, fais un export avec moi avant une évolution de structure.
4. Dans l'Explorateur de Windows 11, active l'affichage des **extensions de noms de fichiers**. Copie `config/config.example.php` vers `config/config.local.php`, seulement si cette copie n'existe pas déjà ; vérifie le nom complet pour éviter une extension `.php` en double. Compare les valeurs du tableau ci-dessous à ton installation et adapte **la copie locale seulement**. Vérifie qu'elle n'apparaît pas dans les changements proposés par GitHub Desktop. Dans `src/`, prépare la connexion avec **PDO**, l'outil PHP qui dialogue ici avec MySQL ; conserve l'encodage `utf8mb4` dans la connexion.
5. Relie le formulaire au traitement PHP : réception en **POST**, vérification de la demande, validation du nom, puis enregistrement par une **requête préparée**. Elle sépare l’instruction SQL des valeurs saisies. N’insère pas le nom directement dans une chaîne SQL.
6. Relis le projet depuis la base pour constater sa présence. Au moment de l’afficher dans du HTML, utilise un échappement adapté, tel que `htmlspecialchars` pour le texte et les attributs entre guillemets. Le nom saisi doit rester du texte.

### Configure ton application pour WampServer

Le modèle correspond à un exercice local avec MySQL dans WampServer :

| Clé du modèle | Valeur proposée | À vérifier sur ton poste |
| --- | --- | --- |
| `host` | `127.0.0.1` | Le serveur SQL tourne sur ton ordinateur. |
| `port` | `3306` | Relève le port MySQL dans WampServer ; adapte-le s'il diffère. |
| `name` | `atelier_mosaique` | Utilise exactement le nom de la base créée à l'étape 2. |
| `charset` | `utf8mb4` | Garde cet encodage pour les accents et les autres caractères. |
| `user` | `root` | Compte initial local de WampServer ; conserve ton compte existant si l'installation a été configurée. |
| `password` | `''` (chaîne vide) | Aucun mot de passe à l'installation initiale ; renseigne celui déjà défini s'il existe. |

Ces accès initiaux sont documentés dans les [prérequis WampServer cités en mission 2](02-demarrage.md#pour-explorer).
Ils concernent le poste local de l'exercice. Nous définirons d'autres accès si
nous préparons un hébergement.

`config.example.php` est un modèle pour **ton application**, pas un fichier de
réglage de WampServer. Copier ce fichier ne crée ni base ni connexion : ton code
dans `src/` devra charger `config.local.php` et utiliser les valeurs du tableau
retourné pour ouvrir la connexion PDO. Garde ce fichier hors de `public/`.

### Quelles informations garder ?

| Table | Informations à prévoir | Questions pour choisir tes colonnes |
| --- | --- | --- |
| `projects` | Identité, nom, dates utiles, puis réglages de mosaïque | Comment garantir un identifiant unique ? Quelle longueur de nom accepter ? Comment représenter nombres, couleurs et transparence ? |
| `images` | Identité, projet associé, nom d’origine, nom stocké, format vérifié, taille, dimensions | Quelles valeurs sont obligatoires ? Comment empêcher une image d’être liée à un projet absent ? |

Le fichier image reste sur le disque. La base mémorise comment le retrouver. Nous choisirons ensemble les types et les contraintes ; le schéma fourni est volontairement à compléter.

### Dès le premier formulaire : une demande attendue

Une protection **CSRF** évite qu’une autre page fasse envoyer une action non souhaitée à ton atelier. `POST` seul ne suffit pas. Avec moi, ajoute un jeton aléatoire lié à la **session** — la continuité des visites de ce navigateur. Le formulaire le transmet et PHP le vérifie **avant toute écriture**. Jeton absent ou invalide : aucune création. Garde cette protection pour les prochaines actions qui modifient des données.

## Pistes pour avancer

<details>
<summary>Indice 1 — Vérifie chaque étape séparément</summary>

Vérifie d’abord la connexion, puis l’ajout d’un projet, puis sa relecture. Un problème de connexion ne se corrige pas en changeant ton HTML. Ne copie jamais une erreur contenant des accès secrets dans Git ou dans ton journal.

</details>

<details>
<summary>Indice 2 — Le navigateur propose, PHP vérifie</summary>

Teste le nom après retrait des espaces au début et à la fin. Vérifie qu’il s’agit bien d’un texte non vide et qu’il respecte la longueur choisie. Affiche l’erreur près du champ et conserve une saisie exploitable, échappée dans le HTML.

</details>

<details>
<summary>Indice 3 — Suis les données</summary>

Dans la documentation PDO, repère `prepare` et `execute` : où va la structure SQL, où va le nom ? Puis cherche comment rediriger vers une page de résultat après un ajout réussi : actualiser cette page ne devrait pas créer un deuxième projet.

</details>

## Pour explorer

- [PHP — Requêtes préparées](https://www.php.net/manual/fr/pdo.prepared-statements.php) : repère les paramètres qui représentent les valeurs et le rôle de `execute`.
- [SQLBolt — Exercices SQL](https://sqlbolt.com/) **(anglais, à explorer ensemble)** : essaie la leçon 1 pour lire des lignes, puis les leçons 13 et 16 pour comprendre l'ajout et la création d'une table ; les exemples sont à adapter à MySQL/MariaDB.
- [OWASP — Prévention CSRF](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html) **(anglais, à lire ensemble)** : limite-toi à « Synchronizer Token Pattern » pour comprendre le trajet du jeton.

## Pour valider la mission

- [ ] « Bruxelles Babel 26 — édition d'été » reste enregistré après fermeture et réouverture de la page ; les accents et apostrophes sont préservés.
- [ ] Actualiser la page de résultat n’ajoute pas un doublon.
- [ ] **Cas d’erreur :** un nom vide, composé d’espaces ou trop long est refusé côté PHP, sans nouvelle ligne en base.
- [ ] Le nom `<b>Bruxelles Babel 26</b>` est affiché comme du texte, sans devenir du gras, si tes règles de nom l’acceptent.
- [ ] Nous avons vérifié ensemble qu’un jeton absent ou modifié bloque réellement l’ajout.
- [ ] Tes accès locaux n’apparaissent pas dans les fichiers proposés au commit.

[← Mission 3](03-porte-entree.md) · [Le parcours](../parcours.md) · [Mission 5 →](05-projets.md)

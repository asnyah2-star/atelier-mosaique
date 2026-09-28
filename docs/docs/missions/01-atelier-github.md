# Mission 1 — Ouvre ton atelier et partage ton code

[Accueil](../../README.md) · [Carte du parcours](../parcours.md) · [Mission suivante →](02-demarrage.md)

## Objectif

Crée ton espace de code privé et montre-moi une première modification.
Tu as déjà un compte GitHub : nous allons l'utiliser pour préparer le dépôt de l'atelier.

Un **dépôt** garde tes fichiers et leur histoire. Le dépôt `atelier-mosaique`
contiendra le code. « Bruxelles Babel 26 » et « Bruxelles Babel 27 » serviront
d'exemples de projets de mosaïque créés **dans** ton application : chacun
regroupera ses images et ses réglages.

## À réaliser

### 1. Retrouve ton compte GitHub et vérifie ses accès

1. Connecte-toi à GitHub avec ton compte habituel et transmets-moi ton identifiant.
2. Vérifie que ton adresse e-mail est confirmée ; si GitHub demande encore une
   validation, suis le message reçu.
3. Dans ton profil, ouvre **Settings / Paramètres**, puis **Password and authentication**.
   Vérifie que la double authentification est active. Si elle l'est déjà, conserve
   ta méthode habituelle. Sinon, la ressource ci-dessous te guide pour l'activer :
   ton mot de passe sera complété par une deuxième preuve. Nous pouvons choisir
   ensemble une application d'authentification, puis suivre les étapes de GitHub.
4. Vérifie que tes codes de récupération sont conservés dans un endroit sûr,
   hors du dépôt. Ils servent si tu perds ton moyen de connexion. Ne partage ni
   mot de passe, ni QR code, ni codes de récupération.

### 2. Crée ton dépôt et donne-moi accès

1. Sur GitHub, utilise **+ → New repository**. Choisis ton compte comme propriétaire,
   le nom `atelier-mosaique`, la visibilité **Private** et l'ajout d'un **README**.
   Les autres options peuvent rester désactivées. Crée le dépôt et vérifie son badge privé.
2. Demande-moi mon identifiant : **`[IDENTIFIANT_GITHUB_ACCOMPAGNANT]`** est un champ à
   remplacer, pas un compte à inviter tel quel.
3. Dans les paramètres **du dépôt**, ouvre **Collaborators → Add people**.
   Recherche cet identifiant exact, vérifie avec moi le compte trouvé et envoie l'invitation.
4. Je vais accepter l'invitation depuis mon compte. Vérifions ensuite ensemble que je
   figure dans la liste des collaborateurs et que je peux ouvrir le dépôt privé. « Invitation envoyée »
   ne signifie pas encore « accès accepté ».

### 3. Installe GitHub Desktop et clone ton dépôt

1. Depuis le guide officiel GitHub Desktop ci-dessous, télécharge et installe
   l'application Windows. Connecte-la à ton compte GitHub via le navigateur.
   Vérifie dans **File → Options** le compte connecté, ton nom et ton adresse de commit.
2. Choisis **File → Clone repository**, puis sélectionne `atelier-mosaique`.
   Si WampServer est déjà installé, choisis le chemin final
   `C:\wamp64\www\atelier-mosaique`, en adaptant le dossier de WampServer si besoin.
   Sinon, utilise provisoirement `C:\Projets\atelier-mosaique` : nous déplacerons
   ce même dépôt dans `www` après l'installation de WampServer en mission 2.
   Le dossier choisi ne doit pas déjà contenir un autre projet.
   Cloner, c'est récupérer le dépôt avec son historique.
3. Ouvre ce dossier depuis GitHub Desktop avec **Repository → Show in Explorer**.
   C'est ton exemplaire de travail. Garde le nom de branche proposé par ton dépôt
   pour ce premier exercice ; les branches viendront plus tard.

Ce dépôt sera aussi ton dossier de travail avec WampServer, dans `www`.
GitHub Desktop suivra tes modifications, et WampServer permettra de voir leur
résultat dans le navigateur. Nous conserverons ce même dépôt et son historique
pour toutes les missions.

### 4. Ajoute le contenu du ZIP à ton dépôt

Je te transmets `atelier-mosaique-hasnia.zip`, qui contient le canevas technique
de départ. Je te remettrai les fiches de mission une à une après relecture.

1. Enregistre le ZIP dans **Téléchargements**, puis extrais-le à cet endroit.
   Ouvre le dossier `atelier-mosaique` obtenu après extraction : tu dois y voir
   `.gitignore` et les dossiers du canevas.
2. Depuis ce dossier extrait, copie `prototype/`, `public/`, `src/`,
   `config/`, `database/`, `storage/` et `.gitignore` directement dans ton dépôt
   cloné, ouvert avec **Show in Explorer**. Copie ces éléments, pas le
   dossier `atelier-mosaique` qui les contient.
3. Conserve le `README.md` créé avec ton dépôt GitHub : tu le modifieras à
   l'étape suivante.
4. Vérifie que tu obtiens directement `public\index.php` dans ton dossier
   `atelier-mosaique`. Dans GitHub Desktop, retrouve
   les fichiers ajoutés dans **Changes** de ce même dépôt.

Si un fichier à copier existe déjà dans ton dépôt, compare-le avec moi avant de
compléter ; refuse le remplacement automatique. Active l'affichage des extensions
dans l'Explorateur pour distinguer `.php` et `.php.txt`.

### 5. Fais ton premier aller-retour

1. Dans ton README, ajoute « Mon atelier est ouvert ! », puis enregistre le fichier.
2. Dans **Changes** de GitHub Desktop, relis les différences. Vérifie que seuls les
   fichiers du canevas et le README attendus apparaissent, jamais des secrets ou des photos.
3. Saisis le résumé « Ouvre mon atelier » et clique sur **Commit to…**.
   Un **commit** conserve un état du code dans l'historique avec un message qui décrit le changement.
4. Clique sur **Push origin**, puis ouvre le dépôt sur GitHub. Retrouve ta phrase
   et le commit. Demande-moi de les consulter depuis mon compte.

| Ton action | Ce qui change |
| --- | --- |
| Enregistrer dans l'éditeur | Le fichier sur ton ordinateur |
| Faire un commit | L'historique local du dépôt |
| Faire Push | Les commits disponibles sur GitHub pour nous deux |

Avant chaque séance : **Fetch origin**, puis **Pull origin** si proposé, avant de
modifier le code. Termine par une relecture, un commit ciblé et Push. Si un conflit
apparaît, garde les fichiers et demande-moi de l'aide ; ne force pas l'envoi.

## Pistes pour avancer

<details>
<summary>Indice 1 — GitHub ne montre pas ta phrase ?</summary>

Vérifie d'abord le fichier ouvert dans l'éditeur et le dossier choisi dans Desktop.
As-tu enregistré, fait un commit, puis envoyé ce commit ? Ce sont trois étapes.

</details>

<details>
<summary>Indice 2 — Le dépôt reste inaccessible après le partage ?</summary>

Un dépôt privé peut sembler absent pour un compte sans accès. Vérifie le compte
invité, l'acceptation et le compte avec lequel je suis connecté.

</details>

## Pour explorer

- [Configurer la double authentification](https://docs.github.com/fr/authentication/securing-your-account-with-two-factor-authentication-2fa/configuring-two-factor-authentication) : consulte les étapes si elle n'est pas encore active sur ton compte.
- [Inviter un collaborateur](https://docs.github.com/fr/repositories/managing-your-repositorys-settings-and-features/repository-access-and-collaboration/inviting-collaborators-to-a-personal-repository) : repère les paramètres du dépôt et le rôle de l'acceptation.
- [Bien démarrer avec GitHub Desktop](https://docs.github.com/fr/desktop/overview/getting-started-with-github-desktop) : sélectionne Windows et cherche installation, clonage et envoi des changements.

## Pour valider la mission

- [ ] Ton e-mail est vérifié, la double authentification active et tes codes conservés en privé.
- [ ] Le dépôt est privé et je peux lire la phrase envoyée depuis mon compte.
- [ ] Tu sais distinguer enregistrer, commit et Push.
- [ ] Cas d'erreur : si le dépôt ou la modification est introuvable, tu sais vérifier compte, chemin, invitation et envoi avant de demander de l'aide.

[← Accueil](../../README.md) · [Mission 2 — Allume ton atelier →](02-demarrage.md)

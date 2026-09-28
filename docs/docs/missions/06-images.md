# Mission 6 — Accueille tes premières images

[← Mission 5](05-projets.md) · [Le parcours](../parcours.md) · [Mission 7 →](07-mosaiques.md)

## Objectif

Ajoute une image à un projet, retrouve-la dans sa galerie, puis retire-la après confirmation. Une fois ce parcours fiable pour une image, étends-le à plusieurs fichiers.

Un **upload** est un envoi de fichier du navigateur vers le serveur. Il arrive d’abord dans un emplacement temporaire. Ton application décide ensuite si elle l’accepte et où le ranger.

## À réaliser

1. Sur la page d’un projet existant, prépare un formulaire pour **une seule image**. Il utilise POST, le jeton CSRF et l’encodage `multipart/form-data`. Indique près du champ les formats et limites que nous avons choisis. Commence par JPEG et PNG.
2. Côté PHP, contrôle le projet, le jeton, l’état de l’envoi, la taille et le contenu réel du fichier. Le **type MIME** décrit son format, par exemple `image/png` ; vérifie-le avec Fileinfo. Vérifie aussi que l’image peut être décodée et impose des limites de largeur, hauteur et nombre de pixels avant un traitement coûteux. Une extension `.jpg` ou le filtre du navigateur ne constitue pas une validation.
3. Génère un nom de stockage indépendant du nom d’origine, sans collision, avec une extension liée au format accepté. Range l’image dans le dossier de son projet sous `storage/projects/`. Complète la table `images` et enregistre les informations nécessaires pour la retrouver. Réutilise tes requêtes préparées.
4. Construis la galerie à partir des images **du projet ouvert**. Pour afficher une image à partir de son identifiant, prévois un point d’accès PHP qui reçoit cet identifiant : il retrouve la ligne, contrôle son rattachement au projet demandé et construit un chemin autorisé. Il renvoie les octets avec le type image vérifié, sans exécuter le fichier. Ne lui donne pas un chemin arbitraire à ouvrir.
5. Ajoute « Retirer cette image », puis une confirmation nommant l’image et le projet, avec « Annuler ». Une page de confirmation suffit. Seule la confirmation envoyée en POST avec un jeton valide déclenche le retrait. Recontrôle côté serveur le lien image/projet et le chemin avant de toucher au fichier ou à la ligne.
6. Une fois le parcours fiable, autorise plusieurs fichiers. Fixe une limite de nombre et donne un bilan par fichier : acceptés, refusés et raison du refus. Un échec ne doit pas être annoncé comme un succès global.
7. Ajoute ensuite l’envoi AJAX avec jQuery, en passant par `api.js` comme à la mission 5. Utilise **`FormData`**, l’objet du navigateur qui transporte les champs et les fichiers, avec l’identifiant du projet et le jeton CSRF. Conserve les contrôles PHP et le bilan par fichier ; `app.js` s’occupe de l’attente et de la mise à jour de la galerie.

### À examiner ensemble : la cohérence entre fichiers et base

**Que fait-on si le fichier est enregistré mais que la base refuse l’ajout ?** Et si le retrait du fichier échoue ? Dessine les étapes et indique comment retrouver un état cohérent. Une transaction SQL peut annuler des changements en base ; elle n’annule pas toute seule une écriture sur le disque. Décide du nettoyage nécessaire et d’un message honnête, sans toucher aux autres images.

## Pistes pour avancer

<details>
<summary>Indice 1 — Observe les quatre étapes</summary>

Formulaire → fichier temporaire reçu → fichier accepté et stocké → ligne associée en base. Repère où survient l’erreur. Dans PHP, cherche les codes `UPLOAD_ERR_*` et le rôle de `move_uploaded_file`. N’affiche pas de chemin interne dans les messages destinés à l’utilisatrice.

Pour AJAX, `serialize()` ne transmet pas les fichiers : utilise `FormData`. Reprends la [ressource jQuery de la mission 5](05-projets.md#pour-explorer) et vérifie les données réellement envoyées dans l’onglet Réseau.

</details>

<details>
<summary>Indice 2 — Il y a plusieurs plafonds</summary>

Ta limite applicative s’ajoute à celles de PHP : `upload_max_filesize` pour un fichier, `post_max_size` pour l’envoi total, `max_file_uploads` pour le nombre. Si l’envoi total dépasse la limite PHP, les données attendues peuvent manquer entièrement. Prévois ce cas et examine avec moi le comportement attendu ; ne relève pas toutes les limites pour faire disparaître l’erreur.

Relève ces valeurs dans **phpinfo()**, depuis l'accueil WampServer comme en
mission 2 : ce sont les réglages du PHP utilisé par Apache. Si nous devons en
ajuster un, ouvre **PHP → php.ini** dans le menu WampServer et repère-le avec
moi. Après enregistrement, redémarre les services dans WampServer, puis vérifie
la valeur dans phpinfo(). Note les limites retenues dans le guide de transmission.

</details>

<details>
<summary>Indice 3 — Une image ne choisit pas son adresse</summary>

Le nom d’origine sert à informer, avec échappement HTML. Le nom généré sert au stockage. L’identifiant sert à demander l’image. Dans la réponse image, étudie aussi `X-Content-Type-Options: nosniff`. N’utilise jamais `include` pour servir un upload : il faut transmettre un fichier image, pas charger du code PHP.

</details>

## Pour explorer

- [PHP — Gestion des chargements de fichiers](https://www.php.net/manual/fr/features.file-upload.php) : cherche l’envoi par formulaire, les erreurs et les limites de configuration ; adapte les idées, ne copie pas un upload complet.
- [PHP — `finfo_file`](https://www.php.net/manual/fr/function.finfo-file.php) : repère comment examiner le type MIME du fichier reçu.
- [OWASP — File Upload](https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html) **(anglais, à lire ensemble)** : regarde les parties sur les noms de fichiers, les formats acceptés et la vérification du contenu.

## Pour valider la mission

- [ ] Une image valide apparaît dans la bonne galerie ; sa légende et son alternative textuelle permettent de la reconnaître.
- [ ] **Cas d’erreur :** un fichier non autorisé, un faux JPEG et un fichier trop volumineux sont refusés avec une explication utile.
- [ ] Deux fichiers ayant le même nom d’origine coexistent sans écrasement.
- [ ] Un projet inexistant n’accepte aucune image. Changer l’identifiant du projet dans une demande d’affichage ou de retrait ne permet pas d’agir sur l’image d’un autre projet.
- [ ] La galerie du projet A ne montre pas les images du projet B.
- [ ] Annuler le retrait conserve l’image ; confirmer la retire de la galerie et du stockage prévu. Tu as essayé ce test avec une image de démonstration.
- [ ] La galerie utilise ton point d'accès PHP par identifiant ; celui-ci vérifie le fichier et son rattachement au projet demandé.
- [ ] Les erreurs d’envoi multiple et le jeton CSRF invalide ne produisent pas de faux succès. Les fichiers importés n’apparaissent pas dans les changements Git.
- [ ] Avec AJAX, les fichiers arrivent bien côté PHP ; un double clic ne lance pas deux envois et une erreur permet de réessayer sans perdre son message d’explication.

[← Mission 5](05-projets.md) · [Le parcours](../parcours.md) · [Mission 7 →](07-mosaiques.md)

# Mission 9 — notes pour reprendre tranquillement

Tu peux faire une pause : les étapes déjà faites sont enregistrées. Rien ne t'oblige à continuer maintenant.

## Les noms à distinguer

- **Dossier de l'application** : `C:\\wamp64\\www\\atelier-mosaique-reprise`. C'est une copie du code servie par Wamp.
- **Adresse locale** : `http://localhost/atelier-mosaique-reprise/public/`. Elle ouvre cette copie dans le navigateur.
- **Base de données** : `atelier_mosaique_demo`. Elle se trouve dans MySQL et apparaît dans phpMyAdmin. Ce n'est pas un dossier Windows.
- **Fichier d'export** : `C:\\wamp64\\www\\atelier-mosaique\\database\\demo.sql`. Il contient la structure SQL et les données fictives exportées. Ce n'est ni la base en fonctionnement ni le dossier de l'application.
- **Fichiers image** : ils restent sur le disque dans `storage\\projects\\<identifiant du projet>`. Le SQL conserve leurs informations et leur nom de stockage, pas les pixels de l'image.

Les tirets dans `atelier-mosaique-reprise` font partie du nom du dossier. Les tirets bas dans `atelier_mosaique_demo` font partie du nom de la base.

## Ce qui a été fait et vérifié

1. La base vide `atelier_mosaique_demo` a été créée dans phpMyAdmin.
2. `database/schema.sql` y a créé les tables `projects` et `images`.
3. Trois projets fictifs ont été ajoutés. La base de démonstration contient maintenant trois projets et une image.
4. La configuration locale de la copie `atelier-mosaique-reprise` pointe vers `atelier_mosaique_demo`.
5. La page locale répond et affiche les trois projets. Une image a été ajoutée depuis l'application.
6. L'image a été vérifiée dans la table `images` et comme fichier sur le disque, dans le stockage de la copie de test.
7. Un nouvel export a été fait après l'ajout de l'image. Il a remplacé `database/demo.sql` dans le projet principal. Le fichier téléchargé `atelier_mosaique_demo.sql` est resté dans Téléchargements.

La sauvegarde existante `C:\\Users\\asnya\\Downloads\\atelier_mosaique.sql` n'a pas été modifiée ni utilisée pour la démo.

## Ce qu'il restera à faire

- Confirmer que l'image de test peut être partagée. La mission demande des images autorisées à être partagées.
- Ajouter quelques images de démonstration si nécessaire, puis refaire `demo.sql` après les ajouts.
- Préparer avec `demo.sql` les fichiers image correspondants. Une importation SQL seule ne copie pas les images sur le disque. Pour chaque image, il faut conserver son fichier dans le dossier `storage/projects/<identifiant>` attendu par la base.
- Refaire une installation d'essai à partir d'un dossier de code vierge, de la base et des images de démonstration, puis noter les étapes dans le guide de transmission.
- Vérifier avant tout partage que les données, images et configuration privée ne sont pas envoyées sur GitHub.

La mission 9 n'est donc pas encore terminée, mais la base et le premier test de démonstration sont en place. Les bases `atelier_mosaique` et `atelier_mosaique_reprise` restent distinctes de `atelier_mosaique_demo`.

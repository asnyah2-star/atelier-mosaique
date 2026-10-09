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
3. Trois projets fictifs ont été ajoutés. La base de démonstration contient maintenant trois projets et trois images : deux pour le projet 1 et une pour le projet 2.
4. La configuration locale de la copie `atelier-mosaique-reprise` pointe vers `atelier_mosaique_demo`.
5. La page locale répond et affiche les trois projets. Une image a été ajoutée depuis l'application.
6. Les trois images ont été vérifiées dans la table `images` et leurs fichiers existent dans le stockage de la copie de test : deux sous `storage/projects/1` et une sous `storage/projects/2`.
7. Un nouvel export a été fait après l'ajout des trois images. Il a remplacé `database/demo.sql` dans le projet principal. Le fichier téléchargé `atelier_mosaique_demo(1).sql` est resté dans Téléchargements et correspond au fichier `database/demo.sql`.
8. Une archive distincte, `storage/projects/mission-9-demo-images.zip`, a été créée avec exactement les trois images référencées par l'export. Elle conserve les chemins `storage/projects/1` et `storage/projects/2` et est exclue de Git.
9. L'export a été importé dans la base vide `atelier_mosaique_demo_verification` : elle contient deux tables, trois projets et trois images. L'application a ensuite affiché les deux images du projet 1 et l'image du projet 2.
10. Les trois images de l'archive ont été comparées aux fichiers utilisés par l'application ; leurs contenus sont identiques.
11. L'export PNG du premier projet de démonstration a été déclenché avec succès ; le fichier est apparu dans les téléchargements.

La sauvegarde existante `C:\\Users\\asnya\\Downloads\\atelier_mosaique.sql` n'a pas été modifiée ni utilisée pour la démo.

## Ce qu'il restera à faire

- Décompresser `mission-9-demo-images.zip` dans une nouvelle copie de l'application pour tester l'installation des fichiers image depuis l'archive elle-même.
- Après la vérification, la configuration `C:\\wamp64\\www\\atelier-mosaique-reprise\\config\\config.local.php` a été remise sur `atelier_mosaique_demo`. La base `atelier_mosaique_demo_verification` reste disponible pour relire le résultat de l'import.
- Compléter le guide de transmission et tester une copie du code récupérée depuis le dépôt GitHub.
- Préparer avec `demo.sql` les fichiers image correspondants. Une importation SQL seule ne copie pas les images sur le disque. Pour chaque image, il faut conserver son fichier dans le dossier `storage/projects/<identifiant>` attendu par la base.
- Refaire une installation d'essai à partir d'un dossier de code vierge, de la base et des images de démonstration, puis noter les étapes dans le guide de transmission.
- Vérifier avant tout partage que les données, images et configuration privée ne sont pas envoyées sur GitHub.

La mission 9 n'est donc pas encore terminée, mais la base et le premier test de démonstration sont en place. Les bases `atelier_mosaique` et `atelier_mosaique_reprise` restent distinctes de `atelier_mosaique_demo`.

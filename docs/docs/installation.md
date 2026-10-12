# Installation locale sur un autre ordinateur

Procédure préparée le 12 octobre 2026 ; la reprise complète sur une autre machine reste à consigner. Référence relevée dans le guide de transmission : Windows 11, WampServer 3.4.0, PHP 8.3.28, MySQL 8.4.7.

## Préparer le code et la configuration

1. Installer WampServer et démarrer Apache et MySQL.
2. Récupérer le dépôt dans `C:\wamp64\www\atelier-mosaique`, ou adapter le chemin à la machine. Les sorties Electron et `node_modules` ne sont pas nécessaires pour la version web.
3. Vérifier les extensions PHP PDO MySQL, GD, Fileinfo et mbstring. Pour les images jusqu’à 5 Mio, adapter `upload_max_filesize` et `post_max_size`, en tenant compte des envois multiples.
4. Copier `config/config.example.php` vers `config/config.local.php` sans écraser une configuration existante. Renseigner `host`, `port`, `name`, `user` et `password` avec les accès propres à la machine. `src/connexion.php` charge cette configuration.

## Installer la base et les images

Créer une base vide en `utf8mb4` dans phpMyAdmin, par exemple `atelier_mosaique`, et reporter son nom dans la configuration. Choisir une seule voie :

- Sans données : importer `database/schema.sql`, puis créer des projets depuis l’application.
- Démonstration : importer `database/demo.sql`, qui contient déjà structure et données. Ne pas importer aussi le schéma. Obtenir séparément l’archive d’images autorisées.

La migration `database/migrations/2026-10-07-add-project-archive.sql` concerne uniquement une ancienne base sans archivage. Le schéma actuel contient déjà cette colonne.

Décompresser `mission-9-demo-images.zip` dans un dossier temporaire, puis replacer ses images dans `storage/projects/<id>/<nom_stockage>`. Vérifier les chemins pour éviter un dossier `storage/projects/` imbriqué deux fois. Conserver les identifiants et noms exacts du SQL ; ne pas recréer les projets à la main après l’import.

La démonstration attend trois projets et trois images : deux pour le projet 1, une pour le projet 2, aucune pour le projet 3. PHP doit pouvoir lire ces fichiers et écrire dans `storage/projects/`.

## Lancer

Ouvrir PowerShell à la racine du projet et lancer le PHP de Wamp en exposant uniquement `public/` :

```powershell
& 'C:\wamp64\bin\php\php8.3.28\php.exe' -S 127.0.0.1:8080 -t public
```

Adapter le chemin à la version installée. Garder la console ouverte et ouvrir `http://127.0.0.1:8080/`. MySQL doit rester démarré. Ce serveur PHP sert à la démonstration locale ; Apache permet notamment d’ouvrir phpMyAdmin.

L’adresse historique de développement est `http://localhost/atelier-mosaique/public/`, servie par Apache. La commande ci-dessus limite la racine servie à `public/`.

Electron est facultatif et attend par défaut l’adresse historique Apache. Pour le serveur sur le port 8080, définir `ATELIER_MOSAIQUE_URL` à `http://127.0.0.1:8080/` avant de lancer Electron. Il dépend toujours de PHP, MySQL, la configuration et les données.

## Recette après installation

- [ ] Les trois projets et les trois images de démonstration apparaissent.
- [ ] Le renommage conserve les images du projet.
- [ ] Les réglages reviennent après fermeture et réouverture du projet.
- [ ] La mosaïque se génère et le PNG téléchargé s’ouvre.
- [ ] Une image de test peut être ajoutée puis retirée avec confirmation.
- [ ] Un projet de test peut être archivé et restauré.
- [ ] Une autre personne peut suivre le guide sans étape implicite.

Cocher après observation et noter machine, versions, date, commit et résultats dans le guide de transmission.

## Diagnostic

Connexion impossible : vérifier MySQL, le port, les accès et le nom de base. Tables absentes : vérifier où le SQL a été importé. Images manquantes : comparer dossiers et noms des fichiers au SQL. Upload refusé : vérifier format, dimensions, taille, limites PHP, extensions et droits d’écriture. Page inaccessible : vérifier la console du serveur et la disponibilité du port 8080.

Transmettre le code, le SQL fictif et les images autorisées ; conserver secrets et données réelles hors du paquet de livraison.

# Mission 9 — Prépare la transmission

[← Mission 8](08-recette.md) · [Le parcours](../parcours.md)

## Objectif

Je vais installer ton application sur un autre ordinateur en suivant ton guide. L’objectif est de retrouver un atelier fonctionnel, avec les projets et les images de démonstration que tu auras préparés.

## À réaliser

1. Complète le [guide de transmission](../transmission.md) avec les versions testées, le dossier du dépôt dans `www`, les adresses localhost de l'application et du prototype, le schéma SQL final et les limites connues. Remplace les champs à compléter au fil de ton travail. Ne mets aucun secret dans ce document.
2. Prépare un jeu de démonstration non sensible : deux projets fictifs et quelques images autorisées à être partagées. Note leurs identifiants, leurs noms de stockage et les résultats attendus.
3. Vérifie les fichiers proposés dans GitHub Desktop : code, documentation, configuration d'exemple et schéma SQL sont partageables. La configuration réelle, les photos importées, les caches, les exports PNG et les dumps réels restent hors de Git. Un **dump** est un export de structure et/ou de données d'une base.
4. Fais un commit puis un push des fichiers utiles. Transmets séparément l'archive de démonstration selon le guide : un export SQL de test et les fichiers image correspondants. Ne copie jamais le dossier interne du serveur MySQL en fonctionnement.
5. Laisse-moi cloner le dépôt dans un nouveau dossier et suivre l'installation avec une base vide. Note chaque précision manquante, corrige le guide et fais reprendre l'étape concernée. Si l’import échoue, relève le message et identifie l’étape en cause avant de réessayer.

## Pistes pour avancer

<details>
<summary>Indice 1 — Code, base et images suivent des chemins distincts</summary>

Git transporte les fichiers suivis. La base en cours d'utilisation n'est pas dans le dépôt. Les images exclues non plus. Le guide distingue donc le code récupéré par Git, le SQL importé et les images replacées sur disque.

Sur un autre poste WampServer, le dépôt sera cloné dans `www`. Les adresses
localhost reprendront le nom de ce dossier, comme en mission 2. Git transmet le
code ; WampServer et les accès MySQL se préparent sur l'ordinateur de reprise.

</details>

<details>
<summary>Indice 2 — Conserve les liens entre les données et les fichiers</summary>

Si le SQL de test décrit le projet 12 et l'image `photo_ab12.jpg`, l'archive doit contenir exactement le fichier attendu dans `storage/projects/12/`. Ne recrée pas les projets à la main après l'import : de nouveaux identifiants rompraient les liens. Évite les chemins absolus propres à ton ordinateur.

</details>

<details>
<summary>Indice 3 — Rends chaque étape reproductible</summary>

« Configurer la base » ne suffit pas. Indique le fichier exemple à copier, où compléter les valeurs locales, quel SQL importer dans quelle base et quelle page ouvrir. Le mot de passe se transmet par un canal convenu ensemble, jamais par un commit.

</details>

## Pour explorer

- [GitHub Desktop — Cloner un dépôt](https://docs.github.com/fr/desktop/adding-and-cloning-repositories/cloning-and-forking-repositories-from-github-desktop) : retrouve comment repartir du dépôt privé sur une autre machine.
- [phpMyAdmin — Importer et exporter](https://docs.phpmyadmin.net/fr/latest/import_export.html) : regarde l'import SQL dans la base sélectionnée et l'export. Les libellés peuvent varier selon la version installée.
- [OVHcloud — Connexion à l'espace de stockage](https://docs.ovhcloud.com/fr/guides/web-cloud/web-hosting/ftp-connection) : uniquement pour l'option ci-dessous, nous vérifierons les moyens de transfert proposés par mon offre.

## Pour valider la mission

- [ ] Je récupère le code, configure ma copie et ouvre réellement l'application sur une installation vierge.
- [ ] Les projets, leurs images et leurs réglages correspondent au jeu de démonstration ; un PNG s'exporte.
- [ ] Une configuration absente ou une image manquante est identifiée avec un message utile ; le guide indique quoi vérifier sans afficher de secret.
- [ ] Aucun secret, dump réel ou upload n'a été envoyé sur GitHub ; la date et les résultats du test de reprise figurent dans le guide.

## Option — Montrer une démo sur OVH

Nous préparerons cette option après la reprise locale réussie. Je vérifierai la présence de PHP 8.x, GD, PDO MySQL, Fileinfo et MySQL/MariaDB, ainsi que les limites d'upload, l'espace disque et la possibilité de n'exposer que `public/`. Ne suppose pas qu'un espace gratuit de 100 Mo inclut SQL. Je choisirai un transfert chiffré disponible sur mon offre et protégerai l'accès au laboratoire avant toute exposition, upload compris. Les réglages distants restent séparés des réglages locaux. Si une capacité manque, garde la démonstration en local. Le laboratoire ne remplace ni Git ni ton poste de travail ; aucune publication n'est nécessaire pour réussir cette mission.

[← Mission 8](08-recette.md) · [Retour au parcours →](../parcours.md)

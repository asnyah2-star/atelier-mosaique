# Ton journal

Quelques phrases suffisent pour garder une trace du travail et préparer notre
prochain échange. Actualise cette page à la fin de ta séance.

## Ce que j'ai fait

Le 5 octobre 2026, j'ai refait deux exports PNG sur un projet : un avec un
fond coloré et une marge de 0 px, puis un avec un fond transparent après avoir
modifié la largeur et la hauteur. Au premier essai, j'ai exporté sans régénérer
l'aperçu : le PNG est resté en 1200 × 700. J'ai refait l'essai en cliquant sur
« Générer l'aperçu » avant l'export ; les nouvelles dimensions ont alors été
prises en compte. La vérification avec une marge supérieure à 0 px reste à
faire. D'après notre vérification antérieure, les deux projets de test
affichaient chacun uniquement leurs propres images ; ce résultat a été
confirmé de mémoire pendant la reprise de la recette. Le renommage avec
accents et apostrophe, puis le retour au nom initial, a également été confirmé
comme réussi. Le retrait d'image avec annulation puis confirmation fonctionne
bien. Pour rendre les actions importantes plus visibles, une légère pulsation
a été ajoutée au bouton « Générer l'aperçu » et au bouton de confirmation du
retrait ; elle est désactivée lorsque la préférence de réduction des
animations est activée. La demande de confirmation du retrait s'affiche
maintenant à côté de la photo concernée et du bouton « Retirer cette image ».
Après le clic sur « Retirer cette image », la page revient sur la vignette
concernée et met le focus sur « Confirmer le retrait » ; le premier bouton est
remplacé pendant la confirmation.
Le lien « Retour à l'accueil » a été placé en haut de la page du projet et un
bouton « Retour en haut » a été ajouté en bas, avec défilement doux sauf si la
préférence de réduction des animations est activée. Les images de la galerie
s'affichent maintenant dans une boîte limitée à 18 rem de large et 16 rem de
haut ; la page charge une miniature, tandis que l'original reste conservé.
Après la sélection d'un ou plusieurs fichiers, le bouton « Envoyer les images »
attire maintenant l'attention avec la même légère pulsation ; elle s'arrête
pendant l'envoi et reprend en cas d'échec tant que les fichiers restent choisis.
Le style général a été harmonisé dans l'unique feuille `style.css` : fond ivoire,
cartes blanches, texte sombre et actions vertes ; l'aperçu mosaïque conserve son
espace anthracite.
Un renommage AJAX a fonctionné avec WampServer en marche. Le test de réponse
à une panne a ensuite été commencé page déjà chargée : avec WampServer arrêté,
le message « Le renommage a échoué. Réessaie. » apparaît sans réponse technique
brute et le bouton redevient cliquable. Le renommage ne peut pas aboutir tant
que les services restent arrêtés ; après redémarrage, le renommage a réussi.
Pendant l'essai du double-clic d'envoi, une image a été acceptée une fois et le
bilan indiquait 1 acceptée, 0 refusée. Le navigateur a brièvement affiché
« Veuillez sélectionner au moins un fichier » après l'effacement du champ
obligatoire. Le bouton d'envoi est maintenant désactivé quand aucun fichier
n'est sélectionné. Le double-clic a été refait : une seule image est acceptée
et le message du navigateur ne réapparaît plus.
Le contrôle d'affichage HTML a réussi : le nom `<b>Essai</b>` apparaît tel
quel, sans balise interprétée. Une relecture du code confirme l'emploi de
requêtes préparées pour les opérations SQL avec des valeurs.
Le test CSRF a également réussi : une requête AJAX de renommage avec un faux
jeton a reçu une réponse HTTP 403 (« Jeton de sécurité invalide. »). La page du
projet est restée accessible après le test et le nom du projet n'a pas changé.
Le 6 octobre, une miniature du projet 5 a été demandée avec l'identifiant du
projet 4. Le serveur a répondu « Image introuvable dans ce projet. » ; aucune
donnée n'a été modifiée. Le test suivant a remplacé l'identifiant de miniature
par `../../src/connexion.php` ; le serveur a répondu « Image introuvable. » et
n'a révélé aucun fichier.
J'ai aussi pu expliquer le souci des dimensions PNG : après modification de la
largeur ou de la hauteur, il faut cliquer sur « Générer l'aperçu » avant
l'export pour appliquer les nouveaux réglages.
Le 6 octobre, j'ai confirmé que la navigation avec Tab fonctionne. Une mosaïque
avec une seule image s'affiche correctement. Le fichier `smoka.png` a été refusé
avec le message « Le fichier dépasse la limite de 5 Mio. ». J'ai aussi confirmé
qu'un export avec une marge supérieure à 0 px fonctionne. Lors du retour dans
le projet, les anciens réglages sont revenus et aucun message de confirmation
n'était visible. La revue du code a révélé que le bouton de sauvegarde cherchait
un champ `project_id` absent du formulaire. Le formulaire contient `id` ; le
JavaScript utilisait donc un identifiant inexistant et n'envoyait pas la requête.
Le code a été corrigé pour lire cet identifiant ; le test de persistance doit
être refait dans le navigateur. Restent aussi à vérifier : fichier au mauvais
format, identifiant absent, projet vide et autres touches clavier/zoom.
Le message de réussite de l'enregistrement était trop bref pour être remarqué.
L'interface indique maintenant l'ordre conseillé — générer l'aperçu, puis
enregistrer — et laisse le message visible plus longtemps avant le rechargement.

Le 7 octobre, nous avons commencé l'archivage récupérable des projets : le bouton
« Archiver » masque un projet de la liste active sans supprimer sa base ni ses
images ; la liste « Projets archivés » propose de le restaurer. Une colonne
`archived_at` et une migration SQL sont nécessaires. La mise à jour de la base
locale a été appliquée ; le parcours archiver/restaurer reste à vérifier dans
le navigateur. Le lien entre chaque `<li>` et son projet (`data-id_projet`) est
conservé pour le ciblage CSS.
Le 7 octobre, Hasnia a confirmé que la demande de confirmation de l'archivage
s'affiche. La liste active et la liste d'archives utilisent maintenant trois
colonnes sur grand écran et une zone de défilement interne à hauteur fixe. Un
bouton de suppression définitive est proposé uniquement pour un projet archivé ;
il demande confirmation, supprime les lignes en base puis les fichiers d'images
connus. Hasnia a confirmé le message de suppression définitive le 7 octobre.
La page d'accueil est maintenant organisée en trois cartes de même hauteur,
centrées et alignées ; les listes défilent à l'intérieur. La nouvelle
disposition reste à vérifier aux différentes largeurs d'écran.
Le 7 octobre, les projets sont explicitement empilés en une seule colonne à
l'intérieur de chaque liste ; une séparation légère les distingue. La taille
de la police des boutons de l'accueil a été légèrement réduite.

## Ce qui me bloque

Aucun blocage particulier. Pour que l'export reflète une nouvelle largeur ou
hauteur, il faut cliquer sur « Générer l'aperçu » avant d'exporter ; sinon,
l'export conserve les dimensions de l'aperçu déjà affiché.

## Ma prochaine étape

Commencer la mission 9 : compléter le guide de transmission avec les informations
locales vérifiées, puis préparer un jeu de démonstration non sensible.

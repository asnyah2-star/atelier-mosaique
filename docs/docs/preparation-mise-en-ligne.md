# Atelier mosaïque — fiche de reprise avant mise en ligne

Date de rédaction : 17 octobre 2026.

## Situation

L’atelier mosaïque a été installé et livré. Les prochaines modifications seront préparées avec le responsable de stage avant la mise en ligne.

Cette fiche est une liste de travail, pas un audit : le code de l’atelier n’a pas été examiné ici. Les fonctionnalités, l’hébergement et les protections existantes restent à confirmer.

## Contexte du projet : reprise du 17 octobre 2026

La version Windows autonome 1.0.1 a ete livree et installee selon Hasnia. Le README distingue cette version Electron/PHP/SQLite de la version PHP/MySQL sous Wamp. Conserver la livraison et ses donnees separement de la future version web.

Le README annonce deja des validations PHP, des requetes PDO preparees, des jetons CSRF et des limites pour les images. Ces protections documentees restent a examiner et a tester avant publication ; cette fiche ne constitue pas un audit du code.

Le fonctionnement actuel est local, sans comptes utilisateurs. Avec le responsable, choisir le futur usage : demonstration avec donnees fictives, outil prive ou service avec plusieurs utilisateurs. Definir qui peut consulter, modifier, exporter et supprimer chaque projet et ses images.

Pour le web, configurer public/ comme seule racine HTTP. Garder config/, storage/, backups/, database/, les documents et les sources Electron hors de tout acces web direct. Verifier les autorisations dans image.php et dans chaque action sur les projets, notamment leur separation si plusieurs utilisateurs sont prevus.

Choisir la base de donnees pour le serveur et tester toute migration necessaire depuis la version autonome. Pour sauvegarder cette derniere, le README demande de fermer le logiciel puis de copier ensemble atelier.sqlite et projects/ depuis son dossier de donnees.

Mesurer le chargement de la galerie et des miniatures ainsi que la memoire utilisee par Canvas lors des gros exports PNG. Les limites annoncees sont de 5 Mio, 6 000 pixels par cote et 20 millions de pixels ; toute augmentation demande des essais.

Idees de fonctions a discuter : recherche et tri des projets, duplication de composition, modeles de reglages, progression des exports. Rien de cette liste ne constitue une demande validee.

Cette etape prepare seulement la reprise : aucune modification du code ni publication.

## 1. Préparer la reprise

- Retrouver la version livrée et identifier précisément le dossier ou dépôt de référence.
- Sauvegarder les fichiers et, si elle existe, la base de données. Vérifier que la restauration fonctionne.
- Lister les accès nécessaires sans inscrire de mots de passe dans cette fiche ou dans le dépôt.
- Prévoir un environnement de test séparé du site public.
- Avec le responsable, noter les modifications souhaitées, leur priorité et les critères permettant de les valider.

## 2. Sécuriser — priorité avant publication

À adapter aux fonctions réellement présentes :

- Utiliser une version de PHP maintenue et mettre à jour les dépendances.
- Valider les données côté serveur ; échapper les valeurs affichées selon leur contexte pour prévenir les injections de scripts.
- Pour une base de données, utiliser des requêtes préparées et un compte disposant des seuls droits nécessaires.
- Pour les espaces privés, vérifier les autorisations sur chaque action côté serveur ; stocker les mots de passe avec password_hash et les vérifier avec password_verify.
- Protéger les actions qui modifient les données contre les requêtes CSRF ; ne pas effectuer ces actions par de simples liens GET.
- Si des fichiers peuvent être envoyés, limiter taille et types autorisés, vérifier leur contenu, générer leurs noms et empêcher leur exécution.
- Si des sessions sont utilisées, configurer les cookies Secure sous HTTPS, HttpOnly et SameSite ; renouveler l’identifiant après connexion.
- Limiter les tentatives de connexion et les abus sur les formulaires selon les besoins.
- Garder les secrets hors du dossier public et du dépôt ; retirer sauvegardes, fichiers de test et informations de débogage de l’accès web.
- En production, journaliser les erreurs sans afficher les détails techniques aux visiteurs.
- Activer HTTPS et vérifier les réglages de sécurité avec l’hébergeur.
- Prévoir des sauvegardes régulières, leur durée de conservation et une procédure de restauration.

## 3. Optimiser et améliorer l’accès

- Mesurer les performances avant modification pour identifier les vraies lenteurs.
- Redimensionner et compresser les images ; charger progressivement celles qui sont hors écran.
- Supprimer les ressources inutilisées et vérifier les chemins des fichiers.
- Configurer le cache des ressources selon les possibilités de l’hébergement.
- Tester sur mobile, tablette et ordinateur, ainsi qu’avec une connexion lente.
- Vérifier la navigation au clavier, le focus visible, les contrastes, les titres, les textes alternatifs et les libellés des formulaires.

## 4. Ajouter des fonctions

Pour chaque demande, remplir avec le responsable :

| Fonction souhaitée | Besoin utilisateur | Priorité | Données et droits nécessaires | Critère de validation |
| --- | --- | --- | --- | --- |
| À définir | À définir | À définir | À définir | À définir |

Vérifier pour chaque ajout les erreurs possibles, les droits d’accès, l’utilisation sur mobile et les conséquences sur les données existantes.

## 5. Paternité et crédits

- Convenir avec le responsable du libellé, de l’emplacement et du lien éventuel du crédit.
- Proposition à faire valider : « Conception et développement : Hasnia Ghacham — réalisé dans le cadre d’un stage auprès de [structure]. » Adapter aux contributions réelles.
- Distinguer les crédits de réalisation, la propriété du contenu et les conditions de réutilisation du code ; confirmer ces points avec la structure et les documents du stage.
- Recenser les images, polices, bibliothèques et autres ressources utilisées ; conserver leurs licences et les attributions requises.
- Confirmer l’autorisation de présenter le projet dans le portfolio et de publier des captures sans données privées.
- Vérifier les mentions et informations de confidentialité applicables selon l’éditeur du site, les données collectées et les services intégrés.

## 6. Valider la mise en ligne

- Faire valider les contenus, crédits et nouvelles fonctions par le responsable.
- Tester les liens, formulaires, messages d’erreur et permissions des différents profils, s’ils existent.
- Confirmer le domaine, l’hébergement, la configuration PHP et la gestion des éventuels courriels.
- Préparer une sauvegarde et une procédure de retour à la version précédente.
- Après publication, vérifier HTTPS, pages, ressources et fonctions depuis l’adresse publique.
- Définir qui assure la maintenance, les mises à jour, les sauvegardes et le suivi des erreurs.

## Décisions à conserver

- Dossier ou dépôt de référence : à renseigner.
- Responsable de validation : à renseigner.
- Hébergement et domaine : à renseigner.
- Fonctions retenues : à renseigner.
- Crédit validé : à renseigner.
- Date de publication : à fixer après validation.


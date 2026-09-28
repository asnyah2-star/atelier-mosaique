# Mon atelier de mosaïques

Application web pédagogique construite progressivement à partir d'un prototype de mosaïques. Ce README sert de point de reprise ; il ne remplace pas la fiche de mission reçue ni le suivi avec l'accompagnant.

## Pour reprendre dans un nouveau chat

Joindre `contexte-chatgpt.md`, la fiche de mission relue qui est effectivement en cours et ce README. Indiquer l'étape actuelle, ce qui a été essayé et le résultat observé. Ne pas supposer qu'une mission est validée parce qu'elle est décrite dans le contexte.

## Choix techniques

- Windows 11 et WampServer ; PHP 8.x, MySQL avec PDO.
- Dépôt de travail prévu : `C:\wamp64\www\atelier-mosaique` (à confirmer sur le poste). Utiliser ce dépôt comme dossier de travail unique.
- Application : <http://localhost/atelier-mosaique/public/>
- Prototype de référence : <http://localhost/atelier-mosaique/prototype/>
- GitHub Desktop et le compte GitHub existant ; dépôt prévu privé.
- jQuery 4.0.0 locale pour les interactions d'interface. Le moteur de mosaïque et Canvas restent en JavaScript natif. Les appels AJAX centralisés dans `api.js` arrivent à la mission 5, après le formulaire PHP classique.
- Pas de framework, Docker, Node.js, npm, VirtualHost ni serveur PHP séparé. `public/` organise le code et les ressources ; ce n'est pas une racine HTTP isolée du reste du dépôt.

Le contexte général est disponible dans [`contexte-chatgpt.md`](contexte-chatgpt.md) et dans la copie rangée sous [`docs/docs/contexte-chatgpt.md`](docs/docs/contexte-chatgpt.md). Les documents de parcours sont dans [`docs/docs/`](docs/docs/). La fiche remise par l'accompagnant et ses précisions récentes priment en cas de différence.

## Documentation du parcours

- [Parcours des neuf missions](docs/docs/parcours.md)
- [Aide pour démarrer un échange avec ChatGPT](docs/docs/demarrer-chatgpt.md)
- [Mission 3 — Dessine la porte d'entrée](docs/docs/missions/03-porte-entree.md)
- [Journal de travail](docs/docs/journal.md)
- [Guide de transmission](docs/docs/transmission.md)

Les fiches des missions 1 à 9 sont présentes dans `docs/docs/missions/`. Leur présence ne prouve pas qu'elles ont toutes été remises. Le journal ne contient encore aucune note de séance et le guide de transmission est un canevas à compléter.

## Arborescence utile

```text
config/config.example.php    modèle de configuration, sans accès local
database/schema.sql          schéma de base à concevoir progressivement
prototype/                   référence à conserver intacte
public/index.php             point d'entrée de l'application
public/assets/css/style.css  styles de l'application
public/assets/js/            interactions puis appels AJAX
src/                         code PHP réutilisable à construire
storage/projects/            images rangées par identifiant de projet
```

La configuration locale `config/config.local.php`, les images de test, les uploads, les caches et les exports ne doivent pas être partagés dans Git. Ne mettre aucun mot de passe, clé ou autre secret dans ce README.

## État observé dans les fichiers

À la création de ce README, `public/index.php` contient le titre, un affichage de la version PHP et une première maquette avec deux projets fictifs et un formulaire. Le fichier conserve aussi des TODO indiquant que la page et le formulaire restent à construire. Le CSS contient les styles de base et des premiers styles de cartes et de formulaire. Le prototype est destiné à rester la référence intacte.

Cette inspection du code ne confirme pas que WampServer démarre, que les URLs fonctionnent, que PHP ou jQuery sont chargés dans le navigateur, ni que les critères de mission ont été vérifiés. La mission effectivement remise, les essais déjà réalisés et leur résultat sont à confirmer avec Hasnia et son accompagnant.

## État de reprise

- **Fiche disponible :** mission 3, « Dessine la porte d'entrée », dans `docs/docs/missions/03-porte-entree.md`.
- **Mission effectivement remise et étape en cours :** à confirmer avec Hasnia et son accompagnant. La fiche est dans le dossier, mais cela ne confirme pas qu'elle a été remise.
- **État des notes :** [`docs/docs/journal.md`](docs/docs/journal.md) est un modèle vierge. [`docs/docs/transmission.md`](docs/docs/transmission.md) est un guide à compléter ; il ne consigne pas d'environnement testé.
- **Code inspecté :** `public/index.php` a deux états de projets fictifs et un formulaire de départ ; il contient encore les TODO de la mission 3. Le CSS contient une première mise en page.
- **Vérifications observées :** lecture des fichiers et documents indiqués ici. Aucune validation dans le navigateur ou par l'accompagnant n'est consignée.
- **Blocage :** aucun blocage de fonctionnement n'est établi dans les documents consultés.
- **Prochaine action :** confirmer que la mission 3 est bien en cours et choisir un critère de sa fiche à réaliser ou vérifier.

## Notes de séance

Mettre à jour ce bloc après une séance, en séparant les observations des points encore à vérifier.

```text
Date :
Mission et étape :
Ce que j'ai fait (fichiers/notion) :
Test et résultat observé :
Ce qui reste à vérifier ou me bloque :
Ma prochaine petite étape :
```

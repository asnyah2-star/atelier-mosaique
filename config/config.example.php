<?php
declare(strict_types=1);

// Mission 4 — Windows 11 + WampServer, serveur MySQL local.
// Copie ce modèle vers config.local.php, sans écraser une copie existante.
// Vérifie le port et les accès de ton installation dans cette copie, exclue de Git.
// root sans mot de passe est le réglage initial local de WampServer ; conserve
// les accès existants si ton installation a déjà été configurée.
// Ces valeurs servent uniquement à l'exercice local, pas à un hébergement.
// Ton code PHP chargera ce tableau : WampServer ne le lit pas automatiquement.
// Ce modèle ne crée aucune base et n'ouvre aucune connexion.
return [
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'atelier_mosaique',
        'charset' => 'utf8mb4',
        'user' => 'root',
        'password' => '',
    ],
];

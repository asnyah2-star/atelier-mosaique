<?php
declare(strict_types=1);

$donnees = getenv('ATELIER_DATA_DIR') ?: dirname(__DIR__) . '/storage';
$base = $donnees . '/atelier.sqlite';
if (!is_file($base)) {
    http_response_code(503);
    exit('La base SQLite locale est absente.');
}
try {
    $pdo = new PDO('sqlite:' . $base, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    // SQLite doit activer les liens et suppressions en cascade à chaque connexion.
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');
} catch (PDOException $exception) {
    http_response_code(503);
    exit('La base SQLite locale ne peut pas être ouverte. Vérifie les droits du dossier de données.');
}

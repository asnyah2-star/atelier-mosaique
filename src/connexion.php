<?php
declare(strict_types=1);

$cheminConfig = __DIR__ . '/../config/config.local.php';
if (!is_file($cheminConfig)) {
    http_response_code(503);
    exit('Configuration locale absente : copie config/config.example.php vers config/config.local.php et renseigne les accès de ta base.');
}
$config = require $cheminConfig;
$db = $config['database'];

$dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";

try {
    $pdo = new PDO($dsn, $db['user'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $exception) {
    http_response_code(503);
    exit('Connexion à la base impossible : vérifie que MySQL est démarré et que la base, le port et les accès sont corrects dans config/config.local.php.');
}

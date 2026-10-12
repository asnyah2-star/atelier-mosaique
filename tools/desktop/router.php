<?php
declare(strict_types=1);

// Le lanceur donne un secret neuf à chaque démarrage ; aucune action sans lui.
$secret = getenv('ATELIER_SESSION_SECRET');
$recu = $_SERVER['HTTP_X_ATELIER_SESSION'] ?? '';
if (!is_string($secret) || strlen($secret) < 32 || !hash_equals($secret, $recu)) {
    http_response_code(403);
    exit('Accès réservé à la fenêtre de l’application.');
}
header('X-Atelier-Session: ' . $secret);
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; connect-src 'self'; font-src 'self'; object-src 'none'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
header('Referrer-Policy: no-referrer');
$chemin = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$racine = realpath(__DIR__ . '/public');
$cible = realpath($racine . ($chemin === '/' ? '/index.php' : $chemin));
if ($cible === false || !is_file($cible) || !str_starts_with($cible, $racine . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    exit('Ressource introuvable.');
}
return false;

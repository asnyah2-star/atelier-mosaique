<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli' || $argc !== 2) {
    exit(1);
}
$destination = $argv[1];
if (file_exists($destination)) {
    fwrite(STDERR, "La base existe déjà ; création refusée.\n");
    exit(1);
}
$pdo = new PDO('sqlite:' . $destination, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec(file_get_contents(dirname(__DIR__, 2) . '/database/schema.sqlite.sql'));
if ($pdo->query('PRAGMA integrity_check')->fetchColumn() !== 'ok'
    || (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn() !== 0
    || (int) $pdo->query('SELECT COUNT(*) FROM images')->fetchColumn() !== 0) {
    exit(1);
}
echo "Base SQLite vide créée et vérifiée.\n";

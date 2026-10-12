<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Exécution en ligne de commande uniquement.');
}

// Fermer les formulaires et arrêter les modifications pendant cette copie.
$racine = dirname(__DIR__);
$config = require $racine . '/config/config.local.php';
$db = $config['database'];
$pdo = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4", $db['user'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$pdo->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
$pdo->exec('SET TRANSACTION READ ONLY');
$pdo->beginTransaction();
$sql = "-- Sauvegarde locale : importer dans une base vide.\nSET NAMES utf8mb4;\n";
$lignes = [];
foreach (['projects', 'images'] as $table) {
    $creation = $pdo->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM)[1];
    $sql .= $creation . ";\n";
    $lignes[$table] = $pdo->query('SELECT * FROM `' . $table . '` ORDER BY id')->fetchAll();
    foreach ($lignes[$table] as $ligne) {
        $colonnes = implode(',', array_map(static fn($c) => '`' . $c . '`', array_keys($ligne)));
        $valeurs = implode(',', array_map(static fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), array_values($ligne)));
        $sql .= 'INSERT INTO `' . $table . '` (' . $colonnes . ') VALUES (' . $valeurs . ");\n";
    }
}
$pdo->commit();

$dossier = $racine . '/backups';
if (!is_dir($dossier) && !mkdir($dossier, 0700)) {
    throw new RuntimeException('Création du dossier impossible.');
}
$cheminZip = $dossier . '/atelier-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.zip';
$zip = new ZipArchive();
if ($zip->open($cheminZip, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
    throw new RuntimeException('Création du ZIP impossible.');
}
$empreintes = [];
$ajouter = static function (string $nom, string $contenu) use ($zip, &$empreintes): void {
    if (!$zip->addFromString($nom, $contenu)) {
        throw new RuntimeException('Ajout au ZIP impossible.');
    }
    $empreintes[$nom] = hash('sha256', $contenu);
};
$ajouter('database/sauvegarde.sql', $sql);

// Code courant, y compris les scripts SQLite non encore commités.
foreach (['public', 'src', 'tools', 'docs'] as $repertoire) {
    $parcours = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($racine . '/' . $repertoire, FilesystemIterator::SKIP_DOTS));
    foreach ($parcours as $fichier) {
        if ($fichier->isFile() && !$fichier->isLink()) {
            $nom = str_replace('\\', '/', substr($fichier->getPathname(), strlen($racine) + 1));
            $ajouter($nom, file_get_contents($fichier->getPathname()));
        }
    }
}
foreach (['README.md', '.gitignore', 'config/config.example.php', 'database/schema.sql', 'database/schema.sqlite.sql', 'database/demo.sql', 'database/migrations/2026-10-07-add-project-archive.sql', 'electron-test/main.cjs', 'electron-test/package.json', 'electron-test/package-lock.json', 'electron-test/forge.config.cjs', 'electron-test/.gitignore'] as $nom) {
    if (is_file($racine . '/' . $nom)) {
        $ajouter($nom, file_get_contents($racine . '/' . $nom));
    }
}
foreach ($lignes['images'] as $image) {
    if ((int) $image['project_id'] < 1 || !preg_match('/\A[a-f0-9]{32}\.(jpg|png)\z/D', $image['storage_name'])) {
        throw new RuntimeException('Nom image invalide.');
    }
    $nom = 'storage/projects/' . $image['project_id'] . '/' . $image['storage_name'];
    if (!is_file($racine . '/' . $nom)) {
        throw new RuntimeException('Image manquante.');
    }
    $ajouter($nom, file_get_contents($racine . '/' . $nom));
}
$ajouter('RESTAURATION.txt', "Sauvegarde du " . date('c') . "\nBase source : {$db['name']}\n" . count($lignes['projects']) . ' projets ; ' . count($lignes['images']) . " images.\nDécompresser dans un nouveau dossier. Préparer PHP/MySQL. Créer une base vide et importer database/sauvegarde.sql. Copier config/config.example.php vers config/config.local.php et renseigner les accès locaux. Conserver storage/projects avec les identifiants et noms de fichier d'origine. Suivre docs/docs/installation.md puis vérifier galerie, réglages et export.\nLa configuration secrète, Git, les binaires Electron et node_modules sont exclus. Le prototype historique n'est pas inclus ; il reste dans Git.\nCette archive est privée, non chiffrée.\n");
$zip->addFromString('SHA256.json', json_encode($empreintes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
if (!$zip->close()) {
    throw new RuntimeException('Fermeture du ZIP impossible.');
}
$verification = new ZipArchive();
if ($verification->open($cheminZip, ZipArchive::CHECKCONS) !== true) {
    throw new RuntimeException('Archive invalide.');
}
foreach ($empreintes as $nom => $empreinte) {
    $contenu = $verification->getFromName($nom);
    if ($contenu === false || hash('sha256', $contenu) !== $empreinte) {
        throw new RuntimeException('Vérification du contenu échouée.');
    }
}
$verification->close();
echo $cheminZip, PHP_EOL, count($lignes['projects']), ' projets ; ', count($lignes['images']), ' images ; ZIP vérifié (', filesize($cheminZip), ' octets).', PHP_EOL;

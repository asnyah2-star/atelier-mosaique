<?php
declare(strict_types=1);

// Étape de préparation uniquement : ne change pas la connexion de l'application.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script doit être exécuté en ligne de commande.');
}

$racine = dirname(__DIR__);
$destination = $racine . '/storage/desktop-test';
$sourceImages = dirname($racine) . '/atelier-mosaique-reprise/storage/projects';
if (file_exists($destination)) {
    exit("La destination existe déjà : aucune copie n'a été écrasée.\n");
}

try {
    $config = require $racine . '/config/config.local.php';
    $db = $config['database'];
    // La base source est fixée à la démonstration, jamais la base de travail.
    $source = new PDO(
        "mysql:host={$db['host']};port={$db['port']};dbname=atelier_mosaique_demo;charset=utf8mb4",
        $db['user'], $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $source->exec('SET TRANSACTION READ ONLY');
    $source->beginTransaction();
    $projets = $source->query('SELECT * FROM projects ORDER BY id')->fetchAll();
    $images = $source->query('SELECT * FROM images ORDER BY id')->fetchAll();
    $source->commit();

    foreach ($images as $image) {
        if ((int) $image['project_id'] < 1 || !preg_match('/\A[a-f0-9]{32}\.(jpg|png)\z/D', $image['storage_name'])) {
            throw new RuntimeException('Chemin image invalide.');
        }
        $fichier = $sourceImages . '/' . $image['project_id'] . '/' . $image['storage_name'];
        if (!is_file($fichier) || is_link($fichier)) {
            throw new RuntimeException('Image source manquante ou invalide.');
        }
    }
    if (!mkdir($destination, 0700, true)) {
        throw new RuntimeException('Création du dossier impossible.');
    }
    $sqlite = new PDO('sqlite:' . $destination . '/atelier.sqlite', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $sqlite->exec(file_get_contents($racine . '/database/schema.sqlite.sql'));
    $sqlite->beginTransaction();
    foreach (['projects' => $projets, 'images' => $images] as $table => $lignes) {
        foreach ($lignes as $ligne) {
            $colonnes = array_keys($ligne);
            foreach ($colonnes as $colonne) {
                if (!preg_match('/\A[a-z_]+\z/D', $colonne)) {
                    throw new RuntimeException('Colonne invalide.');
                }
            }
            $requete = $sqlite->prepare('INSERT INTO ' . $table . ' (' . implode(',', $colonnes) . ') VALUES (' . implode(',', array_fill(0, count($colonnes), '?')) . ')');
            $requete->execute(array_values($ligne));
        }
    }
    foreach ($images as $image) {
        $dossier = $destination . '/projects/' . $image['project_id'];
        if (!is_dir($dossier) && !mkdir($dossier, 0700, true)) {
            throw new RuntimeException('Création du dossier image impossible.');
        }
        $original = $sourceImages . '/' . $image['project_id'] . '/' . $image['storage_name'];
        $copie = $dossier . '/' . $image['storage_name'];
        if (!copy($original, $copie) || hash_file('sha256', $original) !== hash_file('sha256', $copie)) {
            throw new RuntimeException('Copie image non conforme.');
        }
    }
    if ($sqlite->query('PRAGMA foreign_key_check')->fetchAll() !== [] || $sqlite->query('PRAGMA integrity_check')->fetchColumn() !== 'ok') {
        throw new RuntimeException('Vérification SQLite échouée.');
    }
    $sqlite->commit();
    echo count($projets) . ' projets et ' . count($images) . " images copiés et vérifiés.\n";
    echo "Base locale : storage/desktop-test/atelier.sqlite\n";
} catch (Throwable $erreur) {
    if (isset($sqlite) && $sqlite->inTransaction()) {
        $sqlite->rollBack();
    }
    // Ne pas afficher les détails PDO susceptibles de révéler les accès locaux.
    fwrite(STDERR, "Préparation interrompue. Conserver la copie éventuelle pour diagnostic ; aucune source n'a été modifiée.\n");
    exit(1);
}

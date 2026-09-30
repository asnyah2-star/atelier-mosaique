<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/connexion.php';

$imageIdRecu = $_GET['id'] ?? null;
$projetIdRecu = $_GET['project_id'] ?? null;

if (
    !is_string($imageIdRecu)
    || !ctype_digit($imageIdRecu)
    || (int) $imageIdRecu < 1
    || !is_string($projetIdRecu)
    || !ctype_digit($projetIdRecu)
    || (int) $projetIdRecu < 1
) {
    http_response_code(404);
    exit('Image introuvable.');
}

$imageId = (int) $imageIdRecu;
$projetId = (int) $projetIdRecu;

$requete = $pdo->prepare(
    'SELECT storage_name, mime_type
     FROM images
     WHERE id = :image_id AND project_id = :project_id'
);
$requete->execute([
    'image_id' => $imageId,
    'project_id' => $projetId,
]);
$image = $requete->fetch();

if ($image === false) {
    http_response_code(404);
    exit('Image introuvable dans ce projet.');
}

$extensionsAcceptees = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];

if (
    !isset($extensionsAcceptees[$image['mime_type']])
    || !is_string($image['storage_name'])
    || !preg_match('/\A[a-f0-9]{32}\.(jpg|png)\z/D', $image['storage_name'])
) {
    http_response_code(404);
    exit('Image introuvable.');
}

$extensionAttendue = $extensionsAcceptees[$image['mime_type']];
$extensionEnregistree = pathinfo($image['storage_name'], PATHINFO_EXTENSION);

if ($extensionEnregistree !== $extensionAttendue) {
    http_response_code(404);
    exit('Image introuvable.');
}

$cheminImage = dirname(__DIR__) . '/storage/projects/' . $projetId . '/' . $image['storage_name'];

if (!is_file($cheminImage)) {
    http_response_code(404);
    exit('Image introuvable.');
}

$informationFichier = new finfo(FILEINFO_MIME_TYPE);
$typeMimeReel = $informationFichier->file($cheminImage);

if ($typeMimeReel !== $image['mime_type']) {
    http_response_code(404);
    exit('Image introuvable.');
}

header('Content-Type: ' . $typeMimeReel);
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . (string) filesize($cheminImage));
readfile($cheminImage);

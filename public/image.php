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
    'SELECT images.storage_name, images.mime_type
     FROM images
     INNER JOIN projects ON projects.id = images.project_id
     WHERE images.id = :image_id
       AND images.project_id = :project_id
       AND projects.archived_at IS NULL'
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
// produit une miniature JPEG à partir de cette URL, après avoir vérifié l’identifiant et l’appartenance au projet.
if (($_GET['thumb'] ?? '') === '1') {
    if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
        http_response_code(500);
        exit('Les miniatures ne sont pas disponibles.');
    }

    $maximum = max(50, min(4000, (int) ($_GET['max'] ?? 1200)));
    $details = @getimagesize($cheminImage);
    if (
        $details === false
        || $details[0] < 2
        || $details[1] < 2
        || $details[0] > 6000
        || $details[1] > 6000
        || $details[0] * $details[1] > 20_000_000
    ) {
        http_response_code(415);
        exit('Image non lisible.');
    }

    $imageSource = match ($typeMimeReel) {
        'image/jpeg' => @imagecreatefromjpeg($cheminImage),
        'image/png' => @imagecreatefrompng($cheminImage),
        default => false,
    };
    if ($imageSource === false) {
        http_response_code(415);
        exit('Image non lisible.');
    }

    [$largeurSource, $hauteurSource] = $details;
    $echelle = min(1.0, $maximum / max($largeurSource, $hauteurSource));
    $largeurMiniature = max(1, (int) round($largeurSource * $echelle));
    $hauteurMiniature = max(1, (int) round($hauteurSource * $echelle));

    $miniature = imagecreatetruecolor($largeurMiniature, $hauteurMiniature);
    $blanc = imagecolorallocate($miniature, 255, 255, 255);
    imagefill($miniature, 0, 0, $blanc);
    imagecopyresampled(
        $miniature,
        $imageSource,
        0,
        0,
        0,
        0,
        $largeurMiniature,
        $hauteurMiniature,
        $largeurSource,
        $hauteurSource
    );

    header('Content-Type: image/jpeg');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=604800, immutable');
    imageinterlace($miniature, true);
    imagejpeg($miniature, null, 85);
    imagedestroy($imageSource);
    imagedestroy($miniature);
    exit;
}

header('Content-Type: ' . $typeMimeReel);
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . (string) filesize($cheminImage));
readfile($cheminImage);

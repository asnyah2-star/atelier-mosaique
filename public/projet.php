<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../src/connexion.php';

$idRecu = $_GET['id'] ?? null;

if (!is_string($idRecu) || !ctype_digit($idRecu) || (int) $idRecu < 1) {
    http_response_code(404);
    exit('Projet introuvable. <a href="index.php">Retour à l’accueil</a>');
}

$id = (int) $idRecu;

$requete = $pdo->prepare(
    'SELECT id, name FROM projects WHERE id = :id'
);
$requete->execute(['id' => $id]);
$projet = $requete->fetch();

if ($projet === false) {
    http_response_code(404);
    exit('Projet introuvable. <a href="index.php">Retour à l’accueil</a>');
}

$erreur = '';
$messageImage = '';
$nom = $projet['name'];

$estAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

$repondreJson = static function (array $donnees, int $statut = 200): void {
    http_response_code($statut);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $tokenRecu = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenRecu)
        || !hash_equals($_SESSION['csrf_token'], $tokenRecu)
    ) {
        if ($estAjax) {
            $repondreJson(
                ['success' => false, 'message' => 'Jeton de sécurité invalide.'],
                403
            );
        }

        http_response_code(403);
        exit('Demande refusée : jeton de sécurité invalide.');
    }

    $idPostRecu = $_POST['project_id'] ?? null;

    if (
        !is_string($idPostRecu)
        || !ctype_digit($idPostRecu)
        || (int) $idPostRecu !== $id
    ) {
        if ($estAjax) {
            $repondreJson(
                ['success' => false, 'message' => 'Identifiant du projet invalide.'],
                400
            );
        }

        http_response_code(400);
        exit('Identifiant du projet invalide.');
    }
    if ($action === 'renommer') {
        $nomRecu = $_POST['nom'] ?? null;

        if (!is_string($nomRecu)) {
            $erreur = 'Le nom du projet doit être du texte.';
        } else {
            $nom = trim($nomRecu);

            if ($nom === '') {
                $erreur = 'Saisis un nom pour le projet.';
            } elseif (mb_strlen($nom, 'UTF-8') > 150) {
                $erreur = 'Le nom ne peut pas dépasser 150 caractères.';
            }
        }

        if ($erreur !== '' && $estAjax) {
            $repondreJson(
                ['success' => false, 'message' => $erreur],
                422
            );
        }

        if ($erreur === '') {
            $miseAJour = $pdo->prepare(
                'UPDATE projects SET name = :name WHERE id = :id'
            );
            $miseAJour->execute([
                'name' => $nom,
                'id' => $id,
            ]);

            if ($estAjax) {
                $repondreJson([
                    'success' => true,
                    'message' => 'Le projet a été renommé.',
                    'name' => $nom,
                ]);
            }

            header('Location: projet.php?id=' . $id);
            exit;
        }

// début du traitement de l’image
    } elseif ($action === 'envoyer_image') {
        $fichier = $_FILES['image'] ?? null;
        $tailleMaximale = 5 * 1024 * 1024; // 5 Mio (environ 5 Mo)
        $largeurMaximale = 6000;
        $hauteurMaximale = 6000;
        $pixelsMaximaux = 20_000_000;

        if (!is_array($fichier) || !isset($fichier['error'], $fichier['size'], $fichier['tmp_name'])) {
            $erreur = 'Choisis une image à envoyer.';
        } elseif ($fichier['error'] !== UPLOAD_ERR_OK) {
            $erreur = match ($fichier['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée de 5 Mio.',
                UPLOAD_ERR_NO_FILE => 'Choisis une image à envoyer.',
                UPLOAD_ERR_PARTIAL => 'L’envoi du fichier est incomplet. Réessaie.',
                default => 'Une erreur est survenue pendant l’envoi. Réessaie.',
            };
// vérification de la taille
        } elseif (!is_uploaded_file($fichier['tmp_name'])) {
            $erreur = 'Le fichier reçu n’est pas un envoi valide.';
// détection du type avec Fileinfo.
        } elseif ($fichier['size'] < 1 || $fichier['size'] > $tailleMaximale) {
            $erreur = 'L’image doit peser au maximum 5 Mio.';
        } else {
            $informationFichier = new finfo(FILEINFO_MIME_TYPE);
            $typeMime = $informationFichier->file($fichier['tmp_name']);
            $formatsAcceptes = [
                'image/jpeg' => IMAGETYPE_JPEG,
                'image/png' => IMAGETYPE_PNG,
            ];
// vérification que le contenu correspond bien à un JPEG ou PNG.
            if (!is_string($typeMime) || !isset($formatsAcceptes[$typeMime])) {
                $erreur = 'Le fichier doit être une vraie image JPEG ou PNG.';
            } else {
                $detailsImage = getimagesize($fichier['tmp_name']);

                if (
                    $detailsImage === false
                    || $detailsImage[2] !== $formatsAcceptes[$typeMime]
                ) {
                    $erreur = 'Le fichier ne contient pas une image JPEG ou PNG lisible.';
                } else {
                    [$largeur, $hauteur] = $detailsImage;

// limites de largeur, hauteur et nombre de pixels.
                    if (
                        $largeur < 1
                        || $hauteur < 1
                        || $largeur > $largeurMaximale
                        || $hauteur > $hauteurMaximale
                        || $largeur * $hauteur > $pixelsMaximaux
                    ) {
// tentative de décodage avec GD.
                        $erreur = 'L’image dépasse les dimensions autorisées : 6 000 pixels par côté et 20 millions de pixels au total.';
                    } else {
                        $octetsImage = file_get_contents($fichier['tmp_name']);
                        $imageDecodee = $octetsImage === false
                            ? false
                            : @imagecreatefromstring($octetsImage);

                        if ($imageDecodee === false) {
                            $erreur = 'Le contenu du fichier ne peut pas être décodé comme une image.';
                        } else {
                            imagedestroy($imageDecodee);
// prépare le nom de stockage et le dossier du projet.
                            $nomOriginal = basename(str_replace('\\', '/', (string) $fichier['name']));
                            $nomOriginal = mb_substr($nomOriginal, 0, 255, 'UTF-8');
                            $extension = $typeMime === 'image/jpeg' ? '.jpg' : '.png';
                            $dossierProjet = dirname(__DIR__) . '/storage/projects/' . $id;

                            if (!is_dir($dossierProjet) && !mkdir($dossierProjet, 0750, true) && !is_dir($dossierProjet)) {
                                $erreur = 'Le dossier de rangement du projet n’a pas pu être créé.';
                            } else {
                                do {
                                    $nomStockage = bin2hex(random_bytes(16)) . $extension;
                                    $cheminStockage = $dossierProjet . '/' . $nomStockage;
                                } while (file_exists($cheminStockage));
// déplace le fichier et l’ajoute à la base avec une requête préparée.
                                if (!move_uploaded_file($fichier['tmp_name'], $cheminStockage)) {
                                    $erreur = 'L’image est valide, mais le serveur n’a pas pu la ranger.';
                                } else {
                                    try {
                                        $ajoutImage = $pdo->prepare(
                                            'INSERT INTO images (project_id, original_name, storage_name, mime_type, size_bytes, width, height)
                                             VALUES (:project_id, :original_name, :storage_name, :mime_type, :size_bytes, :width, :height)'
                                        );
                                        $ajoutImage->execute([
                                            'project_id' => $id,
                                            'original_name' => $nomOriginal !== '' ? $nomOriginal : 'image',
                                            'storage_name' => $nomStockage,
                                            'mime_type' => $typeMime,
                                            'size_bytes' => $fichier['size'],
                                            'width' => $largeur,
                                            'height' => $hauteur,
                                        ]);

                                        $messageImage = 'Image enregistrée pour ce projet. La galerie sera ajoutée dans une étape suivante.';
                                    } catch (Throwable $exception) {
                                        if (is_file($cheminStockage) && !@unlink($cheminStockage)) {
                                            $erreur = 'La base a refusé l’image et le fichier temporaire n’a pas pu être nettoyé. Demande de l’aide avant de réessayer.';
                                        } else {
                                            $erreur = 'L’image a été reçue, mais la base n’a pas pu enregistrer ses informations. Tu peux réessayer.';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        http_response_code(400);
        exit('Action inconnue.');
    }
}

$requeteImages = $pdo->prepare(
    'SELECT id, original_name FROM images WHERE project_id = :project_id ORDER BY id DESC'
);
$requeteImages->execute(['project_id' => $id]);
$images = $requeteImages->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?></title>
    <script src="assets/js/vendor/jquery-4.0.0.min.js" defer></script>
    <script src="assets/js/api.js" defer></script>
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Projet numéro <?= (int) $projet['id'] ?></p>

        <form id="form-renommer" method="post">
            <input type="hidden" name="action" value="renommer">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >
            <input
                type="hidden"
                name="project_id"
                value="<?= (int) $projet['id'] ?>"
            >

            <label for="nom">Nouveau nom du projet</label>
            <p id="message-renommage" role="status" aria-live="polite" aria-atomic="true"></p>

            <?php if ($erreur !== '' && ($_POST['action'] ?? '') === 'renommer'): ?>
                <p role="alert">
                    <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <input
                type="text"
                id="nom"
                name="nom"
                value="<?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>"
            >

            <button type="submit" id="bouton-renommer">Renommer</button>
        </form>

        <section aria-labelledby="titre-ajouter-image">
            <h2 id="titre-ajouter-image">Ajouter une image</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="envoyer_image">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >
                <input
                    type="hidden"
                    name="project_id"
                    value="<?= (int) $projet['id'] ?>"
                >

                <label for="image">Choisir une image</label>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png"
                    required
                >
                <p id="aide-image">Formats acceptés : JPEG et PNG. Taille maximale : 5 Mio (5 × 1 024 × 1 024 octets).</p>

                <button type="submit">Envoyer l’image</button>
            </form>
            <?php if ($erreur !== '' && ($_POST['action'] ?? '') === 'envoyer_image'): ?>
                <p role="alert"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($messageImage !== ''): ?>
                <p role="status"><?= htmlspecialchars($messageImage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </section>

        <section aria-labelledby="titre-galerie">
            <h2 id="titre-galerie">Galerie du projet</h2>
            <?php if ($images === []): ?>
                <p>Aucune image dans ce projet pour le moment.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($images as $image): ?>
                        <li>
                            <figure>
                                <img
                                    src="image.php?id=<?= (int) $image['id'] ?>&amp;project_id=<?= (int) $id ?>"
                                    alt="<?= htmlspecialchars($image['original_name'], ENT_QUOTES, 'UTF-8') ?>"
                                    loading="lazy"
                                >
                                <figcaption><?= htmlspecialchars($image['original_name'], ENT_QUOTES, 'UTF-8') ?></figcaption>
                            </figure>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <a href="index.php">Retour à l’accueil</a>
    </main>
</body>
</html>

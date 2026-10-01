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
// WHERE project_id = :project_id -> limite la liste aux images du projet ouvert
$requete = $pdo->prepare(
    'SELECT id, name FROM projects WHERE id = :id'
);
$requete->execute(['id' => $id]);
$projet = $requete->fetch();

if ($projet === false) {
    http_response_code(404);
    exit('Projet introuvable. <a href="index.php">Retour à l’accueil</a>');
}

$mosaiqueW = max(320, min(8000, (int) ($_GET['w'] ?? 1400)));
$mosaiqueH = max(240, min(8000, (int) ($_GET['h'] ?? 900)));
$mosaiqueMode = $_GET['mode'] ?? 'normal';
if (!is_string($mosaiqueMode) || !in_array($mosaiqueMode, ['dense', 'normal', 'aere'], true)) {
    $mosaiqueMode = 'normal';
}
$mosaiqueSeed = $_GET['seed'] ?? '';
if (!is_string($mosaiqueSeed) || ($mosaiqueSeed !== '' && !ctype_digit($mosaiqueSeed))) {
    $mosaiqueSeed = '';
}
$mosaiqueGap = max(0, min(60, (int) ($_GET['gap'] ?? 8)));
$mosaiqueRadius = max(0, min(120, (int) ($_GET['radius'] ?? 12)));
$mosaiqueMargin = max(0, min(800, (int) ($_GET['margin'] ?? 0)));
$mosaiqueBg = $_GET['bg'] ?? '#12121a';
if (!is_string($mosaiqueBg) || !preg_match('/\A#[0-9a-fA-F]{6}\z/', $mosaiqueBg)) {
    $mosaiqueBg = '#12121a';
}
$mosaiqueTransparent = ($_GET['bg_transparent'] ?? '') === '1';

$erreur = '';
$messageImage = '';
$resultatsUpload = [];
$messageSuppression = ($_GET['resultat'] ?? '') === 'image_retiree'
    ? 'L’image a été retirée du projet.'
    : '';
$confirmationSuppression = null;
$nom = $projet['name'];

$estAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

$repondreJson = static function (array $donnees, int $statut = 200): void {
    http_response_code($statut);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
};

$trouverImageProjet = static function (int $imageId, int $projetId) use ($pdo): ?array {
    $requeteImage = $pdo->prepare(
        'SELECT id, project_id, original_name, storage_name, mime_type
         FROM images
         WHERE id = :image_id AND project_id = :project_id'
    );
    $requeteImage->execute([
        'image_id' => $imageId,
        'project_id' => $projetId,
    ]);
    $image = $requeteImage->fetch();

    if ($image === false) {
        return null;
    }

    $extensionsAcceptees = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    if (
        !isset($extensionsAcceptees[$image['mime_type']])
        || !is_string($image['storage_name'])
        || !preg_match('/\A[a-f0-9]{32}\.(jpg|png)\z/D', $image['storage_name'])
        || pathinfo($image['storage_name'], PATHINFO_EXTENSION) !== $extensionsAcceptees[$image['mime_type']]
    ) {
        return null;
    }

    $dossierProjet = realpath(dirname(__DIR__) . '/storage/projects/' . $projetId);
    if ($dossierProjet === false) {
        return null;
    }

    $cheminImage = realpath($dossierProjet . DIRECTORY_SEPARATOR . $image['storage_name']);
    if (
        $cheminImage === false
        || strpos($cheminImage, $dossierProjet . DIRECTORY_SEPARATOR) !== 0
        || !is_file($cheminImage)
    ) {
        return null;
    }

    $informationFichier = new finfo(FILEINFO_MIME_TYPE);
    if ($informationFichier->file($cheminImage) !== $image['mime_type']) {
        return null;
    }

    $image['chemin'] = $cheminImage;
    return $image;
};

$stockerUneImage = static function (array $fichier, int $projetId) use ($pdo): array {
    $refuser = static fn(string $message): array => [
        'success' => false,
        'name' => (string) ($fichier['name'] ?? 'Fichier sans nom'),
        'message' => $message,
    ];
    $tailleMaximale = 5 * 1024 * 1024;
    $largeurMaximale = 6000;
    $hauteurMaximale = 6000;
    $pixelsMaximaux = 20_000_000;

    if (!isset($fichier['error'], $fichier['size'], $fichier['tmp_name'], $fichier['name'])) {
        return $refuser('Informations de fichier incomplètes.');
    }
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $messageErreur = match ($fichier['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la limite de 5 Mio.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier sélectionné.',
            UPLOAD_ERR_PARTIAL => 'L’envoi est incomplet.',
            default => 'PHP a signalé une erreur pendant l’envoi.',
        };
        return $refuser($messageErreur);
    }
    if (!is_uploaded_file($fichier['tmp_name'])) {
        return $refuser('Le fichier reçu n’est pas un envoi valide.');
    }
    if ($fichier['size'] < 1 || $fichier['size'] > $tailleMaximale) {
        return $refuser('Le fichier doit peser au maximum 5 Mio.');
    }

    $informationFichier = new finfo(FILEINFO_MIME_TYPE);
    $typeMime = $informationFichier->file($fichier['tmp_name']);
    $formatsAcceptes = [
        'image/jpeg' => ['type' => IMAGETYPE_JPEG, 'extension' => '.jpg'],
        'image/png' => ['type' => IMAGETYPE_PNG, 'extension' => '.png'],
    ];
    if (!is_string($typeMime) || !isset($formatsAcceptes[$typeMime])) {
        return $refuser('Format refusé : seuls JPEG et PNG sont acceptés.');
    }

    $detailsImage = getimagesize($fichier['tmp_name']);
    if ($detailsImage === false || $detailsImage[2] !== $formatsAcceptes[$typeMime]['type']) {
        return $refuser('Le contenu ne correspond pas à une image JPEG ou PNG lisible.');
    }
    [$largeur, $hauteur] = $detailsImage;
    if (
        $largeur < 1
        || $hauteur < 1
        || $largeur > $largeurMaximale
        || $hauteur > $hauteurMaximale
        || $largeur * $hauteur > $pixelsMaximaux
    ) {
        return $refuser('Dimensions refusées : maximum 6 000 pixels par côté et 20 millions de pixels au total.');
    }

    $octetsImage = file_get_contents($fichier['tmp_name']);
    $imageDecodee = $octetsImage === false ? false : @imagecreatefromstring($octetsImage);
    if ($imageDecodee === false) {
        return $refuser('Le contenu ne peut pas être décodé comme une image.');
    }
    imagedestroy($imageDecodee);

    $nomOriginal = basename(str_replace('\\', '/', (string) $fichier['name']));
    $nomOriginal = mb_substr($nomOriginal, 0, 255, 'UTF-8');
    $dossierProjet = dirname(__DIR__) . '/storage/projects/' . $projetId;
    if (!is_dir($dossierProjet) && !mkdir($dossierProjet, 0750, true) && !is_dir($dossierProjet)) {
        return $refuser('Le dossier de rangement du projet n’a pas pu être créé.');
    }

    do {
        $nomStockage = bin2hex(random_bytes(16)) . $formatsAcceptes[$typeMime]['extension'];
        $cheminStockage = $dossierProjet . '/' . $nomStockage;
    } while (file_exists($cheminStockage));

    if (!move_uploaded_file($fichier['tmp_name'], $cheminStockage)) {
        return $refuser('Image valide, mais le serveur n’a pas pu la ranger.');
    }

    try {
        $ajoutImage = $pdo->prepare(
            'INSERT INTO images (project_id, original_name, storage_name, mime_type, size_bytes, width, height)
             VALUES (:project_id, :original_name, :storage_name, :mime_type, :size_bytes, :width, :height)'
        );
        $ajoutImage->execute([
            'project_id' => $projetId,
            'original_name' => $nomOriginal !== '' ? $nomOriginal : 'image',
            'storage_name' => $nomStockage,
            'mime_type' => $typeMime,
            'size_bytes' => $fichier['size'],
            'width' => $largeur,
            'height' => $hauteur,
        ]);
        $imageId = (int) $pdo->lastInsertId();
    } catch (Throwable $exception) {
        if (is_file($cheminStockage) && !@unlink($cheminStockage)) {
            return $refuser('La base a refusé l’image et le fichier temporaire n’a pas pu être nettoyé. Demande de l’aide avant de réessayer.');
        }
        return $refuser('La base n’a pas pu enregistrer les informations de cette image.');
    }

    return [
        'success' => true,
        'image_id' => $imageId,
        'name' => $nomOriginal !== '' ? $nomOriginal : 'image',
        'src' => 'image.php?id=' . $imageId . '&project_id=' . $projetId . '&thumb=1&max=1200',
        'ar' => $hauteur > 0 ? $largeur / $hauteur : 1,
        'message' => 'Enregistrée dans ce projet.',
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Un ancien api.js en cache envoyait le formulaire de renommage sans action.
    $action = $_POST['action'] ?? (isset($_POST['nom']) ? 'renommer' : '');
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

    } elseif ($action === 'envoyer_image') {
        $fichiersRecus = $_FILES['images'] ?? null;
        $resultatsUpload = [];
        $limiteFichiers = 5;

        if (
            !is_array($fichiersRecus)
            || !isset($fichiersRecus['name'], $fichiersRecus['type'], $fichiersRecus['tmp_name'], $fichiersRecus['error'], $fichiersRecus['size'])
            || !is_array($fichiersRecus['name'])
        ) {
            $erreur = 'Choisis au moins une image à envoyer.';
        } else {
            $nombreChoisi = count($fichiersRecus['name']);
            if ($nombreChoisi < 1) {
                $erreur = 'Choisis au moins une image à envoyer.';
            } else {
                $nombreATraiter = min($nombreChoisi, $limiteFichiers);

                for ($i = 0; $i < $nombreATraiter; $i++) {
                    $fichier = [
                        'name' => $fichiersRecus['name'][$i] ?? '',
                        'type' => $fichiersRecus['type'][$i] ?? '',
                        'tmp_name' => $fichiersRecus['tmp_name'][$i] ?? '',
                        'error' => $fichiersRecus['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $fichiersRecus['size'][$i] ?? 0,
                    ];
                    $resultatsUpload[] = $stockerUneImage($fichier, $id);
                }

                for ($i = $limiteFichiers; $i < $nombreChoisi; $i++) {
                    $resultatsUpload[] = [
                        'success' => false,
                        'name' => (string) ($fichiersRecus['name'][$i] ?? 'Fichier sans nom'),
                        'message' => 'Limite de 5 images par envoi dépassée.',
                    ];
                }

                $acceptees = count(array_filter($resultatsUpload, static fn(array $resultat): bool => $resultat['success']));
                $refusees = count($resultatsUpload) - $acceptees;
                $messageImage = $acceptees . ' image(s) acceptée(s), ' . $refusees . ' refusée(s). Consulte le détail ci-dessous.';
            }
        }

        if ($estAjax) {
            if ($erreur !== '') {
                $repondreJson(['success' => false, 'message' => $erreur], 422);
            }

            $acceptees = count(array_filter($resultatsUpload, static fn(array $resultat): bool => $resultat['success']));
            $refusees = count($resultatsUpload) - $acceptees;
            $repondreJson([
                'success' => $refusees === 0,
                'message' => $acceptees . ' image(s) acceptée(s), ' . $refusees . ' refusée(s).',
                'results' => $resultatsUpload,
                'project_id' => $id,
            ]);
        }
// le retrait avec une confirmation en deux temps
    } elseif ($action === 'demander_retrait' || $action === 'supprimer_image') {
        $imageIdRecu = $_POST['image_id'] ?? null;

        if (!is_string($imageIdRecu) || !ctype_digit($imageIdRecu) || (int) $imageIdRecu < 1) {
            http_response_code(400);
            $erreur = 'Identifiant d’image invalide.';
        } else {
            $imageCible = $trouverImageProjet((int) $imageIdRecu, $id);

            if ($imageCible === null) {
                http_response_code(404);
                $erreur = 'Cette image est introuvable dans ce projet ou son fichier ne peut pas être vérifié.';
            } elseif ($action === 'demander_retrait') {
                $confirmationSuppression = $imageCible;
            } else {
                $fichierSupprime = false;

                try {
                    $pdo->beginTransaction();
                    $suppression = $pdo->prepare(
                        'DELETE FROM images WHERE id = :image_id AND project_id = :project_id'
                    );
                    $suppression->execute([
                        'image_id' => $imageCible['id'],
                        'project_id' => $id,
                    ]);

                    if ($suppression->rowCount() !== 1) {
                        throw new RuntimeException('La ligne de l’image n’a pas été supprimée.');
                    }

                    if (!@unlink($imageCible['chemin'])) {
                        $pdo->rollBack();
                        $erreur = 'Le fichier n’a pas pu être retiré. La fiche de l’image a été conservée.';
                    } else {
                        $fichierSupprime = true;
                        $pdo->commit();
                        header('Location: projet.php?id=' . $id . '&resultat=image_retiree');
                        exit;
                    }
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }

                    $erreur = $fichierSupprime
                        ? 'Le fichier a été retiré, mais la base n’a pas confirmé le retrait. L’état de cette image doit être vérifié.'
                        : 'Le retrait a échoué. Aucune autre image n’a été touchée.';
                }
            }
        }
    } else {
        if ($estAjax) {
            $repondreJson(
                ['success' => false, 'message' => 'Action inconnue. Recharge la page et réessaie.'],
                400
            );
        }

        http_response_code(400);
        exit('Action inconnue.');
    }
}
// liste les images liées au projet ouvert et affiche un message si la galerie est vide.
$requeteImages = $pdo->prepare(
    'SELECT id, original_name, width, height FROM images WHERE project_id = :project_id ORDER BY id DESC'
    // sélectionne que les images liées au projet ouvert, puis transmet au JavaScript leur nom, leurs proportions et une URL.
);
$requeteImages->execute(['project_id' => $id]);
$images = $requeteImages->fetchAll();
$donneesMosaique = array_map(
    static function (array $image) use ($id): array {
        $imageId = (int) $image['id'];
        $largeur = (int) $image['width'];
        $hauteur = (int) $image['height'];

        return [
            'src' => 'image.php?id=' . $imageId . '&project_id=' . $id . '&thumb=1&max=1200',
            'ar' => $hauteur > 0 ? $largeur / $hauteur : 1,
            'name' => (string) $image['original_name'],
        ];
    },
    $images
);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/vendor/jquery-4.0.0.min.js" defer></script>
    <script src="assets/js/api.js" defer></script>
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <script id="donnees-mosaique" type="application/json"><?= json_encode(
        $donneesMosaique,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    ) ?></script>
    <main>
        <h1><?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Projet numéro <?= (int) $projet['id'] ?></p>

        <section class="mosaic-tool" aria-labelledby="titre-mosaique">
            <h2 id="titre-mosaique">Créer une mosaïque</h2>
            <?php if ($images === []): ?>
                <p role="status">Ajoute au moins une image au projet pour créer une mosaïque.</p>
            <?php endif; ?>

            <form class="mosaic-panel" method="get" action="projet.php">
                <input type="hidden" name="id" value="<?= (int) $id ?>">
                <div class="mosaic-field">
                    <label for="w">Largeur (pixels)</label>
                    <input id="w" name="w" type="number" min="320" max="8000" value="<?= $mosaiqueW ?>">
                </div>
                <div class="mosaic-field">
                    <label for="h">Hauteur (pixels)</label>
                    <input id="h" name="h" type="number" min="240" max="8000" value="<?= $mosaiqueH ?>">
                </div>
                <fieldset class="mosaic-field">
                    <legend>Densité</legend>
                    <label><input type="radio" name="mode" value="dense"<?= $mosaiqueMode === 'dense' ? ' checked' : '' ?>> Dense</label>
                    <label><input type="radio" name="mode" value="normal"<?= $mosaiqueMode === 'normal' ? ' checked' : '' ?>> Normale</label>
                    <label><input type="radio" name="mode" value="aere"<?= $mosaiqueMode === 'aere' ? ' checked' : '' ?>> Aérée</label>
                </fieldset>
                <div class="mosaic-field">
                    <label for="seed">Seed (optionnelle)</label>
                    <input id="seed" name="seed" type="text" inputmode="numeric" placeholder="Automatique" value="<?= htmlspecialchars($mosaiqueSeed, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="mosaic-field">
                    <label for="gap">Espacement (pixels)</label>
                    <input id="gap" name="gap" type="number" min="0" max="60" value="<?= $mosaiqueGap ?>">
                </div>
                <div class="mosaic-field">
                    <label for="radius">Arrondi (pixels)</label>
                    <input id="radius" name="radius" type="number" min="0" max="120" value="<?= $mosaiqueRadius ?>">
                </div>
                <div class="mosaic-field">
                    <label for="margin">Marge du PNG (pixels)</label>
                    <input id="margin" name="margin" type="number" min="0" max="800" value="<?= $mosaiqueMargin ?>">
                </div>
                <div class="mosaic-field">
                    <label for="bg">Couleur du fond</label>
                    <input id="bg" name="bg" type="text" value="<?= htmlspecialchars($mosaiqueBg, ENT_QUOTES, 'UTF-8') ?>" pattern="#[0-9a-fA-F]{6}">
                    <input id="bg_color" type="color" value="<?= htmlspecialchars($mosaiqueBg, ENT_QUOTES, 'UTF-8') ?>" aria-label="Choisir la couleur du fond">
                    <label><input id="bg_transparent" type="checkbox" name="bg_transparent" value="1"<?= $mosaiqueTransparent ? ' checked' : '' ?>> Fond transparent</label>
                </div>
                <button class="mosaic-button primary" type="submit">Générer l’aperçu</button>
                <button class="mosaic-button" type="button" id="regenMosaic">Régénérer</button>
                <button class="mosaic-button" type="button" id="exportPng"<?= $images === [] ? ' disabled' : '' ?>>Exporter en PNG</button>
            </form>

            <p id="mosaic-message" class="mosaic-hint" role="status" aria-live="polite">
                <?= $images === [] ? 'Aucune image à disposer pour le moment.' : 'L’aperçu reprend les images de ce projet.' ?>
            </p>
            <div class="mosaic-preview-center">
                <div id="previewViewport">
                    <div class="mosaic-preview-wrap colored" id="previewWrap">
                        <div id="gallery"></div>
                    </div>
                </div>
            </div>
        </section>

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
<!-- Le formulaire accepte maintenant plusieurs fichiers,jusqu’à 5 par envoi. -->
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
            <form id="form-envoi-images" method="post" enctype="multipart/form-data">
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

                <label for="image">Choisir une ou plusieurs images</label>
                <input
                    type="file"
                    id="image"
                    name="images[]"
                    accept="image/jpeg,image/png"
                    multiple
                    required
                >
                <p id="aide-image">Formats acceptés : JPEG et PNG. Taille maximale : 5 Mio par image. Choisis jusqu’à 5 images par envoi.</p>

                    <button type="submit" id="bouton-envoi-images">Envoyer les images</button>
            </form>
            <p id="message-envoi-images" role="status" aria-live="polite" aria-atomic="true"></p>
            <?php if ($erreur !== '' && ($_POST['action'] ?? '') === 'envoyer_image'): ?>
                <p role="alert"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($messageImage !== ''): ?>
                <p role="status"><?= htmlspecialchars($messageImage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <ul id="resultats-envoi-images" aria-label="Résultat de chaque image envoyée"<?= $resultatsUpload === [] ? ' hidden' : '' ?>>
                    <?php foreach ($resultatsUpload as $resultat): ?>
                        <li>
                            <strong><?= htmlspecialchars($resultat['name'], ENT_QUOTES, 'UTF-8') ?> :</strong>
                            <?= $resultat['success'] ? 'acceptée' : 'refusée' ?> —
                            <?= htmlspecialchars($resultat['message'], ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endforeach; ?>
            </ul>
        </section>

        <section aria-labelledby="titre-galerie">
            <h2 id="titre-galerie">Galerie du projet</h2>
            <p id="galerie-vide"<?= $images === [] ? '' : ' hidden' ?>>Aucune image dans ce projet pour le moment.</p>
            <ul id="liste-galerie-images">
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
                            <form method="post">
                                <input type="hidden" name="action" value="demander_retrait">
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                <input type="hidden" name="project_id" value="<?= (int) $id ?>">
                                <input type="hidden" name="image_id" value="<?= (int) $image['id'] ?>">
                                <button type="submit">Retirer cette image</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
            </ul>

            <?php if ($erreur !== '' && in_array($_POST['action'] ?? '', ['demander_retrait', 'supprimer_image'], true)): ?>
                <p role="alert"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <?php if ($messageSuppression !== ''): ?>
                <p role="status"><?= htmlspecialchars($messageSuppression, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($confirmationSuppression !== null): ?>
                <section aria-labelledby="titre-confirmer-retrait">
                    <h3 id="titre-confirmer-retrait">Confirmer le retrait</h3>
                    <p>
                        Retirer « <?= htmlspecialchars($confirmationSuppression['original_name'], ENT_QUOTES, 'UTF-8') ?> »
                        du projet « <?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?> » ?
                    </p>
                    <form method="post">
                        <input type="hidden" name="action" value="supprimer_image">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                        <input type="hidden" name="project_id" value="<?= (int) $id ?>">
                        <input type="hidden" name="image_id" value="<?= (int) $confirmationSuppression['id'] ?>">
                        <button type="submit">Confirmer le retrait</button>
                        <a href="projet.php?id=<?= (int) $id ?>">Annuler</a>
                    </form>
                </section>
            <?php endif; ?>
        </section>

        <a href="index.php">Retour à l’accueil</a>
    </main>
</body>
</html>

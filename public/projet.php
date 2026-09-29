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
$nom = $projet['name'];

$estAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

$repondreJson = static function (array $donnees, int $statut = 200): void {
    http_response_code($statut);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}
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

            <?php if ($erreur !== ''): ?>
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

        <a href="index.php">Retour à l’accueil</a>
    </main>
</body>
</html>
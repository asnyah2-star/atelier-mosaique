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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenRecu = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenRecu)
        || !hash_equals($_SESSION['csrf_token'], $tokenRecu)
    ) {
        http_response_code(403);
        exit('Demande refusée : jeton de sécurité invalide.');
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

    if ($erreur === '') {
        $miseAJour = $pdo->prepare(
            'UPDATE projects SET name = :name WHERE id = :id'
        );
        $miseAJour->execute([
            'name' => $nom,
            'id' => $id,
        ]);

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
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Projet numéro <?= (int) $projet['id'] ?></p>

        <form method="post">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <label for="nom">Nouveau nom du projet</label>

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

            <button type="submit">Renommer</button>
        </form>

        <a href="index.php">Retour à l’accueil</a>
    </main>
</body>
</html>
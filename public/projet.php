<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/connexion.php';

$idRecu = $_GET['id'] ?? null;

if (!is_string($idRecu) || !ctype_digit($idRecu) || (int) $idRecu < 1) {
    http_response_code(404);
    exit('Projet introuvable. <a href="index.php">Retour à l’accueil</a>');
}

$requete = $pdo->prepare(
    'SELECT id, name FROM projects WHERE id = :id'
);
$requete->execute(['id' => (int) $idRecu]);
$projet = $requete->fetch();

if ($projet === false) {
    http_response_code(404);
    exit('Projet introuvable. <a href="index.php">Retour à l’accueil</a>');
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
        <a href="index.php">Retour à l’accueil</a>
    </main>
</body>
</html>
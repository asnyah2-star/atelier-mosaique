<?php
declare(strict_types=1);

// TODO (mission 3) : construire ici la porte d'entrée de ton atelier. maj 1200 290926

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';
$nom = '';

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
        require_once __DIR__ . '/../src/connexion.php';

        $requete = $pdo->prepare(
            'INSERT INTO projects (name) VALUES (:name)'
        );
        $requete->execute(['name' => $nom]);

        header('Location: index.php');
        exit;
    }
}


require_once __DIR__ . '/../src/connexion.php';
// La boucle pourra alors lire $projet['id'] et $projet['name'].
// 'SELECT id, name FROM projects WHERE 1 = 0 ORDER BY id' --> pour tester l'état vide 
$requeteProjets = $pdo->query(
    'SELECT id, name FROM projects ORDER BY id'
);
$projets = $requeteProjets->fetchAll();

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon atelier de mosaïques</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/vendor/jquery-4.0.0.min.js"></script>
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <main>
        <h1>Mon atelier de mosaïques</h1>
        <p><strong>Hasnia Ghachamo</strong>, voici le point de départ de ton application.</p>
        <p>Suis la fiche de mission que je t’ai transmise.</p>
        <p>PHP fonctionne : version <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?>.</p>
        <!-- TODO : ajouter tes projets fictifs et ton formulaire en mission 3. -->
    </main>

    <main class="projetReal">
        <!-- l’état vide est bien prévu dans ton code -->
        <?php if ($projets !== []): ?>
    <section class="etat-projets card">
        <h2>Quelques projets</h2>
        <ul>
            <?php foreach ($projets as $projet): ?>
                <li>
                    <a href="projet.php?id=<?= (int) $projet['id'] ?>">
                        <?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php else: ?>
    <section class="etat-vide card">
        <h2>Aucun projet pour le moment</h2>
    </section>
<?php endif; ?>

        <form class="formulaire card" method="post">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >
            <button type="button" class="btnew">Nouveau projet</button>
            <label for="nom">Nom du projet</label>

<?php if ($erreur !== ''): ?>
    <p id="erreur-nom" role="alert">
        <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<input
    type="text"
    id="nom"
    name="nom"
    value="<?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>"
>
            <!-- <label for="nom">Mosaïque Maker</label>
            <input type="text" id="nom" name="nom"> -->
            <button type="submit">Valider</button>
        </form>
    </main>
</body>
</html>
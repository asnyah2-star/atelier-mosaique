<?php
declare(strict_types=1);

// TODO (mission 3) : construire ici la porte d'entrée de ton atelier. maj 1200 290926

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';
$nom = '';
$messages = [
    'projet_archive' => 'Le projet a été archivé. Tu peux le restaurer depuis la liste des projets archivés.',
    'projet_restaure' => 'Le projet a été restauré.',
    'projet_supprime' => 'Le projet archivé et ses images ont été supprimés définitivement.',
    'projet_supprime_fichiers' => 'Le projet a été supprimé, mais certains fichiers d’images n’ont pas pu être effacés.',
];
$noticeRecu = $_GET['notice'] ?? '';
$message = is_string($noticeRecu) ? ($messages[$noticeRecu] ?? '') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenRecu = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenRecu)
        || !hash_equals($_SESSION['csrf_token'], $tokenRecu)
    ) {
        http_response_code(403);
        exit('Demande refusée : jeton de sécurité invalide.');
    }

    $action = $_POST['action'] ?? 'creer_projet';
    if (in_array($action, ['archiver_projet', 'restaurer_projet', 'supprimer_projet'], true)) {
        $idProjetRecu = $_POST['project_id'] ?? null;
        if (!is_string($idProjetRecu) || !ctype_digit($idProjetRecu) || (int) $idProjetRecu < 1) {
            http_response_code(400);
            exit('Identifiant de projet invalide.');
        }

        require_once __DIR__ . '/../src/connexion.php';
        if ($action === 'archiver_projet') {
            $requeteActionProjet = $pdo->prepare(
                'UPDATE projects SET archived_at = CURRENT_TIMESTAMP
                 WHERE id = :id AND archived_at IS NULL'
            );
            $requeteActionProjet->execute(['id' => (int) $idProjetRecu]);
            $notice = 'projet_archive';
        } elseif ($action === 'restaurer_projet') {
            $requeteActionProjet = $pdo->prepare(
                'UPDATE projects SET archived_at = NULL
                 WHERE id = :id AND archived_at IS NOT NULL'
            );
            $requeteActionProjet->execute(['id' => (int) $idProjetRecu]);
            $notice = 'projet_restaure';
        } else {
            $projetId = (int) $idProjetRecu;
            $dossierProjet = dirname(__DIR__) . '/storage/projects/' . $projetId;
            if (is_link($dossierProjet)) {
                http_response_code(400);
                exit('Suppression refusée : le dossier de stockage du projet est invalide.');
            }

            try {
                $pdo->beginTransaction();
                $verificationProjet = $pdo->prepare(
                    'SELECT id FROM projects WHERE id = :id AND archived_at IS NOT NULL FOR UPDATE'
                );
                $verificationProjet->execute(['id' => $projetId]);
                if ($verificationProjet->fetch() === false) {
                    $pdo->rollBack();
                    http_response_code(404);
                    exit('Projet archivé introuvable. Archive le projet avant de le supprimer définitivement.');
                }

                $requeteImagesProjet = $pdo->prepare(
                    'SELECT storage_name FROM images WHERE project_id = :id'
                );
                $requeteImagesProjet->execute(['id' => $projetId]);
                $nomsFichiers = $requeteImagesProjet->fetchAll(PDO::FETCH_COLUMN);
                foreach ($nomsFichiers as $nomFichier) {
                    if (!is_string($nomFichier) || !preg_match('/\\A[a-f0-9]{32}\\.(jpg|png)\\z/D', $nomFichier)) {
                        $pdo->rollBack();
                        http_response_code(400);
                        exit('Suppression refusée : un nom de fichier du projet est invalide.');
                    }
                }

                $suppressionProjet = $pdo->prepare(
                    'DELETE FROM projects WHERE id = :id AND archived_at IS NOT NULL'
                );
                $suppressionProjet->execute(['id' => $projetId]);
                if ($suppressionProjet->rowCount() !== 1) {
                    $pdo->rollBack();
                    http_response_code(404);
                    exit('Projet archivé introuvable.');
                }
                $pdo->commit();
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                http_response_code(500);
                exit('La suppression a échoué ; le projet reste archivé. Réessaie après vérification.');
            }

            $fichiersRestants = false;
            foreach ($nomsFichiers as $nomFichier) {
                $cheminFichier = $dossierProjet . DIRECTORY_SEPARATOR . $nomFichier;
                if ((is_file($cheminFichier) || is_link($cheminFichier)) && !unlink($cheminFichier)) {
                    $fichiersRestants = true;
                }
            }
            if (is_dir($dossierProjet) && !is_link($dossierProjet)) {
                @rmdir($dossierProjet);
            }
            $notice = $fichiersRestants ? 'projet_supprime_fichiers' : 'projet_supprime';
        }

        if ($action !== 'supprimer_projet' && $requeteActionProjet->rowCount() !== 1) {
            http_response_code(404);
            exit('Projet introuvable ou action déjà effectuée.');
        }
        header('Location: index.php?notice=' . $notice);
        exit;
    }

    if ($action !== 'creer_projet') {
        http_response_code(400);
        exit('Action inconnue.');
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
    'SELECT id, name FROM projects WHERE archived_at IS NULL ORDER BY id'
);
$projets = $requeteProjets->fetchAll();
$requeteProjetsArchives = $pdo->query(
    'SELECT id, name FROM projects WHERE archived_at IS NOT NULL ORDER BY archived_at DESC, id'
);
$projetsArchives = $requeteProjetsArchives->fetchAll();

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
        <!-- <p><strong>Hasnia</strong>, voici le point de départ de ton application.</p>
        <p>Suis la fiche de mission que je t’ai transmise.</p>
        <p>PHP fonctionne : version <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?>.</p> -->
        <!-- TODO : ajouter tes projets fictifs et ton formulaire en mission 3. -->
    </main>

    <main class="projetReal">
        <?php if ($message !== ''): ?>
            <p class="project-notice" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <!-- l’état vide est bien prévu dans ton code -->
        <?php if ($projets !== []): ?>
            <section class="etat-projets card">
                <h2>Quelques projets</h2>
                <ul class="project-list">
                    <?php foreach ($projets as $projet): ?>
                        <li data-id_projet="<?= (int) $projet['id'] ?>">
                            <a href="projet.php?id=<?= (int) $projet['id'] ?>">
                                <?= htmlspecialchars($projet['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <form method="post" class="project-action-form" onsubmit="return confirm('Archiver ce projet ? Il restera récupérable dans la liste des projets archivés.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="archiver_projet">
                                <input type="hidden" name="project_id" value="<?= (int) $projet['id'] ?>">
                                <button type="submit">Archiver</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php else: ?>
            <section class="etat-vide card">
                <h2>Aucun projet actif</h2>
                <p>Crée un projet ou restaure-en un depuis les archives.</p>
            </section>
        <?php endif; ?>

        <section class="projets-archives card" aria-labelledby="titre-projets-archives">
            <h2 id="titre-projets-archives">Projets archivés</h2>
            <?php if ($projetsArchives !== []): ?>
                <ul class="project-list">
                    <?php foreach ($projetsArchives as $projetArchive): ?>
                        <li data-id_projet="<?= (int) $projetArchive['id'] ?>">
                            <span><?= htmlspecialchars($projetArchive['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <form method="post" class="project-action-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="restaurer_projet">
                                <input type="hidden" name="project_id" value="<?= (int) $projetArchive['id'] ?>">
                                <button type="submit">Restaurer</button>
                            </form>
                            <form method="post" class="project-action-form" onsubmit="return confirm('Supprimer définitivement ce projet et ses images ? Cette action est irréversible.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="supprimer_projet">
                                <input type="hidden" name="project_id" value="<?= (int) $projetArchive['id'] ?>">
                                <button type="submit" class="danger-button">Supprimer définitivement</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun projet archivé.</p>
            <?php endif; ?>
        </section>

        <form class="formulaire card" method="post">
            <input type="hidden" name="action" value="creer_projet">
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

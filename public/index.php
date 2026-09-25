<?php
declare(strict_types=1);

// TODO (mission 3) : construire ici la porte d'entrée de ton atelier. maj 1614
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
        <p>Hasnia, voici le point de départ de ton application.</p>
        <p>Suis la fiche de mission que je t’ai transmise.</p>
        <p>PHP fonctionne : version <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?>.</p>
        <!-- TODO : ajouter tes projets fictifs et ton formulaire en mission 3. -->
    </main>

<main class="projetReal">

    <!-- État 1 : il existe des projets -->
    <section class="etat-projets">
        <h2>Quelques projets</h2>

        <ul>
            <li><a href="#">Bruxelles Babel 26</a></li>
            <li><a href="#">Bruxelles Babel 27</a></li>
        </ul>

        <button type="button">Nouveau projet</button>
    </section>


    <!-- État 2 : aucun projet -->
    <section class="etat-vide">
        <h2>Aucun projet pour le moment</h2>

        <p>Commence ton premier projet de mosaïque.</p>

        <button type="button">Nouveau projet</button>
    </section>

</main>

<form class="formulaire">
    <button type="button">Nouveau projet</button>
    <label for="nom">Mosaïque Maker</label>
    <input type="text" id="nom" name="nom">
    <button type="submit">Valider</button>
</form>




</body>
</html>

<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Exécution en ligne de commande uniquement.');
}
$racine = dirname(__DIR__);
$destination = $racine . '/desktop-preview';
if (file_exists($destination)) {
    exit("La copie existe déjà ; aucun fichier n'a été écrasé.\n");
}
if (!is_file($racine . '/storage/desktop-test/atelier.sqlite')) {
    exit("Prépare d'abord la copie SQLite de démonstration.\n");
}
$copier = static function (string $source, string $cible): void {
    if (!is_dir(dirname($cible)) && !mkdir(dirname($cible), 0700, true)) {
        throw new RuntimeException('Création du dossier impossible.');
    }
    if (!copy($source, $cible)) {
        throw new RuntimeException('Copie impossible.');
    }
};
foreach (['public' => 'public', 'storage/desktop-test/projects' => 'storage/projects'] as $source => $cible) {
    $parcours = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($racine . '/' . $source, FilesystemIterator::SKIP_DOTS));
    foreach ($parcours as $fichier) {
        if ($fichier->isFile() && !$fichier->isLink()) {
            $relatif = substr($fichier->getPathname(), strlen($racine . '/' . $source) + 1);
            $copier($fichier->getPathname(), $destination . '/' . $cible . '/' . $relatif);
        }
    }
}
$copier($racine . '/storage/desktop-test/atelier.sqlite', $destination . '/storage/atelier.sqlite');
$copier($racine . '/tools/desktop/connexion.php', $destination . '/src/connexion.php');
// FOR UPDATE est propre à MySQL. La transaction SQLite reste en place ;
// ses écritures sont sérialisées et un conflit provoque le refus de l'action.
$index = $destination . '/public/index.php';
$contenu = file_get_contents($index);
if (substr_count($contenu, ' FOR UPDATE') !== 1) {
    throw new RuntimeException('La requête à adapter a changé : inspecter avant de poursuivre.');
}
file_put_contents($index, str_replace(' FOR UPDATE', '', $contenu));
echo "Copie SQLite prête dans desktop-preview. Version de test, pas encore un installateur.\n";

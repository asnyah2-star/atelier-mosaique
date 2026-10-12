const fs = require('node:fs');
const path = require('node:path');
const source = path.resolve(__dirname, '../desktop-preview');
const target = path.join(__dirname, 'desktop-bundle');
if (fs.existsSync(target)) throw new Error('Le dossier desktop-bundle existe déjà. Ne pas écraser une préparation précédente.');
for (const folder of ['public', 'src', 'storage']) {
  fs.cpSync(path.join(source, folder), path.join(target, folder), { recursive: true,
    filter: p => !['sessions', 'tmp'].includes(path.basename(p)) });
}
fs.copyFileSync(path.resolve(__dirname, '../tools/desktop/router.php'), path.join(target, 'router.php'));
fs.copyFileSync(path.resolve(__dirname, '../tools/desktop/connexion.php'), path.join(target, 'src/connexion.php'));
for (const file of ['index.php', 'projet.php', 'image.php']) {
  const location = path.join(target, 'public', file);
  const content = fs.readFileSync(location, 'utf8').replaceAll("dirname(__DIR__) . '/storage/projects/'", "(getenv('ATELIER_DATA_DIR') ?: dirname(__DIR__) . '/storage') . '/projects/'");
  fs.writeFileSync(location, content);
}
console.log('Code et données de démonstration préparés pour le paquet Windows.');

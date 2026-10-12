const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const source = path.resolve(__dirname, '..');
const target = path.join(__dirname, 'desktop-bundle');
function prepareBundle() {
const staging = path.join(__dirname, 'desktop-bundle-staging-' + Date.now());
const files = execFileSync('git', ['ls-files', '-z', '--', 'public'], { cwd: source }).toString().split('\0').filter(Boolean);
if (!files.includes('public/index.php')) throw new Error('Sources Git incomplètes.');
fs.mkdirSync(path.join(staging, 'storage/projects'), { recursive: true });
fs.mkdirSync(path.join(staging, 'src'), { recursive: true });
for (const file of files) {
  const destination = path.join(staging, file);
  fs.mkdirSync(path.dirname(destination), { recursive: true });
  fs.copyFileSync(path.join(source, file), destination);
}
fs.copyFileSync(path.join(source, 'tools/desktop/router.php'), path.join(staging, 'router.php'));
fs.copyFileSync(path.join(source, 'tools/desktop/connexion.php'), path.join(staging, 'src/connexion.php'));
for (const file of ['index.php', 'projet.php', 'image.php']) {
  const location = path.join(staging, 'public', file);
  let content = fs.readFileSync(location, 'utf8').replaceAll("dirname(__DIR__) . '/storage/projects/'", "(getenv('ATELIER_DATA_DIR') ?: dirname(__DIR__) . '/storage') . '/projects/'");
  if (file === 'index.php') {
    if (content.split(' FOR UPDATE').length !== 2) throw new Error('La requête MySQL à adapter a changé.');
    content = content.replace(' FOR UPDATE', '');
  }
  fs.writeFileSync(location, content);
}
const runtime = path.join(__dirname, 'runtime/php');
execFileSync(path.join(runtime, 'php.exe'), ['-c', path.join(runtime, 'php.ini'), path.join(source, 'tools/desktop/create-empty-db.php'), path.join(staging, 'storage/atelier.sqlite')], { cwd: runtime, stdio: 'inherit' });
if (fs.existsSync(target)) {
  const backups = path.join(source, 'backups');
  fs.mkdirSync(backups, { recursive: true });
  fs.renameSync(target, path.join(backups, 'desktop-bundle-before-' + Date.now()));
}
fs.renameSync(staging, target);
console.log('Paquet préparé depuis les sources Git, avec une base SQLite vide.');
}
module.exports = { prepareBundle };
if (require.main === module) prepareBundle();

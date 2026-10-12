const { app, BrowserWindow, dialog, session } = require('electron');
const path = require('node:path');
const fs = require('node:fs');
const { startServer } = require('./local-server.cjs');
let server;

if (require('electron-squirrel-startup') || !app.requestSingleInstanceLock()) {
  app.quit();
} else {
  app.whenReady().then(async () => {
    try {
      const preview = app.isPackaged ? path.join(process.resourcesPath, 'desktop-bundle') : path.resolve(__dirname, '../desktop-preview');
      const php = app.isPackaged ? path.join(process.resourcesPath, 'runtime/php/php.exe') : path.join(__dirname, 'runtime/php/php.exe');
      if (!fs.existsSync(php) || !fs.existsSync(path.join(preview, 'storage/atelier.sqlite'))) {
        throw new Error('PHP ou la copie SQLite de test est absent.');
      }
      const router = path.join(preview, 'router.php');
      if (!app.isPackaged) fs.copyFileSync(path.resolve(__dirname, '../tools/desktop/router.php'), router);
      const dataDir = app.isPackaged ? path.join(app.getPath('userData'), 'data') : path.join(preview, 'storage');
      if (!fs.existsSync(dataDir)) {
        const staging = dataDir + '.initialisation';
        if (fs.existsSync(staging)) throw new Error('Une initialisation incomplète existe. Conserve ce dossier et demande un diagnostic.');
        fs.mkdirSync(path.dirname(dataDir), { recursive: true });
        fs.cpSync(path.join(preview, 'storage'), staging, { recursive: true, errorOnExist: true, force: false });
        fs.renameSync(staging, dataDir);
      }
      for (const folder of ['sessions', 'tmp']) fs.mkdirSync(path.join(dataDir, folder), { recursive: true });
      server = await startServer(php, path.join(preview, 'public'), router, path.join(path.dirname(php), 'php.ini'), dataDir);
      session.defaultSession.webRequest.onBeforeSendHeaders((details, callback) => {
        if (new URL(details.url).origin === server.origin) {
          details.requestHeaders['X-Atelier-Session'] = server.secret;
        }
        callback({ requestHeaders: details.requestHeaders });
      });
      session.defaultSession.webRequest.onBeforeRequest((details, callback) => {
        callback({ cancel: new URL(details.url).origin !== server.origin });
      });
      session.defaultSession.setPermissionRequestHandler((_contents, _permission, callback) => callback(false));
      const window = new BrowserWindow({
        width: 1280, height: 900,
        webPreferences: { contextIsolation: true, nodeIntegration: false, sandbox: true },
      });
      window.webContents.setWindowOpenHandler(() => ({ action: 'deny' }));
      window.webContents.on('will-navigate', (event, url) => {
        if (new URL(url).origin !== server.origin) event.preventDefault();
      });
      server.child.on('exit', () => {
        if (!window.isDestroyed()) { dialog.showErrorBox('Serveur arrêté', 'Le serveur local s’est arrêté. Ferme puis rouvre l’application.'); window.close(); }
      });
      await window.loadURL(server.origin + '/');
    } catch (error) {
      dialog.showErrorBox('Démarrage impossible', error.message);
      app.quit();
    }
  });
}
app.on('window-all-closed', () => app.quit());
app.on('before-quit', () => { if (server) server.stop(); });

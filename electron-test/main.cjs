const { app, BrowserWindow } = require('electron');
const appUrl = process.env.ATELIER_MOSAIQUE_URL || 'http://localhost/atelier-mosaique/public/';

if (require('electron-squirrel-startup')) app.quit();

function createWindow() {
  const window = new BrowserWindow({
    width: 1280,
    height: 900,
    webPreferences: {
      contextIsolation: true,
      nodeIntegration: false,
    },
  });

  window.loadURL(appUrl);
}

app.whenReady().then(() => {
  createWindow();

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

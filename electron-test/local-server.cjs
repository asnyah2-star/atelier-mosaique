const { spawn } = require('node:child_process');
const net = require('node:net');
const http = require('node:http');
const { randomBytes } = require('node:crypto');
const path = require('node:path');

async function startServer(php, root, router, ini) {
  const port = await new Promise((resolve, reject) => {
    const socket = net.createServer();
    socket.once('error', reject);
    socket.listen(0, '127.0.0.1', () => {
      const selected = socket.address().port;
      socket.close(() => resolve(selected));
    });
  });
  const secret = randomBytes(32).toString('hex');
  const origin = `http://127.0.0.1:${port}`;
  const phpArgs = ini ? ['-c', ini] : [];
  const child = spawn(php, [...phpArgs, '-S', `127.0.0.1:${port}`, '-t', root, router], {
    cwd: path.dirname(php),
    windowsHide: true,
    stdio: ['ignore', 'ignore', 'pipe'],
    env: { ...process.env, ATELIER_SESSION_SECRET: secret },
  });
  // Drain PHP's error stream; never expose paths or local data in the UI.
  child.stderr.on('data', () => {});
  let failed = false;
  child.on('error', () => { failed = true; });
  child.on('exit', () => { failed = true; });
  const stop = () => { if (child.exitCode === null) child.kill(); };
  try {
    for (let attempt = 0; attempt < 80; attempt++) {
      if (failed) throw new Error('Le serveur PHP ne démarre pas.');
      const ready = await new Promise(resolve => {
        const request = http.get(origin + '/', { headers: { 'X-Atelier-Session': secret }, timeout: 500 }, response => {
          response.resume();
          resolve(response.statusCode === 200 && response.headers['x-atelier-session'] === secret);
        });
        request.on('timeout', () => request.destroy());
        request.on('error', () => resolve(false));
      });
      if (ready && !failed) return { origin, secret, stop, child };
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    throw new Error('L’application PHP ne répond pas correctement.');
  } catch (error) { stop(); throw error; }
}
module.exports = { startServer };

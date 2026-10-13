import { execSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const mycabDir = path.join(root, 'dev', 'mycab');
const frontendDir = path.join(mycabDir, 'frontend');
const legacyAssetsDir = path.join(frontendDir, 'legacy', 'assets');
const assetsDir = path.join(mycabDir, 'assets');
const basePath = '/dev/mycab';
const mode = (process.env.MYCAB_BUILD || 'legacy').toLowerCase();

const htaccess = `# SPA: send index.html for client routes (fixes blank page on refresh).
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase ${basePath}/
  RewriteRule ^index\\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . index.html [L]
</IfModule>
`;

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function copyDirContents(from, to) {
  ensureDir(to);
  for (const name of fs.readdirSync(from)) {
    fs.copyFileSync(path.join(from, name), path.join(to, name));
  }
}

function buildIndexHtml(jsFile, cssFile) {
  return `<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HimCab — Book a ride</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,500&display=swap" rel="stylesheet" />
    <script type="module" crossorigin src="${basePath}/assets/${jsFile}"></script>
    <link rel="stylesheet" crossorigin href="${basePath}/assets/${cssFile}">
  </head>
  <body>
    <div id="root"></div>
  </body>
</html>
`;
}

function pickAssetFiles(dir) {
  const files = fs.readdirSync(dir);
  const js = files.find((f) => f.endsWith('.js'));
  const css = files.find((f) => f.endsWith('.css'));
  if (!js || !css) {
    throw new Error(`Expected one .js and one .css in ${dir}`);
  }
  return { js, css };
}

function writeDeployFiles(jsFile, cssFile) {
  fs.writeFileSync(path.join(mycabDir, 'index.html'), buildIndexHtml(jsFile, cssFile));
  fs.writeFileSync(path.join(mycabDir, '.htaccess'), htaccess);
}

function buildLegacy() {
  if (!fs.existsSync(legacyAssetsDir)) {
    console.error('\nERROR: Missing frontend/legacy/assets/. Copy current production assets there first.\n');
    process.exit(1);
  }

  const { js, css } = pickAssetFiles(legacyAssetsDir);
  copyDirContents(legacyAssetsDir, assetsDir);
  writeDeployFiles(js, css);

  console.log('\n✓ HimCab deploy files updated (legacy mode)');
  console.log('  Source: dev/mycab/frontend/legacy/assets/');
  console.log('  Output: dev/mycab/assets/, index.html, .htaccess');
  console.log('  JS:    ', js);
  console.log('  CSS:   ', css);
  console.log('\nFTP: upload dev/mycab/ except api/vendor — keep server .env\n');
}

function buildVite() {
  const apiUrl = process.env.VITE_API_URL || 'https://craftoweb.com/dev/mycab/api/api';
  const distDir = path.join(frontendDir, 'dist');

  console.log('Building HimCab frontend with Vite...\n');
  console.log('  VITE_API_URL:', apiUrl, '\n');

  if (!fs.existsSync(path.join(frontendDir, 'node_modules'))) {
    execSync('npm install', { cwd: frontendDir, stdio: 'inherit' });
  }

  execSync('npx vite build', {
    cwd: frontendDir,
    stdio: 'inherit',
    env: { ...process.env, VITE_API_URL: apiUrl },
  });

  const distAssets = path.join(distDir, 'assets');
  if (!fs.existsSync(distAssets)) {
    console.error('\nERROR: Vite build did not produce dist/assets/\n');
    process.exit(1);
  }

  copyDirContents(distAssets, assetsDir);

  const indexPath = path.join(distDir, 'index.html');
  let html = fs.readFileSync(indexPath, 'utf8');

  if (html.includes('/src/main')) {
    console.error('\nERROR: Build still points to /src/main — fix vite build.\n');
    process.exit(1);
  }

  // Rewrite asset paths for subfolder deploy.
  html = html.replace(/(src|href)="\/assets\//g, `$1="${basePath}/assets/`);

  const jsMatch = html.match(/src="([^"]+\.js)"/);
  const cssMatch = html.match(/href="([^"]+\.css)"/);
  if (!jsMatch || !cssMatch) {
    console.error('\nERROR: Built index.html has no asset references.\n');
    process.exit(1);
  }

  const jsFile = path.basename(jsMatch[1]);
  const cssFile = path.basename(cssMatch[1]);
  writeDeployFiles(jsFile, cssFile);

  console.log('\n✓ HimCab deploy files updated (vite mode)');
  console.log('  JS:    ', jsFile);
  console.log('  CSS:   ', cssFile);
  console.log('\nFTP: upload dev/mycab/ except api/vendor — keep server .env\n');
}

console.log(`HimCab build (${mode} mode)\n`);

if (mode === 'vite') {
  buildVite();
} else if (mode === 'legacy') {
  buildLegacy();
} else {
  console.error('\nERROR: Unknown MYCAB_BUILD value. Use "legacy" or "vite".\n');
  process.exit(1);
}

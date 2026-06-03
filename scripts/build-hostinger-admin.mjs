import { execSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const dashboardDir = path.join(root, 'dashboard-Backend');
const outDir = path.join(root, 'admin');

const apiUrl = process.env.VITE_API_URL || 'https://api.craftoweb.com/api';

const htaccess = `<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^ index.html [L]
</IfModule>
`;

console.log('Building admin dashboard for Hostinger (admin/)...\n');
console.log('  VITE_API_URL:', apiUrl, '\n');

execSync('npx vite build --outDir ../admin --emptyOutDir', {
  cwd: dashboardDir,
  stdio: 'inherit',
  env: { ...process.env, VITE_API_URL: apiUrl },
});

fs.writeFileSync(path.join(outDir, '.htaccess'), htaccess);

const indexPath = path.join(outDir, 'index.html');
const html = fs.readFileSync(indexPath, 'utf8');

if (html.includes('/src/main.jsx')) {
  console.error('\nERROR: Build still points to /src/main.jsx — fix vite build.\n');
  process.exit(1);
}

if (!html.includes('/assets/')) {
  console.error('\nERROR: Built index.html has no /assets/ scripts.\n');
  process.exit(1);
}

console.log('\n✓ Admin dashboard ready in: admin/');
console.log('  index.html uses:', html.match(/src="([^"]+)"/)?.[1] || 'unknown');
console.log('\nHostinger: subdomain admin.craftoweb.com → public_html/admin\n');

import { execSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const outDir = path.join(root, 'hostinger-public');

console.log('Building production site for Hostinger...\n');

execSync('npx vite build --outDir hostinger-public --emptyOutDir', {
  cwd: root,
  stdio: 'inherit',
});

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

console.log('\n✓ Production build ready in: hostinger-public/');
console.log('  index.html uses:', html.match(/src="([^"]+)"/)?.[1] || 'unknown');
console.log('\nGit push ke baad Hostinger par public_html/index.html (dev wala) DELETE karein.');
console.log('  Ya sirf hostinger-public/ folder ki files public_html root par copy karein.\n');

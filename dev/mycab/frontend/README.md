# HimCab frontend — edit & deploy

Yeh folder `dashboard-Backend` jaisa **source** hai. Build output `dev/mycab/` mein jaata hai (FTP upload folder).

## Folder layout

```
frontend/
  legacy/assets/     ← Abhi yahan se production build (purana minified app)
  src/               ← Naya Vite + React source (migration chal rahi hai)
  dist/              ← Vite build output (git ignore)
  package.json
  vite.config.js
```

**Important:** `dev/mycab/api/` Laravel backend hai — build script usko touch nahi karti.

## Abhi ka workflow (legacy — recommended)

Landing / signup labels wagairah edit karne ke liye:

1. Edit karo: `frontend/legacy/assets/index-1e7gYsYw.js` (aur CSS agar chahiye)
2. Root se build:
   ```bash
   npm run build:hostinger-mycab
   ```
3. FTP se upload karo `dev/mycab/` → server `public_html/dev/mycab/`
   - Upload: `index.html`, `assets/`, `.htaccess`
   - **Mat upload karo:** `api/vendor/`, local `.env`

4. Browser hard refresh: `Ctrl+Shift+R`

## Baad mein — poora Vite source

Jab `src/` mein poora app migrate ho:

```bash
cd dev/mycab/frontend
copy .env.production.example .env.production
npm install
npm run dev
```

Production build (PowerShell):

```powershell
$env:MYCAB_BUILD='vite'
npm run build:hostinger-mycab
```

Ya repo root se:

```powershell
$env:MYCAB_BUILD='vite'; npm run build:hostinger-mycab
```

## Local API

```bash
cd dev/mycab/api
php artisan serve
```

Frontend dev (alag terminal):

```bash
cd dev/mycab/frontend
copy .env.development.example .env.development
npm install
npm run dev
```

Open: http://localhost:5175/dev/mycab/

## Scripts (repo root)

| Command | Kya karta hai |
|---------|----------------|
| `npm run build:hostinger-mycab` | Legacy assets → `dev/mycab/` deploy files |
| `MYCAB_BUILD=vite npm run build:hostinger-mycab` | Vite build → `dev/mycab/` |

## Production URL

- App: https://craftoweb.com/dev/mycab
- API: https://craftoweb.com/dev/mycab/api/api/...

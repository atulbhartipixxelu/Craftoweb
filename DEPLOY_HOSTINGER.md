# Hostinger par CraftoWeb deploy (white screen fix)

## Problem (aapka case)

**View Source** mein yeh dikhe to galat deploy hai:

```html
<script type="module" src="/src/main.jsx"></script>
```

Yeh **development** `index.html` hai. Live par **`/assets/index-xxxxx.js`** hona chahiye.

---

## Turant fix (5 minute)

### A) Local build + Git push

```bash
cd d:\Craftweb
npm install
npm run build:hostinger
git add hostinger-public .htaccess
git commit -m "Add production build for Hostinger"
git push origin main
```

Hostinger Git pull hone do (2–5 min).

### B) Hostinger File Manager

`public_html` mein jao:

1. **`index.html` DELETE karo** (jo `/src/main.jsx` wala hai) — **bahut zaroori**
2. Check karo `hostinger-public/` folder hai jisme `index.html` + `assets/` + `.htaccess` ho
3. Root par repo wala **`.htaccess`** bhi hona chahiye (assets redirect ke liye)

### C) Ya seedha copy

`hostinger-public` ki **saari** files `public_html` **root** par copy/paste karo (overwrite).

---

## Problem (general)

Local par site chalti hai, live par **sirf white background** — iska matlab:

1. **Galat files upload** — `src/` wala source code deploy ho gaya, build nahi
2. Ya **JS/CSS load nahi** ho rahi (404 on `/assets/...`)
3. Ya **SPA routing** ke liye `.htaccess` missing hai

`index.html` mein `/src/main.jsx` sirf **development** ke liye hai. Production par **`npm run build:hostinger`** ke baad `hostinger-public/` folder chahiye.

---

## Sahi tareeka (step by step)

### 1. Local par build karein

```bash
cd d:\Craftweb
npm install
npm run build
```

`dist` folder banega — iske andar:
- `index.html`
- `assets/` (JS + CSS)
- `.htaccess`
- `favicon.svg`

### 2. Hostinger `public_html` mein **sirf `dist` ki files**

File Manager ya FTP se:

- `dist/index.html` → `public_html/index.html`
- `dist/assets/` → `public_html/assets/`
- `dist/.htaccess` → `public_html/.htaccess`
- `dist/favicon.svg` → `public_html/favicon.svg`

**Repo root (`src/`, `package.json`) public_html mein mat rakhein** — sirf build output.

### 3. GitHub se auto-deploy

Agar Git se pull ho raha hai aur `dist` **gitignore** mein hai (default), to server par build files **nahi aati** → white screen.

**Option A — Hostinger build (recommended)**

hPanel → **Websites** → **Manage** → **Advanced** → **Git** / **Deployment**:

| Setting | Value |
|---------|--------|
| Build command | `npm install && npm run build` |
| Output / Publish directory | `dist` |
| Node version | 18+ |

**Option B — Manual**

Har update par local `npm run build`, phir `dist` ki files upload.

**Option C — GitHub Actions**

Repo mein workflow se build karke FTP upload (FTP credentials chahiye).

---

## Check karne ke liye (browser)

Live site par **F12 → Console** kholo:

| Error | Fix |
|-------|-----|
| `Failed to load module script` / 404 on `/assets/index-xxx.js` | `dist` upload karo, root `index.html` mat |
| 404 on `/src/main.jsx` | Source deploy ho gaya — `npm run build` + `dist` use karo |
| Blank + no errors | `.htaccess` add karo (build ke baad `dist/.htaccess` copy karo) |

**Network** tab: `index-....js` aur `index-....css` status **200** hona chahiye.

---

## Subfolder par deploy

Agar site `yoursite.com/craftoweb/` par hai:

```bash
# .env.production file banao:
VITE_BASE_URL=/craftoweb/
npm run build
```

Phir `dist` ki files `public_html/craftoweb/` mein upload karo.

---

## Domain root (craftoweb.com)

`.env.production`:

```env
VITE_BASE_URL=/
```

```bash
npm run build
```

---

## Dashboard / API

- **Marketing site** = repo root → `dist` → `public_html`
- **Admin dashboard** = alag subdomain/folder (`dashboard-Backend` ka apna build)
- **Laravel API** = alag subdomain, `api/public` — website ke `public_html` se mix mat karo

# CraftoWeb

Monorepo for **CraftoWeb.com** (marketing site) and the **admin dashboard**, powered by a Laravel API.

## Structure

| Path | Description |
|------|-------------|
| `src/` | Public website (React + Vite) — Home, About, Services, Blog, Reviews, Contact |
| `dashboard-Backend/` | Admin dashboard — projects, daily updates, mockups, users |
| `api/` | Laravel API + Sanctum auth |

> `craftoweb-frontend` and `dashboard-frontend` folders were removed; the marketing app lives at the **repo root**.

## Run locally

**1. API**

```bash
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**2. Marketing website** (port 5173)

```bash
npm install
npm run dev
```

Open http://localhost:5173

**3. Admin dashboard** (port 5174)

```bash
cd dashboard-Backend
npm install
npm run dev
```

Open http://localhost:5174 — login with seeded super admin (see `api/CRAFTOWEB_SETUP.md`).

## Deploy admin dashboard (Git)

Push to `main` — GitHub Action builds `admin/` for Hostinger (`admin.craftoweb.com` → `public_html/admin`). See [DEPLOY_HOSTINGER.md](DEPLOY_HOSTINGER.md).

## Backend CORS

The API allows both frontends. Configure in `api/.env`:

```env
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173,http://localhost:5174,http://127.0.0.1:5174
```

Details: [api/CRAFTOWEB_SETUP.md](api/CRAFTOWEB_SETUP.md)

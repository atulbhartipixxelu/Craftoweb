# CraftoWeb API — Setup

Laravel API for the **admin dashboard** (`dashboard-Backend`). The public marketing website runs from the **repo root** (formerly `craftoweb-frontend`).

## Project layout

```
Craftweb/
├── api/                  ← Laravel API (this folder)
├── dashboard-Backend/    ← React admin dashboard
├── src/                  ← CraftoWeb.com marketing site (React + Vite)
├── package.json          ← Marketing site dependencies
└── vite.config.js
```

## Ports (local)

| App | Folder | URL |
|-----|--------|-----|
| Marketing website | repo root | http://localhost:5173 |
| Admin dashboard | `dashboard-Backend` | http://localhost:5174 |
| Laravel API | `api` | http://127.0.0.1:8000 |

## First-time API setup

```bash
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Environment (`.env`)

Copy from `.env.example` and ensure:

```env
APP_URL=http://127.0.0.1:8000
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173,http://localhost:5174,http://127.0.0.1:5174
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,localhost:5174,127.0.0.1,127.0.0.1:5173,127.0.0.1:5174
```

After changing `.env`, restart `php artisan serve`.

## Dashboard ↔ API

The dashboard uses `VITE_API_URL=/api` and Vite proxies `/api` → `http://127.0.0.1:8000` (see `dashboard-Backend/vite.config.js`).

## Default super admin (after seed)

- Email: `atulbhartipixxelu@gmail.com`
- Password: `atulbhartipixxelu#321`

## API routes (protected with `auth:sanctum`)

- `POST /api/login`, `POST /api/logout`, `GET /api/me`
- `GET /api/dashboard/stats`
- `apiResource` projects, users (super admin), daily-updates, mockups

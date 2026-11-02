# Xuan Jinyu — Portfolio

A Laravel, Inertia, and Svelte portfolio with an admin CMS, media library, and SEO-ready public pages.

Live site: [xuanjinyu.codes](https://xuanjinyu.codes)

## Stack

- Laravel 13, PHP 8.3+ (production runs PHP 8.5)
- Inertia.js 3, Svelte 5, and TypeScript
- Tailwind CSS 4, bits-ui, and Lucide icons
- Laravel Fortify for authentication
- Laravel Wayfinder for typed frontend routes
- Pest 5, Larastan, and Pint

## Requirements

- PHP 8.3 or newer with the usual Laravel extensions
- Composer 2
- Node.js 22 or newer and npm
- SQLite by default; MySQL, MariaDB, or PostgreSQL also supported

## Local setup

```bash
git clone https://github.com/suwantara/portolio.git
cd portolio
composer run setup
php artisan storage:link
composer run dev
```

The app should be available at `http://localhost:8000`.

`composer run setup` installs PHP and JavaScript dependencies, creates `.env` from `.env.example`, generates an application key, runs the SQLite migrations, and builds the frontend. `composer run dev` starts the PHP server, queue listener, log viewer, and Vite together.

For a manual setup instead:

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

## Admin and CMS

Public registration is intentionally disabled and the site is single-admin. To create the administrator account:

```bash
php artisan app:create-admin admin@example.com --name="Admin"
```

The command prompts for a password, or accepts `--password=...`. The password must be at least 12 characters with mixed case, numbers, and symbols. Administrators are redirected to `/settings/portfolio` after login and can edit the portfolio, gallery, media, and social links there.

Portfolio defaults live in `config/portfolio.php`; saved content is stored in the `portfolio_settings` table. Uploaded media is written to `storage/app/public/portfolio`, which is why `php artisan storage:link` is required for local media URLs.

## SEO

The application exposes:

- `/sitemap.xml`
- `/robots.txt`
- `/llms.txt`

Page metadata, canonical URLs, and social sharing tags are rendered through the shared SEO components. Update the production `APP_URL` so generated URLs are correct.

## Quality checks

```bash
npm run check
npm run types:check
npm run build
php artisan test --compact
```

Or run the full project check:

```bash
composer test
```

## Deployment

Production deploys from a deployment branch; use `deploy/dokploy` for Dokploy or `deploy/railway` for Railway. The `main` branch intentionally excludes Docker and deployment files so it stays portable and can be cloned for local development without deployment configuration.

The shared Docker setup contains:

- `Dockerfile` with PHP-FPM, Nginx, and Supervisor
- `docker/` runtime configuration

Dokploy adds `docker-compose.yml`, `docker/healthcheck.sh`, and `.env.dokploy.example`. Railway adds `railway.json` and `.env.railway.example`.

### Dokploy

1. Create a **Docker Compose** service from this repository, with branch `deploy/dokploy` and compose path `docker-compose.yml`.
2. Add the environment variables from `.env.dokploy.example`. At minimum set:
   - `APP_KEY` — generate with `php artisan key:generate --show`
   - `APP_URL` — the public HTTPS URL
   - `DB_PASSWORD` — a strong password, set before the first deploy
   - keep `INERTIA_SSR_ENABLED=false` because there is no SSR service
   - keep `QUEUE_CONNECTION=sync` because there is no queue worker
3. Add a domain to the `web` service on port `80`, enable HTTPS/Let's Encrypt, and point the DNS A record at the VPS.
4. Deploy. The entrypoint runs migrations and starts Nginx plus PHP-FPM; the healthcheck uses `/up`.
5. Create the admin from the Compose **Terminal** (select the `web` service):

   ```bash
   php artisan app:create-admin admin@example.com --name="Admin"
   ```

6. Persist and back up the named volumes:
   - `storage` → `/var/www/html/storage/app/public` (uploads)
   - `postgres-data` → `/var/lib/postgresql` (PostgreSQL 18)

   Configure **Volume Backups** to an S3 destination, or take a logical database backup:

   ```bash
   docker exec <postgres-container> sh -c 'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" -Fc' > portolio.dump
   ```

7. `SESSION_SECURE_COOKIE=true` requires HTTPS. If testing over plain HTTP, set it to `false` temporarily.

PostgreSQL 18 uses `PGDATA=/var/lib/postgresql/18/docker` and mounts the volume at `/var/lib/postgresql`. Do not reuse a PostgreSQL 17 data volume; dump and restore when changing major versions.

### Railway

For Railway, provide the environment variables listed in `.env.railway.example`, set `APP_URL` to the public HTTPS URL, mount a persistent volume at `/var/www/html/storage/app/public`, and keep the custom start command empty so the image entrypoint can run migrations and start the services.

Never commit `.env`, application keys, database credentials, or other secrets.

## License

MIT

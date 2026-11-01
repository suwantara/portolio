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

Public registration is intentionally disabled. To create a local administrator:

```bash
php artisan tinker --execute "App\Models\User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@example.com']);"
```

The factory password is `password`; change it after signing in. Administrators are redirected to `/settings/portfolio` after login and can edit the portfolio, gallery, media, and social links there.

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

Production deploys from the `deploy/railway` branch. The `main` branch intentionally excludes Docker and Railway files so it stays portable and can be cloned for local development without deployment configuration.

The deployment branch contains:

- `Dockerfile` with PHP-FPM, Nginx, and Supervisor
- `docker/` runtime configuration
- `railway.json`
- `.env.railway.example`

For Railway, provide the environment variables listed in `.env.railway.example`, set `APP_URL` to the public HTTPS URL, mount a persistent volume at `/var/www/html/storage/app/public`, and keep the custom start command empty so the image entrypoint can run migrations and start the services.

Never commit `.env`, application keys, database credentials, or other secrets.

## License

MIT

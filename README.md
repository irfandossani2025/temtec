# Temtec: Client Registration & Service Requests

Clients register with their company details and choose the services they're interested in. Each request is saved, emailed to the sales team, and shown in the admin panel.

**Stack:** Laravel 12 · PHP 8.2+ · PostgreSQL 10+ · Blade + Tailwind (Breeze) · Filament 5 admin panel

## Features

| Area | URL | What it does |
|---|---|---|
| Registration | `/register` | Company name, contact name, job title, email, phone, website, address/city/country, services (checkboxes), notes, password |
| Client dashboard | `/dashboard` | Company details and past requests |
| Request more services | `/service-requests/create` | Existing clients can submit new requests |
| Profile | `/profile` | Edit company and contact details, change password |
| Admin panel | `/admin` | **Service Requests** (inbox with New/Contacted/Qualified/Won/Lost status and internal notes), **Clients & users**, **Services** (add, edit, hide, reorder) |

**Emails** (sent immediately; no queue worker is needed):
- To every address in `SALES_EMAILS`: the full client details and the services they picked. Reply-To is set to the client.
- To the client: a confirmation.

If an email fails to send, the request is still saved and the error is written to `storage/logs/laravel.log`.

## Local development

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # seeds placeholder services
php artisan temtec:create-admin you@example.com "Your Name"
php artisan serve
```

Run tests with `php artisan test`.

> `composer.json` pins `config.platform.php` to **8.2.0** so that dependencies always match the server, even if your machine runs a newer PHP. Keep that setting.

## Deploying to Plesk (no SSH)

The server can't run `npm` (its Node 17 is too old for Vite), and there's no SSH. So:

- **Front-end assets are built locally and committed** (`public/build/`). Run `npm run build` and commit the result whenever you change Blade/CSS/JS.
- **Filament's assets** are already published in `public/css/filament` and `public/js/filament`, and are committed too.
- **Composer and Artisan** run from the Plesk UI.

### First-time setup

1. **Database:** in Plesk → *Databases*, create a **PostgreSQL** database and user. The Database server dropdown also offers MariaDB, but the server's version (10.1) is older than Laravel 12 supports (10.3+), so don't use it. Plesk requires the user name to differ from the database name, for example `Mugdi_temtec` and `Mugdi_temtec_user`.
2. **Get the code:** in Plesk → *Laravel* (Laravel Toolkit) → *Install Application*, choose the Git repository option and enter `https://github.com/irfandossani2025/temtec.git`, branch `main`. The repository is public, so no deploy key is needed. If it's made private later, add the SSH key that Plesk shows as a read-only **Deploy key** on GitHub.
   - In the Toolkit's **Deployment** tab, untick **"Install package.json dependencies"**. The server's Node 17 can't build this project, and the built files are already in the repo. Leave **"Install composer.json dependencies"** ticked. The Toolkit can't run a custom deployment script without SSH, so migrations are run by hand (see *Updating*).
3. **Document root:** the Toolkit normally sets this to `.../public` itself. Check it in *Hosting Settings*: it must end in `/public`. This matters: it stops `.env` and the source code from being publicly readable.
4. **PHP:** select PHP 8.2 or newer in *PHP Settings* (the live site runs 8.4). The required extensions are pdo_pgsql, mbstring, openssl, intl, fileinfo and zip.
5. **Dependencies:** in Laravel Toolkit → *Composer* (or the *PHP Composer* extension), run `install --no-dev --optimize-autoloader`.
   - *If Composer isn't available on the host:* run `composer install --no-dev --optimize-autoloader` locally, then upload the `vendor/` folder with the Plesk File Manager or FTP.
6. **`.env`:** the Toolkit creates `.env` from `.env.example`. Edit it in Laravel Toolkit → *Dashboard* → *Environment variables* → **Edit**, and set:
   ```ini
   APP_NAME=Temtec
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=pgsql
   DB_HOST=localhost
   DB_PORT=5432
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...

   MAIL_MAILER=smtp
   MAIL_HOST=your-domain.com      # Plesk mail server
   MAIL_PORT=465
   MAIL_SCHEME=smtps
   MAIL_USERNAME=noreply@your-domain.com
   MAIL_PASSWORD=...
   MAIL_FROM_ADDRESS=noreply@your-domain.com

   SALES_EMAILS=sales@your-domain.com,manager@your-domain.com
   ```
7. **Artisan:** run these from Laravel Toolkit → *Artisan*, in order:
   - `key:generate --force`
   - `migrate --force`
   - `db:seed --force` (placeholder services; edit them in `/admin`)
   - `temtec:create-admin sales@your-domain.com Sales`. This prints a generated password. Change it after your first login. The Toolkit's Artisan box doesn't accept quoted arguments, so use a one-word name and rename the user in `/admin` if needed.
   - `storage:link`
   - `optimize`
8. **Permissions:** make sure `storage/` and `bootstrap/cache/` are writable by the site user. Plesk normally sets this up already.

### Updating

1. Locally: make your changes, run `npm run build` if any views or CSS changed, run `php artisan test`, then commit and push.
2. In Plesk: Laravel Toolkit → *Deployment* → **Deploy**. This pulls the code and runs Composer.
3. Run the Artisan commands `migrate --force` and `optimize`.

### Admin accounts

- Promote or create an admin: `temtec:create-admin email@domain.com`.
- Or, from `/admin` → *Clients & users*: edit a user and turn on **Admin / sales team access**.
- Admins can't remove their own admin access or delete their own account from the panel, so nobody can lock themselves out by accident.

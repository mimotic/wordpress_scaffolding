<p align="center"><a href="https://mimotic.com" target="_blank"><img src="https://mimotic.com/wp-content/uploads/2024/09/logo_mimotic.svg" width="200" alt="Mimotic Logo"></a></p>

## About this WordPress Scaffolding

This application is made with WordPress and elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Mimotic takes the pain out of development by easing common tasks used in many web projects.

❤️ love every bit

---

## Security Configuration

This scaffolding includes security hardening via `mu-scripts/`. **By default, the most restrictive configuration is applied.** You can relax settings by adding variables to your `.env` file.

### REST API Security

| Variable | Default | Description |
|----------|---------|-------------|
| `WP_REST_ALLOW_USERS` | `false` | Block `/wp/v2/users` endpoints to prevent username enumeration |
| `WP_REST_PUBLIC_ACCESS` | `false` | Block all public REST API access (only logged-in users can access) |

```bash
# .env - Examples to RELAX security (not recommended)

# Allow public access to user endpoints
WP_REST_ALLOW_USERS=true

# Allow public REST API access
WP_REST_PUBLIC_ACCESS=true
```

**Whitelisted routes** (always accessible without login):
- `/wp-json/contact-form-7/*`
- `/wp-json/mcp/*`

### SVG Upload

| Variable | Value | Description |
|----------|-------|-------------|
| `WP_ALLOW_SVG` | not defined | SVG uploads **NOT allowed** |
| `WP_ALLOW_SVG` | `false` | SVG uploads **NOT allowed** |
| `WP_ALLOW_SVG` | `true` | SVG uploads allowed for **all users** |
| `WP_ALLOW_SVG` | `admin` | SVG uploads allowed **only for administrators** |

```bash
# .env - Enable SVG uploads for admins only (recommended if needed)
WP_ALLOW_SVG=admin
```

### Optional Security (disabled by default)

| Variable | Default | Description |
|----------|---------|-------------|
| `WP_JQUERY_MIGRATE` | `false` | Keep jQuery Migrate for legacy plugins |
| `WP_SHOW_ASSET_VERSIONS` | `false` | Keep `?ver=` on CSS/JS URLs for cache busting |

```bash
# .env - Enable legacy compatibility (if needed)
WP_JQUERY_MIGRATE=true
WP_SHOW_ASSET_VERSIONS=true
```

### Always Active Security (no configuration needed)

These protections are **always enabled** and cannot be disabled:

| Protection | File | Description |
|------------|------|-------------|
| XML-RPC disabled | `xmlrpc.php` | Prevents brute force and DDoS attacks |
| Pingback disabled | `pingback.php` | Removes X-Pingback headers |
| Feeds disabled | `feeds.php` | Disables RSS/Atom feeds |
| Version fingerprint removed | `removeMetaTags.php` | Removes WP version from meta tags |

### Nginx Security Setup on Server
- Link Mimotic nginx snippets inside your host config (adjust paths for the server user/site):
  ```yml
  # MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
  include /home/user/site/nginx/before/*;
  [...]
  # MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
  include /home/user/site/nginx/after/*;
  ```
---



> ### Remember:
> - Install composer and npm in docker way, please follow the instructions.
> 
> - Activate all plugins

> ⚠️ Contributors must follow setup instructions to ensure the same behavior as production

## ⭐ Install and setup Project:

## 0. Download Repository
### 0.1. clone project
At least needs repository read permissions
```bash
git clone git@bitbucket.org:mimotic/scaffolding-wp.git
cd scaffolding-wp
```
## 1. Setup

### 1.1 Copy and configure `.env`
```bash
cp .env.example .env
```
Update credentials plus the runtime block:
```
PHP_VERSION=8.4   # Supported: 8.1 / 8.2 / 8.3 / 8.4
NODE_VERSION=20   # Bundled Node version
WWWUSER=1000      # Host UID (prevents permission issues)
WWWGROUP=1000     # Host GID
```
> Need an older PHP (7.x/5.x) runtime for a legacy site? Fork the Dockerfile and install those packages explicitly—this scaffold focuses on actively supported versions only.
> WordPress uses MySQL 8 (`mysql/mysql-server:8.0`) as the bundled database; Postgres is not included.

### 1.2 Build and start the stack
```bash
docker compose up -d --build
```
This single command provisions nginx, PHP-FPM, MySQL 8, Composer, Node.js, npm, and WP-CLI for the chosen PHP version.

### 1.3 Install PHP dependencies inside the container
```bash
docker compose exec mimotic.test composer install --ignore-platform-reqs
```

### 1.4 Install theme assets (as needed)
Enter the container and install/build from your active theme directory:
```bash
docker compose exec mimotic.test bash -lc "cd public/wp-content/themes/<theme> && npm install && npm run dev"
```

### 1.5 (First run) Flush rewrites and cache
```bash
docker compose exec mimotic.test wp rewrite flush --path=public
docker compose exec mimotic.test wp cache flush --path=public
```

### 1.6 Browse the site
Visit http://localhost/ once the stack is healthy.

---

## 2. Development



### Common commands
- Bootstrap everything in one go: `./sh/boot.sh`
- Rebuild containers: `docker compose up -d --build`
- SSH into the runtime: `docker compose exec mimotic.test bash`
- Run WP-CLI from the project root: `docker compose exec mimotic.test wp --path=public <command>`
- Run WP-CLI from inside `public/`: `docker compose exec --user=mimotic -w /var/www/html/public mimotic.test wp <command>`
- Stop the stack: `docker compose down`

---

#### made with ❤ ️

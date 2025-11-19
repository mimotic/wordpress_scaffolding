<p align="center"><a href="https://mimotic.com" target="_blank"><img src="https://mimotic.com/wp-content/uploads/2024/09/logo_mimotic.svg" width="200" alt="Mimotic Logo"></a></p>


## About this WordPress Scaffolding 

This application is made with WordPress and elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Mimotic takes the pain out of development by easing common tasks used in many web projects.

❤️ love every bit

> ### Remember:
> - Also read specific readme on theme folder
> 
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

### Theme Setup
- Security helpers load via the mu-plugin `public/wp-content/mu-plugins/mimotic-security.php`, which requires `mu-scripts/init.php`. Keep both paths intact so security tweaks stay active even if the active theme changes.
- Link Mimotic nginx snippets inside your host config (adjust paths for the server user/site):
  ```yml
  # MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
  include /home/user/site/nginx/before/*;
  [...]
  # MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
  include /home/user/site/nginx/after/*;
  ```

### Common commands
- Bootstrap everything in one go: `./sh/boot.sh`
- Rebuild containers: `docker compose up -d --build`
- SSH into the runtime: `docker compose exec mimotic.test bash`
- Run WP-CLI from the project root: `docker compose exec mimotic.test wp --path=public <command>`
- Run WP-CLI from inside `public/`: `docker compose exec --user=mimotic -w /var/www/html/public mimotic.test wp <command>`
- Stop the stack: `docker compose down`

---

#### made with ❤ ️

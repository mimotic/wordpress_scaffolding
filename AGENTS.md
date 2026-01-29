# Repository Guidelines

## Project Structure & Module Organization
`public/` is the docroot; Docker, nginx, scripts, and logs stay outside the web root. Code lives in `public/wp-content/`, while `nginx/` mounts into Forge before/after includes to inject headers. `storage/logs/` holds the `wp-config.php` debug logs; keep it writable and out of Git. Secrets flow through `.env` via `dotEnvReader()`; configure `.env` (PHP/NODE versions, PHP supports 8.1–8.4) before booting Docker.

## Build, Test, and Development Commands
`docker compose up -d --build` provisions nginx, PHP-FPM, MySQL, Composer, and Node using the `.env` version matrix. Run `./sh/boot.sh` (or the composer snippet) to install PHP packages. Reach WP CLI via `docker compose exec mimotic.test wp --path=public <command>` and rebuild assets from `public/wp-content/themes/<theme>/` with `npm install` then `npm run dev`/`npm run prod`.

## Coding Style & Naming Conventions
Use the WordPress PHP Coding Standards: four spaces, snake_case functions, PascalCase classes, and hook prefixes such as `mimotic_secure_headers`. Template partials stay inside `partials/` or `inc/` (leading underscores recommended) and shared front-end utilities go under `resources/`. Keep `.env` keys uppercase with `MIMOTIC_` prefix.

## Testing Guidelines
No automated suite exists, so every change requires QA in Docker. Scaffold WP PHPUnit tests (`wp scaffold plugin-tests <slug>`) and run them inside the container with `./vendor/bin/phpunit`; execute any `tests/e2e/` specs via `npm run test`. Flush `wp rewrite flush` + `wp cache flush` whenever routing or performance hooks change.

## Commit & Pull Request Guidelines
History shows conventional commits (`feat:`, `fix:`, `chore:`), so keep the `<type>: <imperative>` pattern and limit each commit to one logical change. Pull requests summarise the change, link the Bitbucket issue, list validation commands or screenshots, and note deployment actions (cache flush, asset rebuild). Run `docker compose up -d` and `npm run prod` before review.

## Security & Configuration Tips
Never commit `.env`, SQL dumps, or `storage/` data. The always-on plugin at `public/wp-content/mu-plugins/mimotic-security.php` requires `mu-scripts/init.php`, so keep that file in place to guarantee security hooks load even if the theme changes. Keep secrets in environment variables and flush caches after authentication or rewrite changes.

## Forge Deployment Notes
Deployments target Laravel Forge, so keep `.env` keys in sync with Forge-managed variables and respect the nginx snippets included from `nginx/before` and `nginx/after`. Snippets are symlinked into Forge’s nginx site to enforce headers—keep the guard comments. Set the Forge document root to `public/`. After merges Forge runs `composer install`, theme `npm run prod`, `wp cache flush`, and reloads PHP-FPM; mirror these steps before pushing.

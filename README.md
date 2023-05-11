<p align="center"><a href="https://mimotic.com" target="_blank"><img src="https://mimotic.com/wp-content/themes/mimotic/assets/img/logo.svg" width="200" alt="Mimotic Logo"></a></p>


## About this Wordpress Scaffolding 

---

This application is made with Wordpress and elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Mimotic takes the pain out of development by easing common tasks used in many web projects.

❤️ love every bit

> ### Remember:
> - Also read specific readme on theme folder
> 
> - Install composer and npm in docker way, please follow the instructions.
> 
> - Activate all plugins

> ⚠️ Contributors must follow setup instructions to ensure the same behavior as production

## ⭐ Install and setup Project:

---

## 0. Download Repository
### 0.1. clone project
At least needs repository read permissions
```bash
git clone git@bitbucket.org:mimotic/scaffolding-wp.git
cd scaffolding-wp
```
## 1. Setup 
### 1.1. create environment file
Copy `.env.example` to `.env` and fill fields.
```bash
$ cp .env.example .env
```

### 1.2. Build Docker Project from scratch
This command will install: nginx, php, mysql, composer, node and npm for you 
```bash
$ docker-compose up --build
```

### 1.3. Install composer dependencies inside docker

```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        composer install --ignore-platform-reqs
```

### 1.4. Run cache and rewrite clear for first time use
```bash
$ docker exec scaffolding-wp-mimotic.test-1 wp rewrite flush --path=public
```
```bash
$ docker exec scaffolding-wp-mimotic.test-1 wp cache flush --path=public
```

### 1.5. Launch Project
Now you can open your browser on ```http://localhost/```

---

## 2. Development
### Theme Setup

- Link mimotic's scaffolding mu-scripts to enhance security

```php
/**
 * Custom security functions.
 */
require dirname(ABSPATH) . '/mu-scripts/init.php';
```

- Link mimotic Nginx server config, please change user and site with proper config

```yml
# MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
include /home/user/site/nginx/before/*;

[...]
  
# MIMOTIC NGINX COFIGS (DO NOT REMOVE!)
include /home/user/site/nginx/after/*;
```

### Other Usefull info and commands

- All before steps together in one command:
  `$ ./sh/boot.sh`

- Container Name for this project:
`scaffolding-wp-mimotic.test-1`

- Shutdown Containers
`$ docker-compose down`

- Open Docker BASH:
`$ docker exec -it scaffolding-wp-mimotic.test-1 bash`
- WP CLI:
`$ docker exec --user=mimotic -itw /var/www/html/public/ scaffolding-wp-mimotic.test-1 sh -c "wp"`

### DEPLOYMENT CI/CD
- Needs repository write permission

- Autodeploy on push development or production branch

- Check deploy script compile all assets for production:

```bash
cd /home/user/site
git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader

cd /home/user/site/public/wp-content/themes/theme

npm install
npm run prod

wp cache flush

cd /home/user/site

( flock -w 10 9 || exit 1
echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

```

---

## wp-cli:

```bash
$ docker exec scaffolding-wp-mimotic.test-1 wp --info --path=public
```
OR
```bash
$ docker-compose exec scaffolding-wp-mimotic.test-1 bash -c "wp --info --path=public"
```

---

#### made with ❤ ️

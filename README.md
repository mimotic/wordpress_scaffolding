# Mimotic Wordpress

## Setup develop environment

* Copy `.env.example` to `.env` and fill fields.

* Execute `docker-compose up --build`


### 1. Restoring data from develop

For restoring data. You can use docker exec command with -i flag, similar to the following:

```

$ docker exec -i scaffolding-wp-db-1 sh -c 'exec mysql -uroot -p"passpass" -e "CREATE DATABASE mimoticdb"'

$ docker exec -i scaffolding-wp-db-1 sh -c 'exec mysql -uroot -p"passpass" --database=mimoticdb' < FOLDER_WHERE_DUMPS_EXISTS/mimotic_DB.sql
```

### 2. Fix URLs


Go to `OHIGT39G3852_options` table and excutes:

```
UPDATE `mimoticdb`.`OHIGT39G3852_options` SET `option_value` = 'http://localhost:8080' WHERE (`option_id` = '1');

UPDATE `mimoticdb`.`OHIGT39G3852_options` SET `option_value` = 'http://localhost:8080' WHERE (`option_id` = '2');
```

### 3. Update environment
You should update `.env` properly. In dev should be 

```
DB_NAME=mimoticdb
DB_USER=mimoticdbu
DB_PASSWORD=passpass
DB_HOST=dba
DB_PORT=3306
TABLE_PREFIX=OHIGT39G3852_
SITE_ENVIROMENT=http://localhost:8080
SITE_URL=http://localhost:8080
WP_ALLOW_MULTISITE=false
MULTISITE=false
DISALLOW_FILE_MODS=true
```


### 4. Launch composer install

```
$ docker exec scaffolding-wp-php-1 composer install
// OR
$ docker-compose exec php composer install 
```

## Executing wp-cli commands:

```
$ docker exec scaffolding-wp-php-1 wp --info
// OR
$ docker-compose exec php bash -c "cd public && pwd && wp --info"
```

`scaffolding-wp-php-1` is the name for the generating container.


## Open BASH

```
$ docker exec -it scaffolding-wp-php-1 bash
```

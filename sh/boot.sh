#!/usr/bin/env bash
#cd ..
cp .env.example .env
docker-compose up -d --build
docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        composer install --ignore-platform-reqs
docker exec -i blog-mysql-1 sh -c 'exec mysql -umimoticdbu -p"password" --database=mimoticdb' < db/mimoticfin_wp.sql
docker ps
open http://localhost/

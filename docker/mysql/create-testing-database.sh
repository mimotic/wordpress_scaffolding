#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS mimoticdb;
    GRANT ALL PRIVILEGES ON \`mimoticdb%\`.* TO '$DB_USER'@'%';
EOSQL

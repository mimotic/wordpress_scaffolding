#!/usr/bin/env bash
route=$1
cd ${route}

rm -rf backups/${route}
mkdir backups/${route}
cd backups/${route}

wp db dump --path=../../public --porcelain db.sql

git config --global user.name bot
git config --global user.email bot@mimotic.com
git config --global core.editor vi

git add db.sql -f
git commit -m 'database server backup'
git push

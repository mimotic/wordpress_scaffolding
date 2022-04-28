#!/usr/bin/env bash
route=$1
cd ${route}
rm -rf backups/${route}
mkdir backups/${route}
cd backups/${route}
wp db dump --path=../../public --porcelain db.sql
git add db.sql -f
git commit -m 'database server backup'
git push

#!/usr/bin/env bash
route=$1
cd ${route}

cd public

wp core update
wp core update-db
wp language core update

wp theme update --all
wp language theme update --all

wp plugin update --all
wp language plugin update --all

wp wc update

git add -A
git commit -m 'server wordpress autoupdates'
git push

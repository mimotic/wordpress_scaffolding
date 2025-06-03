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

git config --global user.name bot
git config --global user.email bot@mimotic.com
git config --global core.editor vi

git add -A
git commit -m 'server wordpress autoupdates'
git push

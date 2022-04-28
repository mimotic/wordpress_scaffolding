#!/usr/bin/env bash
route=$1
cd ${route}

cd public

wp yoast index
wp cache flush
wp rewrite flush

git add -A
git commit -m 'server wordpress cleaner'
git push

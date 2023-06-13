#!/usr/bin/env bash
route=$1
cd ${route}

cd public

wp revisions clean
wp comment delete $(wp comment list --status=spam --format=ids)

wp yoast index
wp cache flush
wp rewrite flush

git add -A
git commit -m 'server wordpress cleaner'
git push

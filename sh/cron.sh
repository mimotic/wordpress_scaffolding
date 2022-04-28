#!/usr/bin/env bash
route=$1
cd ${route}
cd public
wp cron event run --due-now

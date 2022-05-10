#!/usr/bin/env bash

# 1.- Get new salts for your wp-config.php file
wp config shuffle-salts --path=public

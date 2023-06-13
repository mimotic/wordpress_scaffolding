#!/usr/bin/env bash

# install wordpress in spanish
wp core download --locale=es_ES --path=public

# install wordpress cli revisions plugin
wp package install trepmal/wp-revisions-cli


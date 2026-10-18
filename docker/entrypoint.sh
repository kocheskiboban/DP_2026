#!/bin/sh
# The uploads volume is created by Docker as root: give it to the web server user.
mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads 2>/dev/null || true
exec docker-php-entrypoint "$@"

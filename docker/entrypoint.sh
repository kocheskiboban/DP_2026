#!/bin/sh
mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads 2>/dev/null || true
exec docker-php-entrypoint "$@"

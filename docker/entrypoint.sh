#!/bin/sh
<<<<<<< HEAD
# The uploads volume is created by Docker as root: give it to the web server user.
=======
>>>>>>> 040d8159d5b700aee53b4cee7fc521b519432964
mkdir -p /var/www/html/uploads
chown -R www-data:www-data /var/www/html/uploads 2>/dev/null || true
exec docker-php-entrypoint "$@"

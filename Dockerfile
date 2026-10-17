# Ohrid Summer — web container (PHP 8.3 + Apache) for RepoRun / any Docker host
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql mysqli \
 && a2enmod rewrite headers expires \
 && { echo 'upload_max_filesize = 4M'; echo 'post_max_size = 8M'; echo 'expose_php = Off'; } > /usr/local/etc/php/conf.d/ohrid-summer.ini

COPY docker/apache.conf /etc/apache2/conf-enabled/ohrid-summer.conf
COPY docker/entrypoint.sh /usr/local/bin/ohrid-entrypoint
COPY . /var/www/html/

RUN chmod +x /usr/local/bin/ohrid-entrypoint \
 && rm -rf /var/www/html/docker /var/www/html/start.command /var/www/html/start.bat \
 && mkdir -p /var/www/html/uploads \
 && chown -R www-data:www-data /var/www/html/uploads

ENTRYPOINT ["ohrid-entrypoint"]
CMD ["apache2-foreground"]

FROM debian:bullseye as BuilderGit
ARG branch=main
# Build Webserver without Data
# This allow caching this Service
FROM php:8.1-apache as cakephp-webserver
RUN apt-get update && \
    apt-get install -y libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-configure pdo_mysql && \
    docker-php-ext-install pdo_mysql &&  \
    a2enmod rewrite
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Run Composer install
FROM composer as builder-composer
COPY Claninterface /app/
RUN composer install --ignore-platform-reqs


# Add Data and set writing permissions
FROM cakephp-webserver
COPY --from=builder-composer --chown=www-data:www-data /app/ /var/www/html
RUN chmod u+x bin/*
RUN mkdir -p /var/www/html/tmp && chown -R www-data:www-data /var/www/html/tmp/  && chmod u+wx -R /var/www/html/tmp/ && \
    mkdir -p /var/www/html/logs && chown -R www-data:www-data /var/www/html/logs/  && chmod u+wx -R /var/www/html/logs/
WORKDIR /var/www/html




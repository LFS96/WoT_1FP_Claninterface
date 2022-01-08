FROM debian:bullseye as BuilderGit
ARG branch=main
RUN apt-get update && \
    apt-get install -y git && \
    mkdir /git && cd /git && \
    git clone -b $branch --single-branch --depth 1  https://github.com/LFS96/WoT_1FP_Claninterface.git

FROM composer as BuilderComposer
COPY --from=BuilderGit /git/WoT_1FP_Claninterface/Claninterface /app/
RUN composer install --ignore-platform-reqs

FROM php:7.4-apache
COPY --from=BuilderComposer /app/ /var/www/html
COPY --from=BuilderGit /git/WoT_1FP_Claninterface/docker/docker.php /var/www/html/config/docker.php
RUN apt-get update && \
    apt-get install -y libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-configure pdo_mysql && \
    docker-php-ext-install pdo_mysql &&  \
    a2enmod rewrite && \
    chmod u+x bin/*
WORKDIR /var/www/html


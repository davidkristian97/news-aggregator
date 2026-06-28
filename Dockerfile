FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    curl zip unzip autoconf g++ make \
    libxml2-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring xml zip bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts && \
    rm -f bootstrap/cache/*.php

EXPOSE 8000

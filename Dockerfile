FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    curl zip unzip \
    libxml2-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring xml zip bcmath

WORKDIR /var/www

COPY . .

EXPOSE 8000

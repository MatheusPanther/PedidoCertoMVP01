FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache bash curl icu-dev libzip-dev mysql-client nodejs npm supervisor \
    && docker-php-ext-install intl pdo_mysql zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run build \
    && chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]

FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash curl icu-dev libzip-dev \
    postgresql-client postgresql-dev \
    nodejs npm supervisor \
    && docker-php-ext-install intl pdo pdo_pgsql pgsql zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run build \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 9000

CMD ["php-fpm"]
 

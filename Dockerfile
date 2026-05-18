FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    npm \
    nodejs \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm ci || npm install
RUN npm run build

RUN mkdir -p database \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && touch database/database.sqlite

RUN chown -R www-data:www-data /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache database && chmod -R a+rwX storage bootstrap/cache database

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD sh -c "mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database && touch database/database.sqlite && php artisan config:clear || true && php artisan route:clear || true && php artisan view:clear || true && php artisan cache:clear || true && php artisan migrate --force && php artisan db:seed --force && chown -R www-data:www-data storage bootstrap/cache database && chmod -R a+rwX storage bootstrap/cache database && apache2-foreground"
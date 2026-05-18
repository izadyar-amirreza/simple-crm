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

RUN chmod -R 775 storage bootstrap/cache

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD sh -c "touch database/database.sqlite && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache && chmod -R 775 storage bootstrap/cache database && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear && php artisan migrate --force && php artisan db:seed --force && apache2-foreground"
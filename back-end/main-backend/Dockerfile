FROM php:8.2-fpm

# Install dependencies & extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libonig-dev libssl-dev \
    libzip-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring opcache \
        gd zip xml bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8001

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"]

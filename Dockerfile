# STAGE 1: Build Assets (Node.js)
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# STAGE 2: PHP Base & Dependencies
FROM dunglas/frankenphp:1-php8.3 AS runner

# Install System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libmagickwand-dev \
    git curl --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# Install PHP Extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring bcmath zip exif pcntl sockets xml soap intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd
RUN pecl install redis imagick && docker-php-ext-enable redis imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency files first (untuk optimasi cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy seluruh project
COPY . .

# Copy hasil build frontend dari STAGE 1
COPY --from=asset-builder /app/public/build ./public/build

# Finalisasi Composer (Autoload & Scripts)
RUN composer dump-autoload --optimize --no-dev

# Set Permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Jalankan Octane dengan setting agresif untuk 2000 user
# Menggunakan 'auto' pada workers akan menyesuaikan dengan CPU core VM Proxmox Anda
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
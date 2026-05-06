# =========================
# STAGE 1: Build Frontend
# =========================
FROM node:20-alpine AS asset-builder

WORKDIR /app
COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# =========================
# STAGE 2: PHP + Laravel
# =========================
FROM dunglas/frankenphp:1-php8.4

# Install dependency system
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev \
    libmagickwand-dev \
    git curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo pdo_mysql mysqli \
    mbstring bcmath zip exif pcntl \
    sockets xml intl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN pecl install redis imagick \
    && docker-php-ext-enable redis imagick
    
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN echo "memory_limit=1024M" > /usr/local/etc/php/conf.d/custom-limits.ini \
    && echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/custom-limits.ini \
    && echo "post_max_size=60M" >> /usr/local/etc/php/conf.d/custom-limits.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom-limits.ini \
    && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/custom-limits.ini \
    && echo "max_file_uploads=100" >> /usr/local/etc/php/conf.d/custom-limits.ini
# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install dependency Laravel
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader

# Copy project
COPY . .

# Copy hasil build frontend
COPY --from=asset-builder /app/public/build ./public/build

# Optimasi Laravel
RUN composer dump-autoload --optimize --no-dev

# Permission (penting untuk K8s)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Jalankan Octane dengan setting agresif untuk 2000 user
# Menggunakan 'auto' pada workers akan menyesuaikan dengan CPU core VM Proxmox Anda
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

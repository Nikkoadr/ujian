FROM dunglas/frankenphp:latest

# =========================
# SYSTEM DEPENDENCIES
# =========================
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev \
    libmagickwand-dev \
    git curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# =========================
# PHP EXTENSIONS (CORE - PASTI AMAN)
# =========================
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    bcmath \
    zip \
    exif

# =========================
# PHP EXTENSIONS (UNTUK OCTANE)
# =========================
RUN docker-php-ext-install pcntl

# =========================
# GD (IMAGE PROCESSING - DIPISAH)
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# =========================
# OPTIONAL (AMAN)
# =========================
RUN docker-php-ext-install sockets

# =========================
# XML & SOAP
# =========================
RUN docker-php-ext-install xml soap

# =========================
# INTL (OPSIONAL - JANGAN GAGALIN BUILD)
# =========================
RUN docker-php-ext-install intl || true

# =========================
# PECL EXTENSIONS
# =========================
RUN pecl install redis imagick \
    && docker-php-ext-enable redis imagick

# =========================
# COMPOSER
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# APP SETUP
# =========================
WORKDIR /app
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Frontend build (optional)
RUN npm install && npm run build || echo "skip frontend build"

# Permission fix
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# =========================
# PORT
# =========================
EXPOSE 8000

# =========================
# START OCTANE
# =========================
CMD ["sh", "-c", "php artisan optimize:clear || true && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --workers=4 --task-workers=2 --max-requests=500"]
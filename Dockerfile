FROM php:8.3-fpm

# --- Dépendances système ---
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# --- Extensions PHP nécessaires à Laravel + Postgres (Neon) ---
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    gd

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- Copie du code ---
COPY . .

# --- Installation des dépendances PHP (sans les paquets de dev) ---
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# --- Permissions Laravel (storage, cache) ---
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# --- Configuration nginx ---
COPY docker/nginx.conf /etc/nginx/sites-available/default

# --- Configuration php-fpm (écoute sur socket, pas TCP) ---
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# --- Script de démarrage ---
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]

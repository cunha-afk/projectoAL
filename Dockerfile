FROM php:8.2-fpm

# Instalar dependências de sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer (copia do container oficial do composer)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Evita problemas de permissão comuns
RUN useradd -G www-data,root -u 1000 laraveluser

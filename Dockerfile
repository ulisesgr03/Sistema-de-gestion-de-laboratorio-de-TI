FROM php:8.4-apache

# 1. Instalar dependencias del sistema y extensiones de PHP para Laravel (MySQL/MariaDB y Zip)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# 2. Copiar Composer oficial al contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configurar Apache para que apunte a /public y activar mod_rewrite
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Definir directorio de trabajo y copiar todo el proyecto (incluyendo artisan)
WORKDIR /var/www/html
COPY . /var/www/html

# 5. Instalar paquetes de Composer y dar permisos a storage y cache
RUN composer install --no-interaction --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
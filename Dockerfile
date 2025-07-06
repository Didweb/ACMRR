# Dockerfile
FROM php:8.2-fpm

# Instalar extensiones necesarias y utilidades
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip intl pdo_mysql mbstring xml gd

# Instalar Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Symfony CLI (opcional, útil para comandos symfony)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html

# Copiar el proyecto symfony
COPY ./app /var/www/html

# Instalar dependencias si no están
RUN composer install || true

# Permisos para www-data
RUN mkdir -p /var/www/html/var && chown -R www-data:www-data /var/www/html/var

# Instalar Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar Xdebug para cobertura
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


EXPOSE 9000

CMD ["php-fpm"]
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip zip \
    libzip-dev \
    libpq-dev \
    pkg-config \
    libicu-dev \
 && docker-php-ext-install pdo pdo_pgsql pgsql zip intl \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .

# Avoid git "dubious ownership" inside build context when composer inspects VCS
RUN git config --global --add safe.directory /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction \
 && chown -R www-data:www-data /var/www/html/writable

EXPOSE 80

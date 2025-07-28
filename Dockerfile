FROM php:8.2-fpm

# Installer les extensions nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le code source dans le conteneur
COPY . .

# Donner les droits appropriés
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

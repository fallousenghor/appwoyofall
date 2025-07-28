FROM php:8.2-fpm

# Installer nginx, supervisor, libpq-dev (PostgreSQL) et extensions PHP
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copier la config nginx et supervisord (à créer dans ton projet)
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le code source dans le conteneur
COPY . .

# Droits
RUN chown -R www-data:www-data /var/www

# Exposer le port HTTP
EXPOSE 80

# Lancer supervisord (qui lance PHP-FPM + nginx)
CMD ["/usr/bin/supervisord", "-n"]

# =============================================================================
# Optelya — image PHP 8.5 + Apache pour le développement.
#
# Images publiques uniquement (pas de registre privé) : le dépôt est sur GitHub
# et doit pouvoir être construit par n'importe qui.
#
# Le code n'est PAS copié dans l'image : docker-compose bind-monte le projet.
# Image de développement uniquement : le serveur reçoit une archive par rsync
# (voir readme/deploiement.md), pas une image.
# =============================================================================
FROM php:8.5-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype-dev \
        default-mysql-client \
        git \
        curl \
        unzip \
        less \
    && rm -rf /var/lib/apt/lists/*

# gd est compilé avec webp : WordPress sait alors produire des sous-tailles
# WebP. Sans --with-webp, il se rabat en silence sur le format source.
# No opcache here: PHP 8.5 compiles it in, and docker-php-ext-install fails on it.
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        gd \
        exif

RUN { \
        echo 'upload_max_filesize = 64M'; \
        echo 'post_max_size = 64M'; \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 300'; \
        echo 'max_input_vars = 3000'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# revalidate_freq=0 : le code bind-monté est relu à chaque requête, sinon une
# modification sur l'hôte ne serait pas visible avant expiration du cache.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Latest WP-CLI phar. Its bundled code raises PHP 8.5 deprecations; the ini
# setting below hides those raised before WordPress boots. Once booted, the
# development config (WP_DEBUG) resets error_reporting, so later ones still
# reach debug.log — see readme/docker.md.
RUN curl -fsSL -o /usr/local/bin/wp-cli.phar \
        https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && printf '#!/bin/sh\nexec php -d "error_reporting=E_ALL & ~E_DEPRECATED" /usr/local/bin/wp-cli.phar "$@"\n' \
        > /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp

# MySQL 8.4 (TLS actif, voir docker-compose.yml) présente un certificat
# auto-signé, que le client mariadb refuse par défaut. Ce ~/.my.cnf le fait
# accepter par `mysql` lancé dans ce conteneur (vérifié : sans lui, ERROR 2026).
# Sans effet sur `wp db`, qui appelle le client avec --no-defaults.
RUN printf '[client]\nssl-verify-server-cert=0\n' > /root/.my.cnf \
    && chmod 600 /root/.my.cnf

# rewrite : permaliens. headers/expires/deflate : le bloc de sécurité de
# website/.htaccess les exige.
RUN a2enmod rewrite headers expires deflate
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]

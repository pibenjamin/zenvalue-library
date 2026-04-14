FROM php:8.2-apache

# Modules Apache
RUN a2enmod rewrite headers expires deflate
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Dépendances système
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libicu-dev curl \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Config OPcache
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Config PHP
RUN { \
        echo 'memory_limit=512M'; \
        echo 'upload_max_filesize=50M'; \
        echo 'post_max_size=50M'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache config
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Workdir
WORKDIR /var/www/html

EXPOSE 80

# ⭐ Commande simple
CMD ["apache2-foreground"]
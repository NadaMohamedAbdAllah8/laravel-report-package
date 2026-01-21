FROM php:8.3-apache

# enable Apache mod_rewrite (Laravel needs it for pretty URLs)
RUN a2enmod rewrite

# install PHP extensions Laravel usually needs + redis-cli + build deps for pecl
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    redis-tools \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# set the working dir
WORKDIR /var/www/html

# copy your project (you can also rely on docker compose volume instead)
COPY . /var/www/html

# make sure Apache serves from public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

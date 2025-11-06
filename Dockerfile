FROM php:8.3-apache

# enable Apache mod_rewrite (Laravel needs it for pretty URLs)
RUN a2enmod rewrite

# install PHP extensions Laravel usually needs
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# set the working dir
WORKDIR /var/www/html

# copy your project (you can also rely on docker compose volume instead)
COPY . /var/www/html

# make sure Apache serves from public/
# (the image uses /var/www/html by default, so we point it to public)
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

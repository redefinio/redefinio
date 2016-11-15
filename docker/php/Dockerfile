FROM php:7-apache

WORKDIR /var/www

RUN usermod -u 1000 www-data

# Apache cnfiguration
COPY symfony2.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# PHP configuration
RUN apt-get update && apt-get install -y \
  git \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng12-dev \
  libc-client-dev libkrb5-dev \
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-install zip \
  && docker-php-ext-install opcache \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd
COPY php.ini /usr/local/etc/php/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80
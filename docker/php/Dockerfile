FROM php:5-apache

WORKDIR /var/www

RUN usermod -u 1000 www-data

# Apache cnfiguration
COPY symfony2.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# PHP configuration
RUN apt-get update && apt-get install -y \
  && docker-php-ext-install pdo_mysql
COPY php.ini /usr/local/etc/php/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

EXPOSE 80
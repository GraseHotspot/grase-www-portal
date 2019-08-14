FROM php:7.2-apache

ENV APACHE_DOCUMENT_ROOT /usr/share/grase/symfony4/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update \
 && apt-get install -y git zlib1g-dev libicu-dev g++ unzip
RUN docker-php-ext-install pdo pdo_mysql zip intl
RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

ADD 000-default.conf /etc/apache2/sites-available/000-default.conf

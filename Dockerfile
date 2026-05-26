FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

WORKDIR /var/www/html/

RUN composer install

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
FROM php:8.1-apache

COPY ./docker/php/etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/

RUN apt update && \
apt install -y zip && \
a2enmod rewrite && \
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php --filename=composer --install-dir=/bin --version=2.5.4 && \
php -r "unlink('composer-setup.php');"

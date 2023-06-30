FROM php:8.1-apache

COPY ./docker/php/etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/
COPY ./docker/php/usr/local/etc/php/conf.d/20-xdebug.ini /usr/local/etc/php/conf.d/

RUN apt update && \
apt install -y zip && \
a2enmod rewrite && \
pecl install xdebug-3.2.0 && \
docker-php-ext-enable xdebug && \
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php --filename=composer --install-dir=/bin --version=2.5.4 && \
php -r "unlink('composer-setup.php');"

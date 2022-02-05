FROM php:8.1-apache

LABEL org.opencontainers.image.authors="pppq <pppq@users.noreply.github.com>"

RUN apt-get update && apt-get install -y libtidy-dev \
	&& docker-php-ext-configure tidy \
    && docker-php-ext-install -j$(nproc) tidy \
    && docker-php-ext-install mysqli

COPY . /srv/app
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/app

RUN chown -R www-data:www-data /srv/app && a2enmod rewrite

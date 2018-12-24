FROM php:7.2-fpm
MAINTAINER Bezama Marolahy Randriamifidy <marolahy@gmail.com>
RUN docker-php-ext-install pdo_mysql

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get update -yqq \
    && apt-get install git zlib1g-dev libsqlite3-dev -y \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_sqlite

RUN curl -fsSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require phpunit/phpunit ^7.0 --no-progress --no-scripts --no-interaction

ENV PATH /root/.composer/vendor/bin:$PATH
CMD ["phpunit"]

COPY conf/php.ini /etc/php/7.2/fpm/conf.d/40-custom.ini

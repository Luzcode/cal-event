FROM composer as app-composer
WORKDIR /www
COPY composer.* ./
RUN composer update
RUN composer install

FROM php:7-fpm
WORKDIR /www
COPY --from=app-composer /www/vendor ./vendor
COPY . ./
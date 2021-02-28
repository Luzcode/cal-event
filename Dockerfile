FROM composer as app-composer
WORKDIR /www
COPY composer.* ./
RUN composer update
RUN composer install

FROM nginx:alpine
WORKDIR /www
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app-composer /www/vendor ./vendor
COPY . ./
EXPOSE 80
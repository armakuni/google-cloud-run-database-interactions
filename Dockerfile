FROM composer as composer

RUN apk --no-cache add alpine-sdk autoconf \
  && pecl install opencensus-alpha \
  && docker-php-ext-enable opencensus \
  && apk del alpine-sdk autoconf

COPY . /app
RUN composer install -o -a --apcu-autoloader

FROM php:7.3-apache

RUN pecl install opencensus-alpha
COPY --from=composer /app /var/www/html/

RUN chown -R www-data:www-data /var/www/html/
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

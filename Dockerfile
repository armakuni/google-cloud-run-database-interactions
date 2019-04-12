FROM php:7.3-cli as opencensus
RUN apt-get update && apt-get install -y git build-essential
RUN git clone https://github.com/census-instrumentation/opencensus-php.git /extension
WORKDIR /extension/ext
RUN phpize \
    && ./configure \
    && make

FROM composer as composer

COPY --from=opencensus /extension/ext/modules/opencensus.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/opencensus.so
RUN echo "extension=opencensus.so" > /usr/local/etc/php/conf.d/opencensus.ini

COPY . /app
RUN composer install -o -a --apcu-autoloader

FROM php:7.3-apache
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=opencensus /extension/ext/modules/opencensus.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/opencensus.so
RUN echo "extension=opencensus.so" > /usr/local/etc/php/conf.d/opencensus.ini

COPY --from=composer /app /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

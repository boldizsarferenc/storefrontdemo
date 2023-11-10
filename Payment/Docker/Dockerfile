FROM trafex/php-nginx:latest

COPY --from=composer /usr/bin/composer /usr/bin/composer

USER root

RUN apk update
RUN apk add --no-cache \
  git \
  php81-iconv \
  php81-tokenizer \
  php81-simplexml \
  php81-pdo \
  php81-pdo_mysql\
  php81-pecl-xdebug\
  php81-posix

USER nobody

RUN composer install --optimize-autoloader --no-interaction --no-progress

FROM php:8.0.5-fpm

ARG USER_ID
ARG GROUP_ID

RUN mkdir -p /var/www/html &&\
  mkdir -p /var/run/php-fpm &&\
  mkdir -p /var/lib/php/sessions &&\
  mkdir -p /.composer &&\
  mkdir -p /var/www/.npm

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
  userdel -f www-data &&\
  if getent group www-data ; then groupdel www-data; fi &&\
  groupadd -g ${GROUP_ID} www-data &&\
  useradd -l -u ${USER_ID} -g www-data www-data &&\
  install -d -m 0755 -o www-data -g www-data /home/www-data &&\
  chown --changes --silent --no-dereference --recursive \
  --from=33:33 ${USER_ID}:${GROUP_ID} \
  /home/www-data \
  /.composer \
  /var/run/php-fpm \
  /var/lib/php/sessions \
  /var/www/html \
  ;fi

WORKDIR /var/www/html

# Install mysql for php
RUN docker-php-ext-install mysqli pdo pdo_mysql &&\
  docker-php-ext-enable pdo_mysql &&\
  pecl install xdebug &&\
  docker-php-ext-enable xdebug

ADD php/php.ini "$PHP_INI_DIR/php.ini"

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install node 14.x and npm acording to that version
RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash - &&\
    apt update  &&\
    apt install -y nodejs &&\
    chown -R 33:33 "/var/www/.npm"

# Install wp cli
RUN curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar &&\
  cp /bin/wp-cli.phar /bin/wp &&\
  chmod +x /bin/wp-cli.phar /bin/wp

USER www-data

FROM ubuntu:22.04

LABEL maintainer="mimotic"

ARG PHP_VERSION=8.3
ARG NODE_VERSION=20
ARG USER=mimotic
ARG WWWGROUP=1000

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Europe/Madrid
ENV PHP_VERSION=${PHP_VERSION}

WORKDIR /var/www/html

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y --no-install-recommends software-properties-common lsb-release ca-certificates curl gnupg \
    && curl -sS https://keyserver.ubuntu.com/pks/lookup\?op=get\&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c | gpg --dearmor | tee /usr/share/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu $(lsb_release -sc) main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor | tee /usr/share/keyrings/nodesource.gpg > /dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
           nginx supervisor gosu git zip unzip sqlite3 dnsutils python3 nodejs yarn mysql-client \
           libcap2-bin libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
           php${PHP_VERSION}-cli php${PHP_VERSION}-dev php${PHP_VERSION}-fpm \
           php${PHP_VERSION}-curl php${PHP_VERSION}-gd php${PHP_VERSION}-mbstring php${PHP_VERSION}-mysql php${PHP_VERSION}-pgsql php${PHP_VERSION}-sqlite3 \
           php${PHP_VERSION}-xml php${PHP_VERSION}-zip php${PHP_VERSION}-bcmath php${PHP_VERSION}-soap php${PHP_VERSION}-intl php${PHP_VERSION}-readline \
           php${PHP_VERSION}-ldap php${PHP_VERSION}-imagick php${PHP_VERSION}-redis php${PHP_VERSION}-memcached php${PHP_VERSION}-xdebug php${PHP_VERSION}-pcov \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php${PHP_VERSION}

RUN groupadd --force -g ${WWWGROUP} ${USER} \
    && useradd -ms /bin/bash --no-user-group -g ${WWWGROUP} -u 1337 ${USER}

COPY runtimes/start-container /usr/local/bin/start-container
COPY runtimes/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY runtimes/nginx.conf /etc/nginx/conf.d/default.conf
COPY runtimes/php.ini /tmp/php.ini

RUN chmod +x /usr/local/bin/start-container \
    && install -m 0644 /tmp/php.ini /etc/php/${PHP_VERSION}/cli/conf.d/99-mimotic.ini \
    && install -m 0644 /tmp/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-mimotic.ini \
    && sed -i "s@listen = /run/php/php${PHP_VERSION}-fpm.sock@listen = 0.0.0.0:9000@" /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf \
    && sed -i "s/;clear_env = no/clear_env = no/" /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf \
    && sed -i "s/^user = www-data/user = ${USER}/" /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf \
    && sed -i "s/^group = www-data/group = ${USER}/" /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf \
    && mkdir -p /run/php /var/log/nginx

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/start-container"]

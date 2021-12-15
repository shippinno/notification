FROM php:7.4-fpm-alpine

#RUN apk update && \
#    apk add --no-cache git mysql-client curl libmcrypt libmcrypt-dev openssh-client \
#    libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev g++ make autoconf nodejs python2 sqlite-dev && \
#    apk --update add tzdata && \
#    cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && \
#    apk del tzdata && \
#    rm -rf /tmp/src && \
#    rm -rf /var/cache/apk/*

#RUN apk add \
#    --repository http://dl-cdn.alpinelinux.org/alpine/v3.6/main \
#    --no-cache \
#    rabbitmq-c-dev

#RUN docker-php-ext-configure pdo_sqlite
#RUN docker-php-ext-install soap pdo_sqlite bcmath
#RUN pecl install amqp
#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug
#RUN docker-php-ext-enable amqp
#RUN docker-php-ext-install \
#    pdo_mysql \
#    mysqli \
#    mbstring \
#    pcntl

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN echo "memory_limit = 1024M" > /usr/local/etc/php/conf.d/memory_limit.ini

# COPY ./auth.json /root/.composer/auth.json

RUN mkdir /code
WORKDIR /code
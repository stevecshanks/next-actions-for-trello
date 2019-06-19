FROM php:7.3-cli

RUN curl -sS https://getcomposer.org/installer | php \
    && chmod +x composer.phar\
    && mv composer.phar /usr/local/bin/composer

COPY . /usr/src/app

WORKDIR /usr/src/app

RUN composer install

EXPOSE 80

CMD ["bin/console", "server:run", "0.0.0.0:80"]

FROM php:7.1-alpine

LABEL  maintainer "kojiro <kojiro@ryusei-sha.com>"

# Install extensions
RUN apk update && \
    apk add zlib-dev && \
    docker-php-ext-install zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin \
&& ln -s /usr/bin/composer.phar /usr/bin/composer

# Set a path to command.
ENV PATH $PATH:/root/.composer/vendor/bin

# Install php-checklist
RUN composer.phar global require kojiro526/php-checklist


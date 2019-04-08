FROM php:7.1-alpine

LABEL  maintainer "kojiro <kojiro@ryusei-sha.com>"

ARG HTTP_PROXY_REQUEST_FULLURI
ARG HTTPS_PROXY_REQUEST_FULLURI
ENV HTTP_PROXY_REQUEST_FULLURI $HTTP_PROXY_REQUEST_FULLURI
ENV HTTPS_PROXY_REQUEST_FULLURI $HTTPS_PROXY_REQUEST_FULLURI

# Install extensions
RUN apk update && \
    apk add zlib-dev && \
    docker-php-ext-install zip && \
    apk add --no-cache libpng libpng-dev && \
    docker-php-ext-install gd && \
    apk del libpng-dev

# Set proxy
RUN echo "export http_proxy="$HTTP_PROXY >> ~/.bashrc && \
    echo "export https_proxy="$HTTPS_PROXY >> ~/.bashrc

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin \
&& ln -s /usr/bin/composer.phar /usr/bin/composer

# Set a path to command.
ENV PATH $PATH:/root/.composer/vendor/bin

# Install php-checklist
RUN composer.phar global require kojiro526/php-checklist

WORKDIR /work


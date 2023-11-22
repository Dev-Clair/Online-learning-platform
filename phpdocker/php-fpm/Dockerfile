FROM phpdockerio/php:8.2-fpm

WORKDIR "/application"

# PHP extensions
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.2-mysql \
        php8.2-gd \
        php8.2-pdo \
        php8.2-pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Node.js and npm
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        nodejs=14.* \
        npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

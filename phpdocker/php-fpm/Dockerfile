FROM phpdockerio/php:8.2-fpm

WORKDIR "/application"

# PHP extensions
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.2-mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get -y --no-install-recommends install \
        nodejs=18.* \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

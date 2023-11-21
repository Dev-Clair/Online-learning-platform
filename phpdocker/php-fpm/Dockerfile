FROM phpdockerio/php:8.2-fpm
WORKDIR "/application"

# PHP8.2-Mysql
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.2-mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Node.js and npm
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        nodejs \
        npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# npm dependencies
COPY package*.json ./
RUN npm install

# CMD ["php-fpm"]

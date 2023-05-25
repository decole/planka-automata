FROM php:8.1-cli

RUN apt-get update -q -y \
    && apt-get install -q -y --no-install-recommends \
    zlib1g-dev \
    curl \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    zip \
    libmcrypt-dev \
    unzip \
    git \
    procps \
    sudo \
    # Imagick extension
    libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install xml \
    && docker-php-ext-enable xml \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update -q -y \
    && apt-get install -q -y --no-install-recommends \
    libzip-dev \
    zip \
    psmisc

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install zip \
    && docker-php-ext-install sockets

ADD php-fpm.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

#
#RUN composer install

# setup go lang and road-runner
#RUN vendor/bin/rr get --location bin/

EXPOSE 8080/tcp
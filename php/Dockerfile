FROM php:7.4-fpm

RUN apt-get -y update \
    && apt-get install -y libmcrypt-dev unzip libicu-dev g++ libzip-dev zip libonig-dev \
	&& docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install mbstring pdo_mysql pdo intl mysqli zip \
    && docker-php-ext-enable mysqli
	

RUN groupmod -g 1000 www-data && \
    usermod -u 1000 www-data
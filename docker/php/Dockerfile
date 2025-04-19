FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y curl libmagickwand-dev libpng-dev libonig-dev libxml2-dev zip

# Arguments defined in docker-compose.yml
#ARG user
#ARG uid

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql


RUN pecl install redis \
        && docker-php-ext-enable redis

RUN pecl install imagick && docker-php-ext-enable imagick

# Устанавливаем системные зависимости и необходимые библиотеки для расширения GD и Zip
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширение GD с поддержкой FreeType и JPEG
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Устанавливаем расширение Zip
RUN docker-php-ext-install zip


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user


# Set working directory
WORKDIR /var/www

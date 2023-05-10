FROM php:8.1.0-fpm

COPY composer.lock composer.json /var/www/

WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libaio1 wget && apt-get clean autoclean && apt-get autoremove --yes &&  rm -rf /var/lib/{apt,dpkg,cache,log}/ \
    
    


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql 


# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Add user for laravel
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy application folder
COPY . /var/www

# Copy existing permissions from folder to docker
COPY --chown=www:www . /var/www
RUN chown -R www-data:www-data /var/www

# Copy application folder
#COPY . /var/www

# Copy existing permissions from folder to docker
#COPY --chown=www:www . /var/www
#RUN chown -R www-data:www-data /var/www
RUN chown -R $USER:www storage
RUN chown -R $USER:www bootstrap/cache
RUN chmod -R 775 storage
RUN chmod -R 775 bootstrap/cache

USER www

EXPOSE 9000
CMD ["php-fpm"]
# Note that q2a is incompatible with php 8.1 (Last checked 6/10/22)
# 7.4 is the last version with stable support for reCAPTCHA
FROM php:7.4-apache

# Install and enable the mysqli software to connect to our DB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install PHP extension calendar for use of badge plugin
RUN docker-php-ext-install calendar

# General updates to ensure smooth runtime
RUN apt-get update && apt-get upgrade -y && apt-get install -y libpng-dev git zip

# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install Python 3 and pip
RUN apt-get update && apt-get install -y python3 python3-pip

RUN apt-get update && apt-get install -y supervisor && rm -rf /var/lib/apt/lists/*

# Install apachelogs
RUN apt-get update && python3 -m pip install apachelogs

# Set this environment variable to allow Composer to run as super user
ENV COMPOSER_ALLOW_SUPERUSER=1

#Install Composer for downloading Google API and Facebook client with PHP
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /bin/composer
RUN composer require google/apiclient:^2.12
RUN composer require facebook/graph-sdk:~5.7
RUN composer require guzzlehttp/guzzle:^7.0

# Install GD library for dynamic image creation
RUN apt-get install -y libjpeg-dev libfreetype6-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Copy your Apache2 configuration file into the image
RUN a2enmod rewrite
COPY apache2.conf /etc/apache2/apache2.conf

COPY index.php /var/www/html/
COPY . /var/www/html/

# Use the webroot as the working directory
WORKDIR /var/www/html

# Holds config files not served by the website
RUN mkdir -p /var/www/config

# put the sitemap at the domain root 
COPY sitemap.xml /var/www/html/sitemap.xml

COPY supervisor_event_logger.sh /etc/supervisor_event_logger.sh
COPY supervisord.conf /etc/supervisor/supervisord.conf

# Ensure scripts are executable
RUN chmod a+x supervisor_event_logger.sh

EXPOSE 80/tcp
EXPOSE 443/tcp

# Start Supervisor to manage both processes
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]




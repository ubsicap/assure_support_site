# Note that q2a is incompatible with php 8.1 (Last checked 6/10/22)
# 7.4 is the last version with stable support for reCAPTCHA
FROM php:7.4-apache

# Install and enable the mysqli software to connect to our DB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

#Install PHP extension calendar for use of badge plugin
RUN docker-php-ext-install calendar

# General updates to ensure smooth runtime
RUN apt-get update && apt-get upgrade -y

# Use the webroot as the working directory
WORKDIR /var/www/html

# Holds config files not served by the website
RUN mkdir -p /var/www/config

# Install dev dependencies (These can be removed in production)
# RUN apt-get install -y sendmail vim
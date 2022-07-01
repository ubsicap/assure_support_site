# Note that q2a is incompatible with php 8.1 (Last checked 6/10/22)
FROM php:8.0-apache

# Install and enable the mysqli software to connect to our DB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli && docker-php-ext-install pdo_mysql


#Install PHP extension calendar for use of badge plugin
RUN docker-php-ext-install calendar

# Setup SSL info
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb
RUN sed -i 's,/etc/ssl/certs/ssl-cert-snakeoil.pem,/etc/ssl/cert.pem,g' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i 's,/etc/ssl/private/ssl-cert-snakeoil.key,/etc/ssl/private/privkey.pem,g' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i 's,#SSLCertificateChainFile,SSLCertificateChainFile,g' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i 's,/etc/apache2/ssl.crt/server-ca.crt,/etc/ssl/fullchain.pem,g' /etc/apache2/sites-available/default-ssl.conf

# Enable SSL
RUN a2ensite default-ssl

# General updates to ensure smooth runtime
RUN apt-get update && apt-get upgrade -y

# Use the webroot as the working directory
WORKDIR /var/www/html

# Holds config files not served by the website
RUN mkdir -p /var/www/config

# Install dev dependencies (These can be removed in production)
# RUN apt-get install -y sendmail vim
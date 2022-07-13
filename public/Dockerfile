# Note that q2a is incompatible with php 8.1 (Last checked 6/10/22)
# 7.4 is the last version with stable support for reCAPTCHA
FROM php:7.4-apache

# Install and enable the mysqli software to connect to our DB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install PHP extension calendar for use of badge plugin
RUN docker-php-ext-install calendar

# General updates to ensure smooth runtime
RUN apt-get update && apt-get upgrade -y && apt-get install -y libpng-dev

# Install GD library for dynamic image creation
RUN apt-get install -y libjpeg-dev libfreetype6-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Use the webroot as the working directory
WORKDIR /var/www/html

# Holds config files not served by the website
RUN mkdir -p /var/www/config

# Install dev dependencies (These can be removed in production)
# RUN apt-get install -y sendmail vim

# Enable SSL modules
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb

# Build args and environment variables for the web server's domain name
ARG DOMAIN
ENV DOMAIN_NAME=${DOMAIN}

# Modify SSL config files
ARG ssl_path=/etc/apache2/sites-available/default-ssl.conf
RUN sed -i 's,/etc/ssl/certs/ssl-cert-snakeoil.pem,/etc/ssl/cert.pem,g' ${ssl_path} && sed -i 's,/etc/ssl/private/ssl-cert-snakeoil.key,/etc/ssl/private/privkey.pem,g' ${ssl_path} && sed -i 's,#SSLCertificateChainFile,SSLCertificateChainFile,g' ${ssl_path} && sed -i 's,/etc/apache2/ssl.crt/server-ca.crt,/etc/ssl/fullchain.pem,g' ${ssl_path}

# Block direct access to the IP
RUN sed -i -e "/<\/VirtualHost>/i RewriteEngine on" ${ssl_path} && sed -i -e "/<\/VirtualHost>/i RewriteCond %{HTTP_HOST} ^(?:[0-9]{1,3}\.){3}[0-9]{1,3}" ${ssl_path} && sed -i -e "/<\/VirtualHost>/i RewriteRule ^(.*)$ http://${DOMAIN_NAME}/\$1 [L,R=301]" ${ssl_path}

# Redirect all HTTP traffic to HTTPS
RUN sed -i -e "/<\/VirtualHost>/i Redirect \"/\" \"https://${DOMAIN_NAME}/\"" '/etc/apache2/sites-available/000-default.conf'

# Copy the file into the sites-enabled directory
# RUN cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/

# Active the SSL configuration
RUN a2ensite default-ssl
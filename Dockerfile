# Note that q2a is incompatible with php 8.1 (Last checked 6/10/22)
FROM php:8.0-apache

# Copying all contents of the `question2answer/` directory
#   and using that as this container's working directory
COPY . /app
WORKDIR /app

# Install and enable the mysqli software to connect to our DB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# General updates to ensure smooth runtime
RUN apt-get update && apt-get upgrade -y
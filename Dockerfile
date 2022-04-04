FROM php:7.4-cli
COPY . /app
WORKDIR /app

RUN docker-php-ext-install mysqli

	
CMD [ "php", "./index.php" ]
#CMD [ "php", "./phpinfo.php" ]
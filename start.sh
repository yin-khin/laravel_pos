FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
 && docker-php-ext-install zip

COPY . .

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

RUN chmod +x start.sh

EXPOSE 10000
CMD ["./start.sh"]
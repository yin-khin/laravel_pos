FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install zip gd pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Fix Windows line endings + make executable
RUN sed -i 's/\r$//' start.sh && chmod +x start.sh

EXPOSE 10000
CMD ["./start.sh"]
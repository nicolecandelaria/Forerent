# Stage 1: Build assets
FROM node:22-alpine AS builder
WORKDIR /var/www
COPY package*.json ./
RUN npm ci
COPY . .

RUN echo "VITE_APP_NAME=Forerent" > .env

RUN npm run build

# Stage 2: Production image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx supervisor curl zip unzip git \
    libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql \
    mbstring exif pcntl bcmath gd \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . .

# Copy built assets from builder
COPY --from=builder /var/www/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy config files
COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
FROM dunglas/frankenphp:latest-php8.3

# Set environment
ENV SERVER_NAME=":8080"
ENV COMPOSER_ALLOW_SUPERUSER=1

# Pasang ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    intl \
    zip \
    opcache

WORKDIR /app

# Salin source code
COPY . /app

# Install dependensi Laravel tanpa dependensi dev
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan izin ke storage dan bootstrap cache
RUN chmod -R 777 /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
